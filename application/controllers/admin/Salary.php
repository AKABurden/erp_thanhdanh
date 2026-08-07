<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Salary extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->perViewTimekeeping = has_permission('timekeeping', '', 'view');
        $this->perViewOwnTimekeeping = has_permission('timekeeping', '', 'view_own');
        $this->perEditTimekeeping = has_permission('timekeeping', '', 'edit');
        $this->perDeleteTimekeeping = has_permission('timekeeping', '', 'delete');

        $this->perViewDashboardTimekeeping = has_permission('dashboard_timekeeping', '', 'view');
        $this->perViewOwnDashboardTimekeeping = has_permission('dashboard_timekeeping', '', 'view_own');
        $this->perEditDashboardTimekeeping = has_permission('dashboard_timekeeping', '', 'edit');

        $this->perViewSyntheticTimekeeping = has_permission('synthetic_timekeeping', '', 'view');
        $this->perViewOwnSyntheticTimekeeping = has_permission('synthetic_timekeeping', '', 'view_own');
    }


    public function timekeeping()
    {
        if (!$this->perViewTimekeeping && !$this->perViewOwnTimekeeping) {
            accessDenied();
        }
        $data['title'] = lang('CHI TIẾT GIỜ CÔNG');
        $data['staff_id_selected'] = $this->input->get('staff_id') ?? 0;
        $data['month_selected'] = $this->input->get('month') ?? null;
        $data['year_selected'] = $this->input->get('year') ?? null;
        $this->load->view('admin/salary/timekeeping', $data);
    }

    public function loadPersonnelTimekeeping()
    {
        $data = [];
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $staff = $this->input->get('staff');
        $department = $this->input->get('department');

        $tHead = '';
        $html = '';
        $tfoot = '';


        $listDate = getAllDateInMonth($month, $year, 'd/m');
        $tHead = '<tr>
            <th class="text-center" style="min-width: 50px;">' . lang('tnh_numbers') . '</th>
            <th class="text-center" style="min-width: 150px;">' . lang('MSNV') . '</th>
            <th class="text-center" style="min-width: 150px;">' . lang('Nhân viên') . '</th>
            <th class="text-center" style="min-width: 100px;">' . lang('Chức vụ') . '</th>
            <th class="text-center" style="width: 100px;">' . lang('Giờ') . '</th>
        ';

        $countDate = 0;
        foreach ($listDate as $k => $value) {
            $countDate++;
            $day = date("d", strtotime($k));
            $format = 'D';
            $time = mktime(12, 0, 0, $month, $day, $year);
            $date_word = '';
            if (date('m', $time) == $month) {
                $date_word = date($format, $time);
            }
            $date_word = convertDate($date_word);
            $tHead .= '<th class="text-center">' . $value . '<br>'.$date_word.'</th>';
        }
        $tHead .= '</tr>';

        //timekeeping
        $timekeepingId = 0;
        $this->db->select('*');
        $this->db->from('tbl_timekeeping');
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        $timekeeping = $this->db->get()->row_array();
        if (!empty($timekeeping)) {
            $timekeepingId = $timekeeping['id'];
        } else {
            $this->db->insert('tbl_timekeeping', [
                'month' => $month,
                'year' => $year,
                'count_date' => $countDate,
            ]);
            $timekeepingId = $this->db->insert_id();
        }
        //end timekeeping

        //check detail
        $this->db->select('*');
        $this->db->from('tbl_timekeeping_detail');
        $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
        $details = $this->db->get()->row_array();
        $check = false;
        $page = (int)$this->input->get('page');
        $limit = 30;
        $start = ($page - 1) * $limit;
        $array_staff = [];
        if (!empty($details)) {

            $this->db->select('tblstaff.staffid as staffid,tblstaff.code as code, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name,tblroles.name as name_role,tbl_timekeeping_detail_hour.type as type');
            $this->db->from('tblstaff');
            $this->db->where('active', 1);
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            if (!empty($staff)) {
                $this->db->where('tbl_timekeeping_detail.staff_id', $staff);
            }
            if (!empty($department)) {
                $this->db->where("EXISTS (
                    SELECT tblstaff_departments.staffid 
                    FROM tblstaff_departments 
                    WHERE tblstaff_departments.staffid = tblstaff.staffid
                    AND tblstaff_departments.departmentid = $department
                )");
            }
            $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
            $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
            $this->db->join('tbl_timekeeping_detail_hour',
                'tbl_timekeeping_detail_hour.timekeeping_detail_id= tbl_timekeeping_detail.id', 'left');
            $this->db->group_by('tbl_timekeeping_detail.staff_id');
            $this->db->group_by('tbl_timekeeping_detail_hour.type');
            $totalStaff = $this->db->get()->num_rows();

            $totalPage = ceil($totalStaff / $limit);

            $this->db->select('tblstaff.staffid as staffid,tblstaff.code as code, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name,tblroles.name as name_role,tbl_timekeeping_detail_hour.type as type');
            $this->db->from('tblstaff');
            $this->db->where('active', 1);
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            if (!empty($staff)) {
                $this->db->where('tbl_timekeeping_detail.staff_id', $staff);
            }
            if (!empty($department)) {
                $this->db->where("EXISTS (
                    SELECT tblstaff_departments.staffid 
                    FROM tblstaff_departments 
                    WHERE tblstaff_departments.staffid = tblstaff.staffid
                    AND tblstaff_departments.departmentid = $department
                )");
            }
            $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
            $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
            $this->db->join('tbl_timekeeping_detail_hour',
                'tbl_timekeeping_detail_hour.timekeeping_detail_id= tbl_timekeeping_detail.id', 'left');
            $this->db->group_by('tbl_timekeeping_detail.staff_id');
            $this->db->group_by('tbl_timekeeping_detail_hour.type');
            if (empty($staff)) {
                $this->db->limit($limit, $start);
            }
            $personnel = $this->db->get()->result_array();


            if (empty($staff)) {
                foreach ($personnel as $key => $value) {
                    $array_staff[] = $value['staffid'];
                }
                if (!empty($array_staff)) {
                    array_unique($array_staff);
                    $isTimes = "(
                        SELECT COUNT(*)
                        FROM tbl_timekeeping
                        LEFT JOIN tbl_timekeeping_detail on tbl_timekeeping_detail.timekeeping_id = tbl_timekeeping.id
                        WHERE tbl_timekeeping.month = '$month' AND tbl_timekeeping.year = '$year' AND tbl_timekeeping_detail.staff_id = tblstaff.staffid
                    )";

                    $this->db->select('tblstaff.staffid as staffid,tblstaff.code as code, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name,tblroles.name as name_role,0 as type');
                    $this->db->from('tblstaff');
                    $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
                    $this->db->where('active', 1);
                    $this->db->where_not_in('tblstaff.staffid', $array_staff);
                    $this->db->where("($isTimes = 0)");
                    $staffs = $this->db->get()->result_array();

                    if (!empty($staffs)) {
                        $personnel = array_merge($personnel, $staffs);
                    }
                }
            }
        } else {
            $this->db->select('tblstaff.staffid as staffid,tblstaff.code as code, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name,tblroles.name as name_role,0 as type');
            $this->db->from('tblstaff');
            $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
            $this->db->where('active', 1);
            $personnel = $this->db->get()->result_array();
            $check = true;
        }

        $arrPersonnel = [];
        $staff_id = '';
        $i = 0;
        if (!empty($personnel)) {
            foreach ($personnel as $key => $value) {
                $personnel_id = $value['staffid'];

                if ($value['staffid'] != $staff_id) {
                    $i++;
                }
                $background = '';
                if ($i % 2 == 0){
                    $background = 'rgb(212 226 241)';
                }
                $staff_id = $value['staffid'];
                $tdNumber = '<div class="text-center td-number">' . ($i) . '</div>';
                $tdNameStaff = '<div class="td-name-staff">
                    <input type="hidden" name="personnel_id[]" class="form-control personnel_id" value="' . $value['staffid'] . '">
                    ' . $value['name'] . '
                </div>';

                $html .= '<tr style="background-color: '.$background.'">';
                $html .= '<td style="width: 50px;">' . $tdNumber . '</td>';

                $html .= '<td style="min-width: 100px;">' . $value['code'] . '</td>';
                $html .= '<td style="min-width: 150px;">' . $tdNameStaff . '</td>';
                $html .= '<td style="min-width: 100px;">' . $value['name_role'] . '</td>';

                if ($value['type'] == 1) {
                    $html .= '<td style="min-width: 80px;text-align: left">Giờ vào</td>';
                } elseif ($value['type'] == 2) {
                    $html .= '<td style="min-width: 80px;text-align: left">Giờ ra</td>';
                }

                $this->db->select('
                    tbl_timekeeping_detail.staff_id as staff_id,
                    tbl_timekeeping_detail.id as timekeeping_detail_id,
                    tbl_timekeeping_detail.day as day,
                    tbl_timekeeping_detail.date as date,
                    tbl_timekeeping_detail.count_hour as count_hour,
                    tbl_timekeeping_detail_hour.id as id,
                    tbl_timekeeping_detail_hour.hour_real as hour_real,
                    tbl_timekeeping_detail_hour.hour as hour,
                    tbl_timekeeping_detail_hour.type as type,
                    tbl_timekeeping_detail_hour.image as image,
                    tbl_timekeeping_detail_hour.timekeeping_detail_id_old as timekeeping_detail_id_old,
                    tbl_timekeeping_detail_hour.type_check as type_check
                ');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping_detail_hour',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_hour.timekeeping_detail_id', 'left');
                $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
                $this->db->where('tbl_timekeeping_detail.staff_id', $personnel_id);
                $timeKeepingDetail = $this->db->get()->result_array();
                usort($timeKeepingDetail,
                    ch_make_cmp(['timekeeping_detail_id' => "asc", 'timekeeping_detail_id_old' => "asc"]));
                foreach ($listDate as $k => $val) {
                    $date = $k;
                    $day = date("d", strtotime($k));
                    $hourIn = '';
                    $hourOut = '';
                    $textHour = '';
                    $imageIn = '';
                    $imageOut = '';
                    $date_word = '';
                    $image = '';


                    $timekeeping_detail_hour_id_in = '';
                    $timekeeping_detail_id_in = '';
                    $type_hour_in = '';
                    $type_check_in = '';

                    $timekeeping_detail_hour_id_out = '';
                    $timekeeping_detail_id_out = '';
                    $type_hour_out = '';
                    $type_check_out = '';

                    $timekeeping_detail_hour_id_text = '';
                    $timekeeping_detail_id_text = '';
                    $type_hour_text = '';
                    $type_check_text = '';

                    $id_timekeeping_detail_hour = '';
                    $type_hour = '';
                    $type_check = '';

                    $hourCheckInNew = '';
                    $hourCheckOutNew = '';

                    $timekeeping_detail_hour_id_new = '';
                    $timekeeping_detail_id_new = '';

                    $timekeeping_detail_hour_id_out_new = '';
                    $timekeeping_detail_id_out_new = '';


                    $format = 'D';
                    $time = mktime(12, 0, 0, $month, $day, $year);
                    if (date('m', $time) == $month) {
                        $date_word = date($format, $time);
                    }

                    $this->db->from('tbl_timekeeping_detail');
                    $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
                    $this->db->where('tbl_timekeeping_detail.staff_id', $personnel_id);
                    $this->db->where('tbl_timekeeping_detail.date', $date);
                    $this->db->where('tbl_timekeeping_detail.day', $day);
                    $this->db->limit(1);
                    $isTimeKeepingDetail = $this->db->get()->row_array();

                    if (empty($isTimeKeepingDetail)) {
                        if ($date_word == "Sun") {
                            $check_sun = 1;
                        } else {
                            $check_sun = '';
                        }
                        $type = 'X';
                        $arrPersonnel[] = [
                            'timekeeping_id' => $timekeepingId,
                            'staff_id' => $personnel_id,
                            'date' => $date,
                            'day' => $day,
                            'type' => $type,
                            'date_word' => $date_word,
                            'check_sun' => $check_sun
                        ];
                    }

                    if (!empty($timeKeepingDetail)) {
                        foreach ($timeKeepingDetail as $kk => $v) {
                            if ($v['date'] == $date) {
                                if ($v['type_check'] == 1) {
                                    if ($v['type'] == 1) {
                                        $hourIn = $v['hour'];
                                        $imageIn = $v['image'];

                                        $timekeeping_detail_hour_id_in = $v['id'];
                                        $timekeeping_detail_id_in = $v['timekeeping_detail_id'];
                                        $type_hour_in = $v['type'];
                                        $type_check_in = $v['type_check'];

                                    } elseif ($v['type'] == 2) {
                                        $hourOut = $v['hour'];
                                        $imageOut = $v['image'];

                                        $timekeeping_detail_hour_id_out = $v['id'];
                                        $timekeeping_detail_id_out = $v['timekeeping_detail_id'];
                                        $type_hour_out = $v['type'];
                                        $type_check_out = $v['type_check'];
                                    }
                                }
                            }
                        }
                    }

                    if ($value['type'] == 1) {
                        $textHour = $hourIn;
                        $image = $imageIn;

                        $timekeeping_detail_hour_id_text = $timekeeping_detail_hour_id_in;
                        $timekeeping_detail_id_text = $timekeeping_detail_id_in;
                        $type_hour_text = $type_hour_in;
                        $type_check_text = $type_check_in;

                        $type_hour = $type_hour_text;
                        $type_check = $type_check_text;

                        $hourCheckInNew = $textHour;
                        $timekeeping_detail_hour_id_new = $timekeeping_detail_hour_id_text;
                        $timekeeping_detail_id_new = $timekeeping_detail_id_text;

                    } elseif ($value['type'] == 2) {
                        $textHour = $hourOut;
                        $image = $imageOut;

                        $timekeeping_detail_hour_id_text = $timekeeping_detail_hour_id_out;
                        $timekeeping_detail_id_text = $timekeeping_detail_id_out;
                        $type_hour_text = $type_hour_out;
                        $type_check_text = $type_check_out;

                        $type_hour = $type_hour_text;
                        $type_check = $type_check_text;

                        $hourCheckOutNew = $textHour;
                        $timekeeping_detail_hour_id_out_new = $timekeeping_detail_hour_id_text;
                        $timekeeping_detail_id_out_new = $timekeeping_detail_id_text;

                        if (!empty($hourIn)) {
                            if (!empty($timekeeping_detail_hour_id_in)) {
                                $id_timekeeping_detail_hour = $timekeeping_detail_hour_id_in;
                            }
                        }

                    }
                    if (!empty($timeKeepingDetail)) {
                        foreach ($timeKeepingDetail as $kk => $v) {
                            if ($v['date'] == $date) {
                                $htmlEditHour = 'display: none;';
                                $htmlCheckDelete = false;
                                $htmlCheckInDelete = false;
                                if ($v['count_hour'] == 0 || $v['count_hour'] == null) {
                                    if ($textHour == '') {
                                        $htmlEditHour = 'display: block;';
                                    }
                                    if (!empty($hourCheckOutNew)) {
                                        $htmlCheckDelete = true;
                                    }

                                    if (!empty($hourCheckInNew) && $hourCheckOutNew == '') {
                                        $htmlCheckInDelete = true;
                                    }

                                } else {
                                    if (!empty($hourCheckOutNew)) {
                                        $htmlCheckDelete = true;
                                    }
                                    if (!empty($hourCheckInNew) && $hourCheckOutNew == '') {
                                        $htmlCheckInDelete = true;
                                    }
                                }
                            }
                        }
                    }

                    $color_brack = '';
                    $htmlDeleteInNew = '';
                    $htmlDeleteNew = '';
                    $htmlDelete = '';
                    $htmlEdit = '';
                    $dateSalary = to_sql_date($val . '/' . $year);
                    $dateNow = date("Y-m-d", strtotime("now"));
                    if (strtotime($dateSalary) <= strtotime($dateNow)) {
                        $htmlEdit = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/salary/viewEditHour/' . $dateSalary . '/' . $personnel_id . '/' . $type_hour . '/' . $type_check . '/' . $id_timekeeping_detail_hour) . '" data-toggle="modal" data-target="#myModal2" style="font-size: 10px; bottom: 15px; color:#0b0b0b; ' . $htmlEditHour . '"><i class="fa fa-pencil"></i></a>';
                        if (!empty($htmlCheckDelete)) {
                            $htmlDelete = '<a onclick=deleteHourOut(' . $personnel_id . ',' . $type_hour . ',' . $type_check . ',' . $timekeeping_detail_hour_id_out_new . ',' . $timekeeping_detail_id_out_new . ',0) style="font-size: 10px; bottom: 15px; color:#0b0b0b;"><i style="color:red" class="fa fa-times"></i></a>';
                        }
                        if (!empty($htmlCheckInDelete)) {
                            $htmlDeleteInNew = '<a onclick=deleteHourOutNew(' . $personnel_id . ',' . $type_hour . ',' . $type_check . ',' . $timekeeping_detail_hour_id_new . ',' . $timekeeping_detail_id_new . ') style="font-size: 10px; bottom: 15px; color:#0b0b0b;"><i style="color:red" class="fa fa-times"></i></a>';
                        }
                        if (empty($htmlCheckInDelete) && empty($htmlCheckDelete)){
                            if ($i % 2 != 0 ) {
                                $color_brack = '#fcf8e3';
                            }
                        }
                    }

                    $htmlDelete = $this->perDeleteTimekeeping ? $htmlDelete : '';
                    $htmlDeleteInNew = $this->perDeleteTimekeeping ? $htmlDeleteInNew : '';
                    $htmlEdit = $this->perEditTimekeeping ? $htmlEdit : '';

                    $value_hour_new = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/salary/viewCheckHour/' . $timekeeping_detail_hour_id_new . '/' . $timekeeping_detail_id_new . '/1') . '" data-toggle="modal" data-target="#myModal2">' . $hourCheckInNew . ' </a>' . $htmlDeleteInNew;
                    $value_hour_new .= '<a style="color:red" data-tnh="modal" class="tnh-modal" href="' . base_url('admin/salary/viewCheckHour/' . $timekeeping_detail_hour_id_out_new . '/' . $timekeeping_detail_id_out_new . '/2/0') . '" data-toggle="modal" data-target="#myModal2">' . $hourCheckOutNew . '</a>';
                    // '.$value_hour.' '.$value_hour_tct.' '.$value_hour_tcd.' '.$value_hour_hcnt.'
                    $html .= '<td style="width: 100px; position: relative;background: '.$color_brack.'" class="text-center">
                            ' . $htmlEdit . '
                            ' . $value_hour_new . '' . $htmlDelete . '
                    </td>';
                }
                $html .= '</tr>';
            }

            $tfoot = '';
        }

        if (!empty($arrPersonnel)) {
            $this->db->insert_batch('tbl_timekeeping_detail', $arrPersonnel);
        }

        $arrPersonnelDetailHour = [];
        if (!empty($arrPersonnel)) {
            $this->db->select('tbl_timekeeping_detail.*');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
            $personnelDetails = $this->db->get()->result_array();
            if (!empty($personnelDetails)) {
                foreach ($personnelDetails as $key => $value) {
                    $this->db->from('tbl_timekeeping_detail_hour');
                    $this->db->where('tbl_timekeeping_detail_hour.timekeeping_id', $timekeepingId);
                    $this->db->where('tbl_timekeeping_detail_hour.timekeeping_detail_id', $value['id']);
                    $this->db->where('tbl_timekeeping_detail_hour.type', 1);
                    $hourIns = $this->db->get()->num_rows();

                    $this->db->from('tbl_timekeeping_detail_hour');
                    $this->db->where('tbl_timekeeping_detail_hour.timekeeping_id', $timekeepingId);
                    $this->db->where('tbl_timekeeping_detail_hour.timekeeping_detail_id', $value['id']);
                    $this->db->where('tbl_timekeeping_detail_hour.type', 2);
                    $hourOuts = $this->db->get()->num_rows();

                    if (empty($hourIns) || empty($hourOuts)) {
                        if (empty($hourIns)) {
                            $arrPersonnelDetailHour[] = [
                                'timekeeping_id' => $timekeepingId,
                                'timekeeping_detail_id' => $value['id'],
                                'type' => 1,
                            ];
                        }
                        if (empty($hourOuts)) {
                            $arrPersonnelDetailHour[] = [
                                'timekeeping_id' => $timekeepingId,
                                'timekeeping_detail_id' => $value['id'],
                                'type' => 2,
                            ];
                        }
                    }
                }
            }
        }

        if (!empty($arrPersonnelDetailHour)) {
            $check = true;
            $this->db->insert_batch('tbl_timekeeping_detail_hour', $arrPersonnelDetailHour);
        }
        //load lai khi them moi
        if ($check) {
            $this->db->select('tblstaff.staffid as staffid,tblstaff.code as code, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name,tblroles.name as name_role,tbl_timekeeping_detail_hour.type as type');
            $this->db->from('tblstaff');
            $this->db->where('active', 1);
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            if (!empty($staff)) {
                $this->db->where('tbl_timekeeping_detail.staff_id', $staff);
            }
            if (!empty($department)) {
                $this->db->where("EXISTS (
                    SELECT tblstaff_departments.staffid 
                    FROM tblstaff_departments 
                    WHERE tblstaff_departments.staffid = tblstaff.staffid
                    AND tblstaff_departments.departmentid = $department
                )");
            }
            $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
            $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
            $this->db->join('tbl_timekeeping_detail_hour',
                'tbl_timekeeping_detail_hour.timekeeping_detail_id= tbl_timekeeping_detail.id', 'left');
            $this->db->group_by('tbl_timekeeping_detail.staff_id');
            $this->db->group_by('tbl_timekeeping_detail_hour.type');
            $totalStaff = $this->db->get()->num_rows();

            $totalPage = ceil($totalStaff / $limit);

            $html = '';
            $this->db->select('tblstaff.staffid as staffid,tblstaff.code as code, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name,tblroles.name as name_role,tbl_timekeeping_detail_hour.type as type');
            $this->db->from('tblstaff');
            $this->db->where('active', 1);
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            if (!empty($staff)) {
                $this->db->where('tbl_timekeeping_detail.staff_id', $staff);
            }
            if (!empty($department)) {
                $this->db->where("EXISTS (
                    SELECT tblstaff_departments.staffid 
                    FROM tblstaff_departments 
                    WHERE tblstaff_departments.staffid = tblstaff.staffid
                    AND tblstaff_departments.departmentid = $department
                )");
            }
            $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
            $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
            $this->db->join('tbl_timekeeping_detail_hour',
                'tbl_timekeeping_detail_hour.timekeeping_detail_id= tbl_timekeeping_detail.id', 'left');
            $this->db->group_by('tbl_timekeeping_detail.staff_id');
            $this->db->group_by('tbl_timekeeping_detail_hour.type');
            if (empty($staff)) {
                $this->db->limit($limit, $start);
            }
            $personnel = $this->db->get()->result_array();
            if (!empty($personnel)) {
                foreach ($personnel as $key => $value) {
                    $personnel_id = $value['staffid'];
                    $tdNumber = '<div class="text-center td-number">' . (++$key) . '</div>';
                    $tdNameStaff = '<div class="td-name-staff">
                    <input type="hidden" name="personnel_id[]" class="form-control personnel_id" value="' . $value['staffid'] . '">
                    ' . $value['name'] . '
                </div>';

                    $html .= '<tr>';
                    $html .= '<td style="min-width: 50px;">' . $tdNumber . '</td>';
                    $html .= '<td style="min-width: 100px;">' . $value['code'] . '</td>';
                    $html .= '<td style="min-width: 150px;">' . $tdNameStaff . '</td>';
                    $html .= '<td style="min-width: 100px;">' . $value['name_role'] . '</td>';
                    if ($value['type'] == 1) {
                        $html .= '<td style="min-width: 100px;text-align: left">Giờ vào</td>';
                    } elseif ($value['type'] == 2) {
                        $html .= '<td style="min-width: 100px;text-align: left">Giờ ra</td>';
                    }
                    $this->db->select('
                        tbl_timekeeping_detail.staff_id as staff_id,
                        tbl_timekeeping_detail.id as timekeeping_detail_id,
                        tbl_timekeeping_detail.day as day,
                        tbl_timekeeping_detail.date as date,
                        tbl_timekeeping_detail.count_hour as count_hour,
                        tbl_timekeeping_detail_hour.id as id,
                        tbl_timekeeping_detail_hour.hour_real as hour_real,
                        tbl_timekeeping_detail_hour.hour as hour,
                        tbl_timekeeping_detail_hour.type as type,
                        tbl_timekeeping_detail_hour.image as image,
                        tbl_timekeeping_detail_hour.type_check as type_check
                    ');
                    $this->db->from('tbl_timekeeping_detail');
                    $this->db->join('tbl_timekeeping_detail_hour',
                        'tbl_timekeeping_detail.id = tbl_timekeeping_detail_hour.timekeeping_detail_id', 'left');
                    $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
                    $this->db->where('tbl_timekeeping_detail.staff_id', $personnel_id);
                    $timeKeepingDetail = $this->db->get()->result_array();
                    usort($timeKeepingDetail,
                        ch_make_cmp(['timekeeping_detail_id' => "asc", 'timekeeping_detail_id_old' => "asc"]));
                    foreach ($listDate as $k => $val) {
                        $bg = "";
                        $date = $k;
                        $day = date("d", strtotime($k));
                        $type = '';
                        $timeKeepingDetail_id = 0;
                        $hourIn = '';
                        $hourOut = '';
                        $textHour = '';
                        $imageIn = '';
                        $imageOut = '';
                        $image = '';


                        $timekeeping_detail_hour_id_in = '';
                        $timekeeping_detail_id_in = '';
                        $type_hour_in = '';
                        $type_check_in = '';

                        $timekeeping_detail_hour_id_out = '';
                        $timekeeping_detail_id_out = '';
                        $type_hour_out = '';
                        $type_check_out = '';

                        $timekeeping_detail_hour_id_text = '';
                        $timekeeping_detail_id_text = '';
                        $type_hour_text = '';
                        $type_check_text = '';


                        $id_timekeeping_detail_hour = '';
                        $type_hour = '';
                        $type_check = '';

                        $hourCheckInNew = '';
                        $hourCheckOutNew = '';

                        $timekeeping_detail_hour_id_new = '';
                        $timekeeping_detail_id_new = '';

                        $timekeeping_detail_hour_id_out_new = '';
                        $timekeeping_detail_id_out_new = '';


                        $this->db->from('tbl_timekeeping_detail');
                        $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
                        $this->db->where('tbl_timekeeping_detail.staff_id', $personnel_id);
                        $this->db->where('tbl_timekeeping_detail.date', $date);
                        $this->db->where('tbl_timekeeping_detail.day', $day);
                        $this->db->limit(1);
                        $isTimeKeepingDetail = $this->db->get()->row_array();

                        if (empty($isTimeKeepingDetail)) {
                            $type = 'X';
                            $arrPersonnel[] = [
                                'timekeeping_id' => $timekeepingId,
                                'staff_id' => $personnel_id,
                                'date' => $date,
                                'day' => $day,
                                'type' => $type,
                            ];
                        }

                        if (!empty($timeKeepingDetail)) {
                            foreach ($timeKeepingDetail as $kk => $v) {
                                if ($v['date'] == $date) {
                                    if ($v['type_check'] == 1) {
                                        if ($v['type'] == 1) {
                                            $hourIn = $v['hour'];
                                            $imageIn = $v['image'];

                                            $timekeeping_detail_hour_id_in = $v['id'];
                                            $timekeeping_detail_id_in = $v['timekeeping_detail_id'];
                                            $type_hour_in = $v['type'];
                                            $type_check_in = $v['type_check'];

                                        } elseif ($v['type'] == 2) {
                                            $hourOut = $v['hour'];
                                            $imageOut = $v['image'];

                                            $timekeeping_detail_hour_id_out = $v['id'];
                                            $timekeeping_detail_id_out = $v['timekeeping_detail_id'];
                                            $type_hour_out = $v['type'];
                                            $type_check_out = $v['type_check'];
                                        }
                                    }
                                }
                            }
                        }

                        if ($value['type'] == 1) {
                            $textHour = $hourIn;
                            $image = $imageIn;

                            $timekeeping_detail_hour_id_text = $timekeeping_detail_hour_id_in;
                            $timekeeping_detail_id_text = $timekeeping_detail_id_in;
                            $type_hour_text = $type_hour_in;
                            $type_check_text = $type_check_in;

                            $type_hour = $type_hour_text;
                            $type_check = $type_check_text;

                            $hourCheckInNew = $textHour;
                            $timekeeping_detail_hour_id_new = $timekeeping_detail_hour_id_text;
                            $timekeeping_detail_id_new = $timekeeping_detail_id_text;


                        } elseif ($value['type'] == 2) {
                            $textHour = $hourOut;
                            $image = $imageOut;

                            $timekeeping_detail_hour_id_text = $timekeeping_detail_hour_id_out;
                            $timekeeping_detail_id_text = $timekeeping_detail_id_out;
                            $type_hour_text = $type_hour_out;
                            $type_check_text = $type_check_out;

                            $type_hour = $type_hour_text;
                            $type_check = $type_check_text;

                            $hourCheckOutNew = $textHour;
                            $timekeeping_detail_hour_id_out_new = $timekeeping_detail_hour_id_text;
                            $timekeeping_detail_id_out_new = $timekeeping_detail_id_text;

                            if (!empty($hourIn)) {
                                if (!empty($timekeeping_detail_hour_id_in)) {
                                    $id_timekeeping_detail_hour = $timekeeping_detail_hour_id_in;
                                }
                            }
                        }


                        if (!empty($timeKeepingDetail)) {
                            foreach ($timeKeepingDetail as $kk => $v) {
                                if ($v['date'] == $date) {
                                    $htmlEditHour = 'display: none;';
                                    $htmlCheckDelete = false;
                                    $htmlCheckInDelete = false;
                                    if ($v['count_hour'] == 0 || $v['count_hour'] == null) {
                                        if ($textHour == '') {
                                            $htmlEditHour = 'display: block;';
                                        }
                                        if (!empty($hourCheckOutNew)) {
                                            $htmlCheckDelete = true;
                                        }

                                        if (!empty($hourCheckInNew) && $hourCheckOutNew == '') {
                                            $htmlCheckInDelete = true;
                                        }
                                    } else {
                                        if (!empty($hourCheckOutNew)) {
                                            $htmlCheckDelete = true;
                                        }
                                        if (!empty($hourCheckInNew) && $hourCheckOutNew == '') {
                                            $htmlCheckInDelete = true;
                                        }
                                    }
                                }
                            }
                        }

                        $htmlDeleteInNew = '';
                        $htmlDeleteNew = '';
                        $htmlDelete = '';
                        $htmlEdit = '';
                        $dateSalary = to_sql_date($val . '/' . $year);
                        $dateNow = date("Y-m-d", strtotime("now"));
                        if (strtotime($dateSalary) <= strtotime($dateNow)) {
                            $htmlEdit = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/salary/viewEditHour/' . $dateSalary . '/' . $personnel_id . '/' . $type_hour . '/' . $type_check . '/' . $id_timekeeping_detail_hour) . '" data-toggle="modal" data-target="#myModal2" style="font-size: 10px; bottom: 15px; color:#0b0b0b; ' . $htmlEditHour . '"><i class="fa fa-pencil"></i></a>';
                            if (!empty($htmlCheckDelete)) {
                                $htmlDelete = '<a onclick=deleteHourOut(' . $personnel_id . ',' . $type_hour . ',' . $type_check . ',' . $timekeeping_detail_hour_id_out_new . ',' . $timekeeping_detail_id_out_new . ',0) style="font-size: 10px; bottom: 15px; color:#0b0b0b;"><i style="color:red" class="fa fa-times"></i></a>';
                            }
                            if (!empty($htmlCheckInDelete)) {
                                $htmlDeleteInNew = '<a onclick=deleteHourOutNew(' . $personnel_id . ',' . $type_hour . ',' . $type_check . ',' . $timekeeping_detail_hour_id_new . ',' . $timekeeping_detail_id_new . ') style="font-size: 10px; bottom: 15px; color:#0b0b0b;"><i style="color:red" class="fa fa-times"></i></a>';
                            }
                        }

                        $htmlDelete = $this->perDeleteTimekeeping ? $htmlDelete : '';
                        $htmlDeleteInNew = $this->perDeleteTimekeeping ? $htmlDeleteInNew : '';
                        $htmlEdit = $this->perEditTimekeeping ? $htmlEdit : '';

                        $value_hour_new = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/salary/viewCheckHour/' . $timekeeping_detail_hour_id_new . '/' . $timekeeping_detail_id_new . '/1') . '" data-toggle="modal" data-target="#myModal2">' . $hourCheckInNew . ' </a>' . $htmlDeleteInNew;
                        $value_hour_new .= '<a style="color:red" data-tnh="modal" class="tnh-modal" href="' . base_url('admin/salary/viewCheckHour/' . $timekeeping_detail_hour_id_out_new . '/' . $timekeeping_detail_id_out_new . '/2/0') . '" data-toggle="modal" data-target="#myModal2">' . $hourCheckOutNew . '</a>';
                        // '.$value_hour.' '.$value_hour_tct.' '.$value_hour_tcd.' '.$value_hour_hcnt.'
                        $html .= '<td style="width: 100px; position: relative;" class="text-center">
                            ' . $htmlEdit . '
                            ' . $value_hour_new . '' . $htmlDelete . '
                    </td>';
                    }
                    $html .= '</tr>';
                }

                $tfoot = '';
            }
        }

        //end
        $data['totalPage'] = $totalPage;
        $data['page'] = $page;

        $data['tHead'] = $tHead;
        $data['tfoot'] = $tfoot;
        $data['html'] = $html;
        $this->load->view('admin/salary/load_personnel_timekeeping', $data);
    }

    public function deleteHourOutNew()
    {
        $data = [];
        $staff_id = $this->input->post('staff_id');
        $type_hour = $this->input->post('type_hour');
        $type_check = $this->input->post('type_check');
        $id_timekeeping_detail_hour_in = $this->input->post('id_timekeeping_detail_hour_in');
        $id_timekeeping_detail = $this->input->post('id_timekeeping_detail');
        $type_check_delete = $this->input->post('type_check_delete');
        $timekeepingDetailHourIn = get_table_where('tbl_timekeeping_detail_hour',
            ['id' => $id_timekeeping_detail_hour_in], '', 'row_array');
        $success = false;
        if (!empty($timekeepingDetailHourIn)) {
            $checkCheckOut = get_table_where('tbl_timekeeping_detail_hour', [
                'timekeeping_detail_id' => $timekeepingDetailHourIn['timekeeping_detail_id'],
                'type_check' => $timekeepingDetailHourIn['type_check'],
                'type' => 2
            ], '', 'row_array');
            if (!empty($checkCheckOut)) {
                if (!empty($checkCheckOut['hour'])) {
                    $data['result'] = 0;
                    $data['message'] = 'Đã checkout không thể xóa checkin !';
                    echo json_encode($data);
                    die();
                }

                $this->db->where('id', $timekeepingDetailHourIn['id']);
                $this->db->update('tbl_timekeeping_detail_hour', [
                    'hour' => null,
                    'hour_real' => null,
                    'image' => null,
                    'type_check' => 1,
                ]);
                $this->db->where('id', $checkCheckOut['id']);
                $this->db->update('tbl_timekeeping_detail_hour', [
                    'hour' => null,
                    'hour_real' => null,
                    'image' => null,
                    'type_check' => 1,
                ]);

                $this->db->where('id', $timekeepingDetailHourIn['timekeeping_detail_id']);
                $success = $this->db->update('tbl_timekeeping_detail', [
                    'count_late' => 0,
                    'type' => 'X',
                    'count_hour_late' => 0,
                    'count_hour_late_new' => 0,
                ]);
            }
        }
        if ($success) {
            $data['result'] = 1;
            $data['message'] = 'Xóa thành công !';
            echo json_encode($data);
            die();
        } else {
            $data['result'] = 0;
            $data['message'] = 'Xóa thất bại !';
            echo json_encode($data);
            die();
        }


    }

    public function deleteHourOut()
    {
        $data = [];
        $id_timekeeping_detail_hour_out = $this->input->post('id_timekeeping_detail_hour_out');
        $id_timekeeping_detail = $this->input->post('id_timekeeping_detail');

        $timekeepingDetail = get_table_where('tbl_timekeeping_detail', ['id' => $id_timekeeping_detail], '',
            'row_array');


        $staff_id = $timekeepingDetail['staff_id'];
        $date_check = $timekeepingDetail['date'];
        $month = date("m", strtotime($date_check));
        $year = date("Y", strtotime($date_check));

        $this->db->from('tbl_payroll_item');
        $this->db->join('tbl_payroll',
            'tbl_payroll.id = tbl_payroll_item.payroll_id');
        $this->db->where('tbl_payroll.month', $month);
        $this->db->where('tbl_payroll.year', $year);
        $this->db->where('tbl_payroll_item.staff_id', $staff_id);
        $payrollItem = $this->db->get()->row_array();
        if (!empty($payrollItem)) {
            $data['result'] = 0;
            $data['message'] = 'Đã tính bảng lương không thể xóa !';
            echo json_encode($data);
            die();
        }

        $business_fee = 3;

        $this->db->select('tbl_business_fee_boiler_overtime.id as id');
        $this->db->from('tbl_business_fee_boiler_overtime');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $this->db->where('type', $business_fee);
        $checkBusinessFee = $this->db->get()->row_array();
        $day_new = date("Y-m-d", strtotime($date_check));
        if (!empty($checkBusinessFee)) {

            $this->db->from('tbl_business_fee_boiler_calculate');
            $this->db->join('tbl_business_fee_boiler_calculate_item',
                'tbl_business_fee_boiler_calculate_item.business_fee_boiler_calculate_id = tbl_business_fee_boiler_calculate.id');
            $this->db->where('tbl_business_fee_boiler_calculate.month', $month);
            $this->db->where('tbl_business_fee_boiler_calculate.year', $year);
            $this->db->where('tbl_business_fee_boiler_calculate.type', $business_fee);
            $this->db->where('tbl_business_fee_boiler_calculate_item.staff_id', $staff_id);
            $checkBusinessFeeCalucateItem = $this->db->get()->row_array();
            if (!empty($checkBusinessFeeCalucateItem)) {
                $data['result'] = 0;
                $data['message'] = 'Đã tính bảng công tác phí không thể xóa !';
                echo json_encode($data);
                die();
            }

            $this->db->select('tbl_business_fee_boiler_overtime_detail.id as id');
            $this->db->from('tbl_business_fee_boiler_overtime_detail');
            $this->db->where('tbl_business_fee_boiler_overtime_detail.business_fee_boiler_overtime_id',
                $checkBusinessFee['id']);
            $this->db->where('tbl_business_fee_boiler_overtime_detail.date', $day_new);
            $checkBusinessFeeDetail = $this->db->get()->row_array();
            if (!empty($checkBusinessFeeDetail)) {
                $this->db->where('tbl_business_fee_boiler_overtime_detail.id', $checkBusinessFeeDetail['id']);
                $this->db->delete('tbl_business_fee_boiler_overtime_detail');
            }
        }

        $this->db->where('timekeeping_detail_id', $id_timekeeping_detail);
        $this->db->delete('tbl_timekeeping_detail_count_hour');

        $this->db->where('id', $id_timekeeping_detail_hour_out);
        $this->db->update('tbl_timekeeping_detail_hour', [
            'hour' => null,
            'hour_real' => null,
            'image' => null,
        ]);

        $this->db->where('id', $id_timekeeping_detail);
        $this->db->update('tbl_timekeeping_detail', [
            'number_day' => 0,
            'count_hour_overtime' => 0,
            'count_hour' => 0,
            'count_late' => 0,
            'count_rice' => 0,
            'count_hour_late_checkout' => 0,
        ]);


        $data['result'] = 1;
        $data['message'] = 'Xoá thành công';

        echo json_encode($data);


    }

    public function viewEditHour(
        $date_check = '',
        $staff_id = '',
        $type_hour = '',
        $type_check = '',
        $id_timekeeping_detail_hour = ''
    ) {
        $data = [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('hour', lang("Vui lòng chọn giờ công"), 'required');
            if ($this->form_validation->run() == true) {
                $dataPost = $this->input->post();
                $date_check = to_sql_date($dataPost['date']) . ' ' . $dataPost['hour'];
                $staff_id = $dataPost['staff_id'];
                $type_hour = $dataPost['type_hour'];
                $type_check = $dataPost['type_check'];
                $id_timekeeping_detail_hour = $dataPost['id_timekeeping_detail_hour'];
                if ($type_hour == 2) {
                    if (empty($id_timekeeping_detail_hour)) {
                        $data['result'] = 0;
                        $data['message'] = lang("Chưa checkin ngày này !!!");
                        echo json_encode($data);
                        die;
                    }
                    $checkCheckIn = get_table_where('tbl_timekeeping_detail_hour',
                        ['id' => $id_timekeeping_detail_hour], '', 'row_array');
                    if (empty($checkCheckIn['hour'])) {
                        $data['result'] = 0;
                        $data['message'] = lang("Chưa checkin ngày này !!!");
                        echo json_encode($data);
                        die;
                    }
                }
                if (!empty($date_check) && !empty($staff_id) && !empty($type_hour)) {
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => base_url() . 'Qr_staff/QRStaff?csrf_protection=true',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => array(
                            'type_hour' => $type_hour,
                            'staff_id' => $staff_id,
                            'date_check' => $date_check,
                            'type_check' => $type_check,
                            'id_timekeeping_detail_hour' => $id_timekeeping_detail_hour
                        ),
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);
                    $response = json_decode($response);
                    $data['result'] = $response->success;
                    $data['message'] = $response->message;
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang("Vui lòng kiểm tra lại dữ liệu");
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        }
        $data['date_check'] = $date_check;
        $data['staff_id'] = $staff_id;
        $data['type_hour'] = $type_hour;
        $data['type_check'] = $type_check;
        $data['id_timekeeping_detail_hour'] = $id_timekeeping_detail_hour;
        $newdate = strtotime('+0 day', strtotime($date_check));
        $newdate = date('Y-m-d', $newdate);
        $data['newdate'] = $newdate;
        $this->load->view('admin/salary/view_edit_hour', $data);
    }

    public function dashboard_timekeeping()
    {
        if (!$this->perViewDashboardTimekeeping && !$this->perViewOwnDashboardTimekeeping) {
            accessDenied();
        }
        $data['title'] = lang('Thống kê giờ công');
        $this->load->view('admin/salary/dashboard_timekeeping', $data);
    }

    public function loadDashBoardTimekeeping()
    {
        $data = [];
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $staff = $this->input->get('staff');
        $department = $this->input->get('department');

        $tHead = '';
        $html = '';
        $listDate = getAllDateInMonth($month, $year, 'd/m');
        $tHead = '<tr>
            <th class="text-center" style="min-width: 50px;">' . lang('tnh_numbers') . '</th>
            <th class="text-center" style="min-width: 150px;">' . lang('MSNV') . '</th>
            <th class="text-center" style="min-width: 150px;">' . lang('Họ Và Tên') . '</th>
            <th class="text-center" style="min-width: 100px;">' . lang('Chức vụ') . '</th>
        ';

        foreach ($listDate as $k => $value) {
            $day = date("d", strtotime($k));
            $format = 'D';
            $time = mktime(12, 0, 0, $month, $day, $year);
            $date_word = '';
            if (date('m', $time) == $month) {
                $date_word = date($format, $time);
            }
            $date_word = convertDate($date_word);
            $tHead .= '<th class="text-center">' . $value . '<br>'.$date_word.'</th>';
        }
        $tHead .= '<th class="text-center" style="min-width: 80px">Nghĩ phép</th>';
        $tHead .= '<th class="text-center" style="min-width: 80px">Nghĩ không lương</th>';
        $tHead .= '<th class="text-center" style="min-width: 80px">TC giờ làm việc</th>';
        $tHead .= '<th class="text-center" style="min-width: 80px">TC số ngày làm việc</th>';
        $tHead .= '<th class="text-center" style="min-width: 80px">TC giờ được hưởng lương</th>';
        $tHead .= '</tr>';
        $tfoot = '';

        //timekeeping
        $timekeepingId = 0;
        $this->db->select('*');
        $this->db->from('tbl_timekeeping');
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        $timekeeping = $this->db->get()->row_array();
        if (!empty($timekeeping)) {
            $timekeepingId = $timekeeping['id'];
        }
        if ($month == 12) {
            $monthNew = 1;
            $yearNew = $year + 1;
        } else {
            $monthNew = $month + 1;
            $yearNew = $year;
        }
        $timekeepingIdNew = 0;
        $this->db->select('*');
        $this->db->from('tbl_timekeeping');
        $this->db->where('tbl_timekeeping.month', $monthNew);
        $this->db->where('tbl_timekeeping.year', $yearNew);
        $timekeepingNew = $this->db->get()->row_array();
        if (!empty($timekeepingNew)) {
            $timekeepingIdNew = $timekeepingNew['id'];
        }
        //end timekeeping

        $countPaidHoliday = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                IF(type = 'LT',COALESCE(COUNT(tbl_timekeeping_detail.id),0),COALESCE(SUM(tbl_paid_holiday_leave_detail.number_date),0)) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            LEFT JOIN tbl_paid_holiday_leave_detail ON tbl_paid_holiday_leave_detail.id = tbl_timekeeping_detail.paid_holiday_detail_id
            WHERE (type = 'AL' OR type = 'LT' OR type = 'CH') AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_paid_holiday";

        $countPaidHolidayNew = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            WHERE type = 'AL/2' AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_paid_holiday_new";

        $countNotPaidHoliday = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(tbl_paid_holiday_leave_detail.number_date),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            INNER JOIN tbl_paid_holiday_leave_detail ON tbl_paid_holiday_leave_detail.id = tbl_timekeeping_detail.paid_holiday_detail_id
            WHERE (type = 'UP' OR type = 'TS' OR type = 'OD' ) AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_not_paid_holiday";

        $countNotPaidHolidayNew = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            WHERE type = 'UP/2' AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_not_paid_holiday_new";

        $countNumberDay = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count
            FROM tbl_timekeeping_detail
            WHERE (type != 'X' OR (type = 'X' AND number_day > 0 )) AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_number_day";

        $countNumberDayNew = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(number_day),0) as count
            FROM tbl_timekeeping_detail
            WHERE (type = 'X' AND number_day = '0.5' ) AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_number_day_new";

        $countHour= "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count
            FROM tbl_timekeeping_detail
            WHERE ((type = 'X' AND number_day > 0 )) AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_hour";

        $page = (int)$this->input->get('page');
        $limit = 50;
        $start = ($page - 1) * $limit;

        $this->db->select('tblstaff.staffid as staffid,tblstaff.code as code, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name,tblroles.name as name_role');
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        if (!empty($staff)) {
            $this->db->where('tbl_timekeeping_detail.staff_id', $staff);
        }
        if (!empty($department)) {
            $this->db->where("EXISTS (
                    SELECT tblstaff_departments.staffid 
                    FROM tblstaff_departments 
                    WHERE tblstaff_departments.staffid = tblstaff.staffid
                    AND tblstaff_departments.departmentid = $department
                )");
        }
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
        $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
        $this->db->join("$countPaidHoliday", 'tb_count_paid_holiday.timekeeping_id = tbl_timekeeping.id AND tb_count_paid_holiday.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countPaidHolidayNew", 'tb_count_paid_holiday_new.timekeeping_id = tbl_timekeeping.id AND tb_count_paid_holiday_new.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countNotPaidHoliday", 'tb_count_not_paid_holiday.timekeeping_id = tbl_timekeeping.id AND tb_count_not_paid_holiday.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countNotPaidHolidayNew", 'tb_count_not_paid_holiday_new.timekeeping_id = tbl_timekeeping.id AND tb_count_not_paid_holiday_new.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countNumberDay", 'tb_count_number_day.timekeeping_id = tbl_timekeeping.id AND tb_count_number_day.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countNumberDayNew", 'tb_count_number_day_new.timekeeping_id = tbl_timekeeping.id AND tb_count_number_day_new.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countHour", 'tb_count_hour.timekeeping_id = tbl_timekeeping.id AND tb_count_hour.staff_id = tblstaff.staffid', 'left');
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $totalStaff = $this->db->count_all_results();

        $totalPage = ceil($totalStaff / $limit);

        $this->db->select('
            tblstaff.staffid as staffid,
            tblstaff.code as code,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name,
            tblroles.name as name_role,
            COALESCE(tb_count_paid_holiday.count,0) + (COALESCE(tb_count_paid_holiday_new.count,0) * 0.5 ) as totalHoliday, 
            COALESCE(tb_count_not_paid_holiday.count,0) + (COALESCE(tb_count_not_paid_holiday_new.count,0) * 0.5 ) as totalNotHoliday, 
            COALESCE(tb_count_number_day.count,0) as number_day, 
            COALESCE(tb_count_number_day_new.count,0) as number_day_new, 
            COALESCE(tb_count_hour.count,0) as count_hour, 
            COALESCE(tb_count_paid_holiday_new.count_hour,0) + COALESCE(tb_count_paid_holiday.count_hour,0) as count_hour_phep, 
            COALESCE(tb_count_not_paid_holiday_new.count_hour,0) + COALESCE(tb_count_not_paid_holiday.count_hour,0) as count_hour_kphep, 
            ');
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        if (!empty($staff)) {
            $this->db->where('tbl_timekeeping_detail.staff_id', $staff);
        }
        if (!empty($department)) {
            $this->db->where("EXISTS (
                    SELECT tblstaff_departments.staffid 
                    FROM tblstaff_departments 
                    WHERE tblstaff_departments.staffid = tblstaff.staffid
                    AND tblstaff_departments.departmentid = $department
                )");
        }
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
        $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
        $this->db->join("$countPaidHoliday", 'tb_count_paid_holiday.timekeeping_id = tbl_timekeeping.id AND tb_count_paid_holiday.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countPaidHolidayNew", 'tb_count_paid_holiday_new.timekeeping_id = tbl_timekeeping.id AND tb_count_paid_holiday_new.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countNotPaidHoliday", 'tb_count_not_paid_holiday.timekeeping_id = tbl_timekeeping.id AND tb_count_not_paid_holiday.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countNotPaidHolidayNew", 'tb_count_not_paid_holiday_new.timekeeping_id = tbl_timekeeping.id AND tb_count_not_paid_holiday_new.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countNumberDay", 'tb_count_number_day.timekeeping_id = tbl_timekeeping.id AND tb_count_number_day.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countNumberDayNew", 'tb_count_number_day_new.timekeeping_id = tbl_timekeeping.id AND tb_count_number_day_new.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countHour", 'tb_count_hour.timekeeping_id = tbl_timekeeping.id AND tb_count_hour.staff_id = tblstaff.staffid', 'left');
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $this->db->limit($limit, $start);
        $personnel = $this->db->get()->result_array();

        if (!empty($personnel)) {
            foreach ($personnel as $key => $value) {
                $personnel_id = $value['staffid'];
                $countHourNew = number_unformat($value['count_hour']);
                $count_hour_phep = number_unformat($value['count_hour_phep']);
                $count_hour_kphep = number_unformat($value['count_hour_kphep']);
                $totalHoliday = number_unformat($value['totalHoliday']);
                $totalNotHoliday = number_unformat($value['totalNotHoliday']);
                $number_day_new = number_unformat($value['number_day']) - number_unformat($value['number_day_new']);

                $tdNumber = '<div class="text-center td-number">' . (++$key) . '</div>';
                $tdNameStaff = '<div class="td-name-staff">
                    <input type="hidden" name="personnel_id[]" class="form-control personnel_id" value="' . $value['staffid'] . '">
                    ' . $value['name'] . '
                </div>';

                $html .= '<tr>';
                $html .= '<td style="min-width: 50px;">' . $tdNumber . '</td>';
                $html .= '<td style="min-width: 100px;">' . $value['code'] . '</td>';
                $html .= '<td style="min-width: 150px;">' . $tdNameStaff . '</td>';
                $html .= '<td style="min-width: 100px;">' . $value['name_role'] . '</td>';


                $this->db->select('
                    tbl_timekeeping_detail.staff_id as staff_id,
                    tbl_timekeeping_detail.id as id,
                    tbl_timekeeping_detail.day as day,
                    tbl_timekeeping_detail.date as date,
                    tbl_timekeeping_detail.type as type,
                    tbl_timekeeping_detail.date_word as date_word,
                    tbl_timekeeping_detail.count_hour_overtime as count_hour_overtime,
                    tbl_timekeeping_detail.count_hour as count_hour,
                    tbl_timekeeping_detail.number_day as number_day
                ');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
                $this->db->where('tbl_timekeeping_detail.staff_id', $personnel_id);
                $timeKeepingDetailNew = $this->db->get()->result_array();

                foreach ($listDate as $k => $val) {
                    $date = $k;
                    $day = date("d", strtotime($k));
                    $type = 'X';
                    $day_check = explode("/", $val);
                    $style_count_hour = '';

                    $number_day = 0;
                    $count_hour_overtime = 0;
                    $count_hour = 0;
                    $check = false;
                    $day_old = '';
                    $day_new = '';
                    $date_word = '';

                    $id_timekeeping_detail = 0;

                    if (!empty($timeKeepingDetailNew)) {
                        foreach ($timeKeepingDetailNew as $kk => $v) {
                            if ($v['date'] == $date) {
                                if ($day_check['0'] == $v['day'] && $v['staff_id'] == $value['staffid']) {
                                    $number_day = $v['number_day'];
                                    $count_hour_overtime = $v['count_hour_overtime'];
                                    $count_hour = $v['count_hour'];
                                    $day_old = $v['day'];
                                    $type = $v['type'];
                                    $id_timekeeping_detail = $v['id'];
                                    $date_word = $v['date_word'];
                                }
                            }
                        }
                    }
                    $htmlEditNote = 'display: none;';
                    $htmlEditOverTime = 'display: none;';
                    if ($type != '') {
//                        $htmlEditNote = 'display: block;';
                    }
                    if ($number_day == 0){
                        $number_day = '';
                    }
                    $count_hour_new = ($count_hour - $count_hour_overtime);
                    if ($count_hour_new == 0){
                        $count_hour_new = '';
                    }
                    if (!empty($count_hour_overtime)){
                        $htmlNew = '<div style=\'text-align:left\'>Tăng ca : '.$count_hour_overtime.' (h)</div>';
                        $style_count_hour ='<span style="font-weight:bold;color:green;cursor:pointer" data-html="true" data-toggle="tooltip" data-placement="top" title="'.$htmlNew.'">'.$count_hour_new.'</span>';
                    } else {
                        $style_count_hour = '<span style="font-weight:bold">' . $count_hour_new . '</span>';
                    }

                    $styleBackground = '';
                    if (!empty($count_hour_overtime)){
                        $styleBackground = 'background:#f3e8e8;';
                    }

                    $dateSalary = to_sql_date($val . '/' . $year);
                    $dateNow = date("Y-m-d", strtotime("now"));
                    if (strtotime($dateSalary) <= strtotime($dateNow)) {
                        $htmlEditOverTime = 'display: block;';
                    }

                    $styleTd = '';
                    if ($date_word == 'Sun'){
                        $styleTd = 'background:#f3f3f3;';
                        if ($count_hour > 0) {
                            $style_count_hour = '<span style="font-weight:bold">TC: ' . $count_hour . ' (h)</span>';
                        } else {
                            $style_count_hour = '';
                        }
                    }


                    $style_count_hour .= '<br><select style="height: 30px !important;width:65px !important" name="" class="form-control select-custom ' . $timekeepingId . '__' . $personnel_id . '__' . $day . '__' . $id_timekeeping_detail . '" data-none-selected-text="" onchange="changeTimekeeping(' . $timekeepingId . ', ' . $personnel_id . ', \'' . $day . '\',' . $id_timekeeping_detail . ', this, event)">
                        <option ' . ($type == '' ? 'selected' : '') . ' value=""></option>
                        <option ' . ($type == 'AL' ? 'selected' : '') . ' value="AL">CP</option>
                        <option ' . ($type == 'AL/2' ? 'selected' : '') . ' value="AL/2">P 1/2</option>
                        <option ' . ($type == 'UP' ? 'selected' : '') . ' value="UP">PKL</option>
                        <option ' . ($type == 'UP/2' ? 'selected' : '') . ' value="UP/2">PKL 1/2</option>
                        <option ' . ($type == 'CH' ? 'selected' : '') . ' value="CH">CH</option>
                        <option ' . ($type == 'OD' ? 'selected' : '') . ' value="OD">OD</option>
                        <option ' . ($type == 'TS' ? 'selected' : '') . ' value="TS">TS</option>
                        <option ' . ($type == 'TDL' ? 'selected' : '') . ' value="TDL">TDL</option>
                        <option ' . ($type == 'TAL' ? 'selected' : '') . ' value="TAL">TAL</option>
                        <option ' . ($type == 'NCT' ? 'selected' : '') . ' value="NCT">NCT</option>
                        <option ' . ($type == 'QTLĐ' ? 'selected' : '') . ' value="QTLĐ">QTLĐ</option>
                        <option ' . ($type == 'QK' ? 'selected' : '') . ' value="QK">QK</option>
                        <option ' . ($type == 'GTHV' ? 'selected' : '') . ' value="GTHV">GTHV</option>
                        <option ' . ($type == 'KP' ? 'selected' : '') . ' value="KP">KP</option>
                    </select>
                    <a href="javascript:void(0)" class="edit-note" onclick="loadModalTimekeepingDetailNote(' . $timekeepingId . ', ' . $personnel_id . ', \'' . $day . '\',' . $id_timekeeping_detail . ', \'' . $type . '\')" style="position: absolute; right: 1px; font-size: 10px; bottom: 15px; ' . $htmlEditNote . '"><i class="fa fa-pencil"></i></a>
                    <a href="javascript:void(0)" class="edit-overtime" onclick="loadModalOvertime(' . $timekeepingId . ', ' . $personnel_id . ', \'' . $day . '\',' . $id_timekeeping_detail . ', \'' . $type . '\')" style="font-size: 10px;' . $htmlEditOverTime . '"><i class="fa fa-pencil"></i></a>';


                    $html .= '<td style="width: 150px; position: relative;'.$styleBackground.''.$styleTd.'" class="text-center">
                            ' . $style_count_hour . '
                    </td>';
                }
                $total_number_day = $number_day_new - $totalHoliday - $totalNotHoliday;
                $total_number_day = $total_number_day > 0 ? $total_number_day : 0;

                $countHourNew = $countHourNew + $count_hour_phep + $count_hour_kphep;
                $countHourNew = $countHourNew > 0 ? $countHourNew : 0;

                $total_number_day_salary = $countHourNew + ($totalHoliday * 8);
                $total_number_day_salary = $total_number_day_salary > 0 ? $total_number_day_salary : 0;

                $total_number_day_salary_new = ($countHourNew / HOUR_DAY);

                $html .='<td style="width: 120px" class="text-center">'.($totalHoliday > 0 ? ($totalHoliday * 8) : '').'</td>';
                $html .='<td style="width: 120px" class="text-center">'.($totalNotHoliday > 0 ? ($totalNotHoliday * 8) : '').'</td>';
                $html .='<td style="width: 120px" class="text-center">'.($countHourNew > 0 ? ($countHourNew) : '').'</td>';
                $html .='<td style="width: 120px" class="text-center">'.($total_number_day_salary_new > 0 ? ($total_number_day_salary_new) : '').'</td>';
                $html .='<td style="width: 120px" class="text-center">'.($total_number_day_salary > 0 ? ($total_number_day_salary) : '').'</td>';
                $html .= '</tr>';
            }

            $tfoot = '';
        }

        $data['totalPage'] = $totalPage;
        $data['page'] = $page;

        $data['tHead'] = $tHead;
        $data['tfoot'] = $tfoot;
        $data['html'] = $html;
        $this->load->view('admin/salary/load_dashboard_timekeeping', $data);
    }

    public function changeTypeTimekeeping()
    {
        if (!$this->perEditDashboardTimekeeping) {
            $data['type'] = '';
            $data['result'] = 0;
            $data['message'] = lang('Truy cập bị từ chối');
            echo json_encode($data);
            die();
        }
        $data = [];
        if ($this->input->post()) {
            $timekeepingId = $this->input->post('timekeepingId');
            $personnel_id = $this->input->post('personnel_id');
            $day = $this->input->post('day');
            $idTimekeepingDetail = $this->input->post('idTimekeepingDetail');
            $type = $this->input->post('type');


            $this->db->select('tbl_timekeeping_detail.*');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->where('tbl_timekeeping_detail.id', $idTimekeepingDetail);
            $timekeeping = $this->db->get()->row_array();

            $dateTime = $timekeeping['date'];
            $dateTime = explode('-',$dateTime);
            $this->db->from('tbl_payroll_item');
            $this->db->join('tbl_payroll',
                'tbl_payroll.id = tbl_payroll_item.payroll_id');
            $this->db->where('tbl_payroll.month', $dateTime[1]);
            $this->db->where('tbl_payroll.year', $dateTime[0]);
            $this->db->where('tbl_payroll_item.staff_id', $personnel_id);
            $payrollItem = $this->db->get()->row_array();
            if (!empty($payrollItem)) {
                $data['result'] = 0;
                $data['message'] = 'Đã tính bảng lương không thể thay đổi !';
                echo json_encode($data);
                die();
            }

            $typeCheck = 0;
            if ($type == 'AL' || $type == 'AL/2'){
                $typeCheck = 1;
            } elseif ($type == 'UP' || $type == 'UP/2'){
                $typeCheck = 5;
            } elseif ($type == 'CH'){
                $typeCheck = 3;
            } elseif ($type == 'TS'){
                $typeCheck = 4;
            } elseif ($type == 'OD'){
                $typeCheck = 2;
            } elseif ($type == 'F'){
                $typeCheck = 6;
            }
            $this->db->from('tbl_paid_holiday_leave');
            $this->db->join('tbl_paid_holiday_leave_detail','tbl_paid_holiday_leave_detail.paid_holiday_leave_id = tbl_paid_holiday_leave.id');
            $this->db->where('staff_id',$timekeeping['staff_id']);
            $this->db->where('tbl_paid_holiday_leave_detail.status',1);
            $this->db->where('type_magic_id',$typeCheck);
            $this->db->where('tbl_paid_holiday_leave_detail.date_start <=',$timekeeping['date']);
            $this->db->where('tbl_paid_holiday_leave_detail.date_end >=',$timekeeping['date']);
            $checkPaidHoliday = $this->db->get()->row_array();

            $arrLT = ['TDL','TAL','NCT','QTLĐ','QK','GTHV'];

            if ($type != '' && !in_array($type,$arrLT)) {
                if ($type == 'KP') {
                    $data['result'] = 0;
                    $data['message'] = lang('Loại chấm công này nhảy tự động từ đơn xin phép không thể chọn');
                    echo json_encode($data);
                    die();
                } else {
                    if (empty($checkPaidHoliday)) {
                        $data['type'] = $timekeeping['type'];
                        $data['result'] = 0;
                        $data['message'] = lang('Ngày ' . _dhau($timekeeping['date']) . ' chưa được duyệt đơn xin phép hoặc loại chấm công không phù hợp với đơn xin phép');
                        echo json_encode($data);
                        die();
                    } else {
                        $paid_holiday_id = $checkPaidHoliday['paid_holiday_leave_id'];
                        $paid_holiday_detail_id = $checkPaidHoliday['id'];
                    }
                }
            }

            if ($type == '') {
                if ($timekeeping['type'] != 'X' && !in_array($timekeeping['type'],$arrLT)){
                    $data['result'] = 0;
                    $data['message'] = lang('Ngày này đã áp dụng loại phép bên đơn xin phép không thể thay đổi. Vui lòng thay đổi bên đơn xin phép');
                    echo json_encode($data);
                    die();
                }
                $type = 'X';
                $paid_holiday_id = 0;
                $paid_holiday_detail_id = 0;
            } elseif (in_array($type,$arrLT)){
                $type = $type;
                $paid_holiday_id = 0;
                $paid_holiday_detail_id = 0;
            }

            $op = [
                'type' => $type,
                'date_updated' => date('Y-m-d H:i:s'),
                'updated_by' => get_staff_user_id(),
                'paid_holiday_id' => $paid_holiday_id,
                'paid_holiday_detail_id' => $paid_holiday_detail_id,
            ];
            $this->db->where('id', $idTimekeepingDetail);
            $up = $this->db->update('tbl_timekeeping_detail', $op);
            if ($up) {
                $data['type'] = $type;
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['type'] = $timekeeping['type'];
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function synthetic_timekeeping()
    {
        if (!$this->perViewSyntheticTimekeeping && !$this->perViewOwnSyntheticTimekeeping) {
            accessDenied();
        }
        $data['title'] = lang('Tổng hợp giờ công nhân viên');
        $this->load->view('admin/salary/synthetic_timekeeping', $data);
    }

    public function loadSyntheticTimekeeping()
    {
        $data = [];
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $staff = $this->input->get('staff');
        $department = $this->input->get('department');

        $tHead = '';
        $html = '';
        $tHead = '<tr>
            <th rowspan="4" class="text-center" style="min-width: 50px;">' . lang('tnh_numbers') . '</th>
            <th rowspan="4" class="text-center" style="min-width: 150px;">' . lang('Họ Và Tên') . '</th>
            <th rowspan="4" class="text-center" style="min-width: 100px;">' . lang('Đơn vị') . '</th>
            <th colspan="7" class="text-center" style="min-width: 150px;">' . lang('Giờ công(Giờ)') . '</th>
            <th rowspan="4" class="text-center" style="min-width: 100px;">' . lang('Số phần cơm') . '</th>
            <th rowspan="4" class="text-center" style="min-width: 100px;">' . lang('Số lần đi trễ') . '</th>
            <th colspan="9" class="text-center" style="min-width: 150px;">' . lang('Số giờ nghỉ') . '</th>
            <th rowspan="4" class="text-center" style="min-width: 150px;">' . lang('Tổng giờ công chính được tính lương để áp vào cột (5)') . '</th>
            <th rowspan="4" class="text-center" style="min-width: 150px;">' . lang('Tổng giờ công được tính lương đã bao gồm tăng ca, phép năm') . '</th>
        ';
        $tHead .= '</tr>';
        $tHead .= '<tr>
            <th rowspan="3" class="text-center" style="min-width: 50px;">' . lang('50%') . '</th>
            <th rowspan="3" class="text-center" style="min-width: 50px;">' . lang('100%') . '</th>
            <th rowspan="3" class="text-center" style="min-width: 50px;">' . lang('150%') . '</th>
            <th rowspan="3" class="text-center" style="min-width: 50px;">' . lang('200%') . '</th>
            <th rowspan="3" class="text-center" style="min-width: 50px;">' . lang('300%') . '</th>
            <th rowspan="3" class="text-center" style="min-width: 50px;color:red">' . lang('Tổng cộng') . '</th>
            <th rowspan="3" class="text-center" style="min-width: 50px;">' . lang('Phép năm') . '</th>
            <th rowspan="3" class="text-center" style="min-width: 50px;">' . lang('R') . '</th>
            <th colspan="2" class="text-center" style="min-width: 80px;">' . lang('O') . '</th>
            <th colspan="5" class="text-center" style="min-width: 80px;">' . lang('Ro Trong đó ') . '</th>
            <th rowspan="3" class="text-center" style="min-width: 80px;">' . lang('Không phép') . '</th>
        </tr>';
        $tHead .= '<tr>
            <th rowspan="2" class="text-center" style="min-width: 80px;">' . lang('Có giấy nghỉ hưởng BHXH') . '</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">' . lang('Không có giấy nghỉ hưởng BHXH') . '</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">' . lang('Tổng giờ nghỉ') . '</th>
            <th class="text-center" style="min-width: 80px;">' . lang('Được hỗ trợ theo quy định') . '</th>
            <th colspan="2" class="text-center" style="min-width: 80px;">' . lang('Theo quyết định bổ sung') . '</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">' . lang('Không hỗ trợ') . '</th>
        </tr>';
        $tHead .= '<tr>
            <th class="text-center" style="min-width: 80px;">' . lang('50%') . '</th>
            <th class="text-center" style="min-width: 80px;">' . lang('50%') . '</th>
            <th class="text-center" style="min-width: 80px;">' . lang('100%') . '</th>
        </tr>';
        $tfoot = '';

        //timekeeping
        $timekeepingId = 0;
        $this->db->select('*');
        $this->db->from('tbl_timekeeping');
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        $timekeeping = $this->db->get()->row_array();
        if (!empty($timekeeping)) {
            $timekeepingId = $timekeeping['id'];
        }
        //end timekeeping

        $this->db->select('tblstaff.staffid as staffid,tblstaff.firstname as name');
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        $this->db->where('type_staff', 2);
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        if (!empty($staff)) {
            $this->db->where('tbl_timekeeping_detail.staff_id', $staff);
        }
        if (!empty($department)) {
            $staffDepartments = "(
                SELECT
                    tblstaff_departments.staffid as staffid
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid  = tblstaff.staffid AND tblstaff_departments.departmentid = $department
            )";
            $this->db->where("exists ($staffDepartments)");
        }
        $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
        $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
        $this->db->join('tbl_timekeeping_detail_hour',
            'tbl_timekeeping_detail_hour.timekeeping_detail_id= tbl_timekeeping_detail.id', 'left');
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $personnel = $this->db->get()->result_array();
        if (!empty($personnel)) {
            foreach ($personnel as $key => $value) {

                $tdNumber = '<div class="text-center td-number">' . (++$key) . '</div>';
                $tdNameStaff = '<div class="td-name-staff">
                    <input type="hidden" name="personnel_id[]" class="form-control personnel_id" value="' . $value['staffid'] . '">
                    ' . $value['name'] . '
                </div>';
                $this->db->select('tbldepartments.name as name_departments ');
                $this->db->from('tblstaff_departments');
                $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblstaff_departments.departmentid ',
                    'left');
                $this->db->where('tblstaff_departments.staffid', $value['staffid']);
                $department = $this->db->get()->row_array();
                $tdDepartments = '<div class="td-name-deparment">
                    ' . $department['name_departments'] . '
                </div>';

                //new
                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail_count_hour');
                $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
                $this->db->where('(tbl_timekeeping_detail_count_hour.type_check = 3)');
                $this->db->where('tbl_timekeeping_detail.check_sun = 1');
                $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%")');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('EXISTS (
                    SELECT tbl_tamp.timekeeping_detail_id
                    FROM tbl_timekeeping_detail_count_hour tbl_tamp
                    WHERE tbl_tamp.timekeeping_detail_id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id_old
                    AND tbl_timekeeping_detail_count_hour.type_check = 3
                )');
                $count_hour_detail_new = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_new)) {
                    $count_hour_detail_new = '0';
                }

                $this->db->select('SUM(count_hour) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'O_C_BHXH');
                $count_hour_detail_o_c_bhxh_new = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_o_c_bhxh_new)) {
                    $count_hour_detail_o_c_bhxh_new = '0';
                }

                $this->db->select('SUM(count_hour) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'O_K_BHXH');
                $count_hour_detail_o_k_bhxh_new = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_o_k_bhxh_new)) {
                    $count_hour_detail_o_k_bhxh_new = '0';
                }

                $this->db->select('SUM(count_hour) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'Ro_HT_50');
                $count_hour_detail_ro_ht_50_new = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_ro_ht_50_new)) {
                    $count_hour_detail_ro_ht_50_new = '0';
                }
                //end


                // $this->db->select('TIME_FORMAT(SEC_TO_TIME( SUM( time_to_sec(tbl_timekeeping_detail_count_hour.count_hour))),"%H:%i")  as count_hour');
                // $this->db->from('tbl_timekeeping_detail_count_hour');
                // $this->db->where('tbl_timekeeping_detail_count_hour.staff_id',106);
                // $this->db->where('tbl_timekeeping_detail_count_hour.timekeeping_detail_id',2421);
                // $this->db->where('tbl_timekeeping_detail.check_sun = 0');
                // $this->db->join('tbl_timekeeping_detail','tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id','left');
                // $this->db->join('tbl_timekeeping','tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id','left');
                // $this->db->where('tbl_timekeeping.month',$month);
                // $this->db->where('tbl_timekeeping.year',$year);
                // // $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                // $count_hour_detail_12 = $this->db->get()->row_array()['count_hour'];
                // print_arrays($count_hour_detail_12);


                $count_hour_detail_1 = 0;
                $count_hour_detail_2 = 0;
                $count_hour_detail_3 = 0;
                $count_hour_detail_3_sun = 0;
                $count_hour_detail_4 = 0;
                $count_hour_detail_4_sun = 0;
                $this->db->select('COUNT(*) as `count_hour`');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'Ro-50%');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $count_hour_detail_50_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_50 = 0;
                if ($count_hour_detail_50_db > 0) {
                    $count_hour_detail_50 = ($count_hour_detail_50_db * 8) / 2;
                }


                // $this->db->select('TIME_FORMAT(SEC_TO_TIME( SUM( time_to_sec(tbl_timekeeping_detail_count_hour.count_hour))),"%H:%i")  as count_hour');
                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail_count_hour');
                $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 1');
                $this->db->where('tbl_timekeeping_detail.check_sun = 0');
                $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%" OR tbl_timekeeping_detail.type = "P/2")');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                $count_hour_detail_1 = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_1)) {
                    $count_hour_detail_1 = '0';
                }


                // $this->db->select('TIME_FORMAT(SEC_TO_TIME( SUM( time_to_sec(tbl_timekeeping_detail_count_hour.count_hour_late))),"%H:%i")  as count_hour');
                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail_count_hour');
                $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 1');
                $this->db->where('tbl_timekeeping_detail.check_sun = 0');
                $this->db->where('tbl_timekeeping_detail.type !=', 'Ro-TR');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                $count_hour_detail_1_late = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_1_late = '0';
                if (empty($count_hour_detail_1_late)) {
                    $count_hour_detail_1_late = '0';
                }

                $count_hour_detail_1_total = countHourDetail($count_hour_detail_1, $count_hour_detail_1_late);

                $count_hour_detail_1_total = countHourDetail($count_hour_detail_1_total,
                    $count_hour_detail_o_c_bhxh_new);

                $count_hour_detail_1_total = countHourDetail($count_hour_detail_1_total,
                    $count_hour_detail_o_k_bhxh_new);

                $count_hour_detail_1_total = countHourDetail($count_hour_detail_1_total,
                    $count_hour_detail_ro_ht_50_new);

                // $this->db->select('TIME_FORMAT(SEC_TO_TIME( SUM( time_to_sec(tbl_timekeeping_detail_count_hour.count_hour))),"%H:%i")  as count_hour');
                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail_count_hour');
                $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 2');
                $this->db->where('tbl_timekeeping_detail.check_sun = 0');
                $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%" OR tbl_timekeeping_detail.type = "P/2" OR tbl_timekeeping_detail.type = "P")');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                $count_hour_detail_2 = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_2)) {
                    $count_hour_detail_2 = '0';
                }
                $count_hour_detail_2 = countHourDetail(0, $count_hour_detail_2);

                // $this->db->select('TIME_FORMAT(SEC_TO_TIME( SUM( time_to_sec(tbl_timekeeping_detail_count_hour.count_hour))),"%H:%i")  as count_hour');
                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail_count_hour');
                $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 3');
                $this->db->where('tbl_timekeeping_detail.check_sun = 0');
                $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%"  OR tbl_timekeeping_detail.type = "P/2" OR tbl_timekeeping_detail.type = "P")');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                $count_hour_detail_3 = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_3)) {
                    $count_hour_detail_3 = '0';
                }
                $count_hour_detail_3 = countHourDetail(0, $count_hour_detail_3);

                // $this->db->select('TIME_FORMAT(SEC_TO_TIME( SUM( time_to_sec(tbl_timekeeping_detail_count_hour.count_hour))),"%H:%i")  as count_hour');
                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail_count_hour');
                $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 1');
                $this->db->where('tbl_timekeeping_detail.check_sun = 1');
                $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%")');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                $count_hour_detail_3_sun = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_3_sun)) {
                    $count_hour_detail_3_sun = '0';
                }
                $total_count_hour_detail_3 = countHourDetail($count_hour_detail_3, $count_hour_detail_3_sun);

                $total_count_hour_detail_3 = countHourDetail($total_count_hour_detail_3, $count_hour_detail_new);


                // $this->db->select('TIME_FORMAT(SEC_TO_TIME( SUM( time_to_sec(tbl_timekeeping_detail_count_hour.count_hour))),"%H:%i")  as count_hour');
                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail_count_hour');
                $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 4');
                $this->db->where('(tbl_timekeeping_detail.check_sun = 0 OR tbl_timekeeping_detail.check_sun = 1)');
                $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%")');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                $count_hour_detail_4 = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_4)) {
                    $count_hour_detail_4 = '0';
                }
                $count_hour_detail_4 = countHourDetail(0, $count_hour_detail_4);

                // $this->db->select('TIME_FORMAT(SEC_TO_TIME( SUM( time_to_sec(tbl_timekeeping_detail_count_hour.count_hour))),"%H:%i")  as count_hour');
                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail_count_hour');
                $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
                $this->db->where('(tbl_timekeeping_detail_count_hour.type_check = 2 OR tbl_timekeeping_detail_count_hour.type_check = 3)');
                $this->db->where('tbl_timekeeping_detail.check_sun = 1');
                $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%")');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
//                $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                $count_hour_detail_4_sun = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_4_sun)) {
                    $count_hour_detail_4_sun = '0';
                }

                $total_count_hour_detail_4 = countHourDetail($count_hour_detail_4, $count_hour_detail_4_sun);

                $total_count_hour_detail_4 = countHourDetailNew($total_count_hour_detail_4, $count_hour_detail_new);

                $total_hour_detail = $count_hour_detail_50 + $count_hour_detail_1_total + $count_hour_detail_2 + $total_count_hour_detail_3 + $total_count_hour_detail_4;

                if ($total_hour_detail == 0) {
                    $total_hour_detail = '';
                }

                if ($count_hour_detail_50 == 0) {
                    $count_hour_detail_50 = '';
                }

                //phep nam

                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'P');
                $count_hour_detail_phep_nam_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_phep_nam = 0;
                $count_hour_detail_phep_nam = ($count_hour_detail_phep_nam_db * 8);


                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'P/2');
                $count_hour_detail_phep_nam_db_50 = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_phep_nam_50 = 0;
                $count_hour_detail_phep_nam_50 = ($count_hour_detail_phep_nam_db_50 * 4);

                $count_hour_detail_phep_nam = $count_hour_detail_phep_nam + $count_hour_detail_phep_nam_50;

                if ($count_hour_detail_phep_nam == 0) {
                    $count_hour_detail_phep_nam = '';
                }
                //end
                //phan com
                $this->db->select('COUNT(*) as count_rice');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.count_rice ', 1);
                $count_hour_detail_rice = $this->db->get()->row_array()['count_rice'];
                if ($count_hour_detail_rice == 0) {
                    $count_hour_detail_rice = '';
                }
                //end

                //di tre
                $this->db->select('COUNT(*) as count_late');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.count_late ', 1);
                $count_hour_detail_late = $this->db->get()->row_array()['count_late'];
                if ($count_hour_detail_late == 0) {
                    $count_hour_detail_late = '';
                }
                //end
                //khong phep

                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'KP');
                $count_hour_detail_kp_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_kp = 0;
                $count_hour_detail_kp = ($count_hour_detail_kp_db * 8);
                if ($count_hour_detail_kp == 0) {
                    $count_hour_detail_kp = '';
                }
                //end
                // R
                $this->db->select('SUM(tbl_timekeeping_detail_note.value) as count_hour');
                $this->db->from('tbl_timekeeping_detail_note');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail_note.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail_note.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_note.type ', 'R');
                $this->db->group_by('staff_id,type');
                $count_hour_detail_r_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_r = 0;
                $count_hour_detail_r = ($count_hour_detail_r_db * 8);

                //end
                //O_C_BHXH
                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'O_C_BHXH');
                $count_hour_detail_o_c_bhxh_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_o_c_bhxh = 0;
                $count_hour_detail_o_c_bhxh = ($count_hour_detail_o_c_bhxh_db * 8);

                $count_hour_detail_o_c_bhxh = countHourDetailNew($count_hour_detail_o_c_bhxh,
                    $count_hour_detail_o_c_bhxh_new);

                if ($count_hour_detail_o_c_bhxh < 0) {
                    $count_hour_detail_o_c_bhxh = 0;
                }
                //end
                // o_k_bhxh
                $this->db->select('SUM(tbl_timekeeping_detail_note.value) as count_hour');
                $this->db->from('tbl_timekeeping_detail_note');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail_note.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail_note.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_note.type ', 'O_K_BHXH');
                $this->db->group_by('staff_id,type');
                $count_hour_detail_o_k_bhxh_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_o_k_bhxh = 0;
                $count_hour_detail_o_k_bhxh = ($count_hour_detail_o_k_bhxh_db * 8);

                $count_hour_detail_o_k_bhxh = countHourDetailNew($count_hour_detail_o_k_bhxh,
                    $count_hour_detail_o_k_bhxh_new);
                if ($count_hour_detail_o_k_bhxh < 0) {
                    $count_hour_detail_o_k_bhxh = 0;
                }
                //end
                // RO_HT_50
                $this->db->select('SUM(tbl_timekeeping_detail_note.value) as count_hour');
                $this->db->from('tbl_timekeeping_detail_note');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail_note.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail_note.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_note.type ', 'Ro_HT_50');
                $this->db->group_by('staff_id,type');
                $count_hour_detail_ro_ht_50_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_ro_ht_50 = 0;
                $count_hour_detail_ro_ht_50 = ($count_hour_detail_ro_ht_50_db * 8) / 2;

                $count_hour_detail_ro_ht_50 = countHourDetailNew($count_hour_detail_ro_ht_50,
                    $count_hour_detail_ro_ht_50_new);
                if ($count_hour_detail_ro_ht_50 < 0) {
                    $count_hour_detail_ro_ht_50 = 0;
                }
                //end
                //RO_BS_50
                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'Ro_BS_50');
                $count_hour_detail_ro_bs_50_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_ro_bs_50 = 0;
                $count_hour_detail_ro_bs_50 = ($count_hour_detail_ro_bs_50_db * 8) / 2;

                //end
                //RO_BS_100
                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'Ro_BS_100');
                $count_hour_detail_ro_bs_100_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_ro_bs_100 = 0;
                $count_hour_detail_ro_bs_100 = ($count_hour_detail_ro_bs_100_db * 8);

                //end
                //Ro - Không hỗ trợ tính lương
                $count_hour_detail_ro = 0;
                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail.count_hour_late),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'Ro');
                $this->db->where('tbl_timekeeping_detail.count_hour_late !=0 ');
                $count_hour_detail_ro_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_ro = $count_hour_detail_ro_db;

                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'Ro');
                $this->db->where('tbl_timekeeping_detail.count_hour_late', 0);
                $count_hour_detail_ro_db_new = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_ro += ($count_hour_detail_ro_db_new * 8);

                //end

                //tong gio cong chinh
                $total_count_working_hour = 0;
                $total_count_working_hour = ($count_hour_detail_o_k_bhxh + $count_hour_detail_r + $count_hour_detail_1_total) + (($count_hour_detail_ro_ht_50 + $count_hour_detail_ro_bs_50) * 50 / 100) + (($count_hour_detail_ro_bs_100) * 100 / 100);
                //
                // tong gio cong chinh + tang ca
                $total_count_working_hour_overtime = 0;
                $total_count_working_hour_overtime = $count_hour_detail_2 + $total_count_hour_detail_3 + $total_count_hour_detail_4 + $total_count_working_hour;
                //end
                if ($total_count_working_hour_overtime == 0) {
                    $total_count_working_hour_overtime = '';
                }

                if ($total_count_working_hour == 0) {
                    $total_count_working_hour = '';
                }

                if ($total_count_hour_detail_3 == 0) {
                    $total_count_hour_detail_3 = '';
                }

                if ($total_count_hour_detail_4 == 0) {
                    $total_count_hour_detail_4 = '';
                }

                if ($count_hour_detail_o_k_bhxh == 0) {
                    $count_hour_detail_o_k_bhxh = '';
                }

                if ($count_hour_detail_o_c_bhxh == 0) {
                    $count_hour_detail_o_c_bhxh = '';
                }

                if ($count_hour_detail_r == 0) {
                    $count_hour_detail_r = '';
                }

                if ($count_hour_detail_1_total == 0) {
                    $count_hour_detail_1_total = '';
                }
                //end

                //Tổng giò nghỉ
                $total_hour_break = $count_hour_detail_ro_ht_50 + $count_hour_detail_ro_bs_50 + $count_hour_detail_ro_bs_100 + $count_hour_detail_ro;
                //end
                if ($total_hour_break == 0) {
                    $total_hour_break = '';
                }
                if ($count_hour_detail_ro_ht_50 == 0) {
                    $count_hour_detail_ro_ht_50 = '';
                }
                if ($count_hour_detail_ro_bs_50 == 0) {
                    $count_hour_detail_ro_bs_50 = '';
                }
                if ($count_hour_detail_ro_bs_100 == 0) {
                    $count_hour_detail_ro_bs_100 = '';
                }
                if ($count_hour_detail_ro == 0) {
                    $count_hour_detail_ro = '';
                }
                if ($count_hour_detail_2 == 0) {
                    $count_hour_detail_2 = '';
                }


                $html .= '<tr>';
                $html .= '<td style="min-width: 50px;">' . $tdNumber . '</td>';


                $html .= '<td style="min-width: 150px;">' . $tdNameStaff . '</td>';
                $html .= '<td style="min-width: 100px;">' . $tdDepartments . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center">' . $count_hour_detail_50 . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center">' . $count_hour_detail_1_total . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center">' . $count_hour_detail_2 . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center">' . $total_count_hour_detail_3 . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center">' . $total_count_hour_detail_4 . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center;color:red">' . $total_hour_detail . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center">' . $count_hour_detail_phep_nam . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center">' . $count_hour_detail_rice . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center">' . $count_hour_detail_late . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center">' . $count_hour_detail_r . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center">' . $count_hour_detail_o_c_bhxh . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center">' . $count_hour_detail_o_k_bhxh . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center;color:red">' . $total_hour_break . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center">' . $count_hour_detail_ro_ht_50 . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center">' . $count_hour_detail_ro_bs_50 . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center">' . $count_hour_detail_ro_bs_100 . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center">' . $count_hour_detail_ro . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center">' . $count_hour_detail_kp . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center;color:red">' . $total_count_working_hour . '</td>';
                $html .= '<td style="min-width: 50px;text-align: center;color:red">' . $total_count_working_hour_overtime . '</td>';
                $html .= '</tr>';
            }
        }


        $data['tHead'] = $tHead;
        $data['tfoot'] = $tfoot;
        $data['html'] = $html;
        $this->load->view('admin/salary/load_synthetic_timekeeping', $data);
    }

    public function viewCheckHour($timekeeping_detail_hour_id, $timekeeping_detail_id, $type, $type_check_day = 0)
    {
        $timekeeping_detail_in = [];
        if ($type == 1) {
            $data['timekeeping_detail_hour'] = get_table_where('tbl_timekeeping_detail_hour',
                ['id' => $timekeeping_detail_hour_id], '', 'row_array');
        } elseif ($type == 2) {
            $this->db->select("*");
            $this->db->from('tbl_timekeeping_detail_hour');
            $this->db->where('timekeeping_detail_id', $timekeeping_detail_id);
            $this->db->where('hour IS NOT NULL');
            $this->db->where('type', 1);
            $this->db->order_by('id asc');
            $timekeeping_detail_hour = $this->db->get()->row_array();
            $data['timekeeping_detail_hour'] = $timekeeping_detail_hour;
            $timekeeping_detail_in = get_table_where('tbl_timekeeping_detail',
                ['id' => $timekeeping_detail_hour['timekeeping_detail_id']], '', 'row_array');
        }
        $data['timekeeping_detail_id'] = $timekeeping_detail_id;
        $data['timekeeping_detail_hour_id'] = $timekeeping_detail_hour_id;
        $data['timekeeping_detail_in'] = $timekeeping_detail_in;
        $data['type'] = $type;
        $this->load->view('admin/salary/view_check_hour_new', $data);

    }

    public function loadModalOvertime()
    {
        if (!$this->perEditDashboardTimekeeping) {
            accessDenied();
        }
        if ($this->input->post('save')) {
            $data = [];
            $timekeepingId = $this->input->post('timekeepingId');
            $staffId = $this->input->post('staffId');
            $typeTimeKeeping = $this->input->post('typeTimeKeeping');
            $timekeeping_detail_id = $this->input->post('timekeeping_detail_id');
            $filed = $this->input->post('filed');
            $type = $this->input->post('type');
            $rel_id = $this->input->post('rel_id');
            $customer_text = $this->input->post('customer_text');
            $staff_id = $this->input->post('staff_id');
            $note = $this->input->post('note', false);

            $this->db->select('tbl_timekeeping_detail.*,tbl_timekeeping.month, tbl_timekeeping.year');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->join('tbl_timekeeping','tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id');
            $this->db->where('tbl_timekeeping_detail.id',$timekeeping_detail_id);
            $timekeeping_detail_new = $this->db->get()->row_array();
            $date= '';
            $month= '';
            $year= '';
            $up = false;
            $id_detail = '';
            if (!empty($timekeeping_detail_new)) {
                $date = $timekeeping_detail_new['date'];
                $month = $timekeeping_detail_new['month'];
                $year = $timekeeping_detail_new['year'];
            }


            $business_fee = 3;

            $this->db->from('tbl_business_fee_boiler_calculate');
            $this->db->join('tbl_business_fee_boiler_calculate_item',
                'tbl_business_fee_boiler_calculate_item.business_fee_boiler_calculate_id = tbl_business_fee_boiler_calculate.id');
            $this->db->where('tbl_business_fee_boiler_calculate.month', $month);
            $this->db->where('tbl_business_fee_boiler_calculate.year', $year);
            $this->db->where('tbl_business_fee_boiler_calculate.type', $business_fee);
            $this->db->where('tbl_business_fee_boiler_calculate_item.staff_id', $staffId);
            $checkBusinessFeeCalucateItem = $this->db->get()->row_array();
            if (!empty($checkBusinessFeeCalucateItem)) {
                $data['result'] = 0;
                $data['message'] = 'Đã tính bảng công tác phí không thể thay đổi. Vui lòng xóa bảng tính !';
                echo json_encode($data);
                die();
            }

            $this->db->select('tbl_business_fee_boiler_overtime.id as id');
            $this->db->from('tbl_business_fee_boiler_overtime');
            $this->db->where('staff_id', $staffId);
            $this->db->where('month', $month);
            $this->db->where('year', $year);
            $this->db->where('type', $business_fee);
            $checkBusinessFee = $this->db->get()->row_array();
            if (!empty($checkBusinessFee)) {
                $this->db->select('tbl_business_fee_boiler_overtime_detail.*');
                $this->db->from('tbl_business_fee_boiler_overtime_detail');
                $this->db->where('tbl_business_fee_boiler_overtime_detail.business_fee_boiler_overtime_id',
                    $checkBusinessFee['id']);
                $this->db->where('tbl_business_fee_boiler_overtime_detail.date', $date);
                $checkBusinessFeeDetail = $this->db->get()->row_array();
                $arrUpdate = [];
                if (!empty($checkBusinessFeeDetail)){
                    if (!empty($filed)){
                        $holiday = 0;
                        $go_night = 0;
                        $back_night = 0;
                        $construction_allowance = 0;
                        $construction_allowance_province = 0;
                        $allowance_survey = 0;
                        $sunday = $checkBusinessFeeDetail['sunday'];
                        $weekday = $checkBusinessFeeDetail['weekday'];
                        foreach ($filed as $kk => $vv){
                            if ($vv == 1){
                                $holiday = !empty($checkBusinessFeeDetail['holiday']) ? $checkBusinessFeeDetail['holiday'] : (!empty($checkBusinessFeeDetail['weekday']) ? $checkBusinessFeeDetail['weekday'] : $checkBusinessFeeDetail['sunday']);
                            }
                            if (empty($holiday)){
                                if ($timekeeping_detail_new['check_sun'] == 1){
                                    $sunday = $checkBusinessFeeDetail['holiday'];
                                } else {
                                    $weekday = $checkBusinessFeeDetail['holiday'];
                                }
                            }
                            if ($vv == 2){
                                $go_night = 1;
                            }
                            if ($vv == 3){
                                $back_night = 1;
                            }
                            if ($vv == 4){
                                $construction_allowance = 1;
                            }
                            if ($vv == 5){
                                $construction_allowance_province = 1;
                            }
                            if ($vv == 6){
                                $allowance_survey = 1;
                            }
                            $arrUpdate = [
                                'sunday' => !empty($holiday) ? 0 : $sunday,
                                'weekday' => !empty($holiday) ? 0 : $weekday,
                                'holiday' => $holiday,
                                'go_night' => $go_night,
                                'back_night' => $back_night,
                                'construction_allowance' => $construction_allowance,
                                'construction_allowance_province' => $construction_allowance_province,
                                'type' => $type,
                                'customer_id' => $rel_id,
                                'customer_text' => $customer_text,
                                'allowance_survey' => $allowance_survey,
                                'note' => $note,
                            ];
                        }
                    }
                    if(!empty($arrUpdate)){
                        $this->db->where('id',$checkBusinessFeeDetail['id']);
                        $up = $this->db->update('tbl_business_fee_boiler_overtime_detail',$arrUpdate);
                        $id_detail = $checkBusinessFeeDetail['id'];
                    }
                } else {
                    if (!empty($filed)){
                        $holiday = 0;
                        $go_night = 0;
                        $back_night = 0;
                        $construction_allowance = 0;
                        $construction_allowance_province = 0;
                        $allowance_survey = 0;
                        $sunday = 0;
                        $weekday = 0;
                        foreach ($filed as $kk => $vv){
                            if ($vv == 1){
                                $holiday = 0;
                            }
                            if (empty($holiday)){
                                if ($timekeeping_detail_new['check_sun'] == 1){
                                    $sunday = 0;
                                } else {
                                    $weekday = 0;
                                }
                            }
                            if ($vv == 2){
                                $go_night = 1;
                            }
                            if ($vv == 3){
                                $back_night = 1;
                            }
                            if ($vv == 4){
                                $construction_allowance = 1;
                            }
                            if ($vv == 5){
                                $construction_allowance_province = 1;
                            }
                            if ($vv == 6){
                                $allowance_survey = 1;
                            }
                            $arrUpdate = [
                                'sunday' => !empty($holiday) ? 0 : $sunday,
                                'weekday' => !empty($holiday) ? 0 : $weekday,
                                'holiday' => $holiday,
                                'go_night' => $go_night,
                                'back_night' => $back_night,
                                'construction_allowance' => $construction_allowance,
                                'construction_allowance_province' => $construction_allowance_province,
                                'date' => $date,
                                'business_fee_boiler_overtime_id' => $checkBusinessFee['id'],
                                'type' => $type,
                                'customer_id' => $rel_id,
                                'customer_text' => $customer_text,
                                'allowance_survey' => $allowance_survey,
                                'note' => $note,
                            ];
                        }
                    }
                    if(!empty($arrUpdate)){
                        $this->db->insert('tbl_business_fee_boiler_overtime_detail', $arrUpdate);
                        $up = $this->db->insert_id();
                        $id_detail = $up;
                    }
                }
            } else {
                $arrInsert = [];
                if (!empty($filed)){
                    $holiday = 0;
                    $go_night = 0;
                    $back_night = 0;
                    $construction_allowance = 0;
                    $construction_allowance_province = 0;
                    $allowance_survey = 0;
                    $sunday = 0;
                    $weekday = 0;
                    foreach ($filed as $kk => $vv){
                        if ($vv == 1){
                            $holiday = 0;
                        }
                        if (empty($holiday)){
                            if ($timekeeping_detail_new['check_sun'] == 1){
                                $sunday = 0;
                            } else {
                                $weekday = 0;
                            }
                        }
                        if ($vv == 2){
                            $go_night = 1;
                        }
                        if ($vv == 3){
                            $back_night = 1;
                        }
                        if ($vv == 4){
                            $construction_allowance = 1;
                        }
                        if ($vv == 5){
                            $construction_allowance_province = 1;
                        }
                        if ($vv == 6){
                            $allowance_survey = 1;
                        }
                        $arrInsert = [
                            'sunday' => !empty($holiday) ? 0 : $sunday,
                            'weekday' => !empty($holiday) ? 0 : $weekday,
                            'holiday' => $holiday,
                            'go_night' => $go_night,
                            'back_night' => $back_night,
                            'construction_allowance' => $construction_allowance,
                            'construction_allowance_province' => $construction_allowance_province,
                            'type' => $type,
                            'date' => $date,
                            'customer_id' => $rel_id,
                            'customer_text' => $customer_text,
                            'allowance_survey' => $allowance_survey,
                            'note' => $note,
                        ];
                    }
                }
                $name_text = get_table_where('tbl_personnel',
                    ['id' => $staffId], '', 'row_array');
                if (!empty($arrInsert)) {
                    $this->db->insert('tbl_business_fee_boiler_overtime', [
                        'name' => $name_text['fullname'],
                        'month' => $month,
                        'year' => $year,
                        'staff_id' => $staffId,
                        'date_created' => date('Y-m-d H:i:s'),
                        'created_by' => get_staff_user_id(),
                        'type' => $business_fee,
                    ]);
                    $id_insert = $this->db->insert_id();
                    if ($id_insert) {
                        $arrInsert['business_fee_boiler_overtime_id'] = $id_insert;
                        $this->db->insert('tbl_business_fee_boiler_overtime_detail', $arrInsert);
                        $up = $this->db->insert_id();
                        $id_detail = $up;
                    }
                }
            }

            if ($up) {
                $this->db->where('business_fee_boiler_overtime_detail_id',$id_detail);
                $this->db->delete('tbl_business_fee_boiler_overtime_detail_staff');
                if(!empty($staff_id)){
                    foreach ($staff_id as $kk => $vv ){
                        $this->db->insert('tbl_business_fee_boiler_overtime_detail_staff',[
                           'business_fee_boiler_overtime_detail_id' => $id_detail,
                           'staff_id' => $vv,
                        ]);
                    }
                }
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
            echo json_encode($data);
            die;
        }

        $day = $this->input->get('day');
        $timekeepingId = $this->input->get('timekeepingId');
        $personnel_id = $this->input->get('personnel_id');
        $typeTimeKeeping = $this->input->get('typeTimeKeeping');
        $idTimekeepingDetail = $this->input->get('idTimekeepingDetail');

        $type_now = '';
        $date= '';
        $month= '';
        $year= '';
        $this->db->select('tbl_timekeeping_detail.*,tbl_timekeeping.month, tbl_timekeeping.year');
        $this->db->from('tbl_timekeeping_detail');
        $this->db->join('tbl_timekeeping','tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id');
        $this->db->where('tbl_timekeeping_detail.id',$idTimekeepingDetail);
        $timekeeping_detail = $this->db->get()->row_array();

        if (!empty($timekeeping_detail)) {
            $type_now = $timekeeping_detail['type'];
            $date = $timekeeping_detail['date'];
            $month = $timekeeping_detail['month'];
            $year = $timekeeping_detail['year'];
        }


        $business_fee = 3;
        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";

        $this->db->select('tblstaff.staffid as id,tblstaff.code as code,CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname,tb_department.name_department as name_department');
        $this->db->from('tblstaff');
        $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
        $this->db->where('active', 1);
        $data['staffNew'] = $this->db->get()->result_array();


        $checkBusinessFeeDetail = [];
        $this->db->select('tbl_business_fee_boiler_overtime.id as id');
        $this->db->from('tbl_business_fee_boiler_overtime');
        $this->db->where('staff_id', $personnel_id);
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $this->db->where('type', $business_fee);
        $checkBusinessFee = $this->db->get()->row_array();
        if (!empty($checkBusinessFee)) {
            $this->db->select('tbl_business_fee_boiler_overtime_detail.*');
            $this->db->from('tbl_business_fee_boiler_overtime_detail');
            $this->db->where('tbl_business_fee_boiler_overtime_detail.business_fee_boiler_overtime_id',
                $checkBusinessFee['id']);
            $this->db->where('tbl_business_fee_boiler_overtime_detail.date', $date);
            $checkBusinessFeeDetail = $this->db->get()->row_array();
        }

        $data['checkBusinessFeeDetail'] = $checkBusinessFeeDetail;
        $data['listOvertime'] = getListOvertime();
        $data['timekeepingId'] = $timekeepingId;
        $data['personnel_id'] = $personnel_id;
        $data['idTimekeepingDetail'] = $idTimekeepingDetail;
        $data['typeTimeKeeping'] = $typeTimeKeeping;
        $data['day'] = $day;
        $data['type_now'] = $type_now;
        $data['title'] = lang('Tạo tăng ca');
        $this->load->view('admin/salary/created_overtime', $data);
    }

    public function createdNoteTimekeeping()
    {
        if (!$this->perEditDashboardTimekeeping) {
            accessDenied();
        }
        if ($this->input->post('save')) {
            $data = [];
            $this->form_validation->set_rules('reason_id', lang("Nguyên nhân"), 'required');
            $this->form_validation->set_rules('value', lang("Số ngày"), 'required');
            if ($this->form_validation->run() == true) {
                $timekeepingId = $this->input->post('timekeepingId');
                $staffId = $this->input->post('staffId');
                $typeTimeKeeping = $this->input->post('typeTimeKeeping');
                $value = $this->input->post('value');
                $timekeeping_detail_id = $this->input->post('timekeeping_detail_id');
                $reason_id = $this->input->post('reason_id');
                $note = $this->input->post('note', false);

                $this->db->where('staff_id', $staffId);
                $this->db->where('timekeeping_id', $timekeepingId);
                $this->db->where('timekeeping_detail_id', $timekeeping_detail_id);
                $this->db->delete('tbl_timekeeping_detail_note');

                $op = [
                    'type' => $typeTimeKeeping,
                    'date_updated' => date('Y-m-d H:i:s'),
                    'updated_by' => get_staff_user_id(),
                ];
                $this->db->where('id', $timekeeping_detail_id);
                $up = $this->db->update('tbl_timekeeping_detail', $op);
                if ($up) {
                    $option = [
                        'note' => $note,
                        'staff_id' => $staffId,
                        'reason_id' => $reason_id,
                        'type' => $typeTimeKeeping,
                        'value' => $value,
                        'timekeeping_id' => $timekeepingId,
                        'timekeeping_detail_id' => $timekeeping_detail_id,
                        'date_create' => date('Y-m-d H:i:s'),
                        'created_by' => get_staff_user_id(),
                    ];

                    $in = $this->db->insert('tbl_timekeeping_detail_note', $option);
                    $data['type'] = $typeTimeKeeping;
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

        $day = $this->input->get('day');
        $timekeepingId = $this->input->get('timekeepingId');
        $personnel_id = $this->input->get('personnel_id');
        $typeTimeKeeping = $this->input->get('typeTimeKeeping');
        $idTimekeepingDetail = $this->input->get('idTimekeepingDetail');

        $this->db->select('tbl_timekeeping_detail_note.*');
        $this->db->from('tbl_timekeeping_detail_note');
        $this->db->where('tbl_timekeeping_detail_note.timekeeping_detail_id', $idTimekeepingDetail);
        $timeKeepingDetailNote = $this->db->get()->row_array();

        $reasons = get_table_where('tbl_timekeeping_reason', ['type' => $typeTimeKeeping], '', 'result_array');
        $type_now = '';
        $timekeeping_detail = get_table_where('tbl_timekeeping_detail', ['id' => $idTimekeepingDetail], '',
            'row_array');
        if (!empty($timekeeping_detail)) {
            $type_now = $timekeeping_detail['type'];
        }
        $data['timeKeepingDetailNote'] = $timeKeepingDetailNote;
        $data['timekeepingId'] = $timekeepingId;
        $data['personnel_id'] = $personnel_id;
        $data['idTimekeepingDetail'] = $idTimekeepingDetail;
        $data['typeTimeKeeping'] = $typeTimeKeeping;
        $data['reasons'] = $reasons;
        $data['day'] = $day;
        $data['type_now'] = $type_now;
        $data['title'] = lang('Tạo lý do chấm công');
        $this->load->view('admin/salary/created_note_timekeeping', $data);
    }

    public function average_vote()
    {
        if (!$this->perViewAverageVote && !$this->perViewOwnAverageVote) {
            accessDenied();
        }
        $data['title'] = lang('Bảng xét bình bầu');
        $this->load->view('admin/salary/average_vote', $data);
    }

    public function loadAverageVote()
    {
        $data = [];
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $staff = $this->input->get('staff');
        $department = $this->input->get('department');

        $tHead = '';
        $html = '';
        $tHead = '<tr>
            <th rowspan="2" class="text-center" style="min-width: 50px;">' . lang('tnh_numbers') . '</th>
            <th rowspan="2" class="text-center" style="min-width: 150px;">' . lang('Họ Và Tên') . '</th>
            <th rowspan="2" class="text-center" style="min-width: 100px;">' . lang('Bình bầu tháng trước') . '</th>
            <th rowspan="2" class="text-center" style="min-width: 100px;">' . lang('Số lần đi trễ') . '</th>
            <th rowspan="2" class="text-center" style="min-width: 100px;">' . lang('Số giờ nghỉ Ro') . '</th>
            <th rowspan="2" class="text-center" style="min-width: 100px;">' . lang('Số giờ không phép') . '</th>
            <th colspan="2" class="text-center" style="min-width: 100px;">' . lang('Bình bầu') . '</th>
            <th rowspan="2" class="text-center" style="min-width: 150px;">' . lang('Nhận xét') . '</th>
        ';
        $tHead .= '</tr>';
        $tHead .= '<tr>
            <th class="text-center" style="min-width: 100px;">' . lang('Tổ chấm') . '</th>
            <th class="text-center" style="min-width: 100px;">' . lang('BGĐ duyệt') . '</th>
        </tr>';

        $tfoot = '';

        $AverageVoteItems = [];

        $this->db->select('*');
        $this->db->from('tbl_timekeeping');
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        $timekeeping = $this->db->get()->row_array();


        if (!empty($timekeeping)) {

            $AverageVoteId = 0;
            $this->db->select('*');
            $this->db->from('tbl_average_vote');
            $this->db->where('tbl_average_vote.month', $month);
            $this->db->where('tbl_average_vote.year', $year);
            $averagevote = $this->db->get()->row_array();
            if (!empty($averagevote)) {
                $AverageVoteId = $averagevote['id'];
            } else {
                $this->db->insert('tbl_average_vote', [
                    'month' => $month,
                    'year' => $year,
                    'date_created' => date('Y-m-d H:i'),
                    'staff_id' => get_staff_user_id(),
                ]);
                $AverageVoteId = $this->db->insert_id();
            }

            $this->db->select('*');
            $this->db->from('tbl_average_vote_item');
            $this->db->where('tbl_average_vote_item.average_vote_id', $AverageVoteId);
            $details = $this->db->get()->row_array();
            if (!empty($details)) {
                // $this->db->select('tblstaff.staffid as staffid,tblstaff.firstname as name,tbl_average_vote_item.*');
                // $this->db->from('tblstaff');
                // $this->db->where('active', 1);
                // $this->db->where('type_staff', 2);
                // $this->db->where('tbl_average_vote.month', $month);
                // $this->db->where('tbl_average_vote.year', $year);
                // if(!empty($staff)){
                //     $this->db->where('tbl_average_vote_item.staff_id', $staff);
                // }
                // if(!empty($department)){
                //     $staffDepartments = "(
                //         SELECT
                //             tblstaff_departments.staffid as staffid
                //         FROM tblstaff_departments
                //         WHERE tblstaff_departments.staffid  = tblstaff.staffid AND tblstaff_departments.departmentid = $department
                //     )";
                //     $this->db->where("exists ($staffDepartments)");
                // }
                // $this->db->join('tbl_average_vote_item', 'tbl_average_vote_item.staff_id= tblstaff.staffid', 'left');
                // $this->db->join('tbl_average_vote', 'tbl_average_vote.id= tbl_average_vote_item.average_vote_id', 'left');
                // $this->db->group_by('tbl_average_vote_item.staff_id');
                // $AverageVoteItems  = $this->db->get()->result_array();

                $this->db->select('tblstaff.staffid as staffid,tblstaff.firstname as name');
                $this->db->from('tblstaff');
                $this->db->where('active', 1);
                $this->db->where('type_staff', 2);
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where("NOT EXISTS (
                	SELECT
	                    tbl_average_vote_item.staff_id as staff_id
	                FROM tbl_average_vote_item
	                JOIN tbl_average_vote ON tbl_average_vote.id = tbl_average_vote_item.average_vote_id
	                WHERE tblstaff.staffid  = tbl_average_vote_item.staff_id AND tbl_average_vote.month = $month AND tbl_average_vote.year = $year
            	)");
                $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
                $this->db->group_by('tbl_timekeeping_detail.staff_id');
                $personnel = $this->db->get()->result_array();
                if (!empty($personnel)) {
                    foreach ($personnel as $key => $value) {
                        $this->db->from('tbl_average_vote_item');
                        $this->db->where('tbl_average_vote_item.average_vote_id', $AverageVoteId);
                        $this->db->where('tbl_average_vote_item.staff_id', $value['staffid']);
                        $isAverageVoteItem = $this->db->get()->num_rows();

                        if (empty($isAverageVoteItem)) {

                            $this->db->select('SUM(count_hour) as count_hour');
                            $this->db->from('tbl_timekeeping_detail');
                            $this->db->join('tbl_timekeeping',
                                'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                                'left');
                            $this->db->where('tbl_timekeeping.month', $month);
                            $this->db->where('tbl_timekeeping.year', $year);
                            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                            $this->db->where('tbl_timekeeping_detail.type ', 'Ro_HT_50');
                            $count_hour_detail_ro_ht_50_new = $this->db->get()->row_array()['count_hour'];
                            if (empty($count_hour_detail_ro_ht_50_new)) {
                                $count_hour_detail_ro_ht_50_new = '0';
                            }

                            $this->db->select('COUNT(*) as count_late');
                            $this->db->from('tbl_timekeeping_detail');
                            $this->db->join('tbl_timekeeping',
                                'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id', 'left');
                            $this->db->where('tbl_timekeeping.month', $month);
                            $this->db->where('tbl_timekeeping.year', $year);
                            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                            $this->db->where('tbl_timekeeping_detail.count_late ', 1);
                            $count_hour_detail_late = $this->db->get()->row_array()['count_late'];

                            // RO_HT_50
                            $this->db->select('SUM(tbl_timekeeping_detail_note.value) as count_hour');
                            $this->db->from('tbl_timekeeping_detail_note');
                            $this->db->join('tbl_timekeeping',
                                'tbl_timekeeping.id = tbl_timekeeping_detail_note.timekeeping_id', 'left');
                            $this->db->where('tbl_timekeeping.month', $month);
                            $this->db->where('tbl_timekeeping.year', $year);
                            $this->db->where('tbl_timekeeping_detail_note.staff_id', $value['staffid']);
                            $this->db->where('tbl_timekeeping_detail_note.type ', 'Ro_HT_50');
                            $this->db->group_by('staff_id,type');
                            $count_hour_detail_ro_ht_50_db = $this->db->get()->row_array()['count_hour'];
                            $count_hour_detail_ro_ht_50 = 0;
                            $count_hour_detail_ro_ht_50 = ($count_hour_detail_ro_ht_50_db * 8) / 2;

                            $count_hour_detail_ro_ht_50 = countHourDetailNew($count_hour_detail_ro_ht_50,
                                $count_hour_detail_ro_ht_50_new);

                            if ($count_hour_detail_ro_ht_50 < 0) {
                                $count_hour_detail_ro_ht_50 = 0;
                            }

                            //end
                            //RO_BS_50
                            $this->db->select('COUNT(*) as count_hour');
                            $this->db->from('tbl_timekeeping_detail');
                            $this->db->join('tbl_timekeeping',
                                'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id', 'left');
                            $this->db->where('tbl_timekeeping.month', $month);
                            $this->db->where('tbl_timekeeping.year', $year);
                            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                            $this->db->where('tbl_timekeeping_detail.type ', 'Ro_BS_50');
                            $count_hour_detail_ro_bs_50_db = $this->db->get()->row_array()['count_hour'];
                            $count_hour_detail_ro_bs_50 = 0;
                            $count_hour_detail_ro_bs_50 = ($count_hour_detail_ro_bs_50_db * 8) / 2;

                            //end
                            //RO_BS_100
                            $this->db->select('COUNT(*) as count_hour');
                            $this->db->from('tbl_timekeeping_detail');
                            $this->db->join('tbl_timekeeping',
                                'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id', 'left');
                            $this->db->where('tbl_timekeeping.month', $month);
                            $this->db->where('tbl_timekeeping.year', $year);
                            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                            $this->db->where('tbl_timekeeping_detail.type ', 'Ro_BS_100');
                            $count_hour_detail_ro_bs_100_db = $this->db->get()->row_array()['count_hour'];
                            $count_hour_detail_ro_bs_100 = 0;
                            $count_hour_detail_ro_bs_100 = ($count_hour_detail_ro_bs_100_db * 8);

                            //end
                            //Ro - Không hỗ trợ tính lương
                            // $this->db->select('COUNT(*) as count_hour');
                            // $this->db->from('tbl_timekeeping_detail');
                            // $this->db->join('tbl_timekeeping','tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id','left');
                            // $this->db->where('tbl_timekeeping.month',$month);
                            // $this->db->where('tbl_timekeeping.year',$year);
                            // $this->db->where('tbl_timekeeping_detail.staff_id',$value['staffid']);
                            // $this->db->where('tbl_timekeeping_detail.type ','Ro');
                            // $count_hour_detail_ro_db = $this->db->get()->row_array()['count_hour'];


                            $count_hour_detail_ro = 0;
                            $this->db->select('COALESCE(SUM(tbl_timekeeping_detail.count_hour_late),"0")  as count_hour');
                            $this->db->from('tbl_timekeeping_detail');
                            $this->db->join('tbl_timekeeping',
                                'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                                'left');
                            $this->db->where('tbl_timekeeping.month', $month);
                            $this->db->where('tbl_timekeeping.year', $year);
                            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                            $this->db->where('tbl_timekeeping_detail.type ', 'Ro');
                            $this->db->where('tbl_timekeeping_detail.count_hour_late !=0 ');
                            $count_hour_detail_ro_db = $this->db->get()->row_array()['count_hour'];
                            $count_hour_detail_ro = $count_hour_detail_ro_db;

                            $this->db->select('COUNT(*) as count_hour');
                            $this->db->from('tbl_timekeeping_detail');
                            $this->db->join('tbl_timekeeping',
                                'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                                'left');
                            $this->db->where('tbl_timekeeping.month', $month);
                            $this->db->where('tbl_timekeeping.year', $year);
                            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                            $this->db->where('tbl_timekeeping_detail.type ', 'Ro');
                            $this->db->where('tbl_timekeeping_detail.count_hour_late', 0);
                            $count_hour_detail_ro_db_new = $this->db->get()->row_array()['count_hour'];
                            $count_hour_detail_ro += ($count_hour_detail_ro_db_new * 8);

                            $total_hour_break = $count_hour_detail_ro_ht_50 + $count_hour_detail_ro_bs_50 + $count_hour_detail_ro_bs_100 + $count_hour_detail_ro;

                            $this->db->select('COUNT(*) as count_hour');
                            $this->db->from('tbl_timekeeping_detail');
                            $this->db->join('tbl_timekeeping',
                                'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id', 'left');
                            $this->db->where('tbl_timekeeping.month', $month);
                            $this->db->where('tbl_timekeeping.year', $year);
                            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                            $this->db->where('tbl_timekeeping_detail.type ', 'KP');
                            $count_hour_detail_kp_db = $this->db->get()->row_array()['count_hour'];
                            $count_hour_detail_kp = ($count_hour_detail_kp_db * 8);

                            $month_old = '';
                            $year_old = '';
                            if ($month == 1) {
                                $year_old = $year - 1;
                                $month_old = 12;
                            } else {
                                $year_old = $year;
                                $month_old = $month - 1;
                            }
                            $average_vote_old = null;
                            //average old
                            $this->db->select('tbl_average_vote_item.average_vote_manager as average_vote_manager');
                            $this->db->from('tbl_average_vote_item');
                            $this->db->join('tbl_average_vote',
                                'tbl_average_vote.id = tbl_average_vote_item.average_vote_id', 'left');
                            $this->db->where('tbl_average_vote.month', $month_old);
                            $this->db->where('tbl_average_vote.year', $year_old);
                            $this->db->where('tbl_average_vote_item.staff_id', $value['staffid']);
                            $result = $this->db->get()->row_array();
                            if (!empty($result)) {
                                $average_vote_old = $result['average_vote_manager'];
                            }
                            //

                            $arrAverageVoteItem[] = [
                                'average_vote_id' => $AverageVoteId,
                                'staff_id' => $value['staffid'],
                                'average_vote_old' => $average_vote_old,
                                'count_late' => $count_hour_detail_late,
                                'count_hour_ro' => $total_hour_break,
                                'count_hour_kp' => $count_hour_detail_kp,
                                'average_vote' => null,
                                'average_vote_manager' => null,
                                'note' => null,
                            ];
                        }
                    }
                    if (!empty($arrAverageVoteItem)) {
                        $this->db->insert_batch('tbl_average_vote_item', $arrAverageVoteItem);
                    }
                }

                $this->db->select('tblstaff.staffid as staffid,tblstaff.firstname as name,tbl_average_vote_item.*');
                $this->db->from('tblstaff');
                $this->db->where('active', 1);
                $this->db->where('type_staff', 2);
                $this->db->where('tbl_average_vote.month', $month);
                $this->db->where('tbl_average_vote.year', $year);
                if (!empty($staff)) {
                    $this->db->where('tbl_average_vote_item.staff_id', $staff);
                }
                if (!empty($department)) {
                    $staffDepartments = "(
                        SELECT
                            tblstaff_departments.staffid as staffid
                        FROM tblstaff_departments
                        WHERE tblstaff_departments.staffid  = tblstaff.staffid AND tblstaff_departments.departmentid = $department
                    )";
                    $this->db->where("exists ($staffDepartments)");
                }
                $this->db->join('tbl_average_vote_item', 'tbl_average_vote_item.staff_id= tblstaff.staffid', 'left');
                $this->db->join('tbl_average_vote', 'tbl_average_vote.id= tbl_average_vote_item.average_vote_id',
                    'left');
                $this->db->group_by('tbl_average_vote_item.staff_id');
                $AverageVoteItems = $this->db->get()->result_array();


            } else {
                $this->db->select('tblstaff.staffid as staffid,tblstaff.firstname as name');
                $this->db->from('tblstaff');
                $this->db->where('active', 1);
                $this->db->where('type_staff', 2);
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);

                $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
                $this->db->group_by('tbl_timekeeping_detail.staff_id');
                $personnel = $this->db->get()->result_array();
                if (!empty($personnel)) {
                    foreach ($personnel as $key => $value) {
                        $this->db->from('tbl_average_vote_item');
                        $this->db->where('tbl_average_vote_item.average_vote_id', $AverageVoteId);
                        $this->db->where('tbl_average_vote_item.staff_id', $value['staffid']);
                        $isAverageVoteItem = $this->db->get()->num_rows();

                        if (empty($isAverageVoteItem)) {

                            $this->db->select('SUM(count_hour) as count_hour');
                            $this->db->from('tbl_timekeeping_detail');
                            $this->db->join('tbl_timekeeping',
                                'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                                'left');
                            $this->db->where('tbl_timekeeping.month', $month);
                            $this->db->where('tbl_timekeeping.year', $year);
                            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                            $this->db->where('tbl_timekeeping_detail.type ', 'Ro_HT_50');
                            $count_hour_detail_ro_ht_50_new = $this->db->get()->row_array()['count_hour'];
                            if (empty($count_hour_detail_ro_ht_50_new)) {
                                $count_hour_detail_ro_ht_50_new = '0';
                            }

                            $this->db->select('COUNT(*) as count_late');
                            $this->db->from('tbl_timekeeping_detail');
                            $this->db->join('tbl_timekeeping',
                                'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id', 'left');
                            $this->db->where('tbl_timekeeping.month', $month);
                            $this->db->where('tbl_timekeeping.year', $year);
                            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                            $this->db->where('tbl_timekeeping_detail.count_late ', 1);
                            $count_hour_detail_late = $this->db->get()->row_array()['count_late'];

                            // RO_HT_50
                            $this->db->select('SUM(tbl_timekeeping_detail_note.value) as count_hour');
                            $this->db->from('tbl_timekeeping_detail_note');
                            $this->db->join('tbl_timekeeping',
                                'tbl_timekeeping.id = tbl_timekeeping_detail_note.timekeeping_id', 'left');
                            $this->db->where('tbl_timekeeping.month', $month);
                            $this->db->where('tbl_timekeeping.year', $year);
                            $this->db->where('tbl_timekeeping_detail_note.staff_id', $value['staffid']);
                            $this->db->where('tbl_timekeeping_detail_note.type ', 'Ro_HT_50');
                            $this->db->group_by('staff_id,type');
                            $count_hour_detail_ro_ht_50_db = $this->db->get()->row_array()['count_hour'];
                            $count_hour_detail_ro_ht_50 = 0;
                            $count_hour_detail_ro_ht_50 = ($count_hour_detail_ro_ht_50_db * 8) / 2;

                            $count_hour_detail_ro_ht_50 = countHourDetailNew($count_hour_detail_ro_ht_50,
                                $count_hour_detail_ro_ht_50_new);

                            if ($count_hour_detail_ro_ht_50 < 0) {
                                $count_hour_detail_ro_ht_50 = 0;
                            }

                            //end
                            //RO_BS_50
                            $this->db->select('COUNT(*) as count_hour');
                            $this->db->from('tbl_timekeeping_detail');
                            $this->db->join('tbl_timekeeping',
                                'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id', 'left');
                            $this->db->where('tbl_timekeeping.month', $month);
                            $this->db->where('tbl_timekeeping.year', $year);
                            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                            $this->db->where('tbl_timekeeping_detail.type ', 'Ro_BS_50');
                            $count_hour_detail_ro_bs_50_db = $this->db->get()->row_array()['count_hour'];
                            $count_hour_detail_ro_bs_50 = 0;
                            $count_hour_detail_ro_bs_50 = ($count_hour_detail_ro_bs_50_db * 8) / 2;

                            //end
                            //RO_BS_100
                            $this->db->select('COUNT(*) as count_hour');
                            $this->db->from('tbl_timekeeping_detail');
                            $this->db->join('tbl_timekeeping',
                                'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id', 'left');
                            $this->db->where('tbl_timekeeping.month', $month);
                            $this->db->where('tbl_timekeeping.year', $year);
                            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                            $this->db->where('tbl_timekeeping_detail.type ', 'Ro_BS_100');
                            $count_hour_detail_ro_bs_100_db = $this->db->get()->row_array()['count_hour'];
                            $count_hour_detail_ro_bs_100 = 0;
                            $count_hour_detail_ro_bs_100 = ($count_hour_detail_ro_bs_100_db * 8);

                            //end
                            //Ro - Không hỗ trợ tính lương
                            // $this->db->select('COUNT(*) as count_hour');
                            // $this->db->from('tbl_timekeeping_detail');
                            // $this->db->join('tbl_timekeeping','tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id','left');
                            // $this->db->where('tbl_timekeeping.month',$month);
                            // $this->db->where('tbl_timekeeping.year',$year);
                            // $this->db->where('tbl_timekeeping_detail.staff_id',$value['staffid']);
                            // $this->db->where('tbl_timekeeping_detail.type ','Ro');
                            // $count_hour_detail_ro_db = $this->db->get()->row_array()['count_hour'];


                            $count_hour_detail_ro = 0;
                            $this->db->select('COALESCE(SUM(tbl_timekeeping_detail.count_hour_late),"0")  as count_hour');
                            $this->db->from('tbl_timekeeping_detail');
                            $this->db->join('tbl_timekeeping',
                                'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                                'left');
                            $this->db->where('tbl_timekeeping.month', $month);
                            $this->db->where('tbl_timekeeping.year', $year);
                            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                            $this->db->where('tbl_timekeeping_detail.type ', 'Ro');
                            $this->db->where('tbl_timekeeping_detail.count_hour_late !=0 ');
                            $count_hour_detail_ro_db = $this->db->get()->row_array()['count_hour'];
                            $count_hour_detail_ro = $count_hour_detail_ro_db;

                            $this->db->select('COUNT(*) as count_hour');
                            $this->db->from('tbl_timekeeping_detail');
                            $this->db->join('tbl_timekeeping',
                                'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                                'left');
                            $this->db->where('tbl_timekeeping.month', $month);
                            $this->db->where('tbl_timekeeping.year', $year);
                            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                            $this->db->where('tbl_timekeeping_detail.type ', 'Ro');
                            $this->db->where('tbl_timekeeping_detail.count_hour_late', 0);
                            $count_hour_detail_ro_db_new = $this->db->get()->row_array()['count_hour'];
                            $count_hour_detail_ro += ($count_hour_detail_ro_db_new * 8);

                            $total_hour_break = $count_hour_detail_ro_ht_50 + $count_hour_detail_ro_bs_50 + $count_hour_detail_ro_bs_100 + $count_hour_detail_ro;

                            $this->db->select('COUNT(*) as count_hour');
                            $this->db->from('tbl_timekeeping_detail');
                            $this->db->join('tbl_timekeeping',
                                'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id', 'left');
                            $this->db->where('tbl_timekeeping.month', $month);
                            $this->db->where('tbl_timekeeping.year', $year);
                            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                            $this->db->where('tbl_timekeeping_detail.type ', 'KP');
                            $count_hour_detail_kp_db = $this->db->get()->row_array()['count_hour'];
                            $count_hour_detail_kp = ($count_hour_detail_kp_db * 8);

                            $month_old = '';
                            $year_old = '';
                            if ($month == 1) {
                                $year_old = $year - 1;
                                $month_old = 12;
                            } else {
                                $year_old = $year;
                                $month_old = $month - 1;
                            }
                            $average_vote_old = null;
                            //average old
                            $this->db->select('tbl_average_vote_item.average_vote_manager as average_vote_manager');
                            $this->db->from('tbl_average_vote_item');
                            $this->db->join('tbl_average_vote',
                                'tbl_average_vote.id = tbl_average_vote_item.average_vote_id', 'left');
                            $this->db->where('tbl_average_vote.month', $month_old);
                            $this->db->where('tbl_average_vote.year', $year_old);
                            $this->db->where('tbl_average_vote_item.staff_id', $value['staffid']);
                            $result = $this->db->get()->row_array();
                            if (!empty($result)) {
                                $average_vote_old = $result['average_vote_manager'];
                            }
                            //

                            $arrAverageVoteItem[] = [
                                'average_vote_id' => $AverageVoteId,
                                'staff_id' => $value['staffid'],
                                'average_vote_old' => $average_vote_old,
                                'count_late' => $count_hour_detail_late,
                                'count_hour_ro' => $total_hour_break,
                                'count_hour_kp' => $count_hour_detail_kp,
                                'average_vote' => null,
                                'average_vote_manager' => null,
                                'note' => null,
                            ];
                        }
                    }
                    if (!empty($arrAverageVoteItem)) {
                        $this->db->insert_batch('tbl_average_vote_item', $arrAverageVoteItem);
                    }
                }

                $this->db->select('tblstaff.staffid as staffid,tblstaff.firstname as name,tbl_average_vote_item.*');
                $this->db->from('tblstaff');
                $this->db->where('active', 1);
                $this->db->where('type_staff', 2);
                $this->db->where('tbl_average_vote.month', $month);
                $this->db->where('tbl_average_vote.year', $year);
                if (!empty($staff)) {
                    $this->db->where('tbl_average_vote_item.staff_id', $staff);
                }
                if (!empty($department)) {
                    $staffDepartments = "(
                        SELECT
                            tblstaff_departments.staffid as staffid
                        FROM tblstaff_departments
                        WHERE tblstaff_departments.staffid  = tblstaff.staffid AND tblstaff_departments.departmentid = $department
                    )";
                    $this->db->where("exists ($staffDepartments)");
                }
                $this->db->join('tbl_average_vote_item', 'tbl_average_vote_item.staff_id= tblstaff.staffid', 'left');
                $this->db->join('tbl_average_vote', 'tbl_average_vote.id= tbl_average_vote_item.average_vote_id',
                    'left');
                $this->db->group_by('tbl_average_vote_item.staff_id');
                $AverageVoteItems = $this->db->get()->result_array();
            }
        }
        $checkAverage = false;

        if (!empty($AverageVoteItems)) {
            foreach ($AverageVoteItems as $key => $value) {

                $style_count_hour = '';
                $style_count_hour_manager = '';
                $type = $value['average_vote'];
                $type_manager = $value['average_vote_manager'];
                $note = $value['note'];
                $tdNumber = '<div class="text-center td-number">' . (++$key) . '</div>';
                $tdNameStaff = '<div class="td-name-staff">
                    <input type="hidden" name="personnel_id[]" class="form-control personnel_id" value="' . $value['staffid'] . '">
                    ' . $value['name'] . '
                </div>';

                $count_late = $value['count_late'];
                $count_hour_ro = $value['count_hour_ro'];
                $count_hour_kp = $value['count_hour_kp'];
                $idAverageVoteItem = $value['id'];

                $this->db->select('SUM(count_hour) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'Ro_HT_50');
                $count_hour_detail_ro_ht_50_new = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_ro_ht_50_new)) {
                    $count_hour_detail_ro_ht_50_new = '0';
                }

                //update item
                $this->db->select('COUNT(*) as count_late');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staff_id']);
                $this->db->where('tbl_timekeeping_detail.count_late ', 1);
                $count_hour_detail_late = $this->db->get()->row_array()['count_late'];

                // RO_HT_50
                $this->db->select('SUM(tbl_timekeeping_detail_note.value) as count_hour');
                $this->db->from('tbl_timekeeping_detail_note');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail_note.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail_note.staff_id', $value['staff_id']);
                $this->db->where('tbl_timekeeping_detail_note.type ', 'Ro_HT_50');
                $this->db->group_by('staff_id,type');
                $count_hour_detail_ro_ht_50_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_ro_ht_50 = 0;
                $count_hour_detail_ro_ht_50 = ($count_hour_detail_ro_ht_50_db * 8) / 2;

                $count_hour_detail_ro_ht_50 = countHourDetailNew($count_hour_detail_ro_ht_50,
                    $count_hour_detail_ro_ht_50_new);

                if ($count_hour_detail_ro_ht_50 < 0) {
                    $count_hour_detail_ro_ht_50 = 0;
                }

                //end
                //RO_BS_50
                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staff_id']);
                $this->db->where('tbl_timekeeping_detail.type ', 'Ro_BS_50');
                $count_hour_detail_ro_bs_50_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_ro_bs_50 = 0;
                $count_hour_detail_ro_bs_50 = ($count_hour_detail_ro_bs_50_db * 8) / 2;

                //end
                //RO_BS_100
                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staff_id']);
                $this->db->where('tbl_timekeeping_detail.type ', 'Ro_BS_100');
                $count_hour_detail_ro_bs_100_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_ro_bs_100 = 0;
                $count_hour_detail_ro_bs_100 = ($count_hour_detail_ro_bs_100_db * 8);

                //end
                //Ro - Không hỗ trợ tính lương
                $count_hour_detail_ro = 0;
                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail.count_hour_late),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'Ro');
                $this->db->where('tbl_timekeeping_detail.count_hour_late !=0 ');
                $count_hour_detail_ro_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_ro = $count_hour_detail_ro_db;

                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'Ro');
                $this->db->where('tbl_timekeeping_detail.count_hour_late', 0);
                $count_hour_detail_ro_db_new = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_ro += ($count_hour_detail_ro_db_new * 8);
                $total_hour_break = $count_hour_detail_ro_ht_50 + $count_hour_detail_ro_bs_50 + $count_hour_detail_ro_bs_100 + $count_hour_detail_ro;

                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staff_id']);
                $this->db->where('tbl_timekeeping_detail.type ', 'KP');
                $count_hour_detail_kp_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_kp = ($count_hour_detail_kp_db * 8);

                if ($count_hour_detail_late != $count_late || $total_hour_break != $count_hour_ro || $count_hour_detail_kp != $count_hour_kp) {
                    $this->db->where('id', $idAverageVoteItem);
                    $this->db->update('tbl_average_vote_item', [
                        'count_late' => $count_hour_detail_late,
                        'count_hour_ro' => $total_hour_break,
                        'count_hour_kp' => $count_hour_detail_kp
                    ]);
                    $checkAverage = true;
                }

                $month_old = '';
                $year_old = '';
                if ($month == 1) {
                    $year_old = $year - 1;
                    $month_old = 12;
                } else {
                    $year_old = $year;
                    $month_old = $month - 1;
                }
                $average_vote_old = null;
                //average old
                $this->db->select('tbl_average_vote_item.average_vote_manager as average_vote_manager');
                $this->db->from('tbl_average_vote_item');
                $this->db->join('tbl_average_vote', 'tbl_average_vote.id = tbl_average_vote_item.average_vote_id',
                    'left');
                $this->db->where('tbl_average_vote.month', $month_old);
                $this->db->where('tbl_average_vote.year', $year_old);
                $this->db->where('tbl_average_vote_item.staff_id', $value['staff_id']);
                $result = $this->db->get()->row_array();
                if (!empty($result)) {
                    $average_vote_old = $result['average_vote_manager'];
                }
                if ($average_vote_old != $value['average_vote_old']) {
                    $this->db->where('id', $idAverageVoteItem);
                    $this->db->update('tbl_average_vote_item', ['average_vote_old' => $average_vote_old]);
                    $checkAverage = true;
                }
                //

                if ($value['count_late'] == 0) {
                    $count_late = '';
                }
                if ($value['count_hour_ro'] == 0) {
                    $count_hour_ro = '';
                }
                if ($value['count_hour_kp'] == 0) {
                    $count_hour_kp = '';
                }

                $html .= '<tr>';
                $html .= '<td style="min-width: 50px;">' . $tdNumber . '</td>';
                $html .= '<td style="min-width: 150px;">' . $tdNameStaff . '</td>';
                $html .= '<td style="min-width: 100px;text-align:center">' . $value['average_vote_old'] . '</td>';
                $html .= '<td style="min-width: 100px;text-align:center">' . $count_late . '</td>';
                $html .= '<td style="min-width: 100px;text-align:center">' . $count_hour_ro . '</td>';
                $html .= '<td style="min-width: 100px;text-align:center">' . $count_hour_kp . '</td>';
                $style_count_hour .= '<select style="height: 30px !important;width:65px !important" name="" class="form-control select-custom ' . $AverageVoteId . '__' . $value['staffid'] . '__' . $value['id'] . '" data-none-selected-text="" onchange="changeAverage(' . $AverageVoteId . ', ' . $value['staffid'] . ', ' . $value['id'] . ', this, event)">
                    <option ' . ($type == '' ? 'selected' : '') . ' value=""></option>
                    <option ' . ($type == 'A' ? 'selected' : '') . ' value="A">A</option>
                    <option ' . ($type == 'B' ? 'selected' : '') . ' value="B">B</option>
                    <option ' . ($type == 'C' ? 'selected' : '') . ' value="C">C</option>
                </select>';

                $html .= '<td style="width: 100px; position: relative;" class="text-center">
                        ' . $style_count_hour . '
                </td>';

                $style_count_hour_manager .= '<select style="height: 30px !important;width:65px !important" name="" class="form-control select-custom ' . $AverageVoteId . '__' . $value['staffid'] . '__' . $value['id'] . '" data-none-selected-text="" onchange="changeAverageManager(' . $AverageVoteId . ', ' . $value['staffid'] . ', ' . $value['id'] . ', this, event)">
                    <option ' . ($type_manager == '' ? 'selected' : '') . ' value=""></option>
                    <option ' . ($type_manager == 'A' ? 'selected' : '') . ' value="A">A</option>
                    <option ' . ($type_manager == 'B' ? 'selected' : '') . ' value="B">B</option>
                    <option ' . ($type_manager == 'C' ? 'selected' : '') . ' value="C">C</option>
                </select>';

                $html .= '<td style="width: 100px; position: relative;" class="text-center">
                        ' . $style_count_hour_manager . '
                </td>';
                $html .= '<td style="min-width: 100px;">
                    <textarea style="width:100%" rows="2" onchange="changeAverageNote(' . $AverageVoteId . ', ' . $value['staffid'] . ', ' . $value['id'] . ', this, event)">' . $note . '</textarea>
                </td>';

                $html .= '</tr>';
            }
        }

        if ($checkAverage) {
            $html = '';
            $this->db->select('tblstaff.staffid as staffid,tblstaff.firstname as name,tbl_average_vote_item.*');
            $this->db->from('tblstaff');
            $this->db->where('active', 1);
            $this->db->where('type_staff', 2);
            $this->db->where('tbl_average_vote.month', $month);
            $this->db->where('tbl_average_vote.year', $year);
            if (!empty($staff)) {
                $this->db->where('tbl_average_vote_item.staff_id', $staff);
            }
            if (!empty($department)) {
                $staffDepartments = "(
                    SELECT
                        tblstaff_departments.staffid as staffid
                    FROM tblstaff_departments
                    WHERE tblstaff_departments.staffid  = tblstaff.staffid AND tblstaff_departments.departmentid = $department
                )";
                $this->db->where("exists ($staffDepartments)");
            }
            $this->db->join('tbl_average_vote_item', 'tbl_average_vote_item.staff_id= tblstaff.staffid', 'left');
            $this->db->join('tbl_average_vote', 'tbl_average_vote.id= tbl_average_vote_item.average_vote_id', 'left');
            $this->db->group_by('tbl_average_vote_item.staff_id');
            $AverageVoteItems = $this->db->get()->result_array();
            if (!empty($AverageVoteItems)) {
                foreach ($AverageVoteItems as $key => $value) {

                    $style_count_hour = '';
                    $style_count_hour_manager = '';
                    $type = $value['average_vote'];
                    $type_manager = $value['average_vote_manager'];
                    $note = $value['note'];
                    $tdNumber = '<div class="text-center td-number">' . (++$key) . '</div>';
                    $tdNameStaff = '<div class="td-name-staff">
                        <input type="hidden" name="personnel_id[]" class="form-control personnel_id" value="' . $value['staffid'] . '">
                        ' . $value['name'] . '
                    </div>';

                    $count_late = $value['count_late'];
                    $count_hour_ro = $value['count_hour_ro'];
                    $count_hour_kp = $value['count_hour_kp'];


                    if ($value['count_late'] == 0) {
                        $count_late = '';
                    }
                    if ($value['count_hour_ro'] == 0) {
                        $count_hour_ro = '';
                    }
                    if ($value['count_hour_kp'] == 0) {
                        $count_hour_kp = '';
                    }
                    $html .= '<tr>';
                    $html .= '<td style="min-width: 50px;">' . $tdNumber . '</td>';
                    $html .= '<td style="min-width: 150px;">' . $tdNameStaff . '</td>';
                    $html .= '<td style="min-width: 100px;text-align:center">' . $value['average_vote_old'] . '</td>';
                    $html .= '<td style="min-width: 100px;text-align:center">' . $count_late . '</td>';
                    $html .= '<td style="min-width: 100px;text-align:center">' . $count_hour_ro . '</td>';
                    $html .= '<td style="min-width: 100px;text-align:center">' . $count_hour_kp . '</td>';
                    $style_count_hour .= '<select style="height: 30px !important;width:65px !important" name="" class="form-control select-custom ' . $AverageVoteId . '__' . $value['staffid'] . '__' . $value['id'] . '" data-none-selected-text="" onchange="changeAverage(' . $AverageVoteId . ', ' . $value['staffid'] . ', ' . $value['id'] . ', this, event)">
                        <option ' . ($type == '' ? 'selected' : '') . ' value=""></option>
                        <option ' . ($type == 'A' ? 'selected' : '') . ' value="A">A</option>
                        <option ' . ($type == 'B' ? 'selected' : '') . ' value="B">B</option>
                        <option ' . ($type == 'C' ? 'selected' : '') . ' value="C">C</option>
                    </select>';

                    $html .= '<td style="width: 100px; position: relative;" class="text-center">
                            ' . $style_count_hour . '
                    </td>';

                    $style_count_hour_manager .= '<select style="height: 30px !important;width:65px !important" name="" class="form-control select-custom ' . $AverageVoteId . '__' . $value['staffid'] . '__' . $value['id'] . '" data-none-selected-text="" onchange="changeAverageManager(' . $AverageVoteId . ', ' . $value['staffid'] . ', ' . $value['id'] . ', this, event)">
                        <option ' . ($type_manager == '' ? 'selected' : '') . ' value=""></option>
                        <option ' . ($type_manager == 'A' ? 'selected' : '') . ' value="A">A</option>
                        <option ' . ($type_manager == 'B' ? 'selected' : '') . ' value="B">B</option>
                        <option ' . ($type_manager == 'C' ? 'selected' : '') . ' value="C">C</option>
                    </select>';

                    $html .= '<td style="width: 100px; position: relative;" class="text-center">
                            ' . $style_count_hour_manager . '
                    </td>';
                    $html .= '<td style="min-width: 100px;">
                        <textarea rows="2" style="width:100%" onchange="changeAverageNote(' . $AverageVoteId . ', ' . $value['staffid'] . ', ' . $value['id'] . ', this, event)">' . $note . '</textarea>
                    </td>';

                    $html .= '</tr>';
                }
            }
        }

        if (empty($timekeeping)) {
            $data['month'] = $month;
            $data['year'] = $year;
            $this->load->view('admin/salary/load_view_empty', $data);
        } else {
            $data['tHead'] = $tHead;
            $data['tfoot'] = $tfoot;
            $data['html'] = $html;
            $this->load->view('admin/salary/load_average_vote', $data);
        }
    }

    public function changeTypeAverage()
    {
        if (!$this->perEditAverageVote) {
            $data['result'] = 0;
            $data['message'] = lang('Truy cập bị từ chối');
            echo json_encode($data);
            die();
        }
        $data = [];
        if ($this->input->post()) {
            $averageVoteId = $this->input->post('averageVoteId');
            $personnel_id = $this->input->post('personnel_id');
            $idAverageVoteItem = $this->input->post('idAverageVoteItem');
            $type = $this->input->post('type');

            $this->db->select('tbl_average_vote_item.*');
            $this->db->from('tbl_average_vote_item');
            $this->db->where('tbl_average_vote_item.id', $idAverageVoteItem);
            $averageVote = $this->db->get()->row_array();

            $op = [
                'average_vote' => $type,
                'date_updated' => date('Y-m-d H:i:s'),
                'updated_by' => get_staff_user_id(),
            ];
            $this->db->where('id', $idAverageVoteItem);
            $up = $this->db->update('tbl_average_vote_item', $op);
            if ($up) {
                $data['type'] = $type;
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['type'] = $averageVote['average_vote'];
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function changeTypeAverageManager()
    {
        if (!$this->perEditAverageVote) {
            $data['result'] = 0;
            $data['message'] = lang('Truy cập bị từ chối');
            echo json_encode($data);
            die();
        }
        $data = [];
        if ($this->input->post()) {
            $averageVoteId = $this->input->post('averageVoteId');
            $personnel_id = $this->input->post('personnel_id');
            $idAverageVoteItem = $this->input->post('idAverageVoteItem');
            $type = $this->input->post('type');

            $this->db->select('tbl_average_vote_item.*');
            $this->db->from('tbl_average_vote_item');
            $this->db->where('tbl_average_vote_item.id', $idAverageVoteItem);
            $averageVote = $this->db->get()->row_array();

            $op = [
                'average_vote_manager' => $type,
                'date_updated_manager' => date('Y-m-d H:i:s'),
                'updated_by_manager' => get_staff_user_id(),
            ];
            $this->db->where('id', $idAverageVoteItem);
            $up = $this->db->update('tbl_average_vote_item', $op);
            if ($up) {
                $data['type'] = $type;
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['type'] = $averageVote['average_vote_manager'];
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function changeTypeAverageNote()
    {
        if (!$this->perEditAverageVote) {
            $data['result'] = 0;
            $data['message'] = lang('Truy cập bị từ chối');
            echo json_encode($data);
            die();
        }
        $data = [];
        if ($this->input->post()) {
            $averageVoteId = $this->input->post('averageVoteId');
            $personnel_id = $this->input->post('personnel_id');
            $idAverageVoteItem = $this->input->post('idAverageVoteItem');
            $type = $this->input->post('type');

            $this->db->select('tbl_average_vote_item.*');
            $this->db->from('tbl_average_vote_item');
            $this->db->where('tbl_average_vote_item.id', $idAverageVoteItem);
            $averageVote = $this->db->get()->row_array();

            $op = [
                'note' => $type,
            ];
            $this->db->where('id', $idAverageVoteItem);
            $up = $this->db->update('tbl_average_vote_item', $op);
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

    public function print_average_vote()
    {
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $department = $this->input->get('department');
        $staff = $this->input->get('staff');
        ob_end_clean();
        $data = [];

        $this->db->select('tblstaff.staffid as staffid,tblstaff.firstname as name,tbl_average_vote_item.*');
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        $this->db->where('type_staff', 2);
        $this->db->where('tbl_average_vote.month', $month);
        $this->db->where('tbl_average_vote.year', $year);
        if (!empty($staff)) {
            $this->db->where('tbl_average_vote_item.staff_id', $staff);
        }
        if (!empty($department)) {
            $staffDepartments = "(
                SELECT
                    tblstaff_departments.staffid as staffid
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid  = tblstaff.staffid AND tblstaff_departments.departmentid = $department
            )";
            $this->db->where("exists ($staffDepartments)");
        }
        $this->db->join('tbl_average_vote_item', 'tbl_average_vote_item.staff_id= tblstaff.staffid', 'left');
        $this->db->join('tbl_average_vote', 'tbl_average_vote.id= tbl_average_vote_item.average_vote_id', 'left');
        $this->db->group_by('tbl_average_vote_item.staff_id');
        $AverageVoteItems = $this->db->get()->result_array();

        if (!empty($department)) {
            $this->db->where('departmentid', $department);
            $data_departments = $this->db->get('tbldepartments')->row();
        }


        $data['title'] = lang('print') . ' ' . lang('BẢNG XÉT BÌNH BẦU A,B,C') . $month . 'năm ' . $year;
        $data['type'] = 'P';
        $data['img'] = '';

        $bodyItems = '';
        if (!empty($AverageVoteItems)) {
            foreach ($AverageVoteItems as $key => $value) {

                $type = $value['average_vote'];
                $type_manager = $value['average_vote_manager'];
                $note = $value['note'];
                $name = $value['name'];
                $average_vote_old = $value['average_vote_old'];

                $count_late = $value['count_late'];
                $count_hour_ro = $value['count_hour_ro'];
                $count_hour_kp = $value['count_hour_kp'];


                if ($value['count_late'] == 0) {
                    $count_late = '';
                }
                if ($value['count_hour_ro'] == 0) {
                    $count_hour_ro = '';
                }
                if ($value['count_hour_kp'] == 0) {
                    $count_hour_kp = '';
                }


                $tdNumber = '<td style="width:5%;padding-top:15px;height:25px;" class="text-center" ><p>' . (++$key) . '</p></td>';
                $tdName = '<td style="width:27%;padding:15px;"><span style="text-align: left">' . $name . '</span></td>';
                $tdAverageOld = '<td style="width:7%;padding:15px;" class="text-center">' . $average_vote_old . '</td>';
                $tdCountLate = '<td style="width:7%;padding:15px;" class="text-center">' . $count_late . '</td>';
                $tdCountHourRo = '<td style="width:7%;padding:15px;" class="text-center">' . $count_hour_ro . '</td>';
                $tdCountHourKp = '<td style="width:7%;padding:15px;" class="text-center" >' . $count_hour_kp . '</td>';
                $tdType = '<td style="width:6%;padding:15px;" class="text-center">' . $type . '</td>';
                $tdTypeManager = '<td style="width:6%;padding:15px;" class="text-center" >' . $type_manager . '</td>';
                $tdNote = '<td style="width:28%;padding:15px;" class="text-left" >' . $note . '</td>';

                $bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdName . '
                    ' . $tdAverageOld . '
                    ' . $tdCountLate . '
                    ' . $tdCountHourRo . '
                    ' . $tdCountHourKp . '
                    ' . $tdType . '
                    ' . $tdTypeManager . '
                    ' . $tdNote . '
                </tr>';
            }
        }

        ob_start();
        stylePdf();


        echo '
            <br>
            <h1 class="text-center uppercase" style="color:#134490">' . lang('BẢNG XÉT BÌNH BẦU A,B,C') . '</h1>
            <h4 class="text-center"><i>Tháng ' . $month . '/' . $year . '</i></h4>
            ' . (!empty($data_departments) ? ('<h4 class="text-right uppercase">TỔ: ' . $data_departments->name . '</h4>') : '') . '
            <table class="" cellspacing="0" cellpadding="5" border="1" style="font-size: 12px">
                <thead>
                    <tr>
                        <th style="width:5%;padding:15px;" rowspan="2" class="bold text-center" >' . _l('tnh_numbers') . '</th>
                        <th style="width:27%;padding:15px;" rowspan="2" class="bold text-center">' . _l('Họ và tên') . '</th>
                        <th style="width:7%;padding:15px;" rowspan="2" class="bold text-center">' . _l('Bình bầu tháng trước') . '</th>
                        <th style="width:7%;padding:15px;" rowspan="2" class="bold text-center">' . _l('Số lần đi trễ') . '</th>
                        <th style="width:7%;padding:15px;" rowspan="2" class="bold text-center">' . _l('Số giờ nghỉ Ro') . '</th>
                        <th style="width:7%;padding:15px;" rowspan="2" class="bold text-center">' . _l('Số giờ không phép') . '</th>
                        <th style="width:12%;padding:15px;" colspan="2" class="bold text-center">' . _l('Bình bầu') . '</th>
                        <th style="width:28%;padding:15px;" rowspan="2" class="bold text-center">' . _l('Nhận xét') . '</th>
                    </tr>
                    <tr>
                        <th  class="bold text-center">' . _l('Tổ <br/>chấm') . '</th>
                        <th  class="bold text-center">' . _l('BGĐ<br/>duyệt') . '</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $bodyItems . '
                </tbody>
            </table>
            <br><br><br><table>
                <tbody>
                    <tr>
                        <td style="width:33%;" class="bold text-center" ></td>
                        <td style="width:33%" class="bold text-center" ></td>
                        <td style="width:34%" class="text-center" ><i>.......Ngày.......Tháng....... Năm 202......</i></td>
                    </tr>
                    <tr>
                        <td style="width:33%;" class="bold text-center" >NGƯỜI LẬP</td>
                        <td style="width:33%" class="bold text-center" >TỔ TRƯỞNG</td>
                        <td style="width:34%" class="bold text-center" >DUYỆT</td>
                    </tr>
                </tbody>
            </table>
        ';


        $content = ob_get_contents();
        ob_end_clean();

        $data['content'] = $content;
        $data['pageCustome'] = 'list_staff';
        $pdf = @print_pdf_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);

//
//        $pdf = @print_pdf_dt_L($data);
//        $type = 'I';
//        $pdf->Output(slug_it('123').'.pdf', $type);
    }

    // public function print_timekeeping(){
    //     $month = $this->input->get('month');
    //     $year = $this->input->get('year');
    //     $department = $this->input->get('department');
    //     $staff = $this->input->get('staff');
    //     ob_end_clean();
    //     $data = [];

    //     $listDate = getAllDateInMonth($month, $year, 'd');
    //     $widthHead = (79 / count($listDate)).'%';

    //     $timekeepingId = 0;
    //     $this->db->select('*');
    //     $this->db->from('tbl_timekeeping');
    //     $this->db->where('tbl_timekeeping.month', $month);
    //     $this->db->where('tbl_timekeeping.year', $year);
    //     $timekeeping = $this->db->get()->row_array();
    //     if (!empty($timekeeping)) {
    //         $timekeepingId = $timekeeping['id'];
    //     }

    //     $this->db->select('tblstaff.staffid as staffid,tblstaff.firstname as name,tbl_timekeeping_detail_hour.type as type');
    //     $this->db->from('tblstaff');
    //     $this->db->where('active', 1);
    //     $this->db->where('type_staff', 2);
    //     $this->db->where('tbl_timekeeping.month', $month);
    //     $this->db->where('tbl_timekeeping.year', $year);
    //     if(!empty($staff)){
    //         $this->db->where('tbl_timekeeping_detail.staff_id', $staff);
    //     }
    //     if(!empty($department)){
    //         $staffDepartments = "(
    //             SELECT
    //                 tblstaff_departments.staffid as staffid
    //             FROM tblstaff_departments
    //             WHERE tblstaff_departments.staffid  = tblstaff.staffid AND tblstaff_departments.departmentid = $department
    //         )";
    //         $this->db->where("exists ($staffDepartments)");
    //     }
    //     $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
    //     $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
    //     $this->db->join('tbl_timekeeping_detail_hour',
    //         'tbl_timekeeping_detail_hour.timekeeping_detail_id= tbl_timekeeping_detail.id', 'left');
    //     $this->db->group_by('tbl_timekeeping_detail.staff_id');
    //     $this->db->group_by('tbl_timekeeping_detail_hour.type');
    //     $personnel = $this->db->get()->result_array();


    //     $data['title'] = lang('print').' '.lang('BẢNG THỐNG KÊ CHI TIẾT GIỜ VÀO - GIỜ RA THÁNG ').$month.' năm '.$year;
    //     $data['type'] = 'P';
    //     $data['img'] = '';

    //     $bodyItems = '';
    //     $staff_id = '';
    //     $i = 1;
    //     $font_size = '8px';
    //     $font_size_new = '10px';
    //     if (!empty($personnel)) {
    //         foreach ($personnel as $key => $value) {
    //             $name = $value['name'];
    //             $personnel_id = $value['staffid'];
    //             if($key == 0){
    //                 $staff_id = $value['staffid'];
    //                 $tdNumber = '<td style="width:3%;font-size:'.$font_size_new.';" class="text-center" rowspan="2">'.($i).'</td>';
    //                 $tdName = '<td style="width:13%;font-size:'.$font_size_new.'" rowspan="2"><span style="font-weight:bold" >'.$name.'</span></td>';
    //             } else {
    //                 if($value['staffid'] != $staff_id){
    //                     $staff_id = $value['staffid'];
    //                     $i++;

    //                     $tdNumber = '<td style="width:3%;font-size:'.$font_size_new.'" class="text-center" rowspan="2">'.($i).'</td>';
    //                     $tdName = '<td style="width:13%;font-size:'.$font_size_new.'" rowspan="2"><span style="font-weight:bold" >'.$name.'</span></td>';
    //                 } else {
    //                     $tdNumber = '';
    //                     $tdName = '';
    //                 }
    //             }

    //             if ($value['type'] == 1) {
    //                 $tdHour = '<td style="width:5%;font-size:'.$font_size_new.'" class="text-center" >Giờ vào</td>';
    //             } elseif ($value['type'] == 2) {
    //                 $tdHour = '<td style="width:5%;font-size:'.$font_size_new.'" class="text-center">Giờ ra</td>';
    //             }
    //             $tdChild = '';
    //             foreach ($listDate as $k => $val) {
    //                 $date = $k;
    //                 $day = date("d", strtotime($k));
    //                 $type = '';
    //                 $check_sun = '';
    //                 $timeKeepingDetail_id = 0;
    //                 $hourIn = '';
    //                 $hourOut = '';
    //                 $textHour = '';
    //                 $imageIn = '';
    //                 $imageOut = '';
    //                 $date_word = '';
    //                 $image = '';

    //                 $textHourTct = '';
    //                 $hourInTct = '';
    //                 $hourOutTct = '';


    //                 $textHourTcd = '';
    //                 $hourInTcd = '';
    //                 $hourOutTcd = '';

    //                 $textHourHcnt = '';
    //                 $hourInHcnt  = '';
    //                 $hourOutHcnt  = '';

    //                 $type_check_in_Hcnt = '';
    //                 $type_check_out_tct = '';
    //                 $timekeeping_detail_id_out_tct = '';

    //                 $hourCheckInNew = '';
    //                 $hourCheckOutNew = '';

    //                 $this->db->select('tbl_timekeeping_detail.type as type,tbl_timekeeping_detail.id as id');
    //                 $this->db->from('tbl_timekeeping_detail');
    //                 $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
    //                 $this->db->where('tbl_timekeeping_detail.staff_id', $personnel_id);
    //                 $this->db->where('tbl_timekeeping_detail.date', $date);
    //                 $this->db->where('tbl_timekeeping_detail.day', $day);
    //                 $timeKeepingDetail = $this->db->get()->row_array();

    //                 $type = $timeKeepingDetail['type'];
    //                 $timeKeepingDetail_id = $timeKeepingDetail['id'];

    //                 if ($timeKeepingDetail_id != 0) {
    //                     $this->db->select('*');
    //                     $this->db->from('tbl_timekeeping_detail_hour');
    //                     $this->db->where('tbl_timekeeping_detail_hour.timekeeping_id', $timekeepingId);
    //                     $this->db->where('tbl_timekeeping_detail_hour.timekeeping_detail_id', $timeKeepingDetail_id);
    //                     $timeKeepingDetailHour = $this->db->get()->result_array();
    //                     // print_arrays($timeKeepingDetailHour);
    //                     if (!empty($timeKeepingDetailHour)) {
    //                         foreach ($timeKeepingDetailHour as $k => $v) {

    //                             if($v['type_check'] == 1){
    //                                 if ($v['type'] == 1) {
    //                                     $hourIn = $v['hour'];

    //                                 } elseif ($v['type'] == 2) {
    //                                     $hourOut = $v['hour'];
    //                                 }
    //                             } elseif($v['type_check'] == 2){
    //                                 if ($v['type'] == 1) {
    //                                     $hourInTct = $v['hour'];

    //                                 } elseif ($v['type'] == 2) {
    //                                     $hourOutTct = $v['hour'];
    //                                     $type_check_out_tct = $v['type_check'];
    //                                     $timekeeping_detail_id_out_tct = $v['timekeeping_detail_id'];
    //                                 }
    //                             } elseif($v['type_check'] == 3){
    //                                 if ($v['type'] == 1) {
    //                                     $hourInTcd = $v['hour'];
    //                                 } elseif ($v['type'] == 2) {
    //                                     $hourOutTcd = $v['hour'];
    //                                 }
    //                             } elseif($v['type_check'] == 4){
    //                                 if ($v['type'] == 1) {
    //                                     $hourInHcnt = $v['hour'];
    //                                     $type_check_in_Hcnt = $v['type_check'];
    //                                 } elseif ($v['type'] == 2) {
    //                                     $hourOutHcnt = $v['hour'];
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //                 if ($value['type'] == 1) {
    //                     $textHour = $hourIn;
    //                     $textHourTct = $hourInTct;
    //                     $textHourTcd = $hourInTcd;
    //                     $textHourHcnt = $hourInHcnt;

    //                     $type_check_text_Hcnt = $type_check_in_Hcnt;

    //                     if(!empty($textHour)){
    //                         $hourCheckInNew = $textHour;
    //                     } elseif(!empty($textHourTct)){
    //                         $hourCheckInNew = $textHourTct;
    //                     } elseif(!empty($textHourTcd)){
    //                         $hourCheckInNew = $textHourTcd;
    //                     } elseif(!empty($textHourHcnt)){
    //                         $hourCheckInNew = $textHourHcnt;
    //                     }

    //                     if(($type_check_text_Hcnt)){
    //                         $hourCheckInNew = '';
    //                     }

    //                 } elseif ($value['type'] == 2) {
    //                     $textHour = $hourOut;
    //                     $textHourTct = $hourOutTct;
    //                     $textHourTcd = $hourOutTcd;
    //                     $textHourHcnt = $hourOutHcnt;

    //                     $type_check_text_tct = $type_check_out_tct;
    //                     $timekeeping_detail_id_text_tct = $timekeeping_detail_id_out_tct;

    //                     if(!empty($textHour)){
    //                         $hourCheckOutNew = $textHour;
    //                     }
    //                     if(!empty($textHourTct)){
    //                         $hourCheckOutNew = $textHourTct;
    //                     }
    //                     if(!empty($textHourTcd)){
    //                         $hourCheckOutNew = $textHourTcd;
    //                     }
    //                     if(!empty($textHourHcnt)){
    //                         $hourCheckOutNew = $textHourHcnt;
    //                     }

    //                     $checkCounthourDetail = get_table_where('tbl_timekeeping_detail_count_hour',['timekeeping_detail_id_old'=>$timekeeping_detail_id_text_tct],'','row_array');
    //                     if(($type_check_text_tct && !empty($checkCounthourDetail))){
    //                         $hourCheckOutNew = '';
    //                     }
    //                 }

    //                 $htmlTextHour = '<a style="color:#008ece;text-decoration:none">'.$textHour.'</a>';
    //                 $htmlTextHourTct = '<a style="color:red;text-decoration:none">'.$textHourTct.'</a>';
    //                 $htmlTextHourTcd = '<a style="color:#278c15;text-decoration:none">'.$textHourTcd.'</a>';
    //                 $htmlTextHourHcnt = '<a style="color:#ea7922;text-decoration:none">'.$textHourHcnt.'</a>';

    //                 $htmlTextHourNew = '<a style="color:#008ece;text-decoration:none">'.$hourCheckInNew.'</a><a style="color:red;text-decoration:none">'.$hourCheckOutNew.'</a> ';
    //                 $tdChild .='<td class="text-center" style="width:'.$widthHead.';font-size:'.$font_size.'">
    //                     '.$htmlTextHourNew.'
    //                 </td>';
    //             }


    //             $bodyItems .= '<tr nobr="true">
    //                 '.$tdNumber.'
    //                 '.$tdName.'
    //                 '.$tdHour.'
    //                 '.$tdChild.'
    //             </tr>';
    //         }
    //     }

    //     ob_start();
    //     stylePdf();

    //     $tHead = '<tr>
    //         <th class="text-center bold" style="width:3%">'.lang('tnh_numbers').'</th>
    //         <th class="text-center bold" style="width:13%">'.lang('Nhân viên').'</th>
    //         <th class="text-center bold" style="width:5%">'.lang('Giờ').'</th>
    //     ';
    //     foreach ($listDate as $key => $value) {
    //         $tHead .= '<th class="text-center bold" style="width:'.$widthHead.'">'.$value.'</th>';
    //     }
    //     $tHead .= '</tr>';

    //     echo '
    //         <br>
    //         <h1 class="text-center uppercase" style="color:#134490">'.lang('BẢNG THỐNG KÊ CHI TIẾT GIỜ VÀO - GIỜ RA ').'<div style="font-style:italic;color:black;font-size:12px">Tháng '.$month.' năm '.$year.'</div></h1>
    //         <table class="table-items" cellspacing="0" cellpadding="5" border="1">
    //             <thead>
    //                 '.$tHead.'
    //             </thead>
    //             <tbody>
    //                 '.$bodyItems.'
    //             </tbody>
    //         </table>
    //     ';

    //     $content = ob_get_contents();
    //     ob_end_clean();

    //     $data['content'] = $content;
    //     $data['pageCustome'] = 'list_staff';
    //     $pdf = @print_pdf_dt_L($data);
    //     $type = 'I';
    //     $pdf->Output(slug_it('123').'.pdf', $type);
    // }

    public function print_timekeeping()
    {
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $department = $this->input->get('department');
        $staff = $this->input->get('staff');
        ob_end_clean();
        $data = [];

        $listDate = getAllDateInMonth($month, $year, 'd');
        $widthHead = (79 / count($listDate)) . '%';

        $timekeepingId = 0;
        $this->db->select('*');
        $this->db->from('tbl_timekeeping');
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        $timekeeping = $this->db->get()->row_array();
        if (!empty($timekeeping)) {
            $timekeepingId = $timekeeping['id'];
        }

        $this->db->select('tblstaff.staffid as staffid,tblstaff.firstname as name,tbl_timekeeping_detail_hour.type as type');
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        $this->db->where('type_staff', 2);
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        if (!empty($staff)) {
            $this->db->where('tbl_timekeeping_detail.staff_id', $staff);
        }
        if (!empty($department)) {
            $staffDepartments = "(
                SELECT
                    tblstaff_departments.staffid as staffid
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid  = tblstaff.staffid AND tblstaff_departments.departmentid = $department
            )";
            $this->db->where("exists ($staffDepartments)");
        }
        $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
        $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
        $this->db->join('tbl_timekeeping_detail_hour',
            'tbl_timekeeping_detail_hour.timekeeping_detail_id= tbl_timekeeping_detail.id', 'left');
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $this->db->group_by('tbl_timekeeping_detail_hour.type');
        $personnel = $this->db->get()->result_array();


        $data['title'] = lang('print') . ' ' . lang('BẢNG THỐNG KÊ CHI TIẾT GIỜ VÀO - GIỜ RA THÁNG ') . $month . ' năm ' . $year;
        $data['type'] = 'P';
        $data['img'] = '';

        $bodyItems = '';
        $staff_id = '';
        $i = 1;
        $font_size = '8px';
        $font_size_new = '10px';
        if (!empty($personnel)) {
            foreach ($personnel as $key => $value) {
                $name = $value['name'];
                $personnel_id = $value['staffid'];
                if ($key == 0) {
                    $staff_id = $value['staffid'];
                    $tdNumber = '<td style="width:3%;font-size:' . $font_size_new . ';" class="text-center" rowspan="2">' . ($i) . '</td>';
                    $tdName = '<td style="width:13%;font-size:' . $font_size_new . '" rowspan="2"><span style="font-weight:bold" >' . $name . '</span></td>';
                } else {
                    if ($value['staffid'] != $staff_id) {
                        $staff_id = $value['staffid'];
                        $i++;

                        $tdNumber = '<td style="width:3%;font-size:' . $font_size_new . '" class="text-center" rowspan="2">' . ($i) . '</td>';
                        $tdName = '<td style="width:13%;font-size:' . $font_size_new . '" rowspan="2"><span style="font-weight:bold" >' . $name . '</span></td>';
                    } else {
                        $tdNumber = '';
                        $tdName = '';
                    }
                }

                if ($value['type'] == 1) {
                    $tdHour = '<td style="width:5%;font-size:' . $font_size_new . '" class="text-center" >Giờ vào</td>';
                } elseif ($value['type'] == 2) {
                    $tdHour = '<td style="width:5%;font-size:' . $font_size_new . '" class="text-center">Giờ ra</td>';
                }

                $this->db->select('
                    tbl_timekeeping_detail.staff_id as staff_id,
                    tbl_timekeeping_detail.id as timekeeping_detail_id,
                    tbl_timekeeping_detail.day as day,
                    tbl_timekeeping_detail.date as date,
                    tbl_timekeeping_detail.count_hour as count_hour,
                    tbl_timekeeping_detail_hour.id as id,
                    tbl_timekeeping_detail_hour.hour_real as hour_real,
                    tbl_timekeeping_detail_hour.hour as hour,
                    tbl_timekeeping_detail_hour.type as type,
                    tbl_timekeeping_detail_hour.image as image,
                    tbl_timekeeping_detail_hour.timekeeping_detail_id_old as timekeeping_detail_id_old,
                    tbl_timekeeping_detail_hour.type_check as type_check
                ');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping_detail_hour',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_hour.timekeeping_detail_id', 'left');
                $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
                $this->db->where('tbl_timekeeping_detail.staff_id', $personnel_id);
                $this->db->where('tbl_timekeeping_detail_hour.type', $value['type']);
                $timeKeepingDetail = $this->db->get()->result_array();
                usort($timeKeepingDetail,
                    ch_make_cmp(['timekeeping_detail_id' => "asc", 'timekeeping_detail_id_old' => "asc"]));

                $tdChild = '';
                foreach ($listDate as $k => $val) {
                    $date = $k;
                    $day = date("d", strtotime($k));
                    $type = '';
                    $check_sun = '';
                    $timeKeepingDetail_id = 0;
                    $hourIn = '';
                    $hourOut = '';
                    $textHour = '';
                    $imageIn = '';
                    $imageOut = '';
                    $date_word = '';
                    $image = '';

                    $textHourTct = '';
                    $hourInTct = '';
                    $hourOutTct = '';


                    $textHourTcd = '';
                    $hourInTcd = '';
                    $hourOutTcd = '';

                    $textHourHcnt = '';
                    $hourInHcnt = '';
                    $hourOutHcnt = '';

                    $type_check_in = '';
                    $type_check_out = '';
                    $type_check_text = '';

                    $type_check_in_tct = '';
                    $type_check_out_tct = '';
                    $type_check_text_tct = '';

                    $type_check_text_tcd = '';
                    $type_check_in_tcd = '';
                    $type_check_out_tcd = '';

                    $type_check_out_Hcnt = '';
                    $type_check_in_Hcnt = '';
                    $timekeeping_detail_id_out_tct = '';
                    $type_check_text_Hcnt = '';

                    $hourCheckInNew = '';
                    $hourCheckOutNew = '';
                    $hourCheckOutNewVS1 = '';

                    $timekeeping_detail_hour_id_new = '';
                    $timekeeping_detail_id_new = '';

                    $timekeeping_detail_hour_id_out_new = '';
                    $timekeeping_detail_id_out_new = '';

                    $timekeeping_detail_hour_id_out_new_vs1 = '';
                    $timekeeping_detail_id_out_new_vs1 = '';


                    if (!empty($timeKeepingDetail)) {
                        foreach ($timeKeepingDetail as $kk => $v) {
                            if ($v['date'] == $date) {
                                if ($v['type_check'] == 1) {
                                    if ($v['type'] == 1) {
                                        $hourIn = $v['hour'];
                                        $type_check_in = $v['type_check'];

                                    } elseif ($v['type'] == 2) {
                                        $hourOut = $v['hour'];
                                        $type_check_out = $v['type_check'];
                                    }
                                } elseif ($v['type_check'] == 2) {
                                    if ($v['type'] == 1) {
                                        $hourInTct = $v['hour'];
                                        $type_check_in_tct = $v['type_check'];

                                    } elseif ($v['type'] == 2) {
                                        $hourOutTct = $v['hour'];
                                        $type_check_out_tct = $v['type_check'];
                                        $timekeeping_detail_id_out_tct = $v['timekeeping_detail_id'];
                                    }
                                } elseif ($v['type_check'] == 3) {
                                    if ($v['type'] == 1) {
                                        $hourInTcd = $v['hour'];
                                        $type_check_in_tcd = $v['type_check'];
                                    } elseif ($v['type'] == 2) {
                                        $hourOutTcd = $v['hour'];
                                        $type_check_out_tcd = $v['type_check'];
                                    }
                                } elseif ($v['type_check'] == 4) {
                                    if ($v['type'] == 1) {
                                        $hourInHcnt = $v['hour'];
                                        $type_check_in_Hcnt = $v['type_check'];
                                    } elseif ($v['type'] == 2) {
                                        $hourOutHcnt = $v['hour'];
                                        $type_check_out_Hcnt = $v['type_check'];
                                    }
                                }
                            }
                        }
                    }

                    if ($value['type'] == 1) {
                        $textHour = $hourIn;
                        $textHourTct = $hourInTct;
                        $textHourTcd = $hourInTcd;
                        $textHourHcnt = $hourInHcnt;

                        $type_check_text = $type_check_in;
                        $type_check_text_tct = $type_check_in_tct;
                        $type_check_text_tcd = $type_check_in_tcd;
                        $type_check_text_Hcnt = $type_check_in_Hcnt;

                        if (!empty($type_check_text)) {
                            $type_check = $type_check_text;
                        } elseif (!empty($type_check_text_tct)) {
                            $type_check = $type_check_text_tct;
                        } elseif (!empty($type_check_text_tcd)) {
                            $type_check = $type_check_text_tcd;
                        } elseif (!empty($type_check_text_Hcnt)) {
                            $type_check = $type_check_text_Hcnt;
                        }

                        if (!empty($textHour)) {
                            $hourCheckInNew = $textHour;
                        } elseif (!empty($textHourTct)) {
                            $hourCheckInNew = $textHourTct;
                        } elseif (!empty($textHourTcd)) {
                            $hourCheckInNew = $textHourTcd;
                        } elseif (!empty($textHourHcnt)) {
                            $hourCheckInNew = $textHourHcnt;
                        }

                        if (($type_check_text_Hcnt) && ($textHour == '' && $textHourTct == '')) {
                            $hourCheckInNew = '';
                        }

                    } elseif ($value['type'] == 2) {
                        $textHour = $hourOut;
                        $textHourTct = $hourOutTct;
                        $textHourTcd = $hourOutTcd;
                        $textHourHcnt = $hourOutHcnt;

                        $type_check_text = $type_check_out;
                        $type_check_text_tct = $type_check_out_tct;
                        $type_check_text_tcd = $type_check_out_tcd;
                        $type_check_text_Hcnt = $type_check_out_Hcnt;
                        $timekeeping_detail_id_text_tct = $timekeeping_detail_id_out_tct;

                        if (!empty($type_check_text)) {
                            $type_check = $type_check_text;
                        } elseif (!empty($type_check_text_tct)) {
                            $type_check = $type_check_text_tct;
                        } elseif (!empty($type_check_text_tcd)) {
                            $type_check = $type_check_text_tcd;
                        } elseif (!empty($type_check_text_Hcnt)) {
                            $type_check = $type_check_text_Hcnt;
                        }

                        $checkCounthourDetail = get_table_where('tbl_timekeeping_detail_count_hour',
                            ['timekeeping_detail_id_old' => $timekeeping_detail_id_text_tct], '', 'row_array');
                        if (!empty($textHour)) {
                            $hourCheckOutNew = $textHour;
                            $hourCheckOutNewVS1 = $textHour;
                        }
                        if (!empty($textHourTct)) {
                            $hourCheckOutNew = $textHourTct;
                            $hourCheckOutNewVS1 = $textHourTct;
                        }
                        if (!empty($textHourTcd)) {
                            $hourCheckOutNew = $textHourTcd;
                            if ($type_check == 3) {
                                $hourCheckOutNewVS1 = $textHourTcd;
                            }
                        }
                        if (!empty($textHourHcnt)) {
                            $hourCheckOutNew = $textHourHcnt;
                            if ($type_check == 4) {
                                $hourCheckOutNewVS1 = $textHourHcnt;
                            }
                        }

                        if (($type_check_text_tct && $type_check_text_tcd == '' && !empty($checkCounthourDetail))) {
                            $hourCheckOutNew = '';
                        }
                        if (($type_check_text_tct && !empty($checkCounthourDetail))) {
                            $hourCheckOutNewVS1 = '';
                        }
                        if ($hourCheckOutNew == $hourCheckOutNewVS1) {
                            $hourCheckOutNew = '';
                        }
                    }

                    $htmlTextHour = '<a style="color:#008ece;text-decoration:none">' . $textHour . '</a>';
                    $htmlTextHourTct = '<a style="color:red;text-decoration:none">' . $textHourTct . '</a>';
                    $htmlTextHourTcd = '<a style="color:#278c15;text-decoration:none">' . $textHourTcd . '</a>';
                    $htmlTextHourHcnt = '<a style="color:#ea7922;text-decoration:none">' . $textHourHcnt . '</a>';

                    $htmlTextHourNew = '<a style="color:#008ece;text-decoration:none">' . $hourCheckInNew . '</a><a style="color:red;text-decoration:none">' . $hourCheckOutNewVS1 . '</a> ';
                    $htmlTextHourNew .= '<a style="color:red;text-decoration:none">' . $hourCheckOutNew . '</a> ';
                    $tdChild .= '<td class="text-center" style="width:' . $widthHead . ';font-size:' . $font_size . '">
                        ' . $htmlTextHourNew . '
                    </td>';
                }


                $bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdName . '
                    ' . $tdHour . '
                    ' . $tdChild . '
                </tr>';
            }
        }

        ob_start();
        stylePdf();

        $tHead = '<tr>
            <th class="text-center bold" style="width:3%">' . lang('tnh_numbers') . '</th>
            <th class="text-center bold" style="width:13%">' . lang('Nhân viên') . '</th>
            <th class="text-center bold" style="width:5%">' . lang('Giờ') . '</th>
        ';
        foreach ($listDate as $key => $value) {
            $tHead .= '<th class="text-center bold" style="width:' . $widthHead . '">' . $value . '</th>';
        }
        $tHead .= '</tr>';
        $to_html = '';
        if (!empty($department)) {
            $this->db->where('departmentid', $department);
            $name_department = $this->db->get('tbldepartments')->row('name');
            if (!empty($name_department)) {
                $to_html = '<h3><b>Tổ: </b> ' . $name_department . '</h3>';
            }
        }
        echo '
            <br>
            <h1 class="text-center uppercase" style="color:#134490">' . lang('BẢNG THỐNG KÊ CHI TIẾT GIỜ VÀO - GIỜ RA ') . '<div style="font-style:italic;color:black;font-size:12px">Tháng ' . $month . ' năm ' . $year . '</div></h1>
            ' . $to_html . '
            <table class="table-items" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    ' . $tHead . '
                </thead>
                <tbody>
                    ' . $bodyItems . '
                </tbody>
            </table>
        ';

        $content = ob_get_contents();
        ob_end_clean();

        $data['content'] = $content;
        $data['pageCustome'] = 'list_staff';
        $pdf = @print_pdf_dt_L($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function print_dashboard_timekeeping()
    {
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $department = $this->input->get('department');
        $staff = $this->input->get('staff');
        ob_end_clean();
        $data = [];

        $listDate = getAllDateInMonth($month, $year, 'd');
        $widthHead = (84 / count($listDate)) . '%';

        $timekeepingId = 0;
        $this->db->select('*');
        $this->db->from('tbl_timekeeping');
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        $timekeeping = $this->db->get()->row_array();
        if (!empty($timekeeping)) {
            $timekeepingId = $timekeeping['id'];
        }
        if ($month == 12) {
            $monthNew = 1;
            $yearNew = $year + 1;
        } else {
            $monthNew = $month + 1;
            $yearNew = $year;
        }
        $timekeepingIdNew = 0;
        $this->db->select('*');
        $this->db->from('tbl_timekeeping');
        $this->db->where('tbl_timekeeping.month', $monthNew);
        $this->db->where('tbl_timekeeping.year', $yearNew);
        $timekeepingNew = $this->db->get()->row_array();
        if (!empty($timekeepingNew)) {
            $timekeepingIdNew = $timekeepingNew['id'];
        }

        $this->db->select('tblstaff.staffid as staffid,tblstaff.firstname as name');
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        $this->db->where('type_staff', 2);
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        if (!empty($staff)) {
            $this->db->where('tbl_timekeeping_detail.staff_id', $staff);
        }
        if (!empty($department)) {
            $staffDepartments = "(
                SELECT
                    tblstaff_departments.staffid as staffid
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid  = tblstaff.staffid AND tblstaff_departments.departmentid = $department
            )";
            $this->db->where("exists ($staffDepartments)");
        }
        $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
        $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $personnel = $this->db->get()->result_array();


        $data['title'] = lang('print') . ' ' . lang('BẢNG THỐNG KÊ GIỜ CÔNG HÀNG NGÀY THÁNG ') . $month . ' năm ' . $year;
        $data['type'] = 'P';
        $data['img'] = '';

        $bodyItems = '';
        $font_size = '8px';
        $font_size_new = '10px';
        if (!empty($personnel)) {
            foreach ($personnel as $key => $value) {
                $personnel_id = $value['staffid'];
                $name = $value['name'];

                $tdNumber = '<td style="width:3%;font-size:' . $font_size_new . ';" class="text-center">' . (++$key) . '</td>';
                $tdName = '<td style="width:13%;font-size:' . $font_size_new . '"><span style="font-weight:bold" >' . $name . '</span></td>';
                $tdChild = '';
                $this->db->select('
                    tbl_timekeeping_detail.staff_id as staff_id,
                    tbl_timekeeping_detail.id as id,
                    tbl_timekeeping_detail.day as day,
                    tbl_timekeeping_detail.date as date,
                    tbl_timekeeping_detail.type as type,
                    tbl_timekeeping_detail.count_hour as count_hour
                ');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
                $this->db->where('tbl_timekeeping_detail.staff_id', $personnel_id);
                $timeKeepingDetail = $this->db->get()->result_array();

                $tbTamp = "(
                    SELECT 
                        tbl_timekeeping_detail_count_hour.timekeeping_detail_id_old as timekeeping_detail_id_old,
                        tbl_timekeeping_detail_count_hour.timekeeping_detail_id as timekeeping_detail_id,
                        SUM(tbl_timekeeping_detail_count_hour.count_hour) as count_hour
                    FROM tbl_timekeeping_detail_count_hour
                    GROUP BY timekeeping_detail_id,timekeeping_detail_id_old
                ) tb_tam";

                $this->db->select('
                    tbl_timekeeping_detail.staff_id as staff_id,
                    tbl_timekeeping_detail.id as id,
                    tbl_timekeeping_detail.day as day,
                    tbl_timekeeping_detail.date as date,
                    tbl_timekeeping_detail.type as type,
                    tb_tam.timekeeping_detail_id_old as timekeeping_detail_id_old,
                    COALESCE(tb_tam.count_hour,0) as count_hour
                ');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join("$tbTamp",
                    "tb_tam.timekeeping_detail_id = tbl_timekeeping_detail.id AND tb_tam.timekeeping_detail_id_old != 0",
                    "left");
                $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingIdNew);
                $this->db->where('tbl_timekeeping_detail.staff_id', $personnel_id);
                $this->db->limit(1);
                $timeKeepingDetailNewVs1 = $this->db->get()->result_array();

                $this->db->select('
                    tbl_timekeeping_detail.staff_id as staff_id,
                    tbl_timekeeping_detail.id as id,
                    tbl_timekeeping_detail.day as day,
                    tbl_timekeeping_detail.date as date,
                    tbl_timekeeping_detail.type as type,
                    tb_tam.timekeeping_detail_id_old as timekeeping_detail_id_old,
                    COALESCE(tb_tam.count_hour,0) as count_hour
                ');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join("$tbTamp", "tb_tam.timekeeping_detail_id = tbl_timekeeping_detail.id", "left");
                $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
                $this->db->where('tbl_timekeeping_detail.staff_id', $personnel_id);
                $this->db->where("NOT EXISTS (
                    SELECT tb1.id
                    FROM tbl_timekeeping_detail tb1
                    WHERE tbl_timekeeping_detail.id = tb1.id
                    AND DATE_FORMAT(tbl_timekeeping_detail.date, \"%d\") = 01 AND tb_tam.timekeeping_detail_id_old != 0
                )");
                $timeKeepingDetailNew = $this->db->get()->result_array();
                if (!empty($timeKeepingDetailNewVs1)) {
                    $timeKeepingDetailNew = array_merge($timeKeepingDetailNew, $timeKeepingDetailNewVs1);
                }
                $arr_info = [];
                if (!empty($timeKeepingDetailNew)) {
                    foreach ($timeKeepingDetailNew as $k => $v) {
                        if (!empty($arr_info[$v['timekeeping_detail_id_old']])) {
                            $arr_info[$v['timekeeping_detail_id_old']]['count_hour'] = $arr_info[$v['timekeeping_detail_id_old']]['count_hour'] + $v['count_hour'];
                        } else {
                            $arr_info[$v['id']] = $v;
                        }
                    }
                }

                foreach ($listDate as $k => $val) {
                    $date = $k;
                    $day = date("d", strtotime($k));
                    $count_hour = '';
                    $type = 'X';
                    $day_check = explode("/", $val);
                    $style_count_hour = '';

                    $id_timekeeping_detail_old = 0;
                    $id_timekeeping_detail_new = 0;
                    $count_hour_new = 0;
                    $count_hour_check = 0;
                    $check = false;
                    $day_old = '';
                    $day_new = '';

                    $id_timekeeping_detail = 0;

                    if (!empty($arr_info)) {
                        foreach ($arr_info as $kk => $v) {
                            if ($v['date'] == $date) {
                                if ($day_check['0'] == $v['day'] && $v['staff_id'] == $value['staffid']) {
                                    $count_hour = $v['count_hour'];
                                    $count_hour_check = $v['count_hour'];
                                    $id_timekeeping_detail_old = $v['id'];
                                    $day_old = $v['day'];
                                    $type = $v['type'];
                                    $id_timekeeping_detail = $v['id'];
                                }
                            }
                        }
                    }
//                    $timeKeepingDetailCountHour = get_table_where('tbl_timekeeping_detail_count_hour',['timekeeping_detail_id_old'=>$id_timekeeping_detail_old],'','row_array');
//                    if(!empty($timeKeepingDetailCountHour)){
//                        $id_timekeeping_detail_new = $timeKeepingDetailCountHour['timekeeping_detail_id'];
//                    } else {
//                        $timeKeepingDetailCountHourNew = get_table_where('tbl_timekeeping_detail_count_hour',['timekeeping_detail_id'=>$id_timekeeping_detail_old],'','row_array');
//                        if(!empty($timeKeepingDetailCountHourNew)){
//                            if(!empty($timeKeepingDetailCountHourNew['timekeeping_detail_id_old'])){
//                                $count_hour = 0;
//                            }
//                        }
//                    }
//                    $timeKeepingDetailNew = get_table_where('tbl_timekeeping_detail',['id'=>$id_timekeeping_detail_new],'','row_array');
//                    if(!empty($timeKeepingDetailNew)){
//                        $count_hour_new = str_replace(':','.',$timeKeepingDetailNew['count_hour']);
//                        $day_new = $timeKeepingDetailNew['day'];
//                        $check = true;
//                    }
                    $count_hour_new = 0;
                    $count_hour = str_replace(':', '.', $count_hour);
                    $count_hour = (float)$count_hour + (float)$count_hour_new;
                    if ($type == 'X') {
                        $type = '';
                    }
                    if ($count_hour == 0) {
                        $count_hour = '';
                    }
                    $htmlEditNote = 'display: none;';
                    if ($type != '' && ($type == 'R' || $type == 'O_K_BHXH' || $type == 'Ro_HT_50')) {
                        $htmlEditNote = 'display: block;';
                    }

                    $style_count_hour = '<div style="font-weight:bold">' . $count_hour . '</div>' . $type;


                    $tdChild .= '<td style="width: ' . $widthHead . ';font-size:' . $font_size . '" class="text-center">
                            ' . $style_count_hour . '
                    </td>';
                }
                $bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdName . '
                    ' . $tdChild . '
                </tr>';
            }
        }

        ob_start();
        stylePdf();

        $tHead = '<tr>
            <th class="text-center bold" style="width:3%">' . lang('tnh_numbers') . '</th>
            <th class="text-center bold" style="width:13%">' . lang('Họ Và Tên') . '</th>
        ';
        foreach ($listDate as $key => $value) {
            $tHead .= '<th class="text-center bold" style="width:' . $widthHead . '">' . $value . '</th>';
        }
        $tHead .= '</tr>';
        $to_html = '';
        if (!empty($department)) {
            $this->db->where('departmentid', $department);
            $name_department = $this->db->get('tbldepartments')->row('name');
            if (!empty($name_department)) {
                $to_html = '<h3><b>Tổ: </b> ' . $name_department . '</h3>';
            }
        }
        echo '<br><h1 class="text-center uppercase" style="color:#134490">' . lang('BẢNG THỐNG KÊ GIỜ CÔNG HÀNG NGÀY ') . '<div style="font-style:italic;color:black;font-size:12px">Tháng ' . $month . ' năm ' . $year . '</div></h1>
            ' . $to_html . '
            <table class="table-items" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    ' . $tHead . '
                </thead>
                <tbody>
                    ' . $bodyItems . '
                </tbody>
            </table>
        ';

        $content = ob_get_contents();
        // print_arrays($content);
        ob_end_clean();

        $data['content'] = $content;
        $data['pageCustome'] = 'list_staff';
        $pdf = @print_pdf_dt_L($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function print_synthetic_timekeeping()
    {
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $department = $this->input->get('department');
        $staff = $this->input->get('staff');
        ob_end_clean();
        $data = [];


        $data['title'] = lang('print') . ' ' . lang('TỔNG HỢP GIỜ CÔNG NHÂN VIÊN THÁNG ') . $month . ' năm ' . $year;
        $data['type'] = 'P';
        $data['img'] = '';

        $bodyItems = '';
        $font_size = '8px';
        $font_size_new = '10px';
        // if (!empty($personnel)) {
        //     foreach ($personnel as $key => $value) {
        //         $personnel_id = $value['staffid'];
        //         $name = $value['name'];

        //         $tdNumber = '<td style="width:3%;font-size:'.$font_size_new.';" class="text-center">'.(++$key).'</td>';
        //         $tdName = '<td style="width:13%;font-size:'.$font_size_new.'"><span style="font-weight:bold" >'.$name.'</span></td>';

        //         $bodyItems .= '<tr nobr="true">
        //             '.$tdNumber.'
        //             '.$tdName.'
        //         </tr>';
        //     }
        // }

        ob_start();
        stylePdf();

        $tHead = '<tr>
            <th rowspan="4" class="text-center bold" style="width: 3%;">' . lang('tnh_numbers') . '</th>
            <th rowspan="4" class="text-center bold" style="width: 13%;">' . lang('Họ Và Tên') . '</th>
            <th rowspan="4" class="text-center bold" style="width: 6%;">' . lang('Đơn vị') . '</th>
            <th colspan="7" class="text-center bold" style="width: 40%;">' . lang('Giờ công(Giờ)') . '</th>
            <th rowspan="4" class="text-center bold" style="min-width: 100px;">' . lang('Số phần cơm') . '</th>
            <th rowspan="4" class="text-center bold" style="min-width: 100px;">' . lang('Số lần đi trễ') . '</th>
            <th colspan="9" class="text-center bold" style="min-width: 150px;">' . lang('Số giờ nghỉ') . '</th>
            <th rowspan="4" class="text-center bold" style="min-width: 150px;">' . lang('Tổng giờ công chính được tính lương để áp vào cột (5)') . '</th>
            <th rowspan="4" class="text-center bold" style="min-width: 150px;">' . lang('Tổng giờ công được tính lương đã bao gồm tăng ca, phép năm') . '</th>
        ';
        $tHead .= '</tr>';
        $tHead .= '<tr>
            <th rowspan="3" class="text-center bold" style="min-width: 50px;">' . lang('50%') . '</th>
            <th rowspan="3" class="text-center bold" style="min-width: 50px;">' . lang('100%') . '</th>
            <th rowspan="3" class="text-center bold" style="min-width: 50px;">' . lang('150%') . '</th>
            <th rowspan="3" class="text-center bold" style="min-width: 50px;">' . lang('200%') . '</th>
            <th rowspan="3" class="text-center bold" style="min-width: 50px;">' . lang('300%') . '</th>
            <th rowspan="3" class="text-center bold" style="min-width: 50px;color:red">' . lang('Tổng cộng') . '</th>
            <th rowspan="3" class="text-center bold" style="min-width: 50px;">' . lang('Phép năm') . '</th>
            <th rowspan="3" class="text-center bold" style="min-width: 50px;">' . lang('R') . '</th>
            <th colspan="2" class="text-center bold" style="min-width: 80px;">' . lang('O') . '</th>
            <th colspan="5" class="text-center bold" style="min-width: 80px;">' . lang('Ro Trong đó ') . '</th>
            <th rowspan="3" class="text-center bold" style="min-width: 80px;">' . lang('Không phép') . '</th>
        </tr>';
        $tHead .= '<tr>
            <th rowspan="2" class="text-center bold" style="min-width: 80px;">' . lang('Có giấy nghỉ hưởng BHXH') . '</th>
            <th rowspan="2" class="text-center bold" style="min-width: 80px;">' . lang('Không có giấy nghỉ hưởng BHXH') . '</th>
            <th rowspan="2" class="text-center bold" style="min-width: 80px;">' . lang('Tổng giờ nghỉ') . '</th>
            <th class="text-center bold" style="min-width: 80px;">' . lang('Được hỗ trợ theo quy định') . '</th>
            <th colspan="2" class="text-center bold" style="min-width: 80px;">' . lang('Theo quyết định bổ sung') . '</th>
            <th rowspan="2" class="text-center bold" style="min-width: 80px;">' . lang('Không hỗ trợ') . '</th>
        </tr>';
        $tHead .= '<tr>
            <th class="text-center bold" style="min-width: 80px;">' . lang('50%') . '</th>
            <th class="text-center bold" style="min-width: 80px;">' . lang('50%') . '</th>
            <th class="text-center bold" style="min-width: 80px;">' . lang('100%') . '</th>
        </tr>';

        echo '<br><h1 class="text-center uppercase" style="color:#134490">' . lang('TỔNG HỢP GIỜ CÔNG NHÂN VIÊN ') . '<div style="font-style:italic;color:black;font-size:12px">Tháng ' . $month . ' năm ' . $year . '</div></h1>
            <table class="table-items" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    ' . $tHead . '
                </thead>
                <tbody>
                    ' . $bodyItems . '
                </tbody>
            </table>
        ';


        $content = ob_get_contents();
        // print_arrays($content);
        ob_end_clean();

        $data['content'] = $content;
        $data['pageCustome'] = 'list_staff';
        $pdf = @print_pdf_dt_L($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    function exportExcelSaleListing()
    {
        $c_excel = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'AA',
            'AB',
            'AC',
            'AD',
            'AE',
            'AF',
            'AG',
            'AH',
            'AI',
            'AJ',
            'AK',
            'AL',
            'AM',
            'AN',
            'AO',
            'AP',
            'AQ',
            'AR',
            'AS',
            'AT',
            'AU',
            'AV',
            'AW',
            'AX',
            'AY',
            'AZ',
            'BA',
            'BB',
            'BC',
            'BD',
            'BE',
            'BF',
            'BG',
            'BH',
            'BI',
            'BJ',
            'BK',
            'BL',
            'BM',
            'BN',
            'BO',
            'BP',
            'BQ',
            'BR',
            'BS',
            'BT',
            'BU',
            'BV',
            'BW',
            'BX',
            'BY',
            'BZ',
            'CA',
            'CB',
            'CC',
            'CD',
            'CE',
            'CF',
            'CG',
            'CH',
            'CI',
            'CJ',
            'CK',
            'CL',
            'CM',
            'CN',
            'CO',
            'CP',
            'CQ',
            'CR',
            'CS',
            'CT',
            'CU',
            'CV',
            'CW',
            'CX',
            'CY',
            'CZ',
            'DA',
            'DB',
            'DC',
            'DD',
            'DE',
            'DF',
            'DG',
            'DH',
            'DI',
            'DJ',
            'DK',
            'DL',
            'DM',
            'DN',
            'DO',
            'DP',
            'DQ',
            'DR',
            'DS',
            'DT',
            'DU',
            'DV',
            'DW',
            'DX',
            'DY',
            'DZ'
        ];
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        // print_arrays($this->input->post())
        $staff = $this->input->get('staff');
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $department = $this->input->get('department');

        if (!empty($department)) {
            $this->db->where('departmentid', $department);
            $dtDepartment = $this->db->get('tbldepartments')->row_array();
            $name_department = $dtDepartment['name'];
            $code_department = $dtDepartment['code'];
        }
        $staff = $this->input->get('staff');
        ob_end_clean();
        $data = [];

        $listDate = getAllDateInMonth($month, $year, 'd');
        $widthHead = (79 / count($listDate)) . '%';

        $timekeepingId = 0;
        $this->db->select('*');
        $this->db->from('tbl_timekeeping');
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        $timekeeping = $this->db->get()->row_array();
        if (!empty($timekeeping)) {
            $timekeepingId = $timekeeping['id'];
        }

        $this->db->select('tblstaff.staffid as staffid,tblstaff.code as code, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name,tblroles.name as name_role,tbl_timekeeping_detail_hour.type as type');
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        if (!empty($staff)) {
            $this->db->where('tbl_timekeeping_detail.staff_id', $staff);
        }
        if (!empty($department)) {
            $staffDepartments = "(
                SELECT
                    tblstaff_departments.staffid as staffid
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid  = tblstaff.staffid AND tblstaff_departments.departmentid = $department
            )";
            $this->db->where("exists ($staffDepartments)");
        }
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
        $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
        $this->db->join('tbl_timekeeping_detail_hour',
            'tbl_timekeeping_detail_hour.timekeeping_detail_id= tbl_timekeeping_detail.id', 'left');
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $this->db->group_by('tbl_timekeeping_detail_hour.type');
        $personnel = $this->db->get()->result_array();

        $styleTh = [
            'font' => array(
                'bold' => true,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )

        ];
        $styleTd = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];
        $styleTd_center = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];
        $styleTd_left = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];


        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);

        $decimals_money = get_option('decimals_money');
        $decimals_number = get_option('decimals_number');
        $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
        $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s", 0) : '');

        $company = get_option('invoice_company_name');
        $address = get_option('invoice_company_address');
        $phonenumber = get_option('invoice_company_phonenumber');
        $styleNone = [
            'font' => array(
                'size' => 13,
                'name' => 'Times New Roman'
            )
        ];

        $company_logo = get_option('company_logo');
        if (file_exists('uploads/company/' . $company_logo)) {
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath('uploads/company/' . $company_logo);
            $objDrawing->setCoordinates('A1');
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            $objPHPExcel->getActiveSheet()->getStyle("A1");
            $objDrawing->setOffsetX(5);
            $objDrawing->setOffsetY(5);
            $objDrawing->setResizeProportional(false);

            $objDrawing->setWidth(55);
            $objDrawing->setHeight(55);

        }

        $objPHPExcel->getActiveSheet()->mergeCells('B1:I1');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', $company)->getStyle('B1:I1')->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 14,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);

        $objPHPExcel->getActiveSheet()->mergeCells('B2:I2');
        $objPHPExcel->getActiveSheet()->setCellValue('B2', $address)->getStyle('B2:I2')->applyFromArray($styleNone);

        $objPHPExcel->getActiveSheet()->mergeCells('B3:I3');
        $objPHPExcel->getActiveSheet()->setCellValue('B3',
            'SĐT: ' . $phonenumber)->getStyle('B3:I3')->applyFromArray($styleNone);

        $objPHPExcel->getActiveSheet()->mergeCells('A5:AH5');
        $objPHPExcel->getActiveSheet()->setCellValue('A5',
            'BẢNG THỐNG KÊ CHI TIẾT GIỜ VÀO - GIỜ RA')->getStyle('A5:AH5')->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 25,
                'name' => 'Times New Roman',
                'color' => array('rgb' => 'ff0202'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);

        $objPHPExcel->getActiveSheet()->mergeCells('A6:AH6');
        $objPHPExcel->getActiveSheet()->setCellValue('A6',
            ('THÁNG ' . $month . ' NĂM ' . $year))->getStyle("A6:AH6")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 16,
                'name' => 'Times New Roman',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);
        $rowBegin = 8;
        if (!empty($name_department)) {
            $stt = 0;
            $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt + 5] . $rowBegin);
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                'Phòng ban: ' . $name_department.' ('.$code_department.')')->getStyle($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt + 5] . $rowBegin)->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 14,
                    'name' => 'Times New Roman',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                )
            ]);
            $rowBegin++;
        }
        $sttC = 5;
        $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin",
            'STT')->getStyle("A$rowBegin")->applyFromArray($styleTh);

        $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin",
            'MSNV')->getStyle("B$rowBegin")->applyFromArray($styleTh);

        $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin",
            'Họ Và Tên')->getStyle("C$rowBegin")->applyFromArray($styleTh);

        $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin",
            'Chức vụ')->getStyle("D$rowBegin")->applyFromArray($styleTh);

        $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin",
            'Giờ')->getStyle("E$rowBegin")->applyFromArray($styleTh);

        foreach ($listDate as $key => $value) {
            $date = $key;
            $day = date("d", strtotime($date));
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$sttC]$rowBegin",
                $day)->getStyle("$c_excel[$sttC]$rowBegin")->applyFromArray($styleTh);
            $sttC++;
        }

        $rowBegin++;
        if (!empty($personnel)) {
            $iSTT = 1;
            foreach ($personnel as $key => $value) {
                $sttC = 5;
                $personnel_id = $value['staffid'];
                if (!empty($personnel[$key + 1]) && $personnel[$key + 1]['staffid'] == $value['staffid']) {
                    $objPHPExcel->getActiveSheet()->mergeCells('A' . $rowBegin . ':A' . ($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin",
                        $iSTT)->getStyle('A' . $rowBegin . ':A' . ($rowBegin + 1))->applyFromArray($styleTd_center);
                    $objPHPExcel->getActiveSheet()->mergeCells('B' . $rowBegin . ':B' . ($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$rowBegin",
                        $value['code'],PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('B' . $rowBegin . ':B' . ($rowBegin + 1))->applyFromArray($styleTd_left);
                    $objPHPExcel->getActiveSheet()->mergeCells('C' . $rowBegin . ':C' . ($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin",
                        $value['name'])->getStyle('C' . $rowBegin . ':C' . ($rowBegin + 1))->applyFromArray($styleTd_left);
                    $objPHPExcel->getActiveSheet()->mergeCells('D' . $rowBegin . ':D' . ($rowBegin + 1));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin",
                        $value['name_role'])->getStyle('D' . $rowBegin . ':D' . ($rowBegin + 1))->applyFromArray($styleTd_left);
                    $iSTT++;
                } elseif ($personnel[$key - 1]['staffid'] != $value['staffid']) {
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin",
                        $iSTT)->getStyle('A' . $rowBegin)->applyFromArray($styleTd_center);
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin",
                        $value['code'])->getStyle('B' . $rowBegin)->applyFromArray($styleTd_left);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin",
                        $value['name'])->getStyle('C' . $rowBegin)->applyFromArray($styleTd_left);
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin",
                        $value['name_role'])->getStyle('D' . $rowBegin)->applyFromArray($styleTd_left);
                    $iSTT++;
                }

                $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin",
                    ($value['type'] == 1 ? "Giờ vào" : 'Giờ ra'))->getStyle('E' . $rowBegin)->applyFromArray($styleTd_center);

                $this->db->select('
                    tbl_timekeeping_detail.staff_id as staff_id,
                    tbl_timekeeping_detail.id as timekeeping_detail_id,
                    tbl_timekeeping_detail.day as day,
                    tbl_timekeeping_detail.date as date,
                    tbl_timekeeping_detail.count_hour as count_hour,
                    tbl_timekeeping_detail_hour.id as id,
                    tbl_timekeeping_detail_hour.hour_real as hour_real,
                    tbl_timekeeping_detail_hour.hour as hour,
                    tbl_timekeeping_detail_hour.type as type,
                    tbl_timekeeping_detail_hour.image as image,
                    tbl_timekeeping_detail_hour.timekeeping_detail_id_old as timekeeping_detail_id_old,
                    tbl_timekeeping_detail_hour.type_check as type_check
                ');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping_detail_hour',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_hour.timekeeping_detail_id', 'left');
                $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
                $this->db->where('tbl_timekeeping_detail.staff_id', $personnel_id);
                $this->db->where('tbl_timekeeping_detail_hour.type', $value['type']);
                $timeKeepingDetail = $this->db->get()->result_array();
                usort($timeKeepingDetail,
                    ch_make_cmp(['timekeeping_detail_id' => "asc", 'timekeeping_detail_id_old' => "asc"]));

                foreach ($listDate as $kk => $val) {
                    $date = $kk;
                    $day = date("d", strtotime($kk));
                    $hourIn = '';
                    $hourOut = '';
                    $textHour = '';
                    $imageIn = '';
                    $imageOut = '';
                    $date_word = '';
                    $image = '';

                    $timekeeping_detail_hour_id_in = '';
                    $timekeeping_detail_id_in = '';
                    $type_hour_in = '';
                    $type_check_in = '';

                    $timekeeping_detail_hour_id_out = '';
                    $timekeeping_detail_id_out = '';
                    $type_hour_out = '';
                    $type_check_out = '';

                    $timekeeping_detail_hour_id_text = '';
                    $timekeeping_detail_id_text = '';
                    $type_hour_text = '';
                    $type_check_text = '';

                    $id_timekeeping_detail_hour = '';
                    $type_hour = '';
                    $type_check = '';

                    $hourCheckInNew = '';
                    $hourCheckOutNew = '';

                    $timekeeping_detail_hour_id_new = '';
                    $timekeeping_detail_id_new = '';

                    $timekeeping_detail_hour_id_out_new = '';
                    $timekeeping_detail_id_out_new = '';

                    if (!empty($timeKeepingDetail)) {
                        foreach ($timeKeepingDetail as $kk => $v) {
                            if ($v['date'] == $date) {
                                if ($v['type_check'] == 1) {
                                    if ($v['type'] == 1) {
                                        $hourIn = $v['hour'];
                                        $imageIn = $v['image'];

                                        $timekeeping_detail_hour_id_in = $v['id'];
                                        $timekeeping_detail_id_in = $v['timekeeping_detail_id'];
                                        $type_hour_in = $v['type'];
                                        $type_check_in = $v['type_check'];

                                    } elseif ($v['type'] == 2) {
                                        $hourOut = $v['hour'];
                                        $imageOut = $v['image'];

                                        $timekeeping_detail_hour_id_out = $v['id'];
                                        $timekeeping_detail_id_out = $v['timekeeping_detail_id'];
                                        $type_hour_out = $v['type'];
                                        $type_check_out = $v['type_check'];
                                    }
                                }
                            }
                        }
                    }

                    if ($value['type'] == 1) {
                        $textHour = $hourIn;
                        $image = $imageIn;

                        $timekeeping_detail_hour_id_text = $timekeeping_detail_hour_id_in;
                        $timekeeping_detail_id_text = $timekeeping_detail_id_in;
                        $type_hour_text = $type_hour_in;
                        $type_check_text = $type_check_in;

                        $type_hour = $type_hour_text;
                        $type_check = $type_check_text;

                        $hourCheckInNew = $textHour;
                        $timekeeping_detail_hour_id_new = $timekeeping_detail_hour_id_text;
                        $timekeeping_detail_id_new = $timekeeping_detail_id_text;

                    } elseif ($value['type'] == 2) {
                        $textHour = $hourOut;
                        $image = $imageOut;

                        $timekeeping_detail_hour_id_text = $timekeeping_detail_hour_id_out;
                        $timekeeping_detail_id_text = $timekeeping_detail_id_out;
                        $type_hour_text = $type_hour_out;
                        $type_check_text = $type_check_out;

                        $type_hour = $type_hour_text;
                        $type_check = $type_check_text;

                        $hourCheckOutNew = $textHour;
                        $timekeeping_detail_hour_id_out_new = $timekeeping_detail_hour_id_text;
                        $timekeeping_detail_id_out_new = $timekeeping_detail_id_text;

                        if (!empty($hourIn)) {
                            if (!empty($timekeeping_detail_hour_id_in)) {
                                $id_timekeeping_detail_hour = $timekeeping_detail_hour_id_in;
                            }
                        }

                    }

                    $content_date = $hourCheckInNew;
                    $content_date .= $hourCheckOutNew;
                    $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$sttC]$rowBegin",
                        trim($content_date, "\n"))->getStyle("$c_excel[$sttC]$rowBegin")
                        ->applyFromArray($styleTd)->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getColumnDimension($c_excel[$sttC])->setWidth(6);
                    $sttC++;
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(-1);
                $rowBegin++;
            }
        }


        $filename = lang('tnh_sale_listing') . '.xls';
        $objPHPExcel->getActiveSheet()->freezePane('A1');

        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="BANG_THONG_KE_CHI_TIET_GIO_RA_VAO.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        die();
    }


//     function exportExcelSaleListing()
//     {
//         $c_excel = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
//             'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
//             'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
//             'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
//             'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
//         ];
//         ini_set('memory_limit', '3500M');
//         include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
//         $this->load->library('PHPExcel');

//         // print_arrays($this->input->post());
//         $month = $this->input->get('month');
//         $year = $this->input->get('year');
//         $department = $this->input->get('department');
//         $staff = $this->input->get('staff');
//         $month = $this->input->get('month');
//         $year = $this->input->get('year');
//         $department = $this->input->get('department');
//         $staff = $this->input->get('staff');
//         ob_end_clean();
//         $data = [];

//         $listDate = getAllDateInMonth($month, $year, 'd');
//         $widthHead = (79 / count($listDate)) . '%';

//         $timekeepingId = 0;
//         $this->db->select('*');
//         $this->db->from('tbl_timekeeping');
//         $this->db->where('tbl_timekeeping.month', $month);
//         $this->db->where('tbl_timekeeping.year', $year);
//         $timekeeping = $this->db->get()->row_array();
//         if (!empty($timekeeping)) {
//             $timekeepingId = $timekeeping['id'];
//         }

//         $this->db->select('tblstaff.staffid as staffid,tblstaff.firstname as name,tbl_timekeeping_detail_hour.type as type');
//         $this->db->from('tblstaff');
//         $this->db->where('active', 1);
//         $this->db->where('type_staff', 2);
//         $this->db->where('tbl_timekeeping.month', $month);
//         $this->db->where('tbl_timekeeping.year', $year);
//         if (!empty($staff)) {
//             $this->db->where('tbl_timekeeping_detail.staff_id', $staff);
//         }
//         if (!empty($department)) {
//             $staffDepartments = "(
//                 SELECT
//                     tblstaff_departments.staffid as staffid
//                 FROM tblstaff_departments
//                 WHERE tblstaff_departments.staffid  = tblstaff.staffid AND tblstaff_departments.departmentid = $department
//             )";
//             $this->db->where("exists ($staffDepartments)");
//         }
//         $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
//         $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
//         $this->db->join('tbl_timekeeping_detail_hour',
//             'tbl_timekeeping_detail_hour.timekeeping_detail_id= tbl_timekeeping_detail.id', 'left');
//         $this->db->group_by('tbl_timekeeping_detail.staff_id');
//         $this->db->group_by('tbl_timekeeping_detail_hour.type');
//         $personnel = $this->db->get()->result_array();

//         $styleTh = [
//             'font' => array(
//                 'bold' => true,
//                 'name' => 'Times New Roman'
//             ),
//             'borders' => array(
//                 'allborders' => array(
//                     'style' => PHPExcel_Style_Border::BORDER_THIN
//                 )
//             ),
//             'alignment' => array(
//                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
//                 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
//             )

//         ];
//         $styleTd = [
//             'font' => array(
//                 'bold' => false,
//                 'name' => 'Times New Roman'
//             ),
//             'borders' => array(
//                 'allborders' => array(
//                     'style' => PHPExcel_Style_Border::BORDER_THIN
//                 )
//             ),
//             'alignment' => array(
//                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
//                 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
//             )
//         ];
//         $styleTd_center = [
//             'font' => array(
//                 'bold' => false,
//                 'name' => 'Times New Roman'
//             ),
//             'borders' => array(
//                 'allborders' => array(
//                     'style' => PHPExcel_Style_Border::BORDER_THIN
//                 )
//             ),
//             'alignment' => array(
//                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
//                 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
//             )
//         ];
//         $styleTd_left = [
//             'font' => array(
//                 'bold' => false,
//                 'name' => 'Times New Roman'
//             ),
//             'borders' => array(
//                 'allborders' => array(
//                     'style' => PHPExcel_Style_Border::BORDER_THIN
//                 )
//             ),
//             'alignment' => array(
//                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
//                 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
//             )
//         ];


//         $objPHPExcel = new PHPExcel();
//         $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
//         $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
//         $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
//         $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
//         $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
//         $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

//         $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
//         $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

//         $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
//         $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);

//         $decimals_money = get_option('decimals_money');
//         $decimals_number = get_option('decimals_number');
//         $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
//         $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s", 0) : '');

//         $company = get_option('invoice_company_name');
//         $address = get_option('invoice_company_address');
//         $phonenumber = get_option('invoice_company_phonenumber');
//         $styleNone = [
//             'font' => array(
//                 'size' => 13,
//                 'name' => 'Times New Roman'
//             )
//         ];

//         $company_logo = get_option('company_logo');
//         if (file_exists('uploads/company/' . $company_logo)) {
//             $objDrawing = new PHPExcel_Worksheet_Drawing();
//             $objDrawing->setPath('uploads/company/' . $company_logo);
//             $objDrawing->setCoordinates('A1');
//             $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
//             $objPHPExcel->getActiveSheet()->getStyle("A1");
//             $objDrawing->setOffsetX(5);
//             $objDrawing->setOffsetY(5);
//             $objDrawing->setResizeProportional(false);

//             $objDrawing->setWidth(55);
//             $objDrawing->setHeight(55);

// //              $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(100);
//         }

//         $objPHPExcel->getActiveSheet()->mergeCells('B1:I1');
//         $objPHPExcel->getActiveSheet()->setCellValue('B1', $company)->getStyle('B1:I1')->applyFromArray([
//             'font' => array(
//                 'bold' => true,
//                 'size' => 14,
//                 'name' => 'Times New Roman'
//             ),
//             'alignment' => array(
//                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
//                 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
//             )
//         ]);

//         $objPHPExcel->getActiveSheet()->mergeCells('B2:I2');
//         $objPHPExcel->getActiveSheet()->setCellValue('B2', $address)->getStyle('B2:I2')->applyFromArray($styleNone);

//         $objPHPExcel->getActiveSheet()->mergeCells('B3:I3');
//         $objPHPExcel->getActiveSheet()->setCellValue('B3', 'SĐT: ' . $phonenumber)->getStyle('B3:I3')->applyFromArray($styleNone);

//         $objPHPExcel->getActiveSheet()->mergeCells('A5:Q5');
//         $objPHPExcel->getActiveSheet()->setCellValue('A5', 'BẢNG THỐNG KÊ CHI TIẾT GIỜ VÀO - GIỜ RA')->getStyle('A5:Q5')->applyFromArray([
//             'font' => array(
//                 'bold' => true,
//                 'size' => 25,
//                 'name' => 'Times New Roman',
//                 'color' => array('rgb' => 'ff0202'),
//             ),
//             'alignment' => array(
//                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
//                 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
//             )
//         ]);

//         $objPHPExcel->getActiveSheet()->mergeCells('A6:Q6');
//         $objPHPExcel->getActiveSheet()->setCellValue('A6', ('THÁNG ' . $month . ' NĂM ' . $year))->getStyle("A6:Q6")->applyFromArray([
//             'font' => array(
//                 'bold' => true,
//                 'size' => 16,
//                 'name' => 'Times New Roman',
//             ),
//             'alignment' => array(
//                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
//                 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
//             )
//         ]);
//         $rowBegin = 8;
//         $sttC = 3;
//         $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", 'STT')->getStyle("A$rowBegin")->applyFromArray($styleTh);

//         $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", 'Nhân Viên')->getStyle("B$rowBegin")->applyFromArray($styleTh);

//         $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", 'Giờ')->getStyle("C$rowBegin")->applyFromArray($styleTh);

//         foreach ($listDate as $key => $value) {
//             $date = $key;
//             $day = date("d", strtotime($date));
//             $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$sttC]$rowBegin", $day)->getStyle("$c_excel[$sttC]$rowBegin")->applyFromArray($styleTh);
//             $sttC++;
//         }

//         $rowBegin++;
//         if (!empty($personnel)) {
//             $iSTT = 1;
//             foreach ($personnel as $key => $value) {
//                 $sttC = 3;
//                 $personnel_id = $value['staffid'];
//                 if (!empty($personnel[$key + 1]) && $personnel[$key + 1]['staffid'] == $value['staffid']) {
//                     $objPHPExcel->getActiveSheet()->mergeCells('A' . $rowBegin . ':A' . ($rowBegin + 1));
//                     $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", $iSTT)->getStyle('A' . $rowBegin . ':A' . ($rowBegin + 1))->applyFromArray($styleTd_center);
//                     $objPHPExcel->getActiveSheet()->mergeCells('B' . $rowBegin . ':B' . ($rowBegin + 1));
//                     $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['name'])->getStyle('B' . $rowBegin . ':B' . ($rowBegin + 1))->applyFromArray($styleTd_left);
//                     $iSTT++;
//                 } else if ($personnel[$key - 1]['staffid'] != $value['staffid']) {
//                     $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", $iSTT)->getStyle('A' . $rowBegin)->applyFromArray($styleTd_center);
//                     $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['name'])->getStyle('B' . $rowBegin)->applyFromArray($styleTd_left);
//                     $iSTT++;
//                 }

//                 $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['type'] == 1 ? "Giờ vào" : 'Giờ ra'))->getStyle('C' . $rowBegin)->applyFromArray($styleTd_center);
//                 foreach ($listDate as $kk => $val) {
//                     $date = $kk;
//                     $day = date("d", strtotime($kk));
//                     $hourIn = $hourOut = $textHour = $textHourTct = $hourInTct = $hourOutTct = $textHourTcd = $hourInTcd = $hourOutTcd = '';
//                     $textHourHcnt = $hourInHcnt = $hourOutHcnt = '';

//                     $type_check_in_Hcnt = '';
//                     $type_check_out_tct = '';
//                     $timekeeping_detail_id_out_tct = '';

//                     $hourCheckInNew = '';
//                     $hourCheckOutNew = '';

//                     $this->db->select('*');
//                     $this->db->from('tbl_timekeeping_detail');
//                     $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
//                     $this->db->where('tbl_timekeeping_detail.staff_id', $personnel_id);
//                     $this->db->where('tbl_timekeeping_detail.date', $date);
//                     $this->db->where('tbl_timekeeping_detail.day', $day);
//                     $timeKeepingDetail = $this->db->get()->row_array();
//                     $type = $timeKeepingDetail['type'];
//                     $timeKeepingDetail_id = $timeKeepingDetail['id'];

//                     if ($timeKeepingDetail_id != 0) {
//                         $this->db->select('*');
//                         $this->db->from('tbl_timekeeping_detail_hour');
//                         $this->db->where('tbl_timekeeping_detail_hour.timekeeping_id', $timekeepingId);
//                         $this->db->where('tbl_timekeeping_detail_hour.timekeeping_detail_id', $timeKeepingDetail_id);
//                         $timeKeepingDetailHour = $this->db->get()->result_array();
//                         // print_arrays($timeKeepingDetailHour);
//                         if (!empty($timeKeepingDetailHour)) {
//                             foreach ($timeKeepingDetailHour as $k => $v) {
//                                 if ($v['type_check'] == 1) {
//                                     if ($v['type'] == 1) {
//                                         $hourIn = $v['hour'];

//                                     } elseif ($v['type'] == 2) {
//                                         $hourOut = $v['hour'];
//                                     }
//                                 } elseif ($v['type_check'] == 2) {
//                                     if ($v['type'] == 1) {
//                                         $hourInTct = $v['hour'];

//                                     } elseif ($v['type'] == 2) {
//                                         $hourOutTct = $v['hour'];

//                                         $type_check_out_tct = $v['type_check'];
//                                         $timekeeping_detail_id_out_tct = $v['timekeeping_detail_id'];
//                                     }
//                                 } elseif ($v['type_check'] == 3) {
//                                     if ($v['type'] == 1) {
//                                         $hourInTcd = $v['hour'];
//                                     } elseif ($v['type'] == 2) {
//                                         $hourOutTcd = $v['hour'];
//                                     }
//                                 } elseif ($v['type_check'] == 4) {
//                                     if ($v['type'] == 1) {
//                                         $hourInHcnt = $v['hour'];
//                                         $type_check_in_Hcnt = $v['type_check'];
//                                     } elseif ($v['type'] == 2) {
//                                         $hourOutHcnt = $v['hour'];
//                                     }
//                                 }
//                             }
//                         }
//                     }

//                     if ($value['type'] == 1) {
//                         $textHour = $hourIn;
//                         $textHourTct = $hourInTct;
//                         $textHourTcd = $hourInTcd;
//                         $textHourHcnt = $hourInHcnt;

//                         $type_check_text_Hcnt = $type_check_in_Hcnt;

//                         if(!empty($textHour)){
//                             $hourCheckInNew = $textHour;
//                         } elseif(!empty($textHourTct)){
//                             $hourCheckInNew = $textHourTct;
//                         } elseif(!empty($textHourTcd)){
//                             $hourCheckInNew = $textHourTcd;
//                         } elseif(!empty($textHourHcnt)){
//                             $hourCheckInNew = $textHourHcnt;
//                         }

//                         if(($type_check_text_Hcnt)){
//                             $hourCheckInNew = '';
//                         }

//                     } elseif ($value['type'] == 2) {
//                         $textHour = $hourOut;
//                         $textHourTct = $hourOutTct;
//                         $textHourTcd = $hourOutTcd;
//                         $textHourHcnt = $hourOutHcnt;

//                         $type_check_text_tct = $type_check_out_tct;
//                         $timekeeping_detail_id_text_tct = $timekeeping_detail_id_out_tct;

//                         if(!empty($textHour)){
//                             $hourCheckOutNew = $textHour;
//                         }
//                         if(!empty($textHourTct)){
//                             $hourCheckOutNew = $textHourTct;
//                         }
//                         if(!empty($textHourTcd)){
//                             $hourCheckOutNew = $textHourTcd;
//                         }
//                         if(!empty($textHourHcnt)){
//                             $hourCheckOutNew = $textHourHcnt;
//                         }

//                         $checkCounthourDetail = get_table_where('tbl_timekeeping_detail_count_hour',['timekeeping_detail_id_old'=>$timekeeping_detail_id_text_tct],'','row_array');
//                         if(($type_check_text_tct && !empty($checkCounthourDetail))){
//                             $hourCheckOutNew = '';
//                         }
//                     }

// //                  $htmlTextHour = '<a style="color:#008ece;text-decoration:none">'.$textHour.'</a>';
// //                  $htmlTextHourTct = '<a style="color:red;text-decoration:none">'.$textHourTct.'</a>';
// //                  $htmlTextHourTcd = '<a style="color:#278c15;text-decoration:none">'.$textHourTcd.'</a>';
// //                  $htmlTextHourHcnt = '<a style="color:#ea7922;text-decoration:none">'.$textHourHcnt.'</a>';
//                     // $content_date = (!empty($textHour) ? $textHour . "\n" : '')
//                     //     . (!empty($textHourTct) ? $textHourTct . "\n" : '')
//                     //     . (!empty($textHourTcd) ? $textHourTcd . "\n" : '')
//                     //     . (!empty($textHourHcnt) ? $textHourHcnt : '');
//                     $content_date = (!empty($hourCheckInNew) ? $hourCheckInNew . "\n" : '')
//                     . (!empty($hourCheckOutNew) ? $hourCheckOutNew : '');
//                     $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$sttC]$rowBegin", trim($content_date, "\n"))->getStyle("$c_excel[$sttC]$rowBegin")
//                         ->applyFromArray($styleTd)->getAlignment()->setWrapText(true);

//                     $objPHPExcel->getActiveSheet()->getColumnDimension($c_excel[$sttC])->setWidth(6);
//                     $sttC++;
//                 }
//                 $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(-1);
//                 $rowBegin++;
// //              $rowBegin++;
//             }
//         }


//         $filename = lang('tnh_sale_listing') . '.xls';
//         $objPHPExcel->getActiveSheet()->freezePane('A1');

// //      $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
// //      $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
// //      $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
// //      $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
// //      $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
// //      $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
// //      $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
// //      $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
// //      $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
// //      $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
// //      $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
// //      $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
// //      $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
// //      $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
// //      $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
// //      $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
//         ob_start();
//         header('Content-Type: application/vnd.ms-excel');
//         header('Content-Disposition: attachment;filename="BANG_THONG_KE_CHI_TIET_GIO_RA_VAO.xls"');
//         header('Cache-Control: max-age=0');
//         $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//         $objWriter->save('php://output');
//         die();
// //      $xlsData = ob_get_contents();
// //      ob_end_clean();
// //
// //      $response = array(
// //          'result' => 1,
// //          'filename' => $filename,
// //          'message' => lang('success'),
// //          'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
// //      );
// //      die(json_encode($response));
//     }

    // function exportExcelTimekeeping()
    // {
    //     $c_excel = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
    //         'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
    //         'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
    //         'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
    //         'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
    //     ];
    //     ini_set('memory_limit', '3500M');
    //     include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
    //     $this->load->library('PHPExcel');

    //     // print_arrays($this->input->post());
    //     $month = $this->input->get('month');
    //     $year = $this->input->get('year');
    //     $department = $this->input->get('department');
    //     $staff = $this->input->get('staff');
    //     $month = $this->input->get('month');
    //     $year = $this->input->get('year');
    //     $department = $this->input->get('department');
    //     $staff = $this->input->get('staff');
    //     ob_end_clean();
    //     $data = [];

    //     $listDate = getAllDateInMonth($month, $year, 'd');
    //     $widthHead = (79 / count($listDate)) . '%';

    //     $timekeepingId = 0;
    //     $this->db->select('*');
    //     $this->db->from('tbl_timekeeping');
    //     $this->db->where('tbl_timekeeping.month', $month);
    //     $this->db->where('tbl_timekeeping.year', $year);
    //     $timekeeping = $this->db->get()->row_array();
    //     if (!empty($timekeeping)) {
    //         $timekeepingId = $timekeeping['id'];
    //     }

    //     $this->db->select('tblstaff.staffid as staffid,tblstaff.firstname as name');
    //     $this->db->from('tblstaff');
    //     $this->db->where('active', 1);
    //     $this->db->where('type_staff', 2);
    //     $this->db->where('tbl_timekeeping.month', $month);
    //     $this->db->where('tbl_timekeeping.year', $year);
    //     if (!empty($staff)) {
    //         $this->db->where('tbl_timekeeping_detail.staff_id', $staff);
    //     }
    //     if (!empty($department)) {
    //         $staffDepartments = "(
    //             SELECT
    //                 tblstaff_departments.staffid as staffid
    //             FROM tblstaff_departments
    //             WHERE tblstaff_departments.staffid  = tblstaff.staffid AND tblstaff_departments.departmentid = $department
    //         )";
    //         $this->db->where("exists ($staffDepartments)");
    //     }
    //     $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
    //     $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
    //     $this->db->group_by('tbl_timekeeping_detail.staff_id');
    //     $personnel = $this->db->get()->result_array();

    //     $styleTh = [
    //         'font' => array(
    //             'bold' => true,
    //             'name' => 'Times New Roman'
    //         ),
    //         'borders' => array(
    //             'allborders' => array(
    //                 'style' => PHPExcel_Style_Border::BORDER_THIN
    //             )
    //         ),
    //         'alignment' => array(
    //             'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    //             'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    //         )

    //     ];
    //     $styleThLeft = [
    //         'font' => array(
    //             'bold' => true,
    //             'name' => 'Times New Roman'
    //         ),
    //         'borders' => array(
    //             'allborders' => array(
    //                 'style' => PHPExcel_Style_Border::BORDER_THIN
    //             )
    //         ),
    //         'alignment' => array(
    //             'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
    //             'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    //         )

    //     ];
    //     $styleTd = [
    //         'font' => array(
    //             'bold' => false,
    //             'name' => 'Times New Roman'
    //         ),
    //         'borders' => array(
    //             'allborders' => array(
    //                 'style' => PHPExcel_Style_Border::BORDER_THIN
    //             )
    //         ),
    //         'alignment' => array(
    //             'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    //             'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    //         )
    //     ];
    //     $styleTd_center = [
    //         'font' => array(
    //             'bold' => false,
    //             'name' => 'Times New Roman'
    //         ),
    //         'borders' => array(
    //             'allborders' => array(
    //                 'style' => PHPExcel_Style_Border::BORDER_THIN
    //             )
    //         ),
    //         'alignment' => array(
    //             'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    //             'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    //         )
    //     ];
    //     $styleTd_left = [
    //         'font' => array(
    //             'bold' => false,
    //             'name' => 'Times New Roman'
    //         ),
    //         'borders' => array(
    //             'allborders' => array(
    //                 'style' => PHPExcel_Style_Border::BORDER_THIN
    //             )
    //         ),
    //         'alignment' => array(
    //             'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
    //             'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    //         )
    //     ];


    //     $objPHPExcel = new PHPExcel();
    //     $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
    //     $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
    //     $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
    //     $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
    //     $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
    //     $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

    //     $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
    //     $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

    //     $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
    //     $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(33);

    //     $decimals_money = get_option('decimals_money');
    //     $decimals_number = get_option('decimals_number');
    //     $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
    //     $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s", 0) : '');

    //     $company = get_option('invoice_company_name');
    //     $address = get_option('invoice_company_address');
    //     $phonenumber = get_option('invoice_company_phonenumber');
    //     $styleNone = [
    //         'font' => array(
    //             'size' => 13,
    //             'name' => 'Times New Roman'
    //         )
    //     ];

    //     $company_logo = get_option('company_logo');
    //     if (file_exists('uploads/company/' . $company_logo)) {
    //         $objDrawing = new PHPExcel_Worksheet_Drawing();
    //         $objDrawing->setPath('uploads/company/' . $company_logo);
    //         $objDrawing->setCoordinates('A1');
    //         $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
    //         $objPHPExcel->getActiveSheet()->getStyle("A1");
    //         $objDrawing->setOffsetX(5);
    //         $objDrawing->setOffsetY(5);
    //         $objDrawing->setResizeProportional(false);

    //         $objDrawing->setWidth(55);
    //         $objDrawing->setHeight(55);

    //     }


    //     $objPHPExcel->getActiveSheet()->mergeCells('B1:I1');
    //     $objPHPExcel->getActiveSheet()->setCellValue('B1', $company)->getStyle('B1:I1')->applyFromArray([
    //         'font' => array(
    //             'bold' => true,
    //             'size' => 14,
    //             'name' => 'Times New Roman'
    //         ),
    //         'alignment' => array(
    //             'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
    //             'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    //         )
    //     ]);

    //     $objPHPExcel->getActiveSheet()->mergeCells('B2:I2');
    //     $objPHPExcel->getActiveSheet()->setCellValue('B2', $address)->getStyle('B2:I2')->applyFromArray($styleNone);

    //     $objPHPExcel->getActiveSheet()->mergeCells('B3:I3');
    //     $objPHPExcel->getActiveSheet()->setCellValue('B3', 'SĐT: ' . $phonenumber)->getStyle('B3:I3')->applyFromArray($styleNone);


    //     $objPHPExcel->getActiveSheet()->mergeCells('A5:Q5');
    //     $objPHPExcel->getActiveSheet()->setCellValue('A5', 'BẢNG THỐNG KÊ GIỜ CÔNG HÀNG NGÀY')->getStyle('A5:Q5')->applyFromArray([
    //         'font' => array(
    //             'bold' => true,
    //             'size' => 25,
    //             'name' => 'Times New Roman',
    //             'color' => array('rgb' => 'ff0202'),
    //         ),
    //         'alignment' => array(
    //             'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    //             'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    //         )
    //     ]);
    //     $objPHPExcel->getActiveSheet()->mergeCells('A6:Q6');
    //     $objPHPExcel->getActiveSheet()->setCellValue('A6', ('THÁNG ' . $month . ' NĂM ' . $year))->getStyle("A6:Q6")->applyFromArray([
    //         'font' => array(
    //             'bold' => true,
    //             'size' => 16,
    //             'name' => 'Times New Roman',
    //         ),
    //         'alignment' => array(
    //             'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    //             'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    //         )
    //     ]);
    //     $rowBegin = 8;
    //     $sttC = 2;
    //     $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", 'STT')->getStyle("A$rowBegin")->applyFromArray($styleTh);

    //     $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", 'Họ Và Tên')->getStyle("B$rowBegin")->applyFromArray($styleTh);

    //     foreach ($listDate as $key => $value) {
    //         $date = $key;
    //         $day = date("d", strtotime($date));
    //         $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$sttC]$rowBegin", $day)->getStyle("$c_excel[$sttC]$rowBegin")->applyFromArray($styleTh);
    //         $sttC++;
    //     }

    //     $rowBegin++;
    //     if (!empty($personnel)) {
    //         $iSTT = 1;
    //         foreach ($personnel as $key => $value) {
    //             $sttC = 2;
    //             $personnel_id = $value['staffid'];
    //             $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", $iSTT)->getStyle('A' . $rowBegin)->applyFromArray($styleTd_center);
    //             $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['name'])->getStyle('B' . $rowBegin)->applyFromArray($styleTd_left);
    //             $iSTT++;

    //             foreach ($listDate as $kk => $val) {
    //                 $date = $kk;
    //                 $day = date("d", strtotime($kk));
    //                 $count_hour = '';
    //                 $day_check = explode("/", $val);
    //                 $style_count_hour = '';
    //                 $type = '';

    //                 $id_timekeeping_detail_old = 0;
    //                 $id_timekeeping_detail_new = 0;
    //                 $count_hour_new = 0;
    //                 $count_hour_check = 0;
    //                 $check = false;
    //                 $day_old = '';
    //                 $day_new = '';


    //                 $this->db->select('*');
    //                 $this->db->from('tbl_timekeeping_detail');
    //                 $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
    //                 $this->db->where('tbl_timekeeping_detail.staff_id', $personnel_id);
    //                 $this->db->where('tbl_timekeeping_detail.date', $date);
    //                 $this->db->where('tbl_timekeeping_detail.day', $day);
    //                 $timeKeepingDetail = $this->db->get()->row_array();
    //                 if($day_check['0'] == $timeKeepingDetail['day'] && $timeKeepingDetail['staff_id'] == $value['staffid'] ){
    //                     $count_hour = $timeKeepingDetail['count_hour'];
    //                     $count_hour_check = $timeKeepingDetail['count_hour'];
    //                     $id_timekeeping_detail_old = $timeKeepingDetail['id'];
    //                     $day_old = $timeKeepingDetail['day'];
    //                 }
    //                 $timeKeepingDetailCountHour = get_table_where('tbl_timekeeping_detail_count_hour',['timekeeping_detail_id_old'=>$id_timekeeping_detail_old],'','row_array');
    //                 if(!empty($timeKeepingDetailCountHour)){
    //                     $id_timekeeping_detail_new = $timeKeepingDetailCountHour['timekeeping_detail_id'];
    //                 } else {
    //                     $timeKeepingDetailCountHourNew = get_table_where('tbl_timekeeping_detail_count_hour',['timekeeping_detail_id'=>$id_timekeeping_detail_old],'','row_array');
    //                     if(!empty($timeKeepingDetailCountHourNew)){
    //                         if(!empty($timeKeepingDetailCountHourNew['timekeeping_detail_id_old'])){
    //                             $count_hour = 0;
    //                         }
    //                     }
    //                 }
    //                 $timeKeepingDetailNew = get_table_where('tbl_timekeeping_detail',['id'=>$id_timekeeping_detail_new],'','row_array');
    //                 if(!empty($timeKeepingDetailNew)){
    //                     $count_hour_new = str_replace(':','.',$timeKeepingDetailNew['count_hour']);
    //                     $day_new = $timeKeepingDetailNew['day'];
    //                     $check = true;
    //                 }
    //                 $count_hour = str_replace(':','.',$count_hour);
    //                 $count_hour = (float)$count_hour + (float)$count_hour_new;
    //                 $type = $timeKeepingDetail['type'];
    //                 if ($type == 'X') {
    //                     $type = '';
    //                 }
    //                 $id_timekeeping_detail = $timeKeepingDetail['id'];
    //                 if ($count_hour == 0) {
    //                     $count_hour = '';
    //                 }

    //                 $type = !empty($type) ? ("\n" . $type) : '';
    //                 $content_date = $count_hour . $type;
    //                 $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$sttC]$rowBegin", trim($content_date, "\n"))->getStyle("$c_excel[$sttC]$rowBegin")
    //                     ->applyFromArray($styleTd)->getNumberFormat();
    //                 $objPHPExcel->getActiveSheet()->getStyle("$c_excel[$sttC]$rowBegin")->applyFromArray($styleTd)->getAlignment()->setWrapText(true);
    //                 $objPHPExcel->getActiveSheet()->getColumnDimension($c_excel[$sttC])->setWidth(7);

    //                 $sttC++;
    //             }
    //             $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(-1);
    //             $rowBegin++;
    //         }

    //     }
    //     $rowBegin++;
    //     $rowBegin++;
    //     $objPHPExcel->getActiveSheet()->mergeCells('A' . $rowBegin . ':C' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowBegin, 'Nghỉ theo chế độ quy định')
    //         ->getStyle('A' . $rowBegin . ':C' . ($rowBegin))->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);
    //     $objPHPExcel->getActiveSheet()->getStyle('A' . $rowBegin)->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);

    //     $objPHPExcel->getActiveSheet()->mergeCells('D' . $rowBegin . ':E' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowBegin,
    //         'R')->getStyle('D' . $rowBegin . ':E' . ($rowBegin))->applyFromArray($styleTd);

    //     $objPHPExcel->getActiveSheet()->mergeCells('F' . $rowBegin . ':H' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowBegin,
    //         'Nghỉ phép năm')->getStyle('F' . $rowBegin . ':H' . ($rowBegin))
    //         ->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);
    //     $objPHPExcel->getActiveSheet()->getStyle('F' . $rowBegin)->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);

    //     $objPHPExcel->getActiveSheet()->mergeCells('I' . $rowBegin . ':J' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('I' . $rowBegin,
    //         'P')->getStyle('I' . $rowBegin . ':J' . ($rowBegin))->applyFromArray($styleTd);

    //     $objPHPExcel->getActiveSheet()->mergeCells('K' . $rowBegin . ':M' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('K' . $rowBegin,
    //         'Nghỉ không phép')->getStyle('K' . $rowBegin . ':M' . ($rowBegin))
    //         ->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);
    //     $objPHPExcel->getActiveSheet()->getStyle('K' . $rowBegin)->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);

    //     $objPHPExcel->getActiveSheet()->mergeCells('N' . $rowBegin . ':P' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('N' . $rowBegin,
    //         'KP')->getStyle('N' . $rowBegin . ':P' . ($rowBegin))->applyFromArray($styleTd);

    //     $objPHPExcel->getActiveSheet()->mergeCells('Q' . $rowBegin . ':S' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('Q' . $rowBegin,
    //         'Nghỉ việc riêng đi trễ')->getStyle('Q' . $rowBegin . ':S' . ($rowBegin))
    //         ->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);
    //     $objPHPExcel->getActiveSheet()->getStyle('Q' . $rowBegin)->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);

    //     $objPHPExcel->getActiveSheet()->mergeCells('T' . $rowBegin . ':U' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('T' . $rowBegin,
    //         "Ro-TR")->getStyle('T' . $rowBegin . ':U' . ($rowBegin))->applyFromArray($styleTd);
    //     $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(65);
    //     $rowBegin++;

    //     $objPHPExcel->getActiveSheet()->mergeCells('A' . $rowBegin . ':C' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowBegin,
    //         "Ro - Được hỗ trợ theo quy định,\n tính 50% tiền lương")
    //         ->getStyle('A' . $rowBegin . ':C' . ($rowBegin))->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);
    //     $objPHPExcel->getActiveSheet()->getStyle('A' . $rowBegin)->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);

    //     $objPHPExcel->getActiveSheet()->mergeCells('D' . $rowBegin . ':E' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowBegin,
    //         'Ro_HT_50')->getStyle('D' . $rowBegin . ':E' . ($rowBegin))->applyFromArray($styleTd);

    //     $objPHPExcel->getActiveSheet()->mergeCells('F' . $rowBegin . ':H' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowBegin,
    //         'Nghỉ nửa ngày phép')->getStyle('F' . $rowBegin . ':H' . ($rowBegin))
    //         ->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);
    //     $objPHPExcel->getActiveSheet()->getStyle('F' . $rowBegin)->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);

    //     $objPHPExcel->getActiveSheet()->mergeCells('I' . $rowBegin . ':J' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('I' . $rowBegin,
    //         'P/2')->getStyle('I' . $rowBegin . ':J' . ($rowBegin))->applyFromArray($styleTd);

    //     $objPHPExcel->getActiveSheet()->mergeCells('K' . $rowBegin . ':M' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('K' . $rowBegin,
    //         "O - Không có giấy nghỉ \n hưởng BHXH")->getStyle('K' . $rowBegin . ':M' . ($rowBegin))
    //         ->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);
    //     $objPHPExcel->getActiveSheet()->getStyle('K' . $rowBegin)->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);

    //     $objPHPExcel->getActiveSheet()->mergeCells('N' . $rowBegin . ':P' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('N' . $rowBegin,
    //         'O_K_BHXH')->getStyle('N' . $rowBegin . ':P' . ($rowBegin))->applyFromArray($styleTd);

    //     $objPHPExcel->getActiveSheet()->mergeCells('Q' . $rowBegin . ':S' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('Q' . $rowBegin,
    //         'O - Có giấy nghỉ hưởng BHXH')->getStyle('Q' . $rowBegin . ':S' . ($rowBegin))
    //         ->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);
    //     $objPHPExcel->getActiveSheet()->getStyle('Q' . $rowBegin)->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);

    //     $objPHPExcel->getActiveSheet()->mergeCells('T' . $rowBegin . ':U' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('T' . $rowBegin,
    //         "O_C_BHXH")->getStyle('T' . $rowBegin . ':U' . ($rowBegin))->applyFromArray($styleTd);
    //     $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(65);
    //     $rowBegin++;


    //     $objPHPExcel->getActiveSheet()->mergeCells('A' . $rowBegin . ':C' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowBegin,
    //         'Nghỉ việc')->getStyle('A' . $rowBegin . ':C' . ($rowBegin))
    //         ->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);
    //     $objPHPExcel->getActiveSheet()->getStyle('A' . $rowBegin)->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);

    //     $objPHPExcel->getActiveSheet()->mergeCells('D' . $rowBegin . ':E' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowBegin,
    //         'N')->getStyle('D' . $rowBegin . ':E' . ($rowBegin))->applyFromArray($styleTd);

    //     $objPHPExcel->getActiveSheet()->mergeCells('F' . $rowBegin . ':H' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowBegin,
    //         'Ro - Không hỗ trợ tính lương')->getStyle('F' . $rowBegin . ':H' . ($rowBegin))
    //         ->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);
    //     $objPHPExcel->getActiveSheet()->getStyle('F' . $rowBegin)->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);

    //     $objPHPExcel->getActiveSheet()->mergeCells('I' . $rowBegin . ':J' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('I' . $rowBegin,
    //         "Ro")->getStyle('I' . $rowBegin . ':J' . ($rowBegin))->applyFromArray($styleTd);
    //     $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(65);

    //     $objPHPExcel->getActiveSheet()->mergeCells('K' . $rowBegin . ':M' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('K' . $rowBegin,
    //         '')->getStyle('K' . $rowBegin . ':M' . ($rowBegin))
    //         ->applyFromArray($styleTd)->getAlignment()->setWrapText(true);
    //     $objPHPExcel->getActiveSheet()->getStyle('K' . $rowBegin)->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);

    //     $objPHPExcel->getActiveSheet()->mergeCells('N' . $rowBegin . ':Q' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('N' . $rowBegin,
    //         "")->getStyle('N' . $rowBegin . ':Q' . ($rowBegin))->applyFromArray($styleTd);
    //     $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(65);

    //     $objPHPExcel->getActiveSheet()->mergeCells('Q' . $rowBegin . ':S' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('Q' . $rowBegin,
    //         '')->getStyle('Q' . $rowBegin . ':S' . ($rowBegin))
    //         ->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);
    //     $objPHPExcel->getActiveSheet()->getStyle('Q' . $rowBegin)->applyFromArray($styleThLeft)->getAlignment()->setWrapText(true);

    //     $objPHPExcel->getActiveSheet()->mergeCells('T' . $rowBegin . ':U' . ($rowBegin));
    //     $objPHPExcel->getActiveSheet()->setCellValue('T' . $rowBegin,
    //         "")->getStyle('T' . $rowBegin . ':U' . ($rowBegin))->applyFromArray($styleTd);
    //     $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(65);
    //     $rowBegin++;


    //     $filename = lang('tnh_sale_listing') . '.xls';
    //     $objPHPExcel->getActiveSheet()->freezePane('A1');
    //     ob_start();
    //     header('Content-Type: application/vnd.ms-excel');
    //     header('Content-Disposition: attachment;filename="BANG_THONG_KE_GIO_CONG_HANG_NGAY.xls"');
    //     header('Cache-Control: max-age=0');
    //     $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    //     $objWriter->save('php://output');
    //     die();
    // }

    function exportExcelTimekeeping()
    {
        $c_excel = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'AA',
            'AB',
            'AC',
            'AD',
            'AE',
            'AF',
            'AG',
            'AH',
            'AI',
            'AJ',
            'AK',
            'AL',
            'AM',
            'AN',
            'AO',
            'AP',
            'AQ',
            'AR',
            'AS',
            'AT',
            'AU',
            'AV',
            'AW',
            'AX',
            'AY',
            'AZ',
            'BA',
            'BB',
            'BC',
            'BD',
            'BE',
            'BF',
            'BG',
            'BH',
            'BI',
            'BJ',
            'BK',
            'BL',
            'BM',
            'BN',
            'BO',
            'BP',
            'BQ',
            'BR',
            'BS',
            'BT',
            'BU',
            'BV',
            'BW',
            'BX',
            'BY',
            'BZ',
            'CA',
            'CB',
            'CC',
            'CD',
            'CE',
            'CF',
            'CG',
            'CH',
            'CI',
            'CJ',
            'CK',
            'CL',
            'CM',
            'CN',
            'CO',
            'CP',
            'CQ',
            'CR',
            'CS',
            'CT',
            'CU',
            'CV',
            'CW',
            'CX',
            'CY',
            'CZ',
            'DA',
            'DB',
            'DC',
            'DD',
            'DE',
            'DF',
            'DG',
            'DH',
            'DI',
            'DJ',
            'DK',
            'DL',
            'DM',
            'DN',
            'DO',
            'DP',
            'DQ',
            'DR',
            'DS',
            'DT',
            'DU',
            'DV',
            'DW',
            'DX',
            'DY',
            'DZ'
        ];
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        // print_arrays($this->input->post());
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $department = $this->input->get('department');
        $staff = $this->input->get('staff');
        if (!empty($department)) {
            $this->db->where('departmentid', $department);
            $dtDepartment = $this->db->get('tbldepartments')->row_array();
            $name_department = $dtDepartment['name'];
            $code_department = $dtDepartment['code'];
        }
        ob_end_clean();
        $data = [];

        $listDate = getAllDateInMonth($month, $year, 'd');
        $widthHead = (79 / count($listDate)) . '%';

        $timekeepingId = 0;
        $this->db->select('*');
        $this->db->from('tbl_timekeeping');
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        $timekeeping = $this->db->get()->row_array();
        if (!empty($timekeeping)) {
            $timekeepingId = $timekeeping['id'];
        }

        if ($month == 12) {
            $monthNew = 1;
            $yearNew = $year + 1;
        } else {
            $monthNew = $month + 1;
            $yearNew = $year;
        }
        $timekeepingIdNew = 0;
        $this->db->select('*');
        $this->db->from('tbl_timekeeping');
        $this->db->where('tbl_timekeeping.month', $monthNew);
        $this->db->where('tbl_timekeeping.year', $yearNew);
        $timekeepingNew = $this->db->get()->row_array();
        if (!empty($timekeepingNew)) {
            $timekeepingIdNew = $timekeepingNew['id'];
        }

        $countPaidHoliday = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            WHERE (type = 'AL' OR type = 'LT') AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_paid_holiday";

        $countPaidHolidayNew = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            WHERE type = 'AL/2' AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_paid_holiday_new";

        $countNotPaidHoliday = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            WHERE (type = 'UP' OR type = 'TS' OR type = 'OD' ) AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_not_paid_holiday";

        $countNotPaidHolidayNew = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            WHERE type = 'UP/2' AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_not_paid_holiday_new";

        $countNumberDay = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count
            FROM tbl_timekeeping_detail
            WHERE (type != 'X' OR (type = 'X' AND number_day > 0 )) AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_number_day";

        $countNumberDayNew = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(number_day),0) as count
            FROM tbl_timekeeping_detail
            WHERE (type = 'X' AND number_day = '0.5' ) AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_number_day_new";

        $countHour= "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count
            FROM tbl_timekeeping_detail
            WHERE ((type = 'X' AND number_day > 0 )) AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_hour";

        $this->db->select('tblstaff.staffid as staffid,tblstaff.code as code, 
        CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name,
        tblroles.name as name_role,
        COALESCE(tb_count_paid_holiday.count,0) + (COALESCE(tb_count_paid_holiday_new.count,0) * 0.5 ) as totalHoliday, 
        COALESCE(tb_count_not_paid_holiday.count,0) + (COALESCE(tb_count_not_paid_holiday_new.count,0) * 0.5 ) as totalNotHoliday, 
        COALESCE(tb_count_number_day.count,0) as number_day, 
        COALESCE(tb_count_number_day_new.count,0) as number_day_new, 
        COALESCE(tb_count_hour.count,0) as count_hour, 
        COALESCE(tb_count_paid_holiday_new.count_hour,0) + COALESCE(tb_count_paid_holiday.count_hour,0) as count_hour_phep, 
        COALESCE(tb_count_not_paid_holiday_new.count_hour,0) + COALESCE(tb_count_not_paid_holiday.count_hour,0) as count_hour_kphep, 
        ');
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        if (!empty($staff)) {
            $this->db->where('tbl_timekeeping_detail.staff_id', $staff);
        }
        if (!empty($department)) {
            $staffDepartments = "(
                SELECT
                    tblstaff_departments.staffid as staffid
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid  = tblstaff.staffid AND tblstaff_departments.departmentid = $department
            )";
            $this->db->where("exists ($staffDepartments)");
        }
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
        $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
        $this->db->join("$countPaidHoliday", 'tb_count_paid_holiday.timekeeping_id = tbl_timekeeping.id AND tb_count_paid_holiday.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countPaidHolidayNew", 'tb_count_paid_holiday_new.timekeeping_id = tbl_timekeeping.id AND tb_count_paid_holiday_new.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countNotPaidHoliday", 'tb_count_not_paid_holiday.timekeeping_id = tbl_timekeeping.id AND tb_count_not_paid_holiday.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countNotPaidHolidayNew", 'tb_count_not_paid_holiday_new.timekeeping_id = tbl_timekeeping.id AND tb_count_not_paid_holiday_new.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countNumberDay", 'tb_count_number_day.timekeeping_id = tbl_timekeeping.id AND tb_count_number_day.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countNumberDayNew", 'tb_count_number_day_new.timekeeping_id = tbl_timekeeping.id AND tb_count_number_day_new.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countHour", 'tb_count_hour.timekeeping_id = tbl_timekeeping.id AND tb_count_hour.staff_id = tblstaff.staffid', 'left');
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $personnel = $this->db->get()->result_array();

        $styleTh = [
            'font' => array(
                'bold' => true,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )

        ];
        $styleThLeft = [
            'font' => array(
                'bold' => true,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )

        ];
        $styleTd = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];
        $styleTd_center = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];
        $styleTd_left = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];


        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);

        $decimals_money = get_option('decimals_money');
        $decimals_number = get_option('decimals_number');
        $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
        $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s", 0) : '');

        $company = get_option('invoice_company_name');
        $address = get_option('invoice_company_address');
        $phonenumber = get_option('invoice_company_phonenumber');
        $styleNone = [
            'font' => array(
                'size' => 13,
                'name' => 'Times New Roman'
            )
        ];

        $company_logo = get_option('company_logo');
        if (file_exists('uploads/company/' . $company_logo)) {
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath('uploads/company/' . $company_logo);
            $objDrawing->setCoordinates('A1');
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            $objPHPExcel->getActiveSheet()->getStyle("A1");
            $objDrawing->setOffsetX(5);
            $objDrawing->setOffsetY(5);
            $objDrawing->setResizeProportional(false);

            $objDrawing->setWidth(55);
            $objDrawing->setHeight(55);

        }


        $objPHPExcel->getActiveSheet()->mergeCells('B1:I1');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', $company)->getStyle('B1:I1')->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 14,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);

        $objPHPExcel->getActiveSheet()->mergeCells('B2:I2');
        $objPHPExcel->getActiveSheet()->setCellValue('B2', $address)->getStyle('B2:I2')->applyFromArray($styleNone);

        $objPHPExcel->getActiveSheet()->mergeCells('B3:I3');
        $objPHPExcel->getActiveSheet()->setCellValue('B3',
            'SĐT: ' . $phonenumber)->getStyle('B3:I3')->applyFromArray($styleNone);


        $objPHPExcel->getActiveSheet()->mergeCells('A5:AF5');
        $objPHPExcel->getActiveSheet()->setCellValue('A5',
            'BẢNG THỐNG KÊ GIỜ CÔNG HÀNG NGÀY')->getStyle('A5:AF5')->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 25,
                'name' => 'Times New Roman',
                'color' => array('rgb' => 'ff0202'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);
        $objPHPExcel->getActiveSheet()->mergeCells('A6:AF6');
        $objPHPExcel->getActiveSheet()->setCellValue('A6',
            ('THÁNG ' . $month . ' NĂM ' . $year))->getStyle("A6:AF6")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 16,
                'name' => 'Times New Roman',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);
        $rowBegin = 8;
        if (!empty($name_department)) {
            $sttC = 3;
            $stt = 1;
            $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt + 5] . $rowBegin);
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                'Phòng ban: ' . $name_department .'( '.$code_department.')')->getStyle($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt + 5] . $rowBegin)->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 14,
                    'name' => 'Times New Roman',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                )
            ]);
            $rowBegin++;
        }
        $sttC = 4;
        $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin",
            'STT')->getStyle("A$rowBegin")->applyFromArray($styleTh);

        $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin",
            'MSNV')->getStyle("B$rowBegin")->applyFromArray($styleTh);

        $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin",
            'Họ Và Tên')->getStyle("C$rowBegin")->applyFromArray($styleTh);

        $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin",
            'Chức vụ')->getStyle("D$rowBegin")->applyFromArray($styleTh);
        foreach ($listDate as $key => $value) {
            $date = $key;
            $day = date("d", strtotime($date));
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$sttC]$rowBegin",
                $day)->getStyle("$c_excel[$sttC]$rowBegin")->applyFromArray($styleTh);
            $sttC++;
        }

        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$sttC]$rowBegin",
            'Nghĩ phép')->getStyle("$c_excel[$sttC]$rowBegin")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $sttC++;
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$sttC]$rowBegin",
            'Nghĩ không lương')->getStyle("$c_excel[$sttC]$rowBegin")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $sttC++;
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$sttC]$rowBegin",
            'TC giờ làm việc')->getStyle("$c_excel[$sttC]$rowBegin")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $sttC++;
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$sttC]$rowBegin",
            'TC giờ được hưởng lương')->getStyle("$c_excel[$sttC]$rowBegin")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $sttC++;

        $rowBegin++;
        if (!empty($personnel)) {
            $iSTT = 1;
            foreach ($personnel as $key => $value) {
                $sttC = 4;
                $personnel_id = $value['staffid'];
                $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin",
                    $iSTT)->getStyle('A' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$rowBegin",
                    $value['code'],PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('B' . $rowBegin)->applyFromArray($styleTd_left);
                $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin",
                    $value['name'])->getStyle('C' . $rowBegin)->applyFromArray($styleTd_left);
                $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin",
                    $value['name_role'])->getStyle('D' . $rowBegin)->applyFromArray($styleTd_left);
                $iSTT++;

                $this->db->select('
                    tbl_timekeeping_detail.staff_id as staff_id,
                    tbl_timekeeping_detail.id as id,
                    tbl_timekeeping_detail.day as day,
                    tbl_timekeeping_detail.date as date,
                    tbl_timekeeping_detail.type as type,
                    tbl_timekeeping_detail.date_word as date_word,
                    tbl_timekeeping_detail.count_hour_overtime as count_hour_overtime,
                    tbl_timekeeping_detail.count_hour as count_hour,
                    tbl_timekeeping_detail.number_day as number_day
                ');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
                $this->db->where('tbl_timekeeping_detail.staff_id', $personnel_id);
                $timeKeepingDetailNew = $this->db->get()->result_array();

                foreach ($listDate as $kk => $val) {
                    $date = $kk;
                    $day = date("d", strtotime($kk));
                    $count_hour = '';
                    $type = 'X';
                    $day_check = explode("/", $val);
                    $style_count_hour = '';

                    $number_day = 0;
                    $count_hour_overtime = 0;
                    $count_hour = 0;
                    $check = false;
                    $day_old = '';
                    $day_new = '';
                    $date_word = '';

                    $id_timekeeping_detail = 0;


                    if (!empty($timeKeepingDetailNew)) {
                        foreach ($timeKeepingDetailNew as $kk => $v) {
                            if ($v['date'] == $date) {
                                if ($day_check['0'] == $v['day'] && $v['staff_id'] == $value['staffid']) {
                                    $number_day = $v['number_day'];
                                    $count_hour_overtime = $v['count_hour_overtime'];
                                    $count_hour = $v['count_hour'];
                                    $day_old = $v['day'];
                                    $type = $v['type'];
                                    $id_timekeeping_detail = $v['id'];
                                    $date_word = $v['date_word'];
                                }
                            }
                        }
                    }

                    $count_hour_new = ($count_hour - $count_hour_overtime);
                    if ($count_hour_new == 0){
                        $count_hour_new = '';
                    }
                    if (!empty($count_hour_overtime)){
                        $htmlNew = "\n" .'TC: '.$count_hour_overtime .'(h)';
                        $style_count_hour = $count_hour_new ;
                    } else {
                        $style_count_hour = $count_hour_new;
                    }

                    if ($date_word == 'Sun'){
                        if ($count_hour > 0) {
                            $style_count_hour = 'TC: ' . $count_hour . ' (h)';
                        }
                    }

                    if ($type == 'X'){
                        $type = '';
                    }
                    $type = !empty($type) ? ("\n" . $type) : '';
                    $content_date = $style_count_hour . $type;
                    $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$sttC]$rowBegin",
                        trim($content_date, "\n"))->getStyle("$c_excel[$sttC]$rowBegin")
                        ->applyFromArray($styleTd)->getNumberFormat();
                    $objPHPExcel->getActiveSheet()->getStyle("$c_excel[$sttC]$rowBegin")->applyFromArray($styleTd)->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension($c_excel[$sttC])->setWidth(7);

                    $sttC++;
                }

                $countHourNew = number_unformat($value['count_hour']);
                $count_hour_phep = number_unformat($value['count_hour_phep']);
                $count_hour_kphep = number_unformat($value['count_hour_kphep']);
                $totalHoliday = number_unformat($value['totalHoliday']);
                $totalNotHoliday = number_unformat($value['totalNotHoliday']);
                $number_day_new = number_unformat($value['number_day']) - number_unformat($value['number_day_new']);

                $total_number_day = $number_day_new - $totalHoliday - $totalNotHoliday;
                $total_number_day = $total_number_day > 0 ? $total_number_day : 0;

                $countHourNew = $countHourNew + $count_hour_phep + $count_hour_kphep;
                $countHourNew = $countHourNew > 0 ? $countHourNew : 0;

                $total_number_day_salary = $countHourNew + ($totalHoliday * 8);
                $total_number_day_salary = $total_number_day_salary > 0 ? $total_number_day_salary : 0;

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$sttC]$rowBegin", ($totalHoliday > 0 ? ($totalHoliday * 8) : ''))->getStyle("$c_excel[$sttC]$rowBegin")->applyFromArray($styleTd)->getNumberFormat();
                $objPHPExcel->getActiveSheet()->getColumnDimension($c_excel[$sttC])->setWidth(15);

                $sttC++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$sttC]$rowBegin", ($totalNotHoliday > 0 ? ($totalNotHoliday * 8): ''))->getStyle("$c_excel[$sttC]$rowBegin")->applyFromArray($styleTd)->getNumberFormat();
                $objPHPExcel->getActiveSheet()->getColumnDimension($c_excel[$sttC])->setWidth(15);

                $sttC++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$sttC]$rowBegin", ($countHourNew > 0 ? ($countHourNew): ''))->getStyle("$c_excel[$sttC]$rowBegin")->applyFromArray($styleTd)->getNumberFormat();
                $objPHPExcel->getActiveSheet()->getColumnDimension($c_excel[$sttC])->setWidth(15);

                $sttC++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$sttC]$rowBegin", ($total_number_day_salary > 0 ? ($total_number_day_salary) : ''))->getStyle("$c_excel[$sttC]$rowBegin")->applyFromArray($styleTd)->getNumberFormat();
                $objPHPExcel->getActiveSheet()->getColumnDimension($c_excel[$sttC])->setWidth(15);

                $sttC++;
                $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(-1);
                $rowBegin++;
            }

        }
        $rowBegin++;

        $filename = lang('tnh_sale_listing') . '.xls';
        $objPHPExcel->getActiveSheet()->freezePane('A1');

        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="BANG_THONG_KE_GIO_CONG_HANG_NGAY.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        die();
    }

    function exportExcelSyntheticTimekeeping()
    {
        $c_excel = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'AA',
            'AB',
            'AC',
            'AD',
            'AE',
            'AF',
            'AG',
            'AH',
            'AI',
            'AJ',
            'AK',
            'AL',
            'AM',
            'AN',
            'AO',
            'AP',
            'AQ',
            'AR',
            'AS',
            'AT',
            'AU',
            'AV',
            'AW',
            'AX',
            'AY',
            'AZ',
            'BA',
            'BB',
            'BC',
            'BD',
            'BE',
            'BF',
            'BG',
            'BH',
            'BI',
            'BJ',
            'BK',
            'BL',
            'BM',
            'BN',
            'BO',
            'BP',
            'BQ',
            'BR',
            'BS',
            'BT',
            'BU',
            'BV',
            'BW',
            'BX',
            'BY',
            'BZ',
            'CA',
            'CB',
            'CC',
            'CD',
            'CE',
            'CF',
            'CG',
            'CH',
            'CI',
            'CJ',
            'CK',
            'CL',
            'CM',
            'CN',
            'CO',
            'CP',
            'CQ',
            'CR',
            'CS',
            'CT',
            'CU',
            'CV',
            'CW',
            'CX',
            'CY',
            'CZ',
            'DA',
            'DB',
            'DC',
            'DD',
            'DE',
            'DF',
            'DG',
            'DH',
            'DI',
            'DJ',
            'DK',
            'DL',
            'DM',
            'DN',
            'DO',
            'DP',
            'DQ',
            'DR',
            'DS',
            'DT',
            'DU',
            'DV',
            'DW',
            'DX',
            'DY',
            'DZ'
        ];
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        // print_arrays($this->input->post());
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $department = $this->input->get('department');
        $staff = $this->input->get('staff');
        if (!empty($department)) {
            $this->db->where('departmentid', $department);
            $name_department = $this->db->get('tbldepartments')->row('name');
        }
        ob_end_clean();
        $data = [];
        $styleTh = [
            'font' => array(
                'bold' => true,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )

        ];
        $styleThLeft = [
            'font' => array(
                'bold' => true,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )

        ];
        $styleTd = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];
        $styleTd_center = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];
        $styleTd_left = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];


        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(33);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);

        $decimals_money = get_option('decimals_money');
        $decimals_number = get_option('decimals_number');
        $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
        $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s", 0) : '');

        $company = get_option('invoice_company_name');
        $address = get_option('invoice_company_address');
        $phonenumber = get_option('invoice_company_phonenumber');
        $styleNone = [
            'font' => array(
                'size' => 13,
                'name' => 'Times New Roman'
            )
        ];

        $company_logo = get_option('company_logo');
        if (file_exists('uploads/company/' . $company_logo)) {
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath('uploads/company/' . $company_logo);
            $objDrawing->setCoordinates('A1');
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            $objPHPExcel->getActiveSheet()->getStyle("A1");
            $objDrawing->setOffsetX(5);
            $objDrawing->setOffsetY(5);
            $objDrawing->setResizeProportional(false);

            $objDrawing->setWidth(55);
            $objDrawing->setHeight(55);

            //  $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(100);
        }


        $objPHPExcel->getActiveSheet()->mergeCells('B1:I1');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', $company)->getStyle('B1:I1')->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 14,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);

        $objPHPExcel->getActiveSheet()->mergeCells('B2:I2');
        $objPHPExcel->getActiveSheet()->setCellValue('B2', $address)->getStyle('B2:I2')->applyFromArray($styleNone);

        $objPHPExcel->getActiveSheet()->mergeCells('B3:I3');
        $objPHPExcel->getActiveSheet()->setCellValue('B3',
            'SĐT: ' . $phonenumber)->getStyle('B3:I3')->applyFromArray($styleNone);


        $objPHPExcel->getActiveSheet()->mergeCells('A5:W5');
        $objPHPExcel->getActiveSheet()->setCellValue('A5',
            'TỔNG HỢP GIỜ CÔNG NHÂN VIÊN')->getStyle('A5:W5')->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 25,
                'name' => 'Times New Roman',
                'color' => array('rgb' => 'ff0202'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);
        $objPHPExcel->getActiveSheet()->mergeCells('A6:W6');
        $objPHPExcel->getActiveSheet()->setCellValue('A6',
            ('THÁNG ' . $month . ' NĂM ' . $year))->getStyle("A6:W6")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 16,
                'name' => 'Times New Roman',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);
        $rowBegin = 8;
        $objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(30);
        $objPHPExcel->getActiveSheet()->getRowDimension(9)->setRowHeight(30);
        $objPHPExcel->getActiveSheet()->getRowDimension(10)->setRowHeight(30);
        $objPHPExcel->getActiveSheet()->getRowDimension(11)->setRowHeight(30);
        if (!empty($name_department)) {
            $sttC = 3;
            $stt = 1;
            $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt + 5] . $rowBegin);
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                'Tổ: ' . $name_department)->getStyle($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt + 5] . $rowBegin)->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 14,
                    'name' => 'Times New Roman',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                )
            ]);
            $rowBegin++;

            $objPHPExcel->getActiveSheet()->getRowDimension(12)->setRowHeight(30);
        }

        $sttC = 2;
        $rowBegin_9 = $rowBegin + 1;
        $rowBegin_10 = $rowBegin + 2;
        $rowBegin_11 = $rowBegin + 3;
        $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin",
            'STT')->getStyle("A$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin_9",
            '')->getStyle("A$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin_10",
            '')->getStyle("A$rowBegin_10")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin_11",
            '')->getStyle("A$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells('A' . $rowBegin . ':A' . ($rowBegin + 3));
        $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin",
            'Họ Và Tên')->getStyle("B$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin_9",
            '')->getStyle("B$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin_10",
            '')->getStyle("B$rowBegin_10")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin_11",
            '')->getStyle("B$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells('B' . $rowBegin . ':B' . ($rowBegin + 3));
        $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin",
            'Đơn vị')->getStyle("C$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin_9",
            '')->getStyle("C$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin_10",
            '')->getStyle("C$rowBegin_10")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin_11",
            '')->getStyle("C$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells('C' . $rowBegin . ':C' . ($rowBegin + 3));
        $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin",
            'Giờ công (Giờ)')->getStyle("D$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin",
            '')->getStyle("E$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin",
            '')->getStyle("F$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin",
            '')->getStyle("G$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",
            '')->getStyle("H$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin",
            '')->getStyle("I$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin",
            '')->getStyle("J$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells('D' . $rowBegin . ':J' . ($rowBegin));
        $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin_9",
            '50%')->getStyle("D$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin_10",
            '')->getStyle("D$rowBegin_10")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin_11",
            '')->getStyle("D$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells("D$rowBegin_9:D$rowBegin_11");
        $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin_9",
            '100%')->getStyle("E$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin_10",
            '')->getStyle("E$rowBegin_10")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin_11",
            '')->getStyle("E$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells("E$rowBegin_9:E$rowBegin_11");
        $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin_9",
            '150%')->getStyle("F$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin_10",
            '')->getStyle("F$rowBegin_10")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin_11",
            '')->getStyle("F$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells("F$rowBegin_9:F$rowBegin_11");
        $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin_9",
            '200%')->getStyle("G$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin_10",
            '')->getStyle("G$rowBegin_10")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin_11",
            '')->getStyle("G$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells("G$rowBegin_9:G$rowBegin_11");
        $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin_9",
            '300%')->getStyle("H$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin_10",
            '')->getStyle("H$rowBegin_10")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin_11",
            '')->getStyle("H$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells("H$rowBegin_9:H$rowBegin_11");
        $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin_9",
            'Tổng cộng')->getStyle("I$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin_10",
            '')->getStyle("I$rowBegin_10")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin_11",
            '')->getStyle("I$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells("I$rowBegin_9:I$rowBegin_11");
        $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin_9",
            'Phép năm')->getStyle("J$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin_10",
            '')->getStyle("J$rowBegin_10")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin_11",
            '')->getStyle("J$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells("J$rowBegin_9:J$rowBegin_11");
        $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin",
            'Số phần cơm')->getStyle("K$rowBegin")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin_9",
            '')->getStyle("K$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin_10",
            '')->getStyle("K$rowBegin_10")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin_11",
            '')->getStyle("K$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells('K' . $rowBegin . ':K' . ($rowBegin + 3));
        $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin",
            'Số lần đi trễ')->getStyle("L$rowBegin")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin_9",
            '')->getStyle("L$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin_10",
            '')->getStyle("L$rowBegin_10")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin_11",
            '')->getStyle("L$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells('L' . $rowBegin . ':L' . ($rowBegin + 3));
        $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin",
            'Số giờ nghỉ')->getStyle("M$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin",
            '')->getStyle("N$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin",
            '')->getStyle("O$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin",
            '')->getStyle("P$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin",
            '')->getStyle("Q$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin",
            '')->getStyle("R$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin",
            '')->getStyle("S$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin",
            '')->getStyle("T$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin",
            '')->getStyle("U$rowBegin")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells('M' . $rowBegin . ':U' . ($rowBegin));
        $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin_9",
            'R')->getStyle("M$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin_10",
            '')->getStyle("M$rowBegin_10")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin_11",
            '')->getStyle("M$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells("M$rowBegin_9:M$rowBegin_11");
        $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin_9",
            'O')->getStyle("N$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin_9",
            '')->getStyle("O$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells("N$rowBegin_9:O$rowBegin_9");
        $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin_10",
            'Có giấy nghỉ hưởng BHXH')->getStyle("N$rowBegin_10")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin_11",
            '')->getStyle("N$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells("N$rowBegin_10:N$rowBegin_11");
        $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin_10",
            'Không có giấy nghỉ hưởng BHXH')->getStyle("O$rowBegin_10")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin_11",
            '')->getStyle("O$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells("O$rowBegin_10:O$rowBegin_11");
        $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin_9",
            'Ro Trong đó ')->getStyle("P$rowBegin_9")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin_9",
            '')->getStyle("Q$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin_9",
            '')->getStyle("R$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin_9",
            '')->getStyle("S$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin_9",
            '')->getStyle("T$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells("P$rowBegin_9:T$rowBegin_9");
        $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin_10",
            'Tổng giờ nghỉ')->getStyle("P$rowBegin_10")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin_11",
            '')->getStyle("P$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells("P$rowBegin_10:P$rowBegin_11");
        $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin_10",
            'Được hỗ trợ theo quy định')->getStyle("Q$rowBegin_10")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin_11",
            '50%')->getStyle("Q$rowBegin_11")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin_10",
            'Theo quyết định bổ sung')->getStyle("R$rowBegin_10")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin_10",
            '')->getStyle("S$rowBegin_10")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells("R$rowBegin_10:S$rowBegin_10");
        $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin_11",
            '50%')->getStyle("R$rowBegin_11")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin_11",
            '100%')->getStyle("S$rowBegin_11")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin_10",
            'Không hỗ trợ')->getStyle("T$rowBegin_10")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin_11",
            '')->getStyle("T$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells("T$rowBegin_10:T$rowBegin_11");
        $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin_9",
            'Không phép')->getStyle("U$rowBegin_9")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin_10",
            '')->getStyle("U$rowBegin_10")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin_11",
            '')->getStyle("U$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells("U$rowBegin_9:U$rowBegin_11");
        $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin",
            'Tổng GIỜ CÔNG CHÍNH được tính lương Để áp vào cột (5)')->getStyle("V$rowBegin")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin_9",
            '')->getStyle("V$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin_10",
            '')->getStyle("V$rowBegin_10")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin_11",
            '')->getStyle("V$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells('V' . $rowBegin . ':V' . ($rowBegin + 3));
        $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin",
            'Tổng giờ công được tính lương đã bao gồm tăng ca, phép năm ')->getStyle("W$rowBegin")->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin_9",
            '')->getStyle("W$rowBegin_9")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin_10",
            '')->getStyle("W$rowBegin_10")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin_11",
            '')->getStyle("W$rowBegin_11")->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->mergeCells('W' . $rowBegin . ':W' . ($rowBegin + 3));


        $this->db->select('tblstaff.staffid as staffid,tblstaff.firstname as name');
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        $this->db->where('type_staff', 2);
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        if (!empty($staff)) {
            $this->db->where('tbl_timekeeping_detail.staff_id', $staff);
        }
        if (!empty($department)) {
            $staffDepartments = "(
                SELECT
                    tblstaff_departments.staffid as staffid
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid  = tblstaff.staffid AND tblstaff_departments.departmentid = $department
            )";
            $this->db->where("exists ($staffDepartments)");
        }
        $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
        $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
        $this->db->join('tbl_timekeeping_detail_hour',
            'tbl_timekeeping_detail_hour.timekeeping_detail_id= tbl_timekeeping_detail.id', 'left');
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $personnel = $this->db->get()->result_array();
        $rowBegin += 4;
        if (!empty($personnel)) {
            $iSTT = 1;
            foreach ($personnel as $key => $value) {
                $sttC = 2;
                $personnel_id = $value['staffid'];
                $this->db->select('tbldepartments.name as name_departments ');
                $this->db->from('tblstaff_departments');
                $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblstaff_departments.departmentid ',
                    'left');
                $this->db->where('tblstaff_departments.staffid', $value['staffid']);
                $department = $this->db->get()->row_array();
                $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin",
                    $iSTT)->getStyle('A' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin",
                    $value['name'])->getStyle('B' . $rowBegin)->applyFromArray($styleTd_left);
                $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin",
                    $department['name_departments'])->getStyle('C' . $rowBegin)->applyFromArray($styleTd_left);
                $iSTT++;


                $count_hour_detail_1 = 0;
                $count_hour_detail_2 = 0;
                $count_hour_detail_3 = 0;
                $count_hour_detail_3_sun = 0;
                $count_hour_detail_4 = 0;
                $count_hour_detail_4_sun = 0;

                //new
                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail_count_hour');
                $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
                $this->db->where('(tbl_timekeeping_detail_count_hour.type_check = 3)');
                $this->db->where('tbl_timekeeping_detail.check_sun = 1');
                $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%")');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('EXISTS (
                    SELECT tbl_tamp.timekeeping_detail_id
                    FROM tbl_timekeeping_detail_count_hour tbl_tamp
                    WHERE tbl_tamp.timekeeping_detail_id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id_old
                    AND tbl_timekeeping_detail_count_hour.type_check = 3
                )');
                $count_hour_detail_new = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_new)) {
                    $count_hour_detail_new = '0';
                }

                $this->db->select('SUM(count_hour) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'O_C_BHXH');
                $count_hour_detail_o_c_bhxh_new = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_o_c_bhxh_new)) {
                    $count_hour_detail_o_c_bhxh_new = '0';
                }

                $this->db->select('SUM(count_hour) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'O_K_BHXH');
                $count_hour_detail_o_k_bhxh_new = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_o_k_bhxh_new)) {
                    $count_hour_detail_o_k_bhxh_new = '0';
                }

                $this->db->select('SUM(count_hour) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'Ro_HT_50');
                $count_hour_detail_ro_ht_50_new = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_ro_ht_50_new)) {
                    $count_hour_detail_ro_ht_50_new = '0';
                }
                //end

                $this->db->select('COUNT(*) as `count_hour`');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'Ro-50%');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $count_hour_detail_50_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_50 = 0;
                if ($count_hour_detail_50_db > 0) {
                    $count_hour_detail_50 = ($count_hour_detail_50_db * 8) / 2;
                }


                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail_count_hour');
                $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 1');
                $this->db->where('tbl_timekeeping_detail.check_sun = 0');
                $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%" OR tbl_timekeeping_detail.type = "P/2")');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                $count_hour_detail_1 = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_1)) {
                    $count_hour_detail_1 = '0';
                }

                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail_count_hour');
                $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 1');
                $this->db->where('tbl_timekeeping_detail.check_sun = 0');
                $this->db->where('tbl_timekeeping_detail.type !=', 'Ro-TR');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                $count_hour_detail_1_late = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_1_late = '0';
                if (empty($count_hour_detail_1_late)) {
                    $count_hour_detail_1_late = '0';
                }

                $count_hour_detail_1_total = countHourDetail($count_hour_detail_1, $count_hour_detail_1_late);

                $count_hour_detail_1_total = countHourDetail($count_hour_detail_1_total,
                    $count_hour_detail_o_c_bhxh_new);

                $count_hour_detail_1_total = countHourDetail($count_hour_detail_1_total,
                    $count_hour_detail_o_k_bhxh_new);

                $count_hour_detail_1_total = countHourDetail($count_hour_detail_1_total,
                    $count_hour_detail_ro_ht_50_new);

                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail_count_hour');
                $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 2');
                $this->db->where('tbl_timekeeping_detail.check_sun = 0');
                $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%" OR tbl_timekeeping_detail.type = "P/2" OR tbl_timekeeping_detail.type = "P")');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                $count_hour_detail_2 = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_2)) {
                    $count_hour_detail_2 = '0';
                }
                $count_hour_detail_2 = countHourDetail(0, $count_hour_detail_2);

                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail_count_hour');
                $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 3');
                $this->db->where('tbl_timekeeping_detail.check_sun = 0');
                $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%"  OR tbl_timekeeping_detail.type = "P/2" OR tbl_timekeeping_detail.type = "P")');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                $count_hour_detail_3 = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_3)) {
                    $count_hour_detail_3 = '0';
                }
                $count_hour_detail_3 = countHourDetail(0, $count_hour_detail_3);

                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail_count_hour');
                $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 1');
                $this->db->where('tbl_timekeeping_detail.check_sun = 1');
                $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%")');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                $count_hour_detail_3_sun = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_3_sun)) {
                    $count_hour_detail_3_sun = '0';
                }
                $total_count_hour_detail_3 = countHourDetail($count_hour_detail_3, $count_hour_detail_3_sun);

                $total_count_hour_detail_3 = countHourDetail($total_count_hour_detail_3, $count_hour_detail_new);

                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail_count_hour');
                $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 4');
                $this->db->where('(tbl_timekeeping_detail.check_sun = 0 OR tbl_timekeeping_detail.check_sun = 1)');
                $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%")');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
                $count_hour_detail_4 = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_4)) {
                    $count_hour_detail_4 = '0';
                }
                $count_hour_detail_4 = countHourDetail(0, $count_hour_detail_4);

                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail_count_hour');
                $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
                $this->db->where('(tbl_timekeeping_detail_count_hour.type_check = 2 OR tbl_timekeeping_detail_count_hour.type_check = 3)');
                $this->db->where('tbl_timekeeping_detail.check_sun = 1');
                $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%")');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $count_hour_detail_4_sun = $this->db->get()->row_array()['count_hour'];
                if (empty($count_hour_detail_4_sun)) {
                    $count_hour_detail_4_sun = '0';
                }

                $total_count_hour_detail_4 = countHourDetail($count_hour_detail_4, $count_hour_detail_4_sun);

                $total_count_hour_detail_4 = countHourDetailNew($total_count_hour_detail_4, $count_hour_detail_new);

                $total_hour_detail = $count_hour_detail_50 + $count_hour_detail_1_total + $count_hour_detail_2 + $total_count_hour_detail_3 + $total_count_hour_detail_4;

                if ($total_hour_detail == 0) {
                    $total_hour_detail = '';
                }

                if ($count_hour_detail_50 == 0) {
                    $count_hour_detail_50 = '';
                }

                //phep nam

                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'P');
                $count_hour_detail_phep_nam_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_phep_nam = 0;
                $count_hour_detail_phep_nam = ($count_hour_detail_phep_nam_db * 8);


                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'P/2');
                $count_hour_detail_phep_nam_db_50 = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_phep_nam_50 = 0;
                $count_hour_detail_phep_nam_50 = ($count_hour_detail_phep_nam_db_50 * 4);

                $count_hour_detail_phep_nam = $count_hour_detail_phep_nam + $count_hour_detail_phep_nam_50;

                if ($count_hour_detail_phep_nam == 0) {
                    $count_hour_detail_phep_nam = '';
                }
                //end
                //phan com
                $this->db->select('COUNT(*) as count_rice');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.count_rice ', 1);
                $count_hour_detail_rice = $this->db->get()->row_array()['count_rice'];
                if ($count_hour_detail_rice == 0) {
                    $count_hour_detail_rice = '';
                }
                //end

                //di tre
                $this->db->select('COUNT(*) as count_late');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.count_late ', 1);
                $count_hour_detail_late = $this->db->get()->row_array()['count_late'];
                if ($count_hour_detail_late == 0) {
                    $count_hour_detail_late = '';
                }
                //end
                //khong phep

                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'KP');
                $count_hour_detail_kp_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_kp = 0;
                $count_hour_detail_kp = ($count_hour_detail_kp_db * 8);
                if ($count_hour_detail_kp == 0) {
                    $count_hour_detail_kp = '';
                }
                //end
                // R
                $this->db->select('SUM(tbl_timekeeping_detail_note.value) as count_hour');
                $this->db->from('tbl_timekeeping_detail_note');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail_note.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail_note.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_note.type ', 'R');
                $this->db->group_by('staff_id,type');
                $count_hour_detail_r_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_r = 0;
                $count_hour_detail_r = ($count_hour_detail_r_db * 8);

                //end
                //O_C_BHXH
                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'O_C_BHXH');
                $count_hour_detail_o_c_bhxh_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_o_c_bhxh = 0;
                $count_hour_detail_o_c_bhxh = ($count_hour_detail_o_c_bhxh_db * 8);
                $count_hour_detail_o_c_bhxh = countHourDetailNew($count_hour_detail_o_c_bhxh,
                    $count_hour_detail_o_c_bhxh_new);

                if ($count_hour_detail_o_c_bhxh < 0) {
                    $count_hour_detail_o_c_bhxh = 0;
                }
                //end
                // o_k_bhxh
                $this->db->select('SUM(tbl_timekeeping_detail_note.value) as count_hour');
                $this->db->from('tbl_timekeeping_detail_note');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail_note.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail_note.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_note.type ', 'O_K_BHXH');
                $this->db->group_by('staff_id,type');
                $count_hour_detail_o_k_bhxh_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_o_k_bhxh = 0;
                $count_hour_detail_o_k_bhxh = ($count_hour_detail_o_k_bhxh_db * 8);
                $count_hour_detail_o_k_bhxh = countHourDetailNew($count_hour_detail_o_k_bhxh,
                    $count_hour_detail_o_k_bhxh_new);
                if ($count_hour_detail_o_k_bhxh < 0) {
                    $count_hour_detail_o_k_bhxh = 0;
                }
                //end
                // RO_HT_50
                $this->db->select('SUM(tbl_timekeeping_detail_note.value) as count_hour');
                $this->db->from('tbl_timekeeping_detail_note');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail_note.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail_note.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail_note.type ', 'Ro_HT_50');
                $this->db->group_by('staff_id,type');
                $count_hour_detail_ro_ht_50_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_ro_ht_50 = 0;
                $count_hour_detail_ro_ht_50 = ($count_hour_detail_ro_ht_50_db * 8) / 2;
                $count_hour_detail_ro_ht_50 = countHourDetailNew($count_hour_detail_ro_ht_50,
                    $count_hour_detail_ro_ht_50_new);
                if ($count_hour_detail_ro_ht_50 < 0) {
                    $count_hour_detail_ro_ht_50 = 0;
                }
                //end
                //RO_BS_50
                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'Ro_BS_50');
                $count_hour_detail_ro_bs_50_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_ro_bs_50 = 0;
                $count_hour_detail_ro_bs_50 = ($count_hour_detail_ro_bs_50_db * 8) / 2;

                //end
                //RO_BS_100
                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'Ro_BS_100');
                $count_hour_detail_ro_bs_100_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_ro_bs_100 = 0;
                $count_hour_detail_ro_bs_100 = ($count_hour_detail_ro_bs_100_db * 8);

                //end
                //Ro - Không hỗ trợ tính lương
                $count_hour_detail_ro = 0;
                $this->db->select('COALESCE(SUM(tbl_timekeeping_detail.count_hour_late),"0")  as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'Ro');
                $this->db->where('tbl_timekeeping_detail.count_hour_late !=0 ');
                $count_hour_detail_ro_db = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_ro = $count_hour_detail_ro_db;

                $this->db->select('COUNT(*) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                    'left');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
                $this->db->where('tbl_timekeeping_detail.type ', 'Ro');
                $this->db->where('tbl_timekeeping_detail.count_hour_late', 0);
                $count_hour_detail_ro_db_new = $this->db->get()->row_array()['count_hour'];
                $count_hour_detail_ro += ($count_hour_detail_ro_db_new * 8);

                //end

                //tong gio cong chinh
                $total_count_working_hour = 0;
                $total_count_working_hour = ($count_hour_detail_o_k_bhxh + $count_hour_detail_r + $count_hour_detail_1_total) + (($count_hour_detail_ro_ht_50 + $count_hour_detail_ro_bs_50) * 50 / 100) + (($count_hour_detail_ro_bs_100) * 100 / 100);
                //
                // tong gio cong chinh + tang ca
                $total_count_working_hour_overtime = 0;
                $total_count_working_hour_overtime = $count_hour_detail_2 + $total_count_hour_detail_3 + $total_count_hour_detail_4 + $total_count_working_hour;
                //end
                if ($total_count_working_hour_overtime == 0) {
                    $total_count_working_hour_overtime = '';
                }

                if ($total_count_working_hour == 0) {
                    $total_count_working_hour = '';
                }

                if ($total_count_hour_detail_3 == 0) {
                    $total_count_hour_detail_3 = '';
                }

                if ($total_count_hour_detail_4 == 0) {
                    $total_count_hour_detail_4 = '';
                }

                if ($count_hour_detail_o_k_bhxh == 0) {
                    $count_hour_detail_o_k_bhxh = '';
                }

                if ($count_hour_detail_o_c_bhxh == 0) {
                    $count_hour_detail_o_c_bhxh = '';
                }

                if ($count_hour_detail_r == 0) {
                    $count_hour_detail_r = '';
                }

                if ($count_hour_detail_1_total == 0) {
                    $count_hour_detail_1_total = '';
                }
                //end

                //Tổng giò nghỉ
                $total_hour_break = $count_hour_detail_ro_ht_50 + $count_hour_detail_ro_bs_50 + $count_hour_detail_ro_bs_100 + $count_hour_detail_ro;
                //end
                if ($total_hour_break == 0) {
                    $total_hour_break = '';
                }
                if ($count_hour_detail_ro_ht_50 == 0) {
                    $count_hour_detail_ro_ht_50 = '';
                }
                if ($count_hour_detail_ro_bs_50 == 0) {
                    $count_hour_detail_ro_bs_50 = '';
                }
                if ($count_hour_detail_ro_bs_100 == 0) {
                    $count_hour_detail_ro_bs_100 = '';
                }
                if ($count_hour_detail_ro == 0) {
                    $count_hour_detail_ro = '';
                }
                if ($count_hour_detail_2 == 0) {
                    $count_hour_detail_2 = '';
                }

                $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin",
                    $count_hour_detail_50)->getStyle('D' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin",
                    $count_hour_detail_1_total)->getStyle('E' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin",
                    $count_hour_detail_2)->getStyle('F' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin",
                    $total_count_hour_detail_3)->getStyle('G' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",
                    $total_count_hour_detail_4)->getStyle('H' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin",
                    $total_hour_detail)->getStyle('I' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin",
                    $count_hour_detail_phep_nam)->getStyle('J' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin",
                    $count_hour_detail_rice)->getStyle('K' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin",
                    $count_hour_detail_late)->getStyle('L' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin",
                    $count_hour_detail_r)->getStyle('M' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin",
                    $count_hour_detail_o_c_bhxh)->getStyle('N' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin",
                    $count_hour_detail_o_k_bhxh)->getStyle('O' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin",
                    $total_hour_break)->getStyle('P' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin",
                    $count_hour_detail_ro_ht_50)->getStyle('Q' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin",
                    $count_hour_detail_ro_bs_50)->getStyle('R' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin",
                    $count_hour_detail_ro_bs_100)->getStyle('S' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin",
                    $count_hour_detail_ro)->getStyle('T' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin",
                    $count_hour_detail_kp)->getStyle('U' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin",
                    $total_count_working_hour)->getStyle('V' . $rowBegin)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin",
                    $total_count_working_hour_overtime)->getStyle('W' . $rowBegin)->applyFromArray($styleTd_center);

                $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(-1);
                $rowBegin++;
            }

        }


        $filename = lang('tnh_sale_listing') . '.xls';
        $objPHPExcel->getActiveSheet()->freezePane('A1');


        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="TONG_HOP_GIO_CONG_NHAN_VIEN.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        die();
    }

    public function print_pdf_synthetic_timekeeping()
    {
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $id_department = $department = $this->input->get('department');
        $staff = $this->input->get('staff');

        $arrId = explode(',', $staffid);
        ob_end_clean();
        $data = [];


        $this->db->select('tblstaff.staffid as staffid,tblstaff.firstname as name');
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        $this->db->where('type_staff', 2);
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        if (!empty($staff)) {
            $this->db->where('tbl_timekeeping_detail.staff_id', $staff);
        }
        if (!empty($department)) {
            $staffDepartments = "(
                SELECT
                    tblstaff_departments.staffid as staffid
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid  = tblstaff.staffid AND tblstaff_departments.departmentid = $department
            )";
            $this->db->where("exists ($staffDepartments)");
        }
        $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
        $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
        $this->db->join('tbl_timekeeping_detail_hour',
            'tbl_timekeeping_detail_hour.timekeeping_detail_id= tbl_timekeeping_detail.id', 'left');
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $personnel = $this->db->get()->result_array();
        $htmlBody = '';
        foreach ($personnel as $key => $value) {
            $personnel_id = $value['staffid'];
            $this->db->select('tbldepartments.name as name_departments ');
            $this->db->from('tblstaff_departments');
            $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblstaff_departments.departmentid ',
                'left');
            $this->db->where('tblstaff_departments.staffid', $value['staffid']);
            $department = $this->db->get()->row_array();

            //new
            $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
            $this->db->from('tbl_timekeeping_detail_count_hour');
            $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
            $this->db->where('(tbl_timekeeping_detail_count_hour.type_check = 3)');
            $this->db->where('tbl_timekeeping_detail.check_sun = 1');
            $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%")');
            $this->db->join('tbl_timekeeping_detail',
                'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where('EXISTS (
                    SELECT tbl_tamp.timekeeping_detail_id
                    FROM tbl_timekeeping_detail_count_hour tbl_tamp
                    WHERE tbl_tamp.timekeeping_detail_id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id_old
                    AND tbl_timekeeping_detail_count_hour.type_check = 3
                )');
            $count_hour_detail_new = $this->db->get()->row_array()['count_hour'];
            if (empty($count_hour_detail_new)) {
                $count_hour_detail_new = '0';
            }

            $this->db->select('SUM(count_hour) as count_hour');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail.type ', 'O_C_BHXH');
            $count_hour_detail_o_c_bhxh_new = $this->db->get()->row_array()['count_hour'];
            if (empty($count_hour_detail_o_c_bhxh_new)) {
                $count_hour_detail_o_c_bhxh_new = '0';
            }

            $this->db->select('SUM(count_hour) as count_hour');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail.type ', 'O_K_BHXH');
            $count_hour_detail_o_k_bhxh_new = $this->db->get()->row_array()['count_hour'];
            if (empty($count_hour_detail_o_k_bhxh_new)) {
                $count_hour_detail_o_k_bhxh_new = '0';
            }

            $this->db->select('SUM(count_hour) as count_hour');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail.type ', 'Ro_HT_50');
            $count_hour_detail_ro_ht_50_new = $this->db->get()->row_array()['count_hour'];
            if (empty($count_hour_detail_ro_ht_50_new)) {
                $count_hour_detail_ro_ht_50_new = '0';
            }
            //end

            $this->db->select('COUNT(*) as `count_hour`');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail.type ', 'Ro-50%');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $count_hour_detail_50_db = $this->db->get()->row_array()['count_hour'];
            $count_hour_detail_50 = 0;
            if ($count_hour_detail_50_db > 0) {
                $count_hour_detail_50 = ($count_hour_detail_50_db * 8) / 2;
            }
            $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
            $this->db->from('tbl_timekeeping_detail_count_hour');
            $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 1');
            $this->db->where('tbl_timekeeping_detail.check_sun = 0');
            $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%" OR tbl_timekeeping_detail.type = "P/2")');
            $this->db->join('tbl_timekeeping_detail',
                'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
            $count_hour_detail_1 = $this->db->get()->row_array()['count_hour'];
            if (empty($count_hour_detail_1)) {
                $count_hour_detail_1 = '0';
            }
            $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
            $this->db->from('tbl_timekeeping_detail_count_hour');
            $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 1');
            $this->db->where('tbl_timekeeping_detail.check_sun = 0');
            $this->db->where('tbl_timekeeping_detail.type !=', 'Ro-TR');
            $this->db->join('tbl_timekeeping_detail',
                'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
            $count_hour_detail_1_late = $this->db->get()->row_array()['count_hour'];
            $count_hour_detail_1_late = '0';
            if (empty($count_hour_detail_1_late)) {
                $count_hour_detail_1_late = '0';
            }
            $count_hour_detail_1_total = countHourDetail($count_hour_detail_1, $count_hour_detail_1_late);

            $count_hour_detail_1_total = countHourDetail($count_hour_detail_1_total, $count_hour_detail_o_c_bhxh_new);

            $count_hour_detail_1_total = countHourDetail($count_hour_detail_1_total, $count_hour_detail_o_k_bhxh_new);

            $count_hour_detail_1_total = countHourDetail($count_hour_detail_1_total, $count_hour_detail_ro_ht_50_new);

            $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
            $this->db->from('tbl_timekeeping_detail_count_hour');
            $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 2');
            $this->db->where('tbl_timekeeping_detail.check_sun = 0');
            $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%" OR tbl_timekeeping_detail.type = "P/2" OR tbl_timekeeping_detail.type = "P")');
            $this->db->join('tbl_timekeeping_detail',
                'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
            $count_hour_detail_2 = $this->db->get()->row_array()['count_hour'];
            if (empty($count_hour_detail_2)) {
                $count_hour_detail_2 = '0';
            }
            $count_hour_detail_2 = countHourDetail(0, $count_hour_detail_2);
            $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
            $this->db->from('tbl_timekeeping_detail_count_hour');
            $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 3');
            $this->db->where('tbl_timekeeping_detail.check_sun = 0');
            $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%"  OR tbl_timekeeping_detail.type = "P/2" OR tbl_timekeeping_detail.type = "P")');
            $this->db->join('tbl_timekeeping_detail',
                'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
            $count_hour_detail_3 = $this->db->get()->row_array()['count_hour'];
            if (empty($count_hour_detail_3)) {
                $count_hour_detail_3 = '0';
            }
            $count_hour_detail_3 = countHourDetail(0, $count_hour_detail_3);
            $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
            $this->db->from('tbl_timekeeping_detail_count_hour');
            $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 1');
            $this->db->where('tbl_timekeeping_detail.check_sun = 1');
            $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%")');
            $this->db->join('tbl_timekeeping_detail',
                'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
            $count_hour_detail_3_sun = $this->db->get()->row_array()['count_hour'];
            if (empty($count_hour_detail_3_sun)) {
                $count_hour_detail_3_sun = '0';
            }
            $total_count_hour_detail_3 = countHourDetail($count_hour_detail_3, $count_hour_detail_3_sun);

            $total_count_hour_detail_3 = countHourDetail($total_count_hour_detail_3, $count_hour_detail_new);

            $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
            $this->db->from('tbl_timekeeping_detail_count_hour');
            $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail_count_hour.type_check = 4');
            $this->db->where('(tbl_timekeeping_detail.check_sun = 0 OR tbl_timekeeping_detail.check_sun = 1)');
            $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%")');
            $this->db->join('tbl_timekeeping_detail',
                'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->group_by('tbl_timekeeping_detail_count_hour.type_check');
            $count_hour_detail_4 = $this->db->get()->row_array()['count_hour'];
            if (empty($count_hour_detail_4)) {
                $count_hour_detail_4 = '0';
            }
            $count_hour_detail_4 = countHourDetail(0, $count_hour_detail_4);
            $this->db->select('COALESCE(SUM(tbl_timekeeping_detail_count_hour.count_hour),"0")  as count_hour');
            $this->db->from('tbl_timekeeping_detail_count_hour');
            $this->db->where('tbl_timekeeping_detail_count_hour.staff_id', $value['staffid']);
            $this->db->where('(tbl_timekeeping_detail_count_hour.type_check = 2 OR tbl_timekeeping_detail_count_hour.type_check = 3)');
            $this->db->where('tbl_timekeeping_detail.check_sun = 1');
            $this->db->where('(tbl_timekeeping_detail.type = "X" OR tbl_timekeeping_detail.type = "Ro-TR" OR tbl_timekeeping_detail.type = "Ro" OR tbl_timekeeping_detail.type = "Ro-50%")');
            $this->db->join('tbl_timekeeping_detail',
                'tbl_timekeeping_detail.id = tbl_timekeeping_detail_count_hour.timekeeping_detail_id', 'left');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $count_hour_detail_4_sun = $this->db->get()->row_array()['count_hour'];
            if (empty($count_hour_detail_4_sun)) {
                $count_hour_detail_4_sun = '0';
            }
            $total_count_hour_detail_4 = countHourDetail($count_hour_detail_4, $count_hour_detail_4_sun);

            $total_count_hour_detail_4 = countHourDetailNew($total_count_hour_detail_4, $count_hour_detail_new);

            $total_hour_detail = $count_hour_detail_50 + $count_hour_detail_1_total + $count_hour_detail_2 + $total_count_hour_detail_3 + $total_count_hour_detail_4;
            if ($total_hour_detail == 0) {
                $total_hour_detail = '';
            }
            if ($count_hour_detail_50 == 0) {
                $count_hour_detail_50 = '';
            }
            //phep nam
            $this->db->select('COUNT(*) as count_hour');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail.type ', 'P');
            $count_hour_detail_phep_nam_db = $this->db->get()->row_array()['count_hour'];
            $count_hour_detail_phep_nam = 0;
            $count_hour_detail_phep_nam = ($count_hour_detail_phep_nam_db * 8);
            $this->db->select('COUNT(*) as count_hour');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail.type ', 'P/2');
            $count_hour_detail_phep_nam_db_50 = $this->db->get()->row_array()['count_hour'];
            $count_hour_detail_phep_nam_50 = 0;
            $count_hour_detail_phep_nam_50 = ($count_hour_detail_phep_nam_db_50 * 4);
            $count_hour_detail_phep_nam = $count_hour_detail_phep_nam + $count_hour_detail_phep_nam_50;
            if ($count_hour_detail_phep_nam == 0) {
                $count_hour_detail_phep_nam = '';
            }
            //end
            //phan com
            $this->db->select('COUNT(*) as count_rice');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail.count_rice ', 1);
            $count_hour_detail_rice = $this->db->get()->row_array()['count_rice'];
            if ($count_hour_detail_rice == 0) {
                $count_hour_detail_rice = '';
            }
            //end
            //di tre
            $this->db->select('COUNT(*) as count_late');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail.count_late ', 1);
            $count_hour_detail_late = $this->db->get()->row_array()['count_late'];
            if ($count_hour_detail_late == 0) {
                $count_hour_detail_late = '';
            }
            //end
            //khong phep
            $this->db->select('COUNT(*) as count_hour');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail.type ', 'KP');
            $count_hour_detail_kp_db = $this->db->get()->row_array()['count_hour'];
            $count_hour_detail_kp = 0;
            $count_hour_detail_kp = ($count_hour_detail_kp_db * 8);
            if ($count_hour_detail_kp == 0) {
                $count_hour_detail_kp = '';
            }
            //end
            // R
            $this->db->select('SUM(tbl_timekeeping_detail_note.value) as count_hour');
            $this->db->from('tbl_timekeeping_detail_note');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail_note.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where('tbl_timekeeping_detail_note.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail_note.type ', 'R');
            $this->db->group_by('staff_id,type');
            $count_hour_detail_r_db = $this->db->get()->row_array()['count_hour'];
            $count_hour_detail_r = 0;
            $count_hour_detail_r = ($count_hour_detail_r_db * 8);
            //end
            //O_C_BHXH
            $this->db->select('COUNT(*) as count_hour');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail.type ', 'O_C_BHXH');
            $count_hour_detail_o_c_bhxh_db = $this->db->get()->row_array()['count_hour'];
            $count_hour_detail_o_c_bhxh = 0;
            $count_hour_detail_o_c_bhxh = ($count_hour_detail_o_c_bhxh_db * 8);
            $count_hour_detail_o_c_bhxh = countHourDetailNew($count_hour_detail_o_c_bhxh,
                $count_hour_detail_o_c_bhxh_new);

            if ($count_hour_detail_o_c_bhxh < 0) {
                $count_hour_detail_o_c_bhxh = 0;
            }
            //end
            // o_k_bhxh
            $this->db->select('SUM(tbl_timekeeping_detail_note.value) as count_hour');
            $this->db->from('tbl_timekeeping_detail_note');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail_note.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where('tbl_timekeeping_detail_note.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail_note.type ', 'O_K_BHXH');
            $this->db->group_by('staff_id,type');
            $count_hour_detail_o_k_bhxh_db = $this->db->get()->row_array()['count_hour'];
            $count_hour_detail_o_k_bhxh = 0;
            $count_hour_detail_o_k_bhxh = ($count_hour_detail_o_k_bhxh_db * 8);

            $count_hour_detail_o_k_bhxh = countHourDetailNew($count_hour_detail_o_k_bhxh,
                $count_hour_detail_o_k_bhxh_new);
            if ($count_hour_detail_o_k_bhxh < 0) {
                $count_hour_detail_o_k_bhxh = 0;
            }
            //end
            // RO_HT_50
            $this->db->select('SUM(tbl_timekeeping_detail_note.value) as count_hour');
            $this->db->from('tbl_timekeeping_detail_note');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail_note.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where('tbl_timekeeping_detail_note.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail_note.type ', 'Ro_HT_50');
            $this->db->group_by('staff_id,type');
            $count_hour_detail_ro_ht_50_db = $this->db->get()->row_array()['count_hour'];
            $count_hour_detail_ro_ht_50 = 0;
            $count_hour_detail_ro_ht_50 = ($count_hour_detail_ro_ht_50_db * 8) / 2;

            $count_hour_detail_ro_ht_50 = countHourDetailNew($count_hour_detail_ro_ht_50,
                $count_hour_detail_ro_ht_50_new);
            if ($count_hour_detail_ro_ht_50 < 0) {
                $count_hour_detail_ro_ht_50 = 0;
            }
            //end
            //RO_BS_50
            $this->db->select('COUNT(*) as count_hour');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail.type ', 'Ro_BS_50');
            $count_hour_detail_ro_bs_50_db = $this->db->get()->row_array()['count_hour'];
            $count_hour_detail_ro_bs_50 = 0;
            $count_hour_detail_ro_bs_50 = ($count_hour_detail_ro_bs_50_db * 8) / 2;
            //end
            //RO_BS_100
            $this->db->select('COUNT(*) as count_hour');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail.type ', 'Ro_BS_100');
            $count_hour_detail_ro_bs_100_db = $this->db->get()->row_array()['count_hour'];
            $count_hour_detail_ro_bs_100 = 0;
            $count_hour_detail_ro_bs_100 = ($count_hour_detail_ro_bs_100_db * 8);
            //end
            //Ro - Không hỗ trợ tính lương
            $count_hour_detail_ro = 0;
            $this->db->select('COALESCE(SUM(tbl_timekeeping_detail.count_hour_late),"0")  as count_hour');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail.type ', 'Ro');
            $this->db->where('tbl_timekeeping_detail.count_hour_late !=0 ');
            $count_hour_detail_ro_db = $this->db->get()->row_array()['count_hour'];
            $count_hour_detail_ro = $count_hour_detail_ro_db;
            $this->db->select('COUNT(*) as count_hour');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id',
                'left');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where('tbl_timekeeping_detail.staff_id', $value['staffid']);
            $this->db->where('tbl_timekeeping_detail.type ', 'Ro');
            $this->db->where('tbl_timekeeping_detail.count_hour_late', 0);
            $count_hour_detail_ro_db_new = $this->db->get()->row_array()['count_hour'];
            $count_hour_detail_ro += ($count_hour_detail_ro_db_new * 8);
            //end
            //tong gio cong chinh
            $total_count_working_hour = 0;
            $total_count_working_hour = ($count_hour_detail_o_k_bhxh + $count_hour_detail_r + $count_hour_detail_1_total) + (($count_hour_detail_ro_ht_50 + $count_hour_detail_ro_bs_50) * 50 / 100) + (($count_hour_detail_ro_bs_100) * 100 / 100);
            //
            // tong gio cong chinh + tang ca
            $total_count_working_hour_overtime = 0;
            $total_count_working_hour_overtime = $count_hour_detail_2 + $total_count_hour_detail_3 + $total_count_hour_detail_4 + $total_count_working_hour;
            //end
            if ($total_count_working_hour_overtime == 0) {
                $total_count_working_hour_overtime = '';
            }
            if ($total_count_working_hour == 0) {
                $total_count_working_hour = '';
            }
            if ($total_count_hour_detail_3 == 0) {
                $total_count_hour_detail_3 = '';
            }
            if ($total_count_hour_detail_4 == 0) {
                $total_count_hour_detail_4 = '';
            }
            if ($count_hour_detail_o_k_bhxh == 0) {
                $count_hour_detail_o_k_bhxh = '';
            }
            if ($count_hour_detail_o_c_bhxh == 0) {
                $count_hour_detail_o_c_bhxh = '';
            }
            if ($count_hour_detail_r == 0) {
                $count_hour_detail_r = '';
            }
            if ($count_hour_detail_1_total == 0) {
                $count_hour_detail_1_total = '';
            }
            //end
            //Tổng giò nghỉ
            $total_hour_break = $count_hour_detail_ro_ht_50 + $count_hour_detail_ro_bs_50 + $count_hour_detail_ro_bs_100 + $count_hour_detail_ro;
            //end
            if ($total_hour_break == 0) {
                $total_hour_break = '';
            }
            if ($count_hour_detail_ro_ht_50 == 0) {
                $count_hour_detail_ro_ht_50 = '';
            }
            if ($count_hour_detail_ro_bs_50 == 0) {
                $count_hour_detail_ro_bs_50 = '';
            }
            if ($count_hour_detail_ro_bs_100 == 0) {
                $count_hour_detail_ro_bs_100 = '';
            }
            if ($count_hour_detail_ro == 0) {
                $count_hour_detail_ro = '';
            }
            if ($count_hour_detail_2 == 0) {
                $count_hour_detail_2 = '';
            }

            $htmlBody .= '
				<tr>
					<td class="text-center">' . ($key + 1) . '</td>
					<td class="text-left" style="text-align: left;">' . $value['name'] . '</td>
					<td class="text-left" style="text-align: left;">' . $department['name_departments'] . '</td>
					<td class="text-center">' . $count_hour_detail_50 . '</td>
					<td class="text-center">' . $count_hour_detail_1_total . '</td>
					<td class="text-center">' . $count_hour_detail_2 . '</td>
					<td class="text-center">' . $total_count_hour_detail_3 . '</td>
					<td class="text-center">' . $total_count_hour_detail_4 . '</td>
					<td class="text-center">' . $total_hour_detail . '</td>
					<td class="text-center">' . $count_hour_detail_phep_nam . '</td>
					<td class="text-center">' . $count_hour_detail_rice . '</td>
					<td class="text-center">' . $count_hour_detail_late . '</td>
					<td class="text-center">' . $count_hour_detail_r . '</td>
					<td class="text-center">' . $count_hour_detail_o_c_bhxh . '</td>
					<td class="text-center">' . $count_hour_detail_o_k_bhxh . '</td>
					<td class="text-center">' . $total_hour_break . '</td>
					<td class="text-center">' . $count_hour_detail_ro_ht_50 . '</td>
					<td class="text-center">' . $count_hour_detail_ro_bs_50 . '</td>
					<td class="text-center">' . $count_hour_detail_ro_bs_100 . '</td>
					<td class="text-center">' . $count_hour_detail_ro . '</td>
					<td class="text-center">' . $count_hour_detail_kp . '</td>
					<td class="text-center">' . $total_count_working_hour . '</td>
					<td class="text-center">' . $total_count_working_hour_overtime . '</td>
				</tr>';
        }

        ob_start();
        stylePdf();
        $to_html = '';
        if (!empty($id_department)) {
            $this->db->where('departmentid', $id_department);
            $name_department = $this->db->get('tbldepartments')->row('name');
            if (!empty($name_department)) {
                $to_html = '<h3><b>Tổ: </b> ' . $name_department . '</h3>';
            }
        }

        echo '<br/><h2 class="text-center"><b style="color:#134490">TỔNG HỢP GIỜ CÔNG NHÂN VIÊN</b><h3 class="text-center">THÁNG ' . $month . ' NĂM ' . $year . '</h3></h2>';
        echo $to_html . '
			<table border="1" style="width: 100%;font-size: 9px;">
				<tr>
					<th rowspan="4" class="text-center" style="width: 3%;"><b>STT</b></th>
					<th rowspan="4" class="text-center" style="width: 10%;"><b>Họ Và Tên</b></th>
					<th rowspan="4" class="text-center" style="width: 6%;"><b>Đơn vị</b></th>
					<th colspan="7" class="text-center" style="width: 27%;"><b>Giờ công(Giờ)</b></th>
					<th rowspan="4" class="text-center" style="width: 4%;"><b>Số phần cơm</b></th>
					<th rowspan="4" class="text-center" style="width: 4%;"><b>Số lần đi trễ</b></th>
					<th colspan="9" class="text-center" style="width: 36%;"><b>Số giờ nghỉ</b></th>
					<th rowspan="4" class="text-center" style="width: 5%;"><b>Tổng giờ công chính được tính lương để áp vào cột (5)</b></th>
					<th rowspan="4" class="text-center" style="width: 5%;"><b>Tổng giờ công được tính lương đã bao gồm tăng ca, phép năm</b></th>
				</tr>
				<tr>
					<th rowspan="3" class="text-center" style="width: 3%;"><b>50%</b></th>
					<th rowspan="3" class="text-center" style="width: 4%;"><b>100%</b></th>
					<th rowspan="3" class="text-center" style="width: 4%;"><b>150%</b></th>
					<th rowspan="3" class="text-center" style="width: 4%;"><b>200%</b></th>
					<th rowspan="3" class="text-center" style="width: 4%;"><b>300%</b></th>
					<th rowspan="3" class="text-center" style="width: 4%;"><b>Tổng cộng</b></th>
					<th rowspan="3" class="text-center" style="width: 4%;"><b>Phép năm</b></th>
					<th rowspan="3" class="text-center" style="width: 4%;"><b>R</b></th>
					<th colspan="2" class="text-center" style="width: 8%;"><b>O</b></th>
					<th colspan="5" class="text-center" style="width: 20%;"><b>Ro Trong đó</b></th>
					<th rowspan="3" class="text-center" style="width: 4%;"><b>Không phép</b></th>
				</tr>
				<tr>
					<th rowspan="2" class="text-center" style="width: 4%;"><b>Có giấy nghỉ hưởng BHXH</b></th>
					<th rowspan="2" class="text-center" style="width: 4%;"><b>Không có giấy nghỉ hưởng BHXH</b></th>
					<th rowspan="2" class="text-center" style="width: 4%;"><b>Tổng giờ nghỉ</b></th>
					<th class="text-center" style="width: 4%;"><b>Được hỗ trợ theo quy định</b></th>
					<th colspan="2" class="text-center" style="width: 8%;"><b>Theo quyết định bổ sung</b></th>
					<th rowspan="2" class="text-center" style="width: 4%;"><b>Không hỗ trợ</b></th>
				</tr>
				<tr>
					<th class="text-center" style="width: 4%;"><b>50%</b></th>
					<th class="text-center" style="width: 4%;"><b>50%</b></th>
					<th class="text-center" style="width: 4%;"><b>100%</b></th>
				</tr>    
				' . $htmlBody . '
			</table><br/><br/>';

//		echo '<br/><br/><table width="100%;">
//				<tr>
//					<td style="width: 33%;" class="text-center"></td>
//					<td style="width: 33%;" class="text-center"></td>
//					<td style="width: 34%;" class="text-center"><b>Vĩnh Long, Ngày.....Tháng '.(!empty($month) ? (sprintf("%02s", $month)) : ".....").' Năm '.(!empty($year) ? $year : ".....").'</b><br/></td>
//				</tr>
//				<tr>
//					<td style="width: 33%;" class="text-center"><b>NGƯỜI LẬP</b><i><br/>(ký và ghi rõ họ tên)</i></td>
//					<td style="width: 33%;" class="text-center"><b>KẾ TOÁN</b><i><br/>(ký và ghi rõ họ tên)</i></td>
//					<td style="width: 34%;" class="text-center"><b>GIÁM ĐỐC</b><i><br/>(ký và ghi rõ họ tên)</i></td>
//				</tr>
//			</table>';
        footerPdf();


        $data['title'] = lang('print') . ' ' . lang('Danh sách nhân viên');
        $data['type'] = 'P';
        $data['img'] = '';


        $content = ob_get_contents();
        ob_end_clean();
//
        $data['content'] = $content;
//		$data['pageCustome'] = 'list_staff';
        $data['showHeader'] = 'hide';
        $data['showHeaderOne'] = true;
        $pdf = @print_pdf_dt_L($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function excel_average_vote()
    {
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $department = $this->input->get('department');
        $staff = $this->input->get('staff');
        if (!empty($department)) {
            $this->db->where('departmentid', $department);
            $name_department = $this->db->get('tbldepartments')->row('name');
        }

        $c_excel = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'AA',
            'AB',
            'AC',
            'AD',
            'AE',
            'AF',
            'AG',
            'AH',
            'AI',
            'AJ',
            'AK',
            'AL',
            'AM',
            'AN',
            'AO',
            'AP',
            'AQ',
            'AR',
            'AS',
            'AT',
            'AU',
            'AV',
            'AW',
            'AX',
            'AY',
            'AZ',
            'BA',
            'BB',
            'BC',
            'BD',
            'BE',
            'BF',
            'BG',
            'BH',
            'BI',
            'BJ',
            'BK',
            'BL',
            'BM',
            'BN',
            'BO',
            'BP',
            'BQ',
            'BR',
            'BS',
            'BT',
            'BU',
            'BV',
            'BW',
            'BX',
            'BY',
            'BZ',
            'CA',
            'CB',
            'CC',
            'CD',
            'CE',
            'CF',
            'CG',
            'CH',
            'CI',
            'CJ',
            'CK',
            'CL',
            'CM',
            'CN',
            'CO',
            'CP',
            'CQ',
            'CR',
            'CS',
            'CT',
            'CU',
            'CV',
            'CW',
            'CX',
            'CY',
            'CZ',
            'DA',
            'DB',
            'DC',
            'DD',
            'DE',
            'DF',
            'DG',
            'DH',
            'DI',
            'DJ',
            'DK',
            'DL',
            'DM',
            'DN',
            'DO',
            'DP',
            'DQ',
            'DR',
            'DS',
            'DT',
            'DU',
            'DV',
            'DW',
            'DX',
            'DY',
            'DZ'
        ];
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        ob_end_clean();
        $data = [];

//        $listDate = getAllDateInMonth($month, $year, 'd');
//        $widthHead = (79 / count($listDate)) . '%';

        $timekeepingId = 0;

        $styleTh = [
            'font' => array(
                'bold' => true,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )

        ];
        $styleTd = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];
        $styleTd_center = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];
        $styleTd_left = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];
        $styleTd_right = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];


        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);

        $company = get_option('invoice_company_name');
        $address = get_option('invoice_company_address');
        $phonenumber = get_option('invoice_company_phonenumber');
        $styleNone = [
            'font' => array(
                'size' => 13,
                'name' => 'Times New Roman'
            )
        ];

        $company_logo = get_option('company_logo');
        if (file_exists('uploads/company/' . $company_logo)) {
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath('uploads/company/' . $company_logo);
            $objDrawing->setCoordinates('A1');
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            $objPHPExcel->getActiveSheet()->getStyle("A1");
            $objDrawing->setOffsetX(5);
            $objDrawing->setOffsetY(5);
            $objDrawing->setResizeProportional(false);

            $objDrawing->setWidth(55);
            $objDrawing->setHeight(55);
        }

        $objPHPExcel->getActiveSheet()->mergeCells('B1:I1');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', $company)->getStyle('B1:I1')->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 14,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);

        $objPHPExcel->getActiveSheet()->mergeCells('B2:I2');
        $objPHPExcel->getActiveSheet()->setCellValue('B2', $address)->getStyle('B2:I2')->applyFromArray($styleNone);

        $objPHPExcel->getActiveSheet()->mergeCells('B3:I3');
        $objPHPExcel->getActiveSheet()->setCellValue('B3',
            'SĐT: ' . $phonenumber)->getStyle('B3:I3')->applyFromArray($styleNone);

        $objPHPExcel->getActiveSheet()->mergeCells('A5:H5');
        $objPHPExcel->getActiveSheet()->setCellValue('A5', 'BẢNG XÉT BÌNH BẦU')->getStyle('A5:H5')->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 25,
                'name' => 'Times New Roman',
                'color' => array('rgb' => 'ff0202'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);

        $objPHPExcel->getActiveSheet()->mergeCells('A6:H6');
        $objPHPExcel->getActiveSheet()->setCellValue('A6',
            ('THÁNG ' . $month . ' NĂM ' . $year))->getStyle("A6:H6")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 16,
                'name' => 'Times New Roman',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);
        $rowBegin = 8;
        $rowBeginNext = 9;
        if (!empty($name_department)) {
            $sttC = 3;
            $stt = 1;
            $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt + 7] . $rowBegin);
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                'Tổ: ' . $name_department)->getStyle($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt + 5] . $rowBegin)->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 14,
                    'name' => 'Times New Roman',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                )
            ]);
            $rowBegin++;
            $rowBeginNext++;
        }
        $sttC = 3;
        $stt = 0;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt] . $rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'STT')->getStyle($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt] . $rowBeginNext)->applyFromArray($styleTh);
        $stt++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt] . $rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Họ và Tên')->getStyle($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt] . $rowBeginNext)->applyFromArray($styleTh);
        $stt++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt] . $rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Bình Bầu Tháng Trước')->getStyle($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt] . $rowBeginNext)->applyFromArray($styleTh);
        $stt++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt] . $rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Số Lần Đi Trễ')->getStyle($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt] . $rowBeginNext)->applyFromArray($styleTh);
        $stt++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt] . $rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Số Giờ Không Phép')->getStyle($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt] . $rowBeginNext)->applyFromArray($styleTh);
        $stt++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt] . $rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Số Giờ Nghĩ RO Phép')->getStyle($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt] . $rowBeginNext)->applyFromArray($styleTh);
        $stt++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt + 1] . $rowBegin);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt] . $rowBegin,
            'Bình Bầu')->getStyle($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt + 1] . $rowBegin)->applyFromArray($styleTh);

        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt] . $rowBeginNext,
            'Tổ Chấm')->getStyle($c_excel[$stt] . $rowBeginNext)->applyFromArray($styleTh);
        $stt++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt] . $rowBeginNext,
            'BGĐ Duyệt')->getStyle($c_excel[$stt] . $rowBeginNext)->applyFromArray($styleTh);
        $stt++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt] . $rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt] . $rowBegin,
            'Nhận Xét')->getStyle($c_excel[$stt] . $rowBegin . ':' . $c_excel[$stt] . $rowBeginNext)->applyFromArray($styleTh);
        $stt++;


        $data = [];
        $this->db->select('tblstaff.staffid as staffid,tblstaff.firstname as name,tbl_average_vote_item.*');
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        $this->db->where('type_staff', 2);
        $this->db->where('tbl_average_vote.month', $month);
        $this->db->where('tbl_average_vote.year', $year);
        if (!empty($staff)) {
            $this->db->where('tbl_average_vote_item.staff_id', $staff);
        }
        if (!empty($department)) {
            $staffDepartments = "(
                SELECT
                    tblstaff_departments.staffid as staffid
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid  = tblstaff.staffid AND tblstaff_departments.departmentid = $department
            )";
            $this->db->where("exists ($staffDepartments)");
        }
        $this->db->join('tbl_average_vote_item', 'tbl_average_vote_item.staff_id= tblstaff.staffid', 'left');
        $this->db->join('tbl_average_vote', 'tbl_average_vote.id= tbl_average_vote_item.average_vote_id', 'left');
        $this->db->group_by('tbl_average_vote_item.staff_id');
        $AverageVoteItems = $this->db->get()->result_array();
        if (!empty($department)) {
            $this->db->where('departmentid', $department);
            $data_departments = $this->db->get('tbldepartments')->row();
        }
        $data['title'] = lang('print') . ' ' . lang('BẢNG XÉT BÌNH BẦU A,B,C') . $month . 'năm ' . $year;
        $data['type'] = 'P';
        $data['img'] = '';
        $bodyItems = '';
        $rowBegin++;
        $rowBegin++;
        if (!empty($AverageVoteItems)) {
            foreach ($AverageVoteItems as $key => $value) {
                $type = $value['average_vote'];
                $type_manager = $value['average_vote_manager'];
                $note = $value['note'];
                $name = $value['name'];
                $average_vote_old = $value['average_vote_old'];
                $count_late = $value['count_late'];
                $count_hour_ro = $value['count_hour_ro'];
                $count_hour_kp = $value['count_hour_kp'];
                if ($value['count_late'] == 0) {
                    $count_late = '';
                }
                if ($value['count_hour_ro'] == 0) {
                    $count_hour_ro = '';
                }
                if ($value['count_hour_kp'] == 0) {
                    $count_hour_kp = '';
                }
                $stt = 0;
                $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt] . $rowBegin,
                    (++$key))->getStyle($c_excel[$stt] . $rowBegin)->applyFromArray($styleTd);
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt] . $rowBegin,
                    $name)->getStyle($c_excel[$stt] . $rowBegin)->applyFromArray($styleTd_left);
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt] . $rowBegin,
                    $average_vote_old)->getStyle($c_excel[$stt] . $rowBegin)->applyFromArray($styleTd);
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt] . $rowBegin,
                    $count_late)->getStyle($c_excel[$stt] . $rowBegin)->applyFromArray($styleTd);
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt] . $rowBegin,
                    $count_hour_ro)->getStyle($c_excel[$stt] . $rowBegin)->applyFromArray($styleTd);
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt] . $rowBegin,
                    $count_hour_kp)->getStyle($c_excel[$stt] . $rowBegin)->applyFromArray($styleTd);
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt] . $rowBegin,
                    $type)->getStyle($c_excel[$stt] . $rowBegin)->applyFromArray($styleTd);
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt] . $rowBegin,
                    $type_manager)->getStyle($c_excel[$stt] . $rowBegin)->applyFromArray($styleTd);
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt] . $rowBegin,
                    $note)->getStyle($c_excel[$stt] . $rowBegin)->applyFromArray($styleTd);
                $stt++;
                $rowBegin++;
            }
        }


        $rowBegin++;
        $year = date('Y');
        $objPHPExcel->getActiveSheet()->mergeCells("G$rowBegin" . ':' . "H$rowBegin");
        $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin",
            'Vĩnh Long, Ngày.....Tháng.....Năm ' . $year . '')->getStyle("G$rowBegin")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Times New Roman',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ])->getFont()->setItalic(true);
        $rowBegin++;
        $objPHPExcel->getActiveSheet()->mergeCells("A$rowBegin" . ':' . "C$rowBegin");
        $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin",
            'NGƯỜI LẬP')->getStyle("A$rowBegin")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 13,
                'name' => 'Times New Roman',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);
        $objPHPExcel->getActiveSheet()->mergeCells("D$rowBegin" . ':' . "F$rowBegin");
        $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", 'KẾ TOÁN')->getStyle("D$rowBegin")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 13,
                'name' => 'Times New Roman',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);
        $objPHPExcel->getActiveSheet()->mergeCells("G$rowBegin" . ':' . "H$rowBegin");
        $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", 'GIÁM ĐỐC')->getStyle("G$rowBegin")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 13,
                'name' => 'Times New Roman',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);


        $objPHPExcel->getActiveSheet()->freezePane('A1');
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="BẢNG XÉT BÌNH BẦU.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

    }

}