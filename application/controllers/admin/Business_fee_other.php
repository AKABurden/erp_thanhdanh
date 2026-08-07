<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Carbon\Carbon;

class Business_fee_other extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->type = 3;
        $this->isAdmin = is_admin();
        $this->perViewBusinessOvertime = has_permission('business_overtime','','view');
        $this->perApproveBusinessOvertime = has_permission('business_overtime','','approve');

        $this->perViewBusinessReportOvertime = has_permission('business_report_overtime','','view');

        $this->perViewBusinessCalculate = has_permission('business_calculate','','view');
        $this->perAddBusinessCalculate = has_permission('business_calculate','','create');
        $this->perEditBusinessCalculate = has_permission('business_calculate','','edit');
        $this->perDeleteBusinessCalculate = has_permission('business_calculate','','delete');
    }


    public function business_fee_other()
    {
        $data = [];
        $data['title'] = lang('Bảng theo dõi Km Tháng');
        $data['staff'] = getPersonDeparmentdt($this->type);
        $this->load->view('admin/business_fee_other/business_fee_boiler', $data);
    }

    public function getBusinessFeeBoilers()
    {
        $name_search = $this->input->post('name_search');
        $staff_search = $this->input->post('staff_search');
        $month_search = $this->input->post('month_search');
        $year_search = $this->input->post('year_search');
        $status_table = $this->input->post('status_table');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tbl_business_fee_boiler.id as id',
            'tbl_business_fee_boiler.name as name',
            'tbl_business_fee_boiler.month as month',
            'tbl_personnel.fullname as name_staff',
            'tbl_business_fee_boiler.status as status',
            'tbl_business_fee_boiler.created_by as created_by',
            '1 as action ',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_business_fee_boiler';
        $where = [
        ];
        $filter = [];
        $join = [
            'INNER JOIN tbl_personnel ON tbl_personnel.id = tbl_business_fee_boiler.staff_id',
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_business_fee_boiler.created_by',
            'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbl_personnel.departments',
            'LEFT JOIN tblroles ON tblroles.roleid = tbl_personnel.role',
        ];

        array_push($where,
            'AND tbl_business_fee_boiler.type = '.$this->type.'');

        if (!empty($name_search)) {
            array_push($where,
                'AND ( tbl_business_fee_boiler.name like "%'.$name_search.'%")');
        }
        if (!empty($staff_search)) {
            array_push($where,
                'AND ( tbl_business_fee_boiler.staff_id IN ('.implode(',', $staff_search).'))');
        }
        if (!empty($month_search)) {
            array_push($where,
                'AND ( tbl_business_fee_boiler.month = '.$month_search.')');
        }
        if (!empty($year_search)) {
            array_push($where,
                'AND ( tbl_business_fee_boiler.year = '.$year_search.')');
        }

        if ($status_table != 'all') {
            if ($status_table == 'un_approved') {
                array_push($where,
                    'AND ( tbl_business_fee_boiler.status = 0)');
            } elseif ($status_table == 'approved') {
                array_push($where,
                    'AND ( tbl_business_fee_boiler.status = 1)');
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
            'tbl_business_fee_boiler.date_created as date_created',
            'tbl_business_fee_boiler.year as year',
            'tbl_business_fee_boiler.date_status as date_status',
            'tbl_business_fee_boiler.staff_status as staff_status',
            'tbldepartments.name as name_deparment',
            'tblroles.name as name_roles',
            'tbl_personnel.telephone as telephone',
            'tbl_personnel.current_accommodation as current_accommodation',
        ], '', [], []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $stt = 1;
        foreach ($rResult as $key => $aRow) {
            $start++;

            $row = array();

            $row[0] = '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child fa fa-caret-right"></a></div>';
            $row[1] = '<div>'.$aRow['name'].'</div>';
            $role_name = !empty($aRow['name_roles']) ? '('.$aRow['name_roles'].')' : '';
            $info = '<div style="font-style: italic;font-size: 12px">
                <div>Bộ phận: '.$aRow['name_deparment'].'</div>
                <div>Địa chỉ: '.$aRow['current_accommodation'].'</div>
                <div>Số điện thoại: '.$aRow['telephone'].'</div>
            </div>';
            $row[2] = '<div><span style="font-weight: bold">'.$aRow['name_staff'].'</span>'.$info.'</div>';
            $row[3] = '<div class="text-center">'.$aRow['month'].'/'.$aRow['year'].'</div>';
            $user_status = $aRow['staff_status'];
            if (!empty($aRow['date_status'])) {
                $date_status = _d($aRow['date_status']);
            }
            $full_name = get_staff_full_name($user_status);
            $strApproveHtml = '';
            if (!empty($user_status)) {
                $strApproveHtml = '<a class="mright5 mtop5" data-toggle="tooltip" data-title="'.$full_name.'" href="'.admin_url('profile/'.$user_status).'">'.staff_profile_image(
                        $user_status,
                        ['staff-profile-image-small mbot5']
                    ).'</a> <span>'.$full_name.'<br/><i style="font-size: 9px;">'.$date_status.'</i>';
            }

            $strApprove = '';
            if ($aRow['status'] == 0) {
                $html = "<p><a id='agree' value='1' data-id='".$aRow['id']."' class='btn btn-success btn-icon'>Duyệt</a><button class='btn po-close  btn-icon'>Thoát</button></p>";
                $strApprove = '<div class="text-center mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="'.$html.'" class="label label-warning po" data-original-title="Duyệt">Chưa duyệt</span></div>';
            } elseif ($aRow['status'] == 1) {
                $html = "<p><a id='agree' value='0' data-id='".$aRow['id']."' class='btn btn-danger btn-icon'>Bỏ duyệt</a><button class='btn po-close  btn-icon'>Thoát</button></p>";
                $strApprove = '<div class="text-center mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="'.$html.'" class="label label-success po" data-original-title="Duyệt">Đã duyệt</span></div>';
            }
            $row[4] = $strApprove.$strApproveHtml;;
            $staff_created = staff_profile_image($aRow['created_by'], array('staff-profile-image-small mright5'),
                    'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => ' Vào lúc: '._dt($aRow['date_created']),
                    )).get_staff_full_name($aRow['created_by']).'<br>';
            $row[5] = '<div class="text-left">'.$staff_created.'<div style="font-style: italic; font-size: 12px">
                '._dt($aRow['date_created']).'
            </div></div>';

            $actions = '<div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">'._l('action').'
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu h_right" style="width: 200px">';
            $actions .= '<li><a href="" onclick="edit('.$aRow['id'].','.$aRow['status'].');return false;" class="text-danger"><i class="fa fa-edit"></i> '._l('Sửa theo dõi km tháng').'</a></li>';
            $actions .= '<li><a href="'.admin_url('business_fee_other/print_pdf/'.$aRow['id']).'" target="_blank"><i class="fa fa-file-pdf-o width-icon-actions"></i> '._l('In theo dõi km tháng').'</a></li>';
            $actions .= '<li><a href="" onclick="deleteTicket('.$aRow['id'].');return false;" class="text-danger delete-remind"><i class="fa fa-times"></i> '._l('Xóa theo dõi km tháng').'</a></li>';
            $actions .= '</ul></div>';
            $row[6] = '<div class="text-center">'.$actions.'</div>';

            $trItems = '';
            $this->db->select('
                tbl_business_fee_boiler_detail.id as id,
                tbl_business_fee_boiler_detail.date as date,
                tbl_business_fee_boiler_detail.total_km as total_km,
                tbl_business_fee_boiler_detail.distance_detail as distance_detail,
                tbl_business_fee_boiler_detail.note as note
            ');
            $this->db->from('tbl_business_fee_boiler_detail');
            $this->db->where('tbl_business_fee_boiler_detail.business_fee_boiler_id', $aRow['id']);
            $businessFeeDetail = $this->db->get()->result_array();
            $totalKm = 0;
            foreach ($businessFeeDetail as $k => $v) {

                $this->db->select('tblclients.company as company,tbl_business_fee_boiler_detail_customer.total_km');
                $this->db->from('tbl_business_fee_boiler_detail_customer');
                $this->db->join('tblclients',
                    'tblclients.userid = tbl_business_fee_boiler_detail_customer.customer_id');
                $this->db->where('tbl_business_fee_boiler_detail_customer.business_fee_boiler_detail_id', $v['id']);
                $customer = $this->db->get('')->result_array();
                $htmlCustomer = '';

                if (!empty($customer)) {
                    foreach ($customer as $kk => $vv) {
                        $htmlCustomer .= '<div class="col-md-12 pull-left img-tasks-result" style="padding: 5px">'.$vv['company'].' : <span style="color: green">'.formatNumber($vv['total_km']).'</span></div>';
                    }
                }

                if (count($customer) > 0) {
                    $_outputStatusCustomer = '<div class="dropdown" style="text-align: center;margin-top:10px">
                    <button class="dropdown-toggle no_background label label-info" type="button" data-toggle="dropdown">'.count_number_customer(count($customer)).'
                      </button>
                    <ul style="top:unset;bottom:100%;left:unset;right: 0%;width: 300px" class="dropdown-menu ch_foso" >';
                    $_outputStatusCustomer .= $htmlCustomer;
                    $_outputStatusCustomer .= '</ul></div>';
                } else {
                    $_outputStatusCustomer = '';
                }

                $htmlStaff = '';
                $this->db->select('tbl_personnel.fullname as fullname');
                $this->db->from('tbl_business_fee_boiler_detail_staff');
                $this->db->join('tbl_personnel', 'tbl_personnel.id = tbl_business_fee_boiler_detail_staff.staff_id');
                $this->db->where('tbl_business_fee_boiler_detail_staff.business_fee_boiler_detail_id', $v['id']);
                $staffs = $this->db->get('')->result_array();
                if (!empty($staffs)) {
                    foreach ($staffs as $kk => $vv) {
                        $htmlStaff .= '<div class="col-md-12 pull-left img-tasks-result" style="padding: 5px">'.$vv['fullname'].'</div>';
                    }
                }
                if (count($staffs) > 0) {
                    $_outputStatusFiles = '<div class="dropdown" style="text-align: center;margin-top:10px">
                <button class="dropdown-toggle no_background label label-info" type="button" data-toggle="dropdown">'.count_number_staff(count($staffs)).'
                    </button>
                    <ul style="top:unset;bottom:100%;left:unset;right: 25%" class="dropdown-menu ch_foso" >';
                    $_outputStatusFiles .= $htmlStaff;
                    $_outputStatusFiles .= '</ul></div>';
                } else {
                    $_outputStatusFiles = '';
                }

                $trItems .= '<tr>
                        <td class="text-center">'.(++$k).'</td>
                        <td class="text-left">'._dhau($v['date']).'</td>
                        <td class="text-center">'.formatNumber($v['total_km']).'</td>
                        <td class="text-left">'.($v['distance_detail']).'</td>
                        <td class="text-left" style="width: 150px;">'.$_outputStatusCustomer.'</td>
                        <td class="text-left" style="width: 100px;">'.$v['note'].'</td>
                        <td class="text-left" style="width: 150px;">'.$_outputStatusFiles.'</td>
                    </tr>';
                $totalKm += $v['total_km'];
            }
            $_data = '
                <div class="scrolling-stone pr-4 position-absolute h-100 w-100 max-height">
                    <div class="">
                         <div class="col-md-10">
                            <table class="table" style="margin-top: 0px;">
                                <thead>
                                    <tr>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;" class="text-center" style="width: 50px;">STT</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;" class="text-center" style="width: 100px;">'.lang('Ngày').'</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;" class="text-center" style="width: 120px;">'.lang('Km Tổng').'</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;" class="text-center" style="width: 120px;">'.lang('Diễn giải chi tiết').'</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;" class="text-center" style="width: 150px;">'.lang('Tên công ty công tác').'</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;" class="text-center" style="width: 100px;">'.lang('Lý do').'</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;" class="text-center" style="width: 150px;">'.lang('Nhân viên đi cùng').'</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    '.$trItems.'
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" style="text-transform: uppercase">Tổng cộng</td>
                                        <td class="text-center">'.formatNumber($totalKm).'</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            ';
            $row[7] = '<div class="text-left">'.$_data.'</div>';
            $output['aaData'][] = $row;
            $stt++;

        }
        echo json_encode($output);
    }

    public function add_business_fee_boiler($id = '')
    {
        $data = [];

        if ($this->input->post()) {
            $dataPost = $this->input->post();
//            print_arrays($dataPost);
            $name = $dataPost['name'];
            $staff_id = $dataPost['staff_id'];
            $month = $dataPost['month'];
            $year = $dataPost['year'];
            $pm = $dataPost['pm'];
            $items = [];
            if ($id == '') {
                $option = [
                    'name' => $name,
                    'staff_id' => $staff_id,
                    'month' => $month,
                    'year' => $year,
                    'status' => 0,
                    'type' => $this->type,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s'),
                ];

                if (!empty($pm)) {
                    foreach ($pm as $key => $value) {
                        $conter = $value['conter'];
                        $total_km = number_unformat($value['total_km']);
                        $staff_id_new_vs1 = !empty($value['staff_id']) ? $value['staff_id'] : [];
                        $sub = [];
                        $staff_id_new = [];
                        $customer_sub = !empty($this->input->post('rel_id')[$conter]) ? $this->input->post('rel_id')[$conter] : false;
                        $total_quantity_sub = 0;
                        if (!empty($customer_sub)) {
                            foreach ($customer_sub as $k => $val) {
                                if (empty($val)) {
                                    continue;
                                }
                                $quantity_sub = $this->input->post('quantity_sub')[$conter][$k];
                                $sub[] = [
                                    'customer_id' => $val,
                                    'total_km' => $quantity_sub,
                                ];

                                $total_quantity_sub += $quantity_sub;
                            }
                        }
                        if ($total_quantity_sub > $total_km) {
                            $data['result'] = 0;
                            $data['message'] = 'Không thể lớn hơn số Km tổng';
                            echo json_encode($data);
                            die();
                        }
                        if (!empty($staff_id_new_vs1)) {
                            foreach ($staff_id_new_vs1 as $k => $val) {
                                $staff_id_new[] = [
                                    'staff_id' => $val,
                                ];
                            }
                        }
                        $items[] = [
                            'total_km' => $total_km,
                            'date' => to_sql_date($value['date']),
                            'distance_detail' => $value['distance_detail'],
                            'note' => $value['note'],
                            'sub' => $sub,
                            'staff_id_new' => $staff_id_new,
                        ];
                    }
                }

                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = 'Không có dữ liệu chi tiết';
                    echo json_encode($data);
                    die();
                }


                $this->db->insert('tbl_business_fee_boiler', $option);
                $id_insert = $this->db->insert_id();
                if ($id_insert) {
                    foreach ($items as $key => $value) {
                        $value['business_fee_boiler_id'] = $id_insert;
                        $sub = $value['sub'];
                        unset($value['sub']);
                        $staff_id_new = $value['staff_id_new'];
                        unset($value['staff_id_new']);
                        $this->db->insert('tbl_business_fee_boiler_detail', $value);
                        $id_insert_detail = $this->db->insert_id();
                        if ($id_insert_detail) {
                            if (!empty($sub)) {
                                foreach ($sub as $k => $v) {
                                    $v['business_fee_boiler_detail_id'] = $id_insert_detail;
                                    $this->db->insert('tbl_business_fee_boiler_detail_customer', $v);
                                }
                            }
                            if (!empty($staff_id_new)) {
                                foreach ($staff_id_new as $k => $v) {
                                    $v['business_fee_boiler_detail_id'] = $id_insert_detail;
                                    $this->db->insert('tbl_business_fee_boiler_detail_staff', $v);
                                }
                            }
                        }
                    }
                    $get_code = get_table_where('tbl_business_fee_boiler', array('id' => $id_insert), '', 'row');
                    activity_log_v2('business_fee_boiler', 'tbl_business_fee_boiler', $id_insert, $get_code->name,
                        'Thêm km tháng phòng ban khác ['.$get_code->name.']');
                    $data['result'] = 1;
                    $data['message'] = 'Thêm thành công';
                } else {
                    $data['result'] = 0;
                    $data['message'] = 'Thêm thất bại';
                }
            } else {
                $checkPaid = get_table_where('tbl_business_fee_boiler', array('id' => $id), '', 'row_array');
                if ($checkPaid['status'] == 1) {
                    $data['result'] = 0;
                    $data['message'] = 'Đã duyệt không thể sửa';
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
                        $conter = $value['conter'];
                        $total_km = number_unformat($value['total_km']);
                        $staff_id_new_vs1 = !empty($value['staff_id']) ? $value['staff_id'] : [];
                        $sub = [];
                        $staff_id_new = [];
                        $customer_sub = !empty($this->input->post('rel_id')[$conter]) ? $this->input->post('rel_id')[$conter] : false;
                        $total_quantity_sub = 0;
                        if (!empty($customer_sub)) {
                            foreach ($customer_sub as $k => $val) {
                                if (empty($val)) {
                                    continue;
                                }
                                $quantity_sub = $this->input->post('quantity_sub')[$conter][$k];
                                $sub[] = [
                                    'customer_id' => $val,
                                    'total_km' => $quantity_sub,
                                ];

                                $total_quantity_sub += $quantity_sub;
                            }
                        }
                        if ($total_quantity_sub > $total_km) {
                            $data['result'] = 0;
                            $data['message'] = 'Không thể lớn hơn số Km tổng';
                            echo json_encode($data);
                            die();
                        }
                        if (!empty($staff_id_new_vs1)) {
                            foreach ($staff_id_new_vs1 as $k => $val) {
                                $staff_id_new[] = [
                                    'staff_id' => $val,
                                ];
                            }
                        }
                        $items[] = [
                            'id' => !empty($value['id']) ? $value['id'] : 0,
                            'total_km' => $total_km,
                            'date' => to_sql_date($value['date']),
                            'distance_detail' => $value['distance_detail'],
                            'note' => $value['note'],
                            'sub' => $sub,
                            'staff_id_new' => $staff_id_new,
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
                $success = $this->db->update('tbl_business_fee_boiler', $option);
                if ($success) {
                    $arrId = [];
                    $itemsOld = get_table_where('tbl_business_fee_boiler_detail', ['business_fee_boiler_id' => $id]);
                    if (!empty($itemsOld)) {
                        foreach ($itemsOld as $key => $value) {
                            $this->db->where('business_fee_boiler_detail_id', $value['id']);
                            $this->db->delete('tbl_business_fee_boiler_detail_customer');

                            $this->db->where('business_fee_boiler_detail_id', $value['id']);
                            $this->db->delete('tbl_business_fee_boiler_detail_staff');
                        }
                    }
                    foreach ($items as $key => $value) {
                        $checkExisit = get_table_where('tbl_business_fee_boiler_detail', ['id' => $value['id']], '',
                            'row_array');
                        if (!empty($checkExisit)) {
                            $arrId[] = $checkExisit['id'];
                            $sub = $value['sub'];
                            $staff_id_new = $value['staff_id_new'];
                            unset($value['sub']);
                            unset($value['staff_id_new']);
                            $this->db->where('id', $value['id']);
                            $this->db->update('tbl_business_fee_boiler_detail', $value);
                            if (!empty($sub)) {
                                foreach ($sub as $k => $v) {
                                    $v['business_fee_boiler_detail_id'] = $value['id'];
                                    $this->db->insert('tbl_business_fee_boiler_detail_customer', $v);
                                }
                            }
                            if (!empty($staff_id_new)) {
                                foreach ($staff_id_new as $k => $v) {
                                    $v['business_fee_boiler_detail_id'] = $value['id'];
                                    $this->db->insert('tbl_business_fee_boiler_detail_staff', $v);
                                }
                            }
                        } else {
                            $value['business_fee_boiler_id'] = $id;
                            $sub = $value['sub'];
                            $staff_id_new = $value['staff_id_new'];
                            unset($value['sub']);
                            unset($value['staff_id_new']);
                            $this->db->insert('tbl_business_fee_boiler_detail', $value);
                            $insert_id_item = $this->db->insert_id();
                            if ($insert_id_item) {
                                if (!empty($sub)) {
                                    foreach ($sub as $k => $v) {
                                        $v['business_fee_boiler_detail_id'] = $insert_id_item;
                                        $this->db->insert('tbl_business_fee_boiler_detail_customer', $v);
                                    }
                                }
                                if (!empty($staff_id_new)) {
                                    foreach ($staff_id_new as $k => $v) {
                                        $v['business_fee_boiler_detail_id'] = $insert_id_item;
                                        $this->db->insert('tbl_business_fee_boiler_detail_staff', $v);
                                    }
                                }
                            }
                            $arrId[] = $insert_id_item;
                        }
                    }

                    if (empty($arrId)) {
                        $this->db->where('business_fee_boiler_id', $id);
                        $this->db->delete('tbl_business_fee_boiler_detail');
                    } else {
                        $this->db->where('business_fee_boiler_id', $id);
                        $this->db->where_not_in('id', $arrId);
                        $this->db->delete('tbl_business_fee_boiler_detail');
                    }

                    $get_code = get_table_where('tbl_business_fee_boiler', array('id' => $id), '', 'row');
                    activity_log_v2('edit_business_fee_boiler', 'tbl_business_fee_boiler', $id, $get_code->name,
                        'Sửa km tháng phòng ban khác ['.$get_code->name.']');
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

        $data['staff'] = getPersonDeparmentdt($this->type);
        $this->db->select('tbl_personnel.*,tbldepartments.name as name_department');
        $this->db->from('tbl_personnel');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbl_personnel.departments', 'left');
        $this->db->where('status_work', 2);
        $this->db->where('tbldepartments.business_fee', $this->type);
        $data['staffNew'] = $this->db->get()->result_array();
        $data['typeMagic'] = get_table_where('tbl_type_magic', [], 'id ASC', 'result_array');
        if (!empty($id)) {
            $data['id'] = $id;
            $data['title'] = lang('Sửa theo dõi km tháng');
            $data['businessFeeBoiler'] = get_table_where('tbl_business_fee_boiler', ['id' => $id], '', 'row_array');
            $data['businessFeeBoilerDetail'] = get_table_where('tbl_business_fee_boiler_detail',
                ['business_fee_boiler_id' => $id]);

        } else {
            $data['id'] = '';
            $data['title'] = lang('Thêm theo dõi km tháng');
        }
        $this->load->view('admin/business_fee_other/add_business_fee_boiler', $data);
    }

    public function get_total()
    {
        $name_search = $this->input->post('name_search');
        $staff_search = $this->input->post('staff_search');

        $this->db->from('tbl_business_fee_boiler');
        if (!empty($name_search)) {
            $this->db->where('( tbl_business_fee_boiler.name like "%'.$name_search.'%")');
        }
        if (!empty($staff_search)) {
            $this->db->where('tbl_business_fee_boiler.staff_id IN ('.implode(',', $staff_search).')');
        }
        $this->db->where('type', $this->type);
        $data['all'] = $this->db->count_all_results();

        $this->db->from('tbl_business_fee_boiler');
        $this->db->where('status', 0);
        if (!empty($name_search)) {
            $this->db->where('( tbl_business_fee_boiler.name like "%'.$name_search.'%")');
        }
        if (!empty($staff_search)) {
            $this->db->where('tbl_business_fee_boiler.staff_id IN ('.implode(',', $staff_search).')');
        }
        $this->db->where('type', $this->type);
        $data['un_approved'] = $this->db->count_all_results();

        $this->db->from('tbl_business_fee_boiler');
        $this->db->where('status', 1);
        if (!empty($name_search)) {
            $this->db->where('( tbl_business_fee_boiler.name like "%'.$name_search.'%")');
        }
        if (!empty($staff_search)) {
            $this->db->where('tbl_business_fee_boiler.staff_id IN ('.implode(',', $staff_search).')');
        }
        $this->db->where('type', $this->type);
        $data['approved'] = $this->db->count_all_results();

        echo json_encode($data);
    }

    public function update_status()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        if (!empty($id)) {

            $this->db->where('id', $id);
            $businessFeeBoiler = $this->db->get('tbl_business_fee_boiler')->row_array();
            if ($businessFeeBoiler['status'] == $status) {
                echo json_encode([
                    'success' => false,
                    'message' => _l('Phiếu đang ở trạng thái này không thể duyệt được nữa'),
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
            $success = $this->db->update('tbl_business_fee_boiler', $data_update);
            if (!empty($success)) {
                $get_code = get_table_where('tbl_business_fee_boiler', array('id' => $id), '', 'row');
                activity_log_v2('status_business_fee_boiler', 'tbl_business_fee_boiler', $id, $get_code->name,
                    'Duyệt phiếu theo dõi km tháng phòng ban khác ['.$get_code->name.']');
                echo json_encode([
                    'result' => $success,
                    'message' => _l('cong_update_true'),
                ]);
                die();
            }
        }
        echo json_encode([
            'result' => false,
            'message' => _l('cong_update_false'),
        ]);
        die();
    }

    public function deleteTicket()
    {
        $id = $this->input->post('id');
        if (!empty($id)) {

            $this->db->where('id', $id);
            $businessFeeBoiler = $this->db->get('tbl_business_fee_boiler')->row_array();

            if ($businessFeeBoiler['status'] == 1) {
                echo json_encode([
                    'result' => false,
                    'message' => _l('Đã duyệt không thể xóa'),
                ]);
                die();
            }

            $this->db->from('tbl_business_fee_boiler_calculate_item');
            $this->db->join('tbl_business_fee_boiler_calculate',
                'tbl_business_fee_boiler_calculate.id = tbl_business_fee_boiler_calculate_item.business_fee_boiler_calculate_id');
            $this->db->where('tbl_business_fee_boiler_calculate_item.staff_id', $businessFeeBoiler['staff_id']);
            $this->db->where('tbl_business_fee_boiler_calculate.month', $businessFeeBoiler['month']);
            $this->db->where('tbl_business_fee_boiler_calculate.year', $businessFeeBoiler['year']);
            $checkExists = $this->db->count_all_results();
            if (!empty($checkExists)) {
                echo json_encode([
                    'result' => false,
                    'message' => _l('Đã tính bảng tính công tác phí. Không thể xóa !'),
                ]);
                die();
            }

            $get_code = get_table_where('tbl_business_fee_boiler', array('id' => $id), '', 'row');
            $this->db->where('id', $id);
            $success = $this->db->delete('tbl_business_fee_boiler');

            $itemOld = get_table_where('tbl_business_fee_boiler_detail', ['business_fee_boiler_id' => $id]);

            if (!empty($success)) {

                $this->db->where('business_fee_boiler_id', $id);
                $this->db->delete('tbl_business_fee_boiler_detail');

                if (!empty($itemOld)) {
                    foreach ($itemOld as $key => $value) {
                        $this->db->where('business_fee_boiler_detail_id', $value['id']);
                        $this->db->delete('tbl_business_fee_boiler_detail_customer');

                        $this->db->where('business_fee_boiler_detail_id', $value['id']);
                        $this->db->delete('tbl_business_fee_boiler_detail_staff');
                    }
                }

                activity_log_v2('delete_busniness_fee_boiler', 'tbl_business_fee_boiler', $id, $get_code->name,
                    'Xoá phiếu thoi dõi km tháng phòng ban khác ['.$get_code->name.']');
                echo json_encode([
                    'result' => $success,
                    'message' => _l('cong_update_true'),
                ]);
                die();
            }
        }
        echo json_encode([
            'result' => false,
            'message' => _l('cong_update_false'),
        ]);
        die();
    }

    public function print_pdf($id = '')
    {
        ob_start();
        $data = new stdClass();

        $this->db->select('
            tbl_business_fee_boiler.id as id,
            tbl_business_fee_boiler.name as name,
            tbl_business_fee_boiler.date_created as date_created,
            tbl_personnel.fullname as name_staff,
            tbldepartments.name as name_deparment,
            tblroles.name as name_roles,
            tbl_personnel.telephone as telephone,
            tbl_personnel.current_accommodation as current_accommodation
        ');
        $this->db->from('tbl_business_fee_boiler');
        $this->db->join('tbl_personnel', 'tbl_personnel.id = tbl_business_fee_boiler.staff_id');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbl_personnel.departments', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tbl_personnel.role', 'left');
        $this->db->where('tbl_business_fee_boiler.id', $id);
        $businessFeeBoiler = $this->db->get()->row_array();

        $table = '';
        $data->content = '';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;text-transform: uppercase">'._l('Công tác phí lò hơi').'</span><br>';

        $day = date('d', strtotime($businessFeeBoiler['date_created']));
        $month = date('m', strtotime($businessFeeBoiler['date_created']));
        $year = date('Y', strtotime($businessFeeBoiler['date_created']));
        $date = _l('ch_day').' '.$day.' '._l('ch_month').' '.$month.' '._l('ch_year').' '.$year;
        $data->content .= '<span style="text-align: center;font-style: italic;">'.$date.'</span><br>';


        $data->content .= '
            <span style="font-weight: bold;">'._l('Nhân viên').': </span><span>'.$businessFeeBoiler['name_staff'].'</span><br><br>
            <span style="font-weight: bold;">'._l('Bộ phận').': </span><span>'.$businessFeeBoiler['name_deparment'].'</span><br><br>
            <span style="font-weight: bold;">'._l('Địa chỉ liên lạc').': </span><span>'.$businessFeeBoiler['current_accommodation'].'</span><br><br>
            <span style="font-weight: bold;">'._l('Số điện thoại').': </span><span>'.$businessFeeBoiler['telephone'].'</span><br><br>';

        $trItems = '';
        $this->db->select('
                tbl_business_fee_boiler_detail.id as id,
                tbl_business_fee_boiler_detail.date as date,
                tbl_business_fee_boiler_detail.total_km as total_km,
                tbl_business_fee_boiler_detail.distance_detail as distance_detail,
                tbl_business_fee_boiler_detail.note as note,
            ');
        $this->db->from('tbl_business_fee_boiler_detail');
        $this->db->where('tbl_business_fee_boiler_detail.business_fee_boiler_id', $id);
        $paidHolidayDetail = $this->db->get()->result_array();
        $totalKm = 0;
        foreach ($paidHolidayDetail as $k => $v) {

            $htmlStaff = '';
            $this->db->select('tbl_personnel.fullname as fullname');
            $this->db->from('tbl_business_fee_boiler_detail_staff');
            $this->db->join('tbl_personnel', 'tbl_personnel.id = tbl_business_fee_boiler_detail_staff.staff_id');
            $this->db->where('tbl_business_fee_boiler_detail_staff.business_fee_boiler_detail_id', $v['id']);
            $staffs = $this->db->get('')->result_array();
            if (!empty($staffs)) {
                foreach ($staffs as $kk => $vv) {
                    $htmlStaff .= '<span style="font-size: 10px">'.$vv['fullname'].'</span>'.', ';
                }
            }
            $htmlStaff = trim($htmlStaff, ', ');

            $this->db->select('tblclients.company as company,tbl_business_fee_boiler_detail_customer.total_km');
            $this->db->from('tbl_business_fee_boiler_detail_customer');
            $this->db->join('tblclients', 'tblclients.userid = tbl_business_fee_boiler_detail_customer.customer_id');
            $this->db->where('tbl_business_fee_boiler_detail_customer.business_fee_boiler_detail_id', $v['id']);
            $customer = $this->db->get('')->result_array();
            $htmlCustomer = '';

            if (!empty($customer)) {
                foreach ($customer as $kk => $vv) {
                    $htmlCustomer .= '<span style="font-size: 10px">'.$vv['company'].' </span>'.', ';
                }
            }

            $htmlCustomer = trim($htmlCustomer, ', ');

            $trItems .= '<tr>
                        <td style="width: 6%;text-align: center" class="text-center">'.(++$k).'</td>
                        <td style="width: 12%" class="text-left">'._dhau($v['date']).'</td>
                        <td style="width: 10%;text-align:center">'.formatNumber($v['total_km']).'</td>
                        <td style="width: 15%" class="text-left">'.($v['distance_detail']).'</td>
                        <td style="width: 21%;text-align: left">'.$htmlCustomer.'</td>
                        <td style="width: 15%" class="text-left">'.($v['note']).'</td>
                        <td style="width: 21%" class="text-left">'.$htmlStaff.'</td>
                    </tr>';
            $totalKm += $v['total_km'];
        }

        $data->content .= '<table class="table table-bordered" border="1" width="100%">
                <thead>
                    <tr>
                        <th style="text-align: center;width: 6%;font-weight: bold">STT</th>
                        <th style="text-align: center;width: 12%;font-weight: bold">Ngày</th>
                        <th style="text-align: center;width: 10%;font-weight: bold">Km Tổng</th>
                        <th style="text-align: center;width: 15%;font-weight: bold">Diễn giải chi tiết</th>
                        <th style="text-align: center;width: 21%;font-weight: bold">Tên công ty công tác</th>
                        <th style="text-align: center;width: 15%;font-weight: bold">Lý do</th>
                        <th style="text-align: center;width: 21%;font-weight: bold">Nhân viên đi cùng</th>
                    </tr>
                </thead>
                <tbody>
                    '.$trItems.'
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align: center">TỔNG CỘNG</td>
                        <td style="text-align: center;font-weight: bold">'.formatNumber($totalKm).'</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
              </table><br><br>';
        $date_2 = _l('ch_day').' ........ '._l('ch_month').' ........ '._l('ch_year').' ........';
        $data->content .= '<span style="text-align: right;font-style: italic;">'.$date_2.'</span><br>';
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
                            <span style="font-weight: bold;">'._l('Người lập').'</span><br>
                            <span>'._l('ch_signature').'</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">'._l('Trưởng bộ phận').'</span><br>
                            <span>'._l('ch_signature').'</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">'._l('Giám đốc').'</span><br>
                            <span>'._l('ch_signature').'</span>
                        </td>
                    </tr>
                </tbody>
            </table>';
        $data->content .= $table;
        $pdf = print_pdf_P_ch($data);
        $type = 'I';
        $pdf->Output($businessFeeBoiler['name'].'.pdf', $type);
    }

    public function business_fee_other_overtime()
    {
        if (!$this->perViewBusinessOvertime){
            access_denied();
        }
        $data = [];
        $data['title'] = lang('Bảng theo dõi tăng ca tháng');
        $data['staff'] = getPersonDeparmentdt($this->type);
        $data['branch'] = getListBranch();
        $this->load->view('admin/business_fee_other/business_fee_boiler_overtime', $data);
    }

    public function getBusinessFeeBoilerOvertimes()
    {
        $arrBranch = get_branch_staff();
        $name_search = $this->input->post('name_search');
        $staff_search = $this->input->post('staff_search');
        $month_search = $this->input->post('month_search');
        $year_search = $this->input->post('year_search');
        $status_table = $this->input->post('status_table');
        $branch_search = $this->input->post('branch_search');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tb_overtime_detail = "(
            SELECT 
                tbl_business_fee_boiler_overtime_detail.business_fee_boiler_overtime_id as business_fee_boiler_overtime_id,
                COUNT(tbl_business_fee_boiler_overtime_detail.id) as total_date,
                SUM(tbl_business_fee_boiler_overtime_detail.weekday) as total_weekday,
                SUM(tbl_business_fee_boiler_overtime_detail.sunday) as total_sunday,
                SUM(tbl_business_fee_boiler_overtime_detail.holiday) as total_holiday
            FROM tbl_business_fee_boiler_overtime_detail
            GROUP BY tbl_business_fee_boiler_overtime_detail.business_fee_boiler_overtime_id
        ) tb_overtime_detail";

        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";

        $aColumns = [
            'tbl_business_fee_boiler_overtime.id as id',
            'tbl_business_fee_boiler_overtime.name as name',
            'CONCAT(tb_staff.firstname," ",tb_staff.lastname) as name_staff',
            'tbl_business_fee_boiler_overtime.month as month',
            '0 as total_hour',
            'tbl_business_fee_boiler_overtime.created_by as created_by',
            '1 as action ',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_business_fee_boiler_overtime';
        $where = [
        ];
        $filter = [];
        $join = [
            'INNER JOIN tblstaff tb_staff ON tb_staff.staffid = tbl_business_fee_boiler_overtime.staff_id',
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_business_fee_boiler_overtime.created_by',
            'LEFT JOIN '.$tbDepartment.' ON tb_department.staffid = tb_staff.staffid',
            'LEFT JOIN tblroles ON tblroles.roleid = tb_staff.role',
            'LEFT JOIN '.$tb_overtime_detail.' ON tb_overtime_detail.business_fee_boiler_overtime_id = tbl_business_fee_boiler_overtime.id',
        ];

        array_push($where,
            'AND ( tbl_business_fee_boiler_overtime.type =  '.$this->type.')');

        if (!empty($name_search)) {
            array_push($where,
                'AND ( tbl_business_fee_boiler_overtime.name like "%'.$name_search.'%")');
        }
        if (!empty($staff_search)) {
            array_push($where,
                'AND ( tbl_business_fee_boiler_overtime.staff_id IN ('.implode(',', $staff_search).'))');
        }
        if (!empty($month_search)) {
            array_push($where,
                'AND ( tbl_business_fee_boiler_overtime.month =  '.$month_search.')');
        }
        if (!empty($year_search)) {
            array_push($where,
                'AND ( tbl_business_fee_boiler_overtime.year =  '.$year_search.')');
        }

        if (!empty($branch_search)) {
            array_push($where,
                'AND ( tb_staff.branch_salary = '.$branch_search.')');
        }

        if (!$this->isAdmin) {
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                array_push($where, 'AND tb_staff.branch_salary IN ('.$coverStrBranch.')');
            } else {
                array_push($where,
                    'AND ( tbl_business_fee_boiler_overtime.id = 0)');
            }
        }

        if ($status_table != 'all') {
            if ($status_table == 'un_approved') {
                array_push($where,
                    'AND ( tbl_business_fee_boiler_overtime.status = 0)');
            } elseif ($status_table == 'approved') {
                array_push($where,
                    'AND ( tbl_business_fee_boiler_overtime.status = 1)');
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
            'tbl_business_fee_boiler_overtime.date_created as date_created',
            'tbl_business_fee_boiler_overtime.date_status as date_status',
            'tbl_business_fee_boiler_overtime.staff_status as staff_status',
            'tbl_business_fee_boiler_overtime.year as year',
            'tb_overtime_detail.total_date as total_date',
            'coalesce(tb_overtime_detail.total_weekday,0) as total_weekday',
            'coalesce(tb_overtime_detail.total_sunday,0) as total_sunday',
            'coalesce(tb_overtime_detail.total_holiday,0) as total_holiday',
            'tb_department.name_department as name_deparment',
            'tblroles.name as name_roles',
            'tb_staff.phonenumber as telephone',
            'tb_staff.current_accommodation as current_accommodation',
        ], '', [], []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $stt = 1;
        foreach ($rResult as $key => $aRow) {
            $start++;

            $row = array();

            $row[0] = '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" id="rows-child-'.$aRow['id'].'" class="rows-child fa fa-caret-right"></a></div>';
            $row[1] = '<div>'.$aRow['name'].'</div>';
            $role_name = !empty($aRow['name_roles']) ? '('.$aRow['name_roles'].')' : '';
            $info = '<div style="font-style: italic;font-size: 12px">
                <div>Bộ phận: '.$aRow['name_deparment'].'</div>
                <div>Địa chỉ: '.$aRow['current_accommodation'].'</div>
                <div>Số điện thoại: '.$aRow['telephone'].'</div>
            </div>';
            $row[2] = '<div><span style="font-weight: bold">'.$aRow['name_staff'].'</span>'.$info.'</div>';
            $row[3] = '<div><span>'.$aRow['month'].'/'.$aRow['year'].'</span></div>';
            $totalDay = '<div class="bold">Số ngày : <span>'.$aRow['total_date'].'</span></div>';
            $totalHour = '<div class="bold">Số giờ : <span>'.($aRow['total_weekday'] + $aRow['total_sunday'] + $aRow['total_holiday']).'</span></div>';
            $row[4] = '<div>'.$totalDay.$totalHour.'</div>';
            $staff_created = staff_profile_image($aRow['created_by'], array('staff-profile-image-small mright5'),
                    'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => ' Vào lúc: '._dt($aRow['date_created']),
                    )).get_staff_full_name($aRow['created_by']).'<br>';
            $row[5] = '<div class="text-left">'.$staff_created.'<div style="font-style: italic; font-size: 12px">
                '._dt($aRow['date_created']).'
            </div></div>';

            $actions = '<div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">'._l('action').'
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu h_right" style="width: 200px">';
            $actions .= '<li><a href="'.admin_url('business_fee_other/print_pdf_new/'.$aRow['id']).'" target="_blank"><i class="fa fa-file-pdf-o width-icon-actions"></i> '._l('In tăng ca tháng').'</a></li>';
            $actions .= '</ul></div>';
            $row[6] = '<div class="text-center">'.$actions.'</div>';

            $trItems = '';
            $this->db->select('
                tbl_business_fee_boiler_overtime_detail.id as id,
                tbl_business_fee_boiler_overtime_detail.date as date,
                tbl_business_fee_boiler_overtime_detail.hour_start as hour_start,
                tbl_business_fee_boiler_overtime_detail.hour_end as hour_end,
                tbl_business_fee_boiler_overtime_detail.weekday as weekday,
                tbl_business_fee_boiler_overtime_detail.sunday as sunday,
                tbl_business_fee_boiler_overtime_detail.holiday as holiday,
                tbl_business_fee_boiler_overtime_detail.go_night as go_night,
                tbl_business_fee_boiler_overtime_detail.back_night as back_night,
                tbl_business_fee_boiler_overtime_detail.construction_allowance as construction_allowance,
                tbl_business_fee_boiler_overtime_detail.allowance_survey as allowance_survey,
                tbl_business_fee_boiler_overtime_detail.construction_allowance_province as construction_allowance_province,
                tbl_business_fee_boiler_overtime_detail.type as type,
                tblclients.company as company,
                tbl_business_fee_boiler_overtime_detail.customer_text as customer_text,
                tbl_business_fee_boiler_overtime_detail.note as note,
                tbl_business_fee_boiler_overtime_detail.status as status,
                tbl_business_fee_boiler_overtime_detail.staff_status as staff_status,
                tbl_business_fee_boiler_overtime_detail.date_status as date_status,
                tbl_business_fee_boiler_overtime_detail.note_status as note_status,
                tbl_business_fee_boiler_overtime_detail.suggest_overtime_detail_id as suggest_overtime_detail_id,
            ');
            $this->db->from('tbl_business_fee_boiler_overtime_detail');
            $this->db->join('tblclients', 'tblclients.userid = tbl_business_fee_boiler_overtime_detail.customer_id',
                'left');
            $this->db->where('tbl_business_fee_boiler_overtime_detail.business_fee_boiler_overtime_id', $aRow['id']);
            $businessFeeOvertimeDetail = $this->db->get()->result_array();
            $total_weekday = 0;
            $total_sunday = 0;
            $total_holiday = 0;
            $total_to = 0;
            $total_go = 0;
            $total_vs1 = 0;
            $total_vs2 = 0;
            $countStatus = 0;
            foreach ($businessFeeOvertimeDetail as $k => $v) {

                $htmlCustomer = '';
                if ($v['type'] == 1) {
                    $htmlCustomer = $v['company'];
                } else {
                    $htmlCustomer = $v['customer_text'];
                }

                $htmlStaff = '';
                $this->db->select('tbl_personnel.fullname as fullname');
                $this->db->from('tbl_business_fee_boiler_overtime_detail_staff');
                $this->db->join('tbl_personnel',
                    'tbl_personnel.id = tbl_business_fee_boiler_overtime_detail_staff.staff_id');
                $this->db->where('tbl_business_fee_boiler_overtime_detail_staff.business_fee_boiler_overtime_detail_id',
                    $v['id']);
                $staffs = $this->db->get()->result_array();
                if (!empty($staffs)) {
                    foreach ($staffs as $kk => $vv) {
                        $htmlStaff .= '<div class="col-md-12 pull-left img-tasks-result" style="padding: 5px">'.$vv['fullname'].'</div>';
                    }
                }
                if (count($staffs) > 0) {
                    $_outputStatusFiles = '<div class="dropdown" style="text-align: center;margin-top:10px">
                <button class="dropdown-toggle no_background label label-info" type="button" data-toggle="dropdown">'.count_number_staff(count($staffs)).'
                    </button>
                    <ul style="top:unset;bottom:100%;left:unset;right: 25%" class="dropdown-menu ch_foso" >';
                    $_outputStatusFiles .= $htmlStaff;
                    $_outputStatusFiles .= '</ul></div>';
                } else {
                    $_outputStatusFiles = '';
                }

                $user_status = $v['staff_status'];
                if (!empty($v['date_status'])) {
                    $date_status = _d($v['date_status']);
                }
                $full_name = get_staff_full_name($user_status);
                $strApproveHtml = '';
                if (!empty($user_status)) {
                    $strApproveHtml = '<a class="mright5 mtop5" data-toggle="tooltip" data-title="'.$full_name.'" href="'.admin_url('profile/'.$user_status).'">'.staff_profile_image(
                            $user_status,
                            ['staff-profile-image-small mbot5']
                        ).'</a> <span>'.$full_name.'<br/><i style="font-size: 9px;">'.$date_status.'</i>';
                }

                $strApprove = '';
                $strNote = '';
                if ($v['status'] == 0) {
                    $countStatus++;
                    $html = "<p><a id='agree_child' value='1' data-id='".$v['id']."' class='btn btn-success btn-icon'>Duyệt</a>
                             <a id='agree_child' data-id= '".$v['id']."' value='2' class='btn btn-danger label not_approve'>Không duyệt</a><br><label style='margin-top:10px' class='label-note hide'>Ghi chú</label><textarea class='form-control hide note_approve_task' name='note_approve_task' rows='3' placeholder=' nhập ghi chú '></textarea>
                             <button style='margin-top:5px;margin-left:5px' class='btn btn-info hide po-save'>Lưu</button>
                             <button class='btn po-close  btn-icon hide'>Thoát</button></p>";
                    $strApprove = '<div class="text-center mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="'.$html.'" class="label label-warning po" data-original-title="Duyệt">Chưa duyệt</span></div>';
                } elseif ($v['status'] == 1) {
                    $html = "<p><a id='agree_child' value='0' data-id='".$v['id']."' class='btn btn-warning btn-icon'>Bỏ duyệt</a>
                            <a id='agree_child' data-id= '".$v['id']."' value='2' class='btn btn-danger label not_approve'>Không duyệt</a><br><label style='margin-top:10px' class='label-note hide'>Ghi chú</label><textarea class='form-control hide note_approve_task' name='note_approve_task' rows='3' placeholder=' nhập ghi chú '></textarea>
                            <button style='margin-top:5px;margin-left:5px' class='btn btn-info hide po-save'>Lưu</button>
                            <button class='btn po-close  btn-icon hide'>Thoát</button></p>";
                    $strApprove = '<div class="text-center mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="'.$html.'" class="label label-success po" data-original-title="Duyệt">Đã duyệt</span></div>';
                } elseif ($v['status'] == 2) {
                    $html = "<p>
                             <a id='agree_child' value='1' data-id='".$v['id']."' class='btn btn-success btn-icon'>Duyệt</a>
                            <a id='agree_child' value='0' data-id='".$v['id']."' class='btn btn-danger btn-icon'>Bỏ duyệt</a>
                            <button class='btn po-close  btn-icon hide'>Thoát</button></p>";
                    $strApprove = '<div class="text-center mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="'.$html.'" class="label label-danger po" data-original-title="Duyệt">Không duyệt</span></div>';
                    $strNote = '<div>'.$v['note_status'].'</div>';
                }

                $dtSuggestDetail = get_table_where('tbl_suggest_overtime_detail',['id' => $v['suggest_overtime_detail_id']],'','row_array');
                $htmlSuggest = '';
                if (!empty($dtSuggestDetail)){
                    $htmlSuggest = '<div style="color: green">Có phiếu đề xuất '.($dtSuggestDetail['status'] == 1 ? '(đã duyệt)' : '(chưa duyệt)').'</div>';
                }

                $trItems .= '<tr>
                        <td class="text-center">'.(++$k).'</td>
                        <td class="text-left">'._dhau($v['date']).$htmlSuggest.'</td>
                        <td class="text-left">'.$v['hour_start'].'-'.$v['hour_end'].' (h)</td>
                        <td class="text-center">'.(!empty($v['weekday']) ? ($v['weekday']) : '').'</td>
                        <td class="text-center">'.(!empty($v['sunday']) ? ($v['sunday']) : '').'</td>
                        <td class="text-center">'.(!empty($v['holiday']) ? ($v['holiday']) : '').'</td>
                        <td class="text-left" style="width: 100px;">'.$v['note'].'</td>
                        <td class="text-left" style="width: 100px;">'.$strApprove.$strNote.$strApproveHtml.'</td>
                    </tr>';
                $total_weekday += $v['weekday'];
                $total_sunday += $v['sunday'];
                $total_holiday += $v['holiday'];
            }
            $_data = '
                <div class="scrolling-stone pr-4 position-absolute h-100 w-100 max-height">
                    <div class="">
                         <div class="col-md-10">
                            <table class="table" style="margin-top: 0px;">
                                <thead>
                                    <tr>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;width: 30px;" class="text-center">STT</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;width: 130px;" class="text-center">'.lang('Ngày').'</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;width: 60px;" class="text-center">'.lang('Thời gian').'</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;width: 50px;" class="text-center" >'.lang('Ngày thường').'</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;width: 60px;" class="text-center" >'.lang('Chủ nhật').'</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;width: 60px;" class="text-center" >'.lang('Lễ tết').'</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;width: 100px;" class="text-center" >'.lang('Lý do').'</th>
                                        <th style="background: #d9edf7 !important; border: 1px solid #93b4d6 !important; color: #0e5dab !important;width: 100px;" class="text-center" >'.lang('Trạng thái').'</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    '.$trItems.'
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="uppercase bold">Tổng cộng</td>
                                        <td class="text-center bold">'.($total_weekday > 0 ? ($total_weekday) : '').'</td>
                                        <td class="text-center bold">'.($total_sunday > 0 ? ($total_sunday) : '').'</td>
                                        <td class="text-center bold">'.($total_holiday > 0 ? ($total_holiday) : '').'</td>
                                        <td class="text-center bold"></td>
                                        <td class="text-center bold"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                         <div class="col-md-2">
                            '.($countStatus > 0 ? '<div class="btn btn-success" type="button" onclick="clickAgreeAll('.$aRow['id'].')">Duyệt tất cả</div>' : '').'
                        </div>
                    </div>
                </div>
            ';
            $row[7] = '<div class="text-left">'.$_data.'</div>';
            $row[1] = '<div>'.$aRow['name'].'</div>'.($countStatus > 0 ? '<div style="color: green;font-style: italic">Còn '.$countStatus.' dòng chi tiết chưa duyệt</div>' : '');
            $output['aaData'][] = $row;
            $stt++;

        }
        echo json_encode($output);
    }

    function get_total_overtime()
    {
        $name_search = $this->input->post('name_search');
        $staff_search = $this->input->post('staff_search');
        $month_search = $this->input->post('month_search');
        $year_search = $this->input->post('year_search');

        $this->db->from('tbl_business_fee_boiler_overtime');
        if (!empty($name_search)) {
            $this->db->where('( tbl_business_fee_boiler_overtime.name like "%'.$name_search.'%")');
        }
        if (!empty($staff_search)) {
            $this->db->where('tbl_business_fee_boiler_overtime.staff_id IN ('.implode(',', $staff_search).')');
        }
        if (!empty($month_search)) {
            $this->db->where('tbl_business_fee_boiler_overtime.month', $month_search);
        }
        if (!empty($year_search)) {
            $this->db->where('tbl_business_fee_boiler_overtime.year', $year_search);
        }
        $this->db->where('type', $this->type);
        $data['all'] = $this->db->count_all_results();

        $this->db->from('tbl_business_fee_boiler_overtime');
        $this->db->where('status', 0);
        if (!empty($name_search)) {
            $this->db->where('( tbl_business_fee_boiler_overtime.name like "%'.$name_search.'%")');
        }
        if (!empty($staff_search)) {
            $this->db->where('tbl_business_fee_boiler_overtime.staff_id IN ('.implode(',', $staff_search).')');
        }
        if (!empty($month_search)) {
            $this->db->where('tbl_business_fee_boiler_overtime.month', $month_search);
        }
        if (!empty($year_search)) {
            $this->db->where('tbl_business_fee_boiler_overtime.year', $year_search);
        }
        $this->db->where('type', $this->type);
        $data['un_approved'] = $this->db->count_all_results();

        $this->db->from('tbl_business_fee_boiler_overtime');
        $this->db->where('status', 1);
        if (!empty($name_search)) {
            $this->db->where('( tbl_business_fee_boiler_overtime.name like "%'.$name_search.'%")');
        }
        if (!empty($staff_search)) {
            $this->db->where('tbl_business_fee_boiler_overtime.staff_id IN ('.implode(',', $staff_search).')');
        }
        if (!empty($month_search)) {
            $this->db->where('tbl_business_fee_boiler_overtime.month', $month_search);
        }
        if (!empty($year_search)) {
            $this->db->where('tbl_business_fee_boiler_overtime.year', $year_search);
        }
        $this->db->where('type', $this->type);
        $data['approved'] = $this->db->count_all_results();

        echo json_encode($data);
    }

    public function add_business_fee_boiler_overtime($id = '')
    {
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
                $option = [
                    'name' => $name,
                    'staff_id' => $staff_id,
                    'month' => $month,
                    'year' => $year,
                    'status' => 0,
                    'type' => $this->type,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s'),
                ];

                if (!empty($pm)) {
                    foreach ($pm as $key => $value) {
                        $conter = $value['conter'];
                        $staff_id_new_vs1 = !empty($value['staff_id']) ? $value['staff_id'] : [];
                        $staff_id_new = [];
                        if (!empty($staff_id_new_vs1)) {
                            foreach ($staff_id_new_vs1 as $k => $val) {
                                $staff_id_new[] = [
                                    'staff_id' => $val,
                                ];
                            }
                        }
                        $items[] = [
                            'type' => $value['type'],
                            'customer_id' => !empty($value['rel_id']) ? $value['rel_id'] : 0,
                            'customer_text' => $value['customer_text'],
                            'hour_start' => $value['hour_start'],
                            'hour_end' => $value['hour_end'],
                            'weekday' => $value['weekday'],
                            'sunday' => $value['sunday'],
                            'holiday' => $value['holiday'],
                            'go_night' => 0,
                            'back_night' => 0,
                            'construction_allowance' => 0,
                            'allowance_survey' => $value['allowance_survey'],
                            'construction_allowance_province' => $value['construction_allowance_province'],
                            'date' => to_sql_date($value['date']),
                            'note' => $value['note'],
                            'staff_id_new' => $staff_id_new,
                        ];
                    }
                }

                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = 'Không có dữ liệu chi tiết';
                    echo json_encode($data);
                    die();
                }


                $this->db->insert('tbl_business_fee_boiler_overtime', $option);
                $id_insert = $this->db->insert_id();
                if ($id_insert) {
                    foreach ($items as $key => $value) {
                        $value['business_fee_boiler_overtime_id'] = $id_insert;
                        $staff_id_new = $value['staff_id_new'];
                        unset($value['staff_id_new']);
                        $this->db->insert('tbl_business_fee_boiler_overtime_detail', $value);
                        $id_insert_detail = $this->db->insert_id();
                        if ($id_insert_detail) {
                            if (!empty($staff_id_new)) {
                                foreach ($staff_id_new as $k => $v) {
                                    $v['business_fee_boiler_overtime_detail_id'] = $id_insert_detail;
                                    $this->db->insert('tbl_business_fee_boiler_overtime_detail_staff', $v);
                                }
                            }
                        }
                    }
                    $get_code = get_table_where('tbl_business_fee_boiler_overtime', array('id' => $id_insert), '',
                        'row');
                    activity_log_v2('business_fee_boiler_overtime', 'tbl_business_fee_boiler_overtime', $id_insert,
                        $get_code->name,
                        'Thêm tăng ca tháng phòng ban khác ['.$get_code->name.']');
                    $data['result'] = 1;
                    $data['message'] = 'Thêm thành công';
                } else {
                    $data['result'] = 0;
                    $data['message'] = 'Thêm thất bại';
                }
            } else {
                $checkPaid = get_table_where('tbl_business_fee_boiler_overtime', array('id' => $id), '', 'row_array');
                if ($checkPaid['status'] == 1) {
                    $data['result'] = 0;
                    $data['message'] = 'Đã duyệt không thể sửa';
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
                        $conter = $value['conter'];
                        $staff_id_new_vs1 = !empty($value['staff_id']) ? $value['staff_id'] : [];
                        $staff_id_new = [];
                        if (!empty($staff_id_new_vs1)) {
                            foreach ($staff_id_new_vs1 as $k => $val) {
                                $staff_id_new[] = [
                                    'staff_id' => $val,
                                ];
                            }
                        }
                        $items[] = [
                            'id' => !empty($value['id']) ? $value['id'] : 0,
                            'type' => $value['type'],
                            'customer_id' => !empty($value['rel_id']) ? $value['rel_id'] : 0,
                            'customer_text' => $value['customer_text'],
                            'hour_start' => $value['hour_start'],
                            'hour_end' => $value['hour_end'],
                            'weekday' => $value['weekday'],
                            'sunday' => $value['sunday'],
                            'holiday' => $value['holiday'],
                            'allowance_survey' => $value['allowance_survey'],
                            'construction_allowance_province' => $value['construction_allowance_province'],
                            'date' => to_sql_date($value['date']),
                            'note' => $value['note'],
                            'staff_id_new' => $staff_id_new,
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
                $success = $this->db->update('tbl_business_fee_boiler_overtime', $option);
                if ($success) {
                    $arrId = [];
                    $itemsOld = get_table_where('tbl_business_fee_boiler_overtime_detail',
                        ['business_fee_boiler_overtime_id' => $id]);
                    if (!empty($itemsOld)) {
                        foreach ($itemsOld as $key => $value) {

                            $this->db->where('business_fee_boiler_overtime_detail_id', $value['id']);
                            $this->db->delete('tbl_business_fee_boiler_overtime_detail_staff');
                        }
                    }
                    foreach ($items as $key => $value) {
                        $checkExisit = get_table_where('tbl_business_fee_boiler_overtime_detail',
                            ['id' => $value['id']], '', 'row_array');
                        if (!empty($checkExisit)) {
                            $arrId[] = $checkExisit['id'];
                            $staff_id_new = $value['staff_id_new'];
                            unset($value['staff_id_new']);
                            $this->db->where('id', $value['id']);
                            $this->db->update('tbl_business_fee_boiler_overtime_detail', $value);
                            if (!empty($staff_id_new)) {
                                foreach ($staff_id_new as $k => $v) {
                                    $v['business_fee_boiler_overtime_detail_id'] = $value['id'];
                                    $this->db->insert('tbl_business_fee_boiler_overtime_detail_staff', $v);
                                }
                            }
                        } else {
                            $value['business_fee_boiler_overtime_id'] = $id;
                            $staff_id_new = $value['staff_id_new'];
                            unset($value['staff_id_new']);
                            $this->db->insert('tbl_business_fee_boiler_overtime_detail', $value);
                            $insert_id_item = $this->db->insert_id();
                            if ($insert_id_item) {
                                if (!empty($staff_id_new)) {
                                    foreach ($staff_id_new as $k => $v) {
                                        $v['business_fee_boiler_overtime_detail_id'] = $insert_id_item;
                                        $this->db->insert('tbl_business_fee_boiler_overtime_detail_staff', $v);
                                    }
                                }
                            }
                            $arrId[] = $insert_id_item;
                        }
                    }

                    if (empty($arrId)) {
                        $this->db->where('business_fee_boiler_overtime_id', $id);
                        $this->db->delete('tbl_business_fee_boiler_overtime_detail');
                    } else {
                        $this->db->where('business_fee_boiler_overtime_id', $id);
                        $this->db->where_not_in('id', $arrId);
                        $this->db->delete('tbl_business_fee_boiler_overtime_detail');
                    }

                    $get_code = get_table_where('tbl_business_fee_boiler_overtime', array('id' => $id), '', 'row');
                    activity_log_v2('edit_business_fee_boiler_overtime', 'tbl_business_fee_boiler_overtime', $id,
                        $get_code->name,
                        'Sửa tăng ca tháng phòng ban khác ['.$get_code->name.']');
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

        $data['staff'] = getPersonDeparmentdt($this->type);
        $this->db->select('tbl_personnel.*,tbldepartments.name as name_department');
        $this->db->from('tbl_personnel');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbl_personnel.departments', 'left');
        $this->db->where('status_work', 2);
        $this->db->where('tbldepartments.business_fee', $this->type);
        $data['staffNew'] = $this->db->get()->result_array();
        if (!empty($id)) {
            $data['id'] = $id;
            $data['title'] = lang('Sửa theo dõi tăng tháng');
            $data['businessFeeBoilerOvertime'] = get_table_where('tbl_business_fee_boiler_overtime', ['id' => $id], '',
                'row_array');
            $data['businessFeeBoilerDetailOvertime'] = get_table_where('tbl_business_fee_boiler_overtime_detail',
                ['business_fee_boiler_overtime_id' => $id]);

        } else {
            $data['id'] = '';
            $data['title'] = lang('Thêm theo dõi tăng ca tháng');
        }
        $this->load->view('admin/business_fee_other/add_business_fee_boiler_overtime', $data);
    }

    public function update_status_overtime()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        if (!empty($id)) {

            $this->db->where('id', $id);
            $businessFeeBoiler = $this->db->get('tbl_business_fee_boiler_overtime')->row_array();
            if ($businessFeeBoiler['status'] == $status) {
                echo json_encode([
                    'success' => false,
                    'message' => _l('Phiếu đang ở trạng thái này không thể duyệt được nữa'),
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
            $success = $this->db->update('tbl_business_fee_boiler_overtime', $data_update);
            if (!empty($success)) {
                $get_code = get_table_where('tbl_business_fee_boiler_overtime', array('id' => $id), '', 'row');
                activity_log_v2('status_business_fee_boiler_overtime', 'tbl_business_fee_boiler_overtime', $id,
                    $get_code->name,
                    'Duyệt phiếu theo dõi tăng ca tháng phòng ban khác ['.$get_code->name.']');
                echo json_encode([
                    'result' => $success,
                    'message' => _l('cong_update_true'),
                ]);
                die();
            }
        }
        echo json_encode([
            'result' => false,
            'message' => _l('cong_update_false'),
        ]);
        die();
    }

    public function update_status_overtime_child()
    {
        if (!$this->perApproveBusinessOvertime){
            echo json_encode([
                'success' => false,
                'message' => _l('Không có quyệt duyệt'),
            ]);
            die();
        }
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        $note = $this->input->post('note');
        if (!empty($id)) {

            $this->db->where('id', $id);
            $businessFeeBoiler = $this->db->get('tbl_business_fee_boiler_overtime_detail')->row_array();
            if ($businessFeeBoiler['status'] == $status) {
                echo json_encode([
                    'success' => false,
                    'message' => _l('Ngày tăng ca này đang ở trạng thái này không thể duyệt được nữa'),
                ]);
                die();
            }

            if ($status == 0) {
                $this->db->where('id', $businessFeeBoiler['business_fee_boiler_overtime_id']);
                $businessFeeBoilerCheck = $this->db->get('tbl_business_fee_boiler_overtime')->row_array();
                $this->db->from('tbl_business_fee_boiler_calculate_item');
                $this->db->join('tbl_business_fee_boiler_calculate',
                    'tbl_business_fee_boiler_calculate.id = tbl_business_fee_boiler_calculate_item.business_fee_boiler_calculate_id');
                $this->db->where('tbl_business_fee_boiler_calculate_item.staff_id',
                    $businessFeeBoilerCheck['staff_id']);
                $this->db->where('tbl_business_fee_boiler_calculate.month', $businessFeeBoilerCheck['month']);
                $this->db->where('tbl_business_fee_boiler_calculate.year', $businessFeeBoilerCheck['year']);
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)) {
                    echo json_encode([
                        'result' => false,
                        'message' => _l('Đã tính bảng tính công tác phí. Không thể bỏ duyệt !'),
                    ]);
                    die();
                }
            }
            if ($status == 2) {
                $this->db->where('id', $businessFeeBoiler['business_fee_boiler_overtime_id']);
                $businessFeeBoilerCheck = $this->db->get('tbl_business_fee_boiler_overtime')->row_array();
                $this->db->from('tbl_business_fee_boiler_calculate_item');
                $this->db->join('tbl_business_fee_boiler_calculate',
                    'tbl_business_fee_boiler_calculate.id = tbl_business_fee_boiler_calculate_item.business_fee_boiler_calculate_id');
                $this->db->where('tbl_business_fee_boiler_calculate_item.staff_id',
                    $businessFeeBoilerCheck['staff_id']);
                $this->db->where('tbl_business_fee_boiler_calculate.month', $businessFeeBoilerCheck['month']);
                $this->db->where('tbl_business_fee_boiler_calculate.year', $businessFeeBoilerCheck['year']);
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)) {
                    echo json_encode([
                        'result' => false,
                        'message' => _l('Đã tính bảng tính công tác phí!'),
                    ]);
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
            $success = $this->db->update('tbl_business_fee_boiler_overtime_detail', $data_update);
            if (!empty($success)) {
                $get_code = get_table_where('tbl_business_fee_boiler_overtime',
                    array('id' => $businessFeeBoiler['business_fee_boiler_overtime_id']), '', 'row');
                $get_code_child = get_table_where('tbl_business_fee_boiler_overtime_detail', array('id' => $id), '',
                    'row');
                activity_log_v2('status_business_fee_boiler_overtime_child', 'tbl_business_fee_boiler_overtime_detail',
                    $id,
                    $get_code->name,
                    'Duyệt phiếu theo dõi tăng ca tháng ['.$get_code->name.']['._dhau($get_code_child->date).']');
                echo json_encode([
                    'result' => $success,
                    'id' => $businessFeeBoiler['business_fee_boiler_overtime_id'],
                    'message' => _l('cong_update_true'),
                ]);
                die();
            }
        }
        echo json_encode([
            'result' => false,
            'message' => _l('cong_update_false'),
        ]);
        die();
    }

    public function clickAgreeAll()
    {
        if (!$this->perApproveBusinessOvertime){
            echo json_encode([
                'success' => false,
                'message' => _l('Không có quyệt duyệt'),
            ]);
            die();
        }
        $id = $this->input->post('id');
        if (!empty($id)) {
            $this->db->where('business_fee_boiler_overtime_id', $id);
            $this->db->where('status', 0);
            $businessFeeBoiler = $this->db->get('tbl_business_fee_boiler_overtime_detail')->result_array();

            $status = 1;
            $data_update = ['status' => $status];
            if (!empty($status)) {
                $data_update['staff_status'] = get_staff_user_id();
                $data_update['date_status'] = date('Y-m-d H:i:s');
            } else {
                $data_update['staff_status'] = null;
                $data_update['date_status'] = null;
                $data_update['status'] = 0;
            }
            $count = 0;
            if (!empty($businessFeeBoiler)) {
                foreach ($businessFeeBoiler as $key => $value) {
                    $this->db->where('id', $value['id']);
                    $success = $this->db->update('tbl_business_fee_boiler_overtime_detail', $data_update);
                    if ($success) {
                        $count++;
                        $get_code = get_table_where('tbl_business_fee_boiler_overtime', array('id' => $id), '', 'row');
                        $get_code_child = get_table_where('tbl_business_fee_boiler_overtime_detail',
                            array('id' => $value['id']),
                            '', 'row');
                        activity_log_v2('status_business_fee_boiler_overtime_child',
                            'tbl_business_fee_boiler_overtime_detail', $value['id'],
                            $get_code->name,
                            'Duyệt phiếu theo dõi tăng ca tháng ['.$get_code->name.']['._dhau($get_code_child->date).']');
                    }
                }
            }
            if (!empty($count)) {
                echo json_encode([
                    'result' => true,
                    'id' => $id,
                    'message' => _l('cong_update_true'),
                ]);
                die();
            }
        }
        echo json_encode([
            'result' => false,
            'message' => _l('cong_update_false'),
        ]);
        die();
    }

    public function checkExistsOvertime()
    {
        $data = [];
        $staff_id = $this->input->post('staff_id');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $id = $this->input->post('id');
        $data['result'] = 0;
        $data['id'] = 0;
        if ($staff_id && $month && $year) {
            $this->db->select('tbl_business_fee_boiler_overtime.id as id,status');
            $this->db->from('tbl_business_fee_boiler_overtime');
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
                $data['status'] = $result['status'];
            }
        }
        echo json_encode($data);
    }

    public function checkExists()
    {
        $data = [];
        $staff_id = $this->input->post('staff_id');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $id = $this->input->post('id');
        $data['result'] = 0;
        $data['id'] = 0;
        if ($staff_id && $month && $year) {
            $this->db->select('tbl_business_fee_boiler.id as id,status');
            $this->db->from('tbl_business_fee_boiler');
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
                $data['status'] = $result['status'];
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
        $data['startdate'] = date(''.$year.'-'.$month.'-01');
        $data['newdate'] = date(''.$year.'-'.$month.'-t');
        echo json_encode($data);
    }

    public function deleteTicketOvertime()
    {
        $id = $this->input->post('id');
        if (!empty($id)) {

            $this->db->where('id', $id);
            $businessFeeBoiler = $this->db->get('tbl_business_fee_boiler_overtime')->row_array();

            if ($businessFeeBoiler['status'] == 1) {
                echo json_encode([
                    'result' => false,
                    'message' => _l('Đã duyệt không thể xóa'),
                ]);
                die();
            }

            $this->db->from('tbl_business_fee_boiler_calculate_item');
            $this->db->join('tbl_business_fee_boiler_calculate',
                'tbl_business_fee_boiler_calculate.id = tbl_business_fee_boiler_calculate_item.business_fee_boiler_calculate_id');
            $this->db->where('tbl_business_fee_boiler_calculate_item.staff_id', $businessFeeBoiler['staff_id']);
            $this->db->where('tbl_business_fee_boiler_calculate.month', $businessFeeBoiler['month']);
            $this->db->where('tbl_business_fee_boiler_calculate.year', $businessFeeBoiler['year']);
            $checkExists = $this->db->count_all_results();
            if (!empty($checkExists)) {
                echo json_encode([
                    'result' => false,
                    'message' => _l('Đã tính bảng tính công tác phí. Không thể xóa !'),
                ]);
                die();
            }


            $get_code = get_table_where('tbl_business_fee_boiler_overtime', array('id' => $id), '', 'row');
            $this->db->where('id', $id);
            $success = $this->db->delete('tbl_business_fee_boiler_overtime');

            $itemOld = get_table_where('tbl_business_fee_boiler_overtime_detail',
                ['business_fee_boiler_overtime_id' => $id]);

            if (!empty($success)) {

                $this->db->where('business_fee_boiler_overtime_id', $id);
                $this->db->delete('tbl_business_fee_boiler_overtime_detail');

                if (!empty($itemOld)) {
                    foreach ($itemOld as $key => $value) {
                        $this->db->where('business_fee_boiler_overtime_detail_id', $value['id']);
                        $this->db->delete('tbl_business_fee_boiler_overtime_detail_staff');
                    }
                }

                activity_log_v2('delete_busniness_fee_boiler_overtime', 'tbl_business_fee_boiler_overtime', $id,
                    $get_code->name,
                    'Xoá phiếu theo dõi tăng ca tháng phòng ban khác ['.$get_code->name.']');
                echo json_encode([
                    'result' => $success,
                    'message' => _l('cong_update_true'),
                ]);
                die();
            }
        }
        echo json_encode([
            'result' => false,
            'message' => _l('cong_update_false'),
        ]);
        die();
    }

    public function business_fee_other_calculate()
    {
        if(!$this->perViewBusinessCalculate){
            access_denied();
        }
        $data = [];
        $data['title'] = lang('Bảng tính công tăng ca');
        $data['staff'] = getPersonDeparmentdt($this->type);
        $data['branch'] = getListBranch();
        $this->load->view('admin/business_fee_other/business_fee_boiler_calculate', $data);
    }

    public function getBusinessFeeBoilerCalculate()
    {
        $arrBranch = get_branch_staff();
        $staff_search = $this->input->post('staff_search');
        $month_search = $this->input->post('month_search');
        $year_search = $this->input->post('year_search');
        $branch_search = $this->input->post('branch_search');
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
            'tbl_business_fee_boiler_calculate_item.id as id',
            'tblstaff.code as code',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname',
            'tblroles.name as role',
            '(tbl_business_fee_boiler_calculate_item.salary_bhxh + tbl_business_fee_boiler_calculate_item.concurrently + tbl_business_fee_boiler_calculate_item.sales + tbl_business_fee_boiler_calculate_item.seniority) as salary_all',
            '(tbl_business_fee_boiler_calculate_item.salary_bhxh) as salary',
            '(tbl_business_fee_boiler_calculate_item.concurrently) as concurrently',
            'tbl_business_fee_boiler_calculate_item.sales as sales',
            'tbl_business_fee_boiler_calculate_item.seniority as seniority',
            'tbl_business_fee_boiler_calculate_item.total_weekday as total_weekday',
            'tbl_business_fee_boiler_calculate_item.total_weekday_money as total_weekday_money',
            'tbl_business_fee_boiler_calculate_item.total_sunday as total_sunday',
            'tbl_business_fee_boiler_calculate_item.total_sunday_money as total_sunday_money',
            'tbl_business_fee_boiler_calculate_item.total_holiday as total_holiday',
            'tbl_business_fee_boiler_calculate_item.total_holiday_money as total_holiday_money',
            'tbl_business_fee_boiler_calculate_item.total_weekday_night as total_weekday_night',
            'tbl_business_fee_boiler_calculate_item.total_weekday_night_money as total_weekday_night_money',
            'tbl_business_fee_boiler_calculate_item.total_sunday_night as total_sunday_night',
            'tbl_business_fee_boiler_calculate_item.total_sunday_night_money as total_sunday_night_money',
            'tbl_business_fee_boiler_calculate_item.total as total',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_business_fee_boiler_calculate';
        $where = [
        ];
        $filter = [];
        $join = [
            'INNER JOIN tbl_business_fee_boiler_calculate_item ON tbl_business_fee_boiler_calculate_item.business_fee_boiler_calculate_id = tbl_business_fee_boiler_calculate.id',
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_business_fee_boiler_calculate_item.staff_id',
            'LEFT JOIN '.$tbDepartment.' ON tb_department.staffid = tblstaff.staffid',
            'LEFT JOIN tblroles ON tblroles.roleid = tblstaff.role',
        ];

        array_push($where,
            'AND ( tbl_business_fee_boiler_calculate.type = '.$this->type.')');

        if (!empty($staff_search)) {
            array_push($where,
                'AND ( tbl_business_fee_boiler_calculate_item.staff_id IN ('.implode(',', $staff_search).'))');
        }
        if (!empty($month_search)) {
            array_push($where,
                'AND ( tbl_business_fee_boiler_calculate.month = '.$month_search.')');
        }
        if (!empty($year_search)) {
            array_push($where,
                'AND ( tbl_business_fee_boiler_calculate.year = '.$year_search.')');
        }
        if (!empty($branch_search)) {
            array_push($where,
                'AND ( tblstaff.branch_salary = '.$branch_search.')');
        }

        if (!$this->isAdmin) {
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                array_push($where, 'AND tblstaff.branch_salary IN ('.$coverStrBranch.')');
            } else {
                array_push($where,
                    'AND ( tbl_business_fee_boiler_calculate.id = 0)');
            }
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', [], []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $stt = 1;
        $total_salary = 0;
        $total_km = 0;
        $total_km_money = 0;
        $total_sunday = 0;
        $total_sunday_money = 0;
        $total_holiday = 0;
        $total_holiday_money = 0;
        $total_weekday = 0;
        $total_weekday_money = 0;
        $total_weekday_night = 0;
        $total_weekday_night_money = 0;
        $total_sunday_night = 0;
        $total_sunday_night_money = 0;
        $total_to_go_night = 0;
        $total_to_go_night_money = 0;
        $total_allowance = 0;
        $total_allowance_money = 0;
        $total_allowance_province = 0;
        $total_allowance_province_money = 0;
        $total_allowance_phone_money = 0;
        $total_allowance_bike_money = 0;
        $total_allowance_rice = 0;
        $total_allowance_rice_money = 0;
        $total = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;

            $row = array();

            $row[0] = '<div class="text-center checkbox" style="width: 40px"><input type="checkbox" name="items[]" id="check-item'.$aRow['id'].'" value="'.$aRow['id'].'"><label for="check-item'.$aRow['id'].'"></label></div>';
            $row[1] = '<div style="">'.$aRow['code'].'</div>';
            $row[2] = '<div style="width: 150px">'.$aRow['fullname'].'</div>';
            $row[3] = '<div style="" class="text-left">'.$aRow['role'].'</div>';
            $row[4] = '<div style="text-align: right;width: 100px">'.(!empty($aRow['salary_all']) ? formatMoney($aRow['salary_all']) : '').'</div>';
            $row[5] = '<div style="text-align: right;width: 100px">'.(!empty($aRow['salary']) ? formatMoney($aRow['salary']) : '').'</div>';
            $row[6] = '<div style="text-align: right;width: 100px">'.(!empty($aRow['concurrently']) ? formatMoney($aRow['concurrently']) : '').'</div>';
            $row[7] = '<div style="text-align: right;width: 100px">'.(!empty($aRow['sales']) ? formatMoney($aRow['sales']) : '').'</div>';
            $row[8] = '<div style="text-align: right;width: 100px">'.(!empty($aRow['seniority']) ? formatMoney($aRow['seniority']) : '').'</div>';
            $row[9] = '<div style="text-align: center">'.($aRow['total_sunday'] > 0 ? ($aRow['total_sunday']) : '').'</div>';
            $row[10] = '<div style="text-align: right">'.($aRow['total_sunday_money'] > 0 ? formatMoney($aRow['total_sunday_money']) : '').'</div>';
            $row[11] = '<div style="text-align: center">'.($aRow['total_holiday'] > 0 ? ($aRow['total_holiday']) : '').'</div>';
            $row[12] = '<div style="text-align: right">'.($aRow['total_holiday_money'] > 0 ? formatMoney($aRow['total_holiday_money']) : '').'</div>';
            $row[13] = '<div style="text-align: center;">'.($aRow['total_weekday'] > 0 ? ($aRow['total_weekday']) : '').'</div>';
            $row[14] = '<div style="text-align: right">'.($aRow['total_weekday_money'] > 0 ? formatMoney($aRow['total_weekday_money']) : '').'</div>';
            $row[15] = '<div style="text-align: right">'.($aRow['total_weekday_night'] > 0 ? formatMoney($aRow['total_weekday_night']) : '').'</div>';
            $row[16] = '<div style="text-align: right">'.($aRow['total_weekday_night_money'] > 0 ? formatMoney($aRow['total_weekday_night_money']) : '').'</div>';
            $row[17] = '<div style="text-align: right">'.($aRow['total_sunday_night'] > 0 ? formatMoney($aRow['total_sunday_night']) : '').'</div>';
            $row[18] = '<div style="text-align: right">'.($aRow['total_sunday_night_money'] > 0 ? formatMoney($aRow['total_sunday_night_money']) : '').'</div>';
            $row[19] = '<div style="text-align: right;width: 120px">'.($aRow['total'] > 0 ? formatMoney($aRow['total']) : '').'</div>';

            $total_sunday += $aRow['total_sunday'];
            $total_sunday_money += $aRow['total_sunday_money'];
            $total_holiday += $aRow['total_holiday'];
            $total_holiday_money += $aRow['total_holiday_money'];
            $total_weekday += $aRow['total_weekday'];
            $total_weekday_money += $aRow['total_weekday_money'];
            $total_weekday_night += $aRow['total_weekday_night'];
            $total_weekday_night_money += $aRow['total_weekday_night_money'];
            $total_sunday_night += $aRow['total_sunday_night'];
            $total_sunday_night_money += $aRow['total_sunday_night_money'];
            $total += $aRow['total'];

            $output['aaData'][] = $row;
            $stt++;

        }
        $output['total_km'] = $total_km;
        $output['total_km_money'] = $total_km_money;
        $output['total_sunday'] = $total_sunday;
        $output['total_sunday_money'] = $total_sunday_money;
        $output['total_holiday'] = $total_holiday;
        $output['total_holiday_money'] = $total_holiday_money;
        $output['total_weekday'] = $total_weekday;
        $output['total_weekday_money'] = $total_weekday_money;
        $output['total_weekday_night'] = $total_weekday_night;
        $output['total_weekday_night_money'] = $total_weekday_night_money;
        $output['total_sunday_night'] = $total_sunday_night;
        $output['total_sunday_night_money'] = $total_sunday_night_money;
        $output['total_to_go_night'] = $total_to_go_night;
        $output['total_to_go_night_money'] = $total_to_go_night_money;
        $output['total_allowance'] = $total_allowance;
        $output['total_allowance_money'] = $total_allowance_money;
        $output['total_allowance_province'] = $total_allowance_province;
        $output['total_allowance_province_money'] = $total_allowance_province_money;
        $output['total_allowance_phone_money'] = $total_allowance_phone_money;
        $output['total_allowance_bike_money'] = $total_allowance_bike_money;
        $output['total_allowance_rice'] = $total_allowance_rice;
        $output['total_allowance_rice_money'] = $total_allowance_rice_money;
        $output['total'] = $total;
        echo json_encode($output);
    }

    public function add_business_fee_boiler_calculate()
    {
        if (!$this->perAddBusinessCalculate){
            access_denied();
        }
        $coefficient = get_option('coefficient');
        $coefficient_sunday = get_option('coefficient_sunday');
        $coefficient_holiday = get_option('coefficient_holiday');
        $coefficient_default = get_option('coefficient_default');
        $coefficient_default_night = get_option('coefficient_default_night');
        $coefficient_sunday_night = get_option('coefficient_sunday_night');
        $day_work = get_option('day_work');
        $hour_day = get_option('hour_day');
        $rice_money = 0;
        if ($this->input->post('save')) {
            $data = [];
            $this->form_validation->set_rules('month', lang("month"), 'required');
            $this->form_validation->set_rules('year', lang("year"), 'required');
            $this->form_validation->set_rules('staff_id[]', lang("Nhân viên"), 'required');
            if ($this->form_validation->run() == true) {
//                 print_arrays($this->input->post());
                $month = $this->input->post('month');
                $year = $this->input->post('year');
                $counter = $this->input->post('counter');

                $arrPayrollItem = [];
                if (!empty($counter)) {
                    $allowance_post = $this->input->post('allowance');
                    $salary_bhxh_post = $this->input->post('salary_bhxh');
                    $salary_responsibility_post = ($this->input->post('salary_responsibility'));
                    $salary_position_post = ($this->input->post('salary_position'));
                    $gasonline_cars_post = ($this->input->post('gasonline_cars'));
                    $sales_post = ($this->input->post('sales'));
                    $concurrently_post = ($this->input->post('concurrently'));
                    $seniority_post = ($this->input->post('seniority'));
                    $phone_post = ($this->input->post('phone'));
                    $motel_post = ($this->input->post('motel'));
                    $allowance_phone_post = $this->input->post('allowance_phone');
                    $allowance_bike_post = $this->input->post('allowance_bike');
                    $total_km_post = $this->input->post('total_km');
                    $weekday_post = ($this->input->post('weekday'));
                    $sunday_post = ($this->input->post('sunday'));
                    $holiday_post = ($this->input->post('holiday'));
                    $weekday_night_post = ($this->input->post('weekday_night'));
                    $sunday_night_post = ($this->input->post('sunday_night'));
                    $to_go_noight_post = $this->input->post('to_go_noight');
                    $construction_allowance_post = $this->input->post('construction_allowance');
                    $allowance_survey_post = $this->input->post('allowance_survey');
                    $construction_allowance_province_post = $this->input->post('construction_allowance_province');
                    $rice_post = $this->input->post('rice');
                    $day_work_post = $this->input->post('total_date');
                    foreach ($counter as $key => $value) {
                        $staff_id = $this->input->post('staff_id')[$key];
                        $personnel = get_table_where('tblstaff', ['staffid' => $staff_id], '', 'row_array');
                        if (empty($personnel)) {
                            continue;
                        }

                        $allowance = $allowance_post[$key];
                        $salary_bhxh = $salary_bhxh_post[$key];
                        $salary_responsibility = number_unformat($salary_responsibility_post[$key]);
                        $salary_position = number_unformat($salary_position_post[$key]);
                        $gasonline_cars = number_unformat($gasonline_cars_post[$key]);
                        $sales = number_unformat($sales_post[$key]);
                        $concurrently = number_unformat($concurrently_post[$key]);
                        $seniority = number_unformat($seniority_post[$key]);
                        $phone = number_unformat($phone_post[$key]);
                        $motel = number_unformat($motel_post[$key]);
                        $allowance_phone = $allowance_phone_post[$key];
                        $allowance_bike = $allowance_bike_post[$key];
                        $total_km = $total_km_post[$key];
                        $weekday = number_unformat($weekday_post[$key]);
                        $sunday = number_unformat($sunday_post[$key]);
                        $holiday = number_unformat($holiday_post[$key]);
                        $weekday_night = number_unformat($weekday_night_post[$key]);
                        $sunday_night = number_unformat($sunday_night_post[$key]);
                        $to_go_noight = $to_go_noight_post[$key];
                        $construction_allowance = $construction_allowance_post[$key];
                        $allowance_survey = $allowance_survey_post[$key];
                        $construction_allowance_province = $construction_allowance_province_post[$key];
                        $rice = $rice_post[$key];
                        $day_work = $day_work_post[$key];

                        $allowance_phone = empty($allowance_phone) ? 0 : $allowance_phone;
                        $allowance_bike = empty($allowance_bike) ? 0 : $allowance_bike;
                        $total_km = empty($total_km) ? 0 : $total_km;
                        $weekday = empty($weekday) ? 0 : $weekday;
                        $sunday = empty($sunday) ? 0 : $sunday;
                        $holiday = empty($holiday) ? 0 : $holiday;
                        $to_go_noight = empty($to_go_noight) ? 0 : $to_go_noight;
                        $rice = empty($rice) ? 0 : $rice;

                        $salary = $salary_bhxh + $concurrently + $sales + $seniority;

                        $sunday_money = ($salary / $day_work / $hour_day) * $coefficient_sunday * $sunday;
                        $holiday_money = ($salary / $day_work / $hour_day) * $coefficient_holiday * $holiday;
                        $weekday_money = ($salary / $day_work / $hour_day) * $coefficient * $weekday;
                        $weekday_night_money = ($salary / $day_work / $hour_day) * $coefficient_default_night * $weekday_night;
                        $sunday_night_money = ($salary / $day_work / $hour_day) * $coefficient_sunday_night * $sunday_night;
                        $total = $sunday_money + $holiday_money + $weekday_money + $weekday_night_money + $sunday_night_money;

                        $arrPayrollItem[] = [
                            'staff_id' => $staff_id,
                            'salary' => ($salary_bhxh + $concurrently + $sales + $seniority),
                            'salary_bhxh' => $salary_bhxh,
                            'salary_responsibility' => $salary_responsibility,
                            'salary_position' => $salary_position,
                            'responsibility_salary' => 0,
                            'sales' => $sales,
                            'concurrently' => $concurrently,
                            'seniority' => $seniority,
                            'gasonline_cars' => $gasonline_cars,
                            'phone' => $phone,
                            'motel' => $motel,
                            'allowance' => $allowance,
                            'total_km' => $total_km,
                            'total_km_money' => 0,
                            'total_weekday' => $weekday,
                            'total_weekday_money' => $weekday_money,
                            'total_sunday' => $sunday,
                            'total_sunday_money' => $sunday_money,
                            'total_holiday' => $holiday,
                            'total_holiday_money' => $holiday_money,
                            'total_weekday_night' => $weekday_night,
                            'total_weekday_night_money' => $weekday_night_money,
                            'total_sunday_night' => $sunday_night,
                            'total_sunday_night_money' => $sunday_night_money,
                            'allowance_survey' => $allowance_survey,
                            'allowance_survey_money' => 0,
                            'construction_allowance_province' => $construction_allowance_province,
                            'construction_allowance_province_money' => 0,
                            'allowance_phone' => 0,
                            'allowance_bike' => 0,
                            'allowance_phone_id' => $allowance_phone,
                            'allowance_bike_id' => $allowance_bike,
                            'allowance_rice' => $rice,
                            'allowance_rice_money' => 0,
                            'total' => $total,
                        ];
                    }
                }
                if (empty($arrPayrollItem)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Không có dữ liệu');
                    echo json_encode($data);
                    die;
                }


                $Idpayroll = 0;
                $this->db->select('*');
                $this->db->from('tbl_business_fee_boiler_calculate');
                $this->db->where('tbl_business_fee_boiler_calculate.month', $month);
                $this->db->where('tbl_business_fee_boiler_calculate.year', $year);
                $this->db->where('tbl_business_fee_boiler_calculate.type', $this->type);
                $payroll = $this->db->get()->row_array();
                if (!empty($payroll)) {
                    $Idpayroll = $payroll['id'];
                } else {
                    $this->db->insert('tbl_business_fee_boiler_calculate', [
                        'month' => $month,
                        'year' => $year,
                        'date_created' => date('Y-m-d H:i'),
                        'created_by' => get_staff_user_id(),
                        'type' => $this->type,
                    ]);
                    $Idpayroll = $this->db->insert_id();
                }
                if ($Idpayroll) {

                    foreach ($arrPayrollItem as $key => $value) {
                        $value['business_fee_boiler_calculate_id'] = $Idpayroll;
                        $this->db->insert('tbl_business_fee_boiler_calculate_item', $value);
                        $payroll_item_id = $this->db->insert_id();
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
            die;
        } else {
            $data['title'] = lang('Tạo tính tăng ca tháng');
            $data['branch'] = getListBranch();
            $data['breadcrumb'] = [
                array(
                    'link' => base_url('admin/business_fee_other/business_fee_other_calculate'),
                    'page' => lang('Bảng tính tăng ca'),
                ),
                array('link' => '#', 'page' => $data['title']),
            ];
            $this->load->view('admin/business_fee_other/add_business_fee_boiler_calculate', $data);
        }

    }

    public function loadBusinessFeeCalculate()
    {
        $data = [];
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $branch_search = $this->input->get('branch_search');

        $salary_minimum_new = number_unformat(get_option('salary_minimum_new'));

        $tHead = '';
        $html = '';

        $tHead = '<tr>
            <th class="text-center">'.lang('STT').'</th>
            <th class="text-center">'.lang('Mã NV').'</th>
            <th class="text-center">'.lang('Họ tên').'</th>
            <th class="text-center">'.lang('Chức vụ').'</th>
            <th class="text-center">'.lang('Tổng lương').'</th>
            <th class="text-center">'.lang('Lương vị trí(LCB)').'</th>
            <th class="text-center">'.lang('Kiêm nhiệm').'</th>
            <th class="text-center">'.lang('Doanh số').'</th>
            <th class="text-center">'.lang('Thâm niên').'</th>
            <th class="text-center">'.lang('TC chủ nhật(H)').'</th>
            <th class="text-center">'.lang('LCB/26/8*2*H').'</th>
            <th class="text-center">'.lang('Tăng ca lễ (H)').'</th>
            <th class="text-center">'.lang('LCB/26/8*3*H').'</th>
            <th class="text-center">'.lang('TC thường (H)').'</th>
            <th class="text-center">'.lang('LCB/26/8*1.5*H').'</th>
            <th class="text-center">'.lang('TC đêm thường (H)').'</th>
            <th class="text-center">'.lang('LCB/26/8*'.get_option('coefficient_default_night').'*H').'</th>
            <th class="text-center">'.lang('TC đêm chủ nhật (H)').'</th>
            <th class="text-center">'.lang('LCB/26/8*'.get_option('coefficient_sunday_night').'*H').'</th>
            <th class="text-center">'.lang('Tổng').'</th>
        </tr>';


        $isPayroll = "(
            SELECT COUNT(*)
            FROM tbl_business_fee_boiler_calculate
            LEFT JOIN tbl_business_fee_boiler_calculate_item on tbl_business_fee_boiler_calculate_item.business_fee_boiler_calculate_id = tbl_business_fee_boiler_calculate.id
            WHERE tbl_business_fee_boiler_calculate.month = '$month' AND tbl_business_fee_boiler_calculate.year = '$year' AND tblstaff.staffid = tbl_business_fee_boiler_calculate_item.staff_id
        )";

        $countBusinessFeeOvertime = "(
            SELECT 
                tbl_business_fee_boiler_overtime.staff_id as staff_id,
                SUM(tbl_business_fee_boiler_overtime_detail.weekday) as weekday,
                SUM(tbl_business_fee_boiler_overtime_detail.sunday) as sunday,
                SUM(tbl_business_fee_boiler_overtime_detail.holiday) as holiday,
                SUM(tbl_business_fee_boiler_overtime_detail.go_night) as go_night,
                SUM(tbl_business_fee_boiler_overtime_detail.back_night) as back_night,
                SUM(tbl_business_fee_boiler_overtime_detail.construction_allowance) as construction_allowance,
                SUM(tbl_business_fee_boiler_overtime_detail.allowance_survey) as allowance_survey,
                SUM(tbl_business_fee_boiler_overtime_detail.construction_allowance_province) as construction_allowance_province
            FROM tbl_business_fee_boiler_overtime
            JOIN tbl_business_fee_boiler_overtime_detail ON tbl_business_fee_boiler_overtime_detail.business_fee_boiler_overtime_id = tbl_business_fee_boiler_overtime.id
            WHERE tbl_business_fee_boiler_overtime.month = '$month' AND tbl_business_fee_boiler_overtime.year = '$year' AND tbl_business_fee_boiler_overtime_detail.status = 1
            AND tbl_business_fee_boiler_overtime.type = '$this->type'
            GROUP BY tbl_business_fee_boiler_overtime.staff_id
        ) tb_count_business_fee_overtime";

        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";

        $this->db->select("
            tblstaff.staffid as staff_id,
            tblstaff.code as code,
            CONCAT(TRIM(tblstaff.firstname),' ',TRIM(tblstaff.lastname)) as fullname,
            tblroles.name as role,
            tblstaff.salary_bhxh as salary_bhxh,
            tblstaff.allowance as allowance,
            tblstaff.coefficient_responsibility as coefficient_responsibility,
            tblstaff.coefficient_position as coefficient_position,
            tblstaff.responsibility_salary as responsibility_salary,
            tblstaff.concurrently as concurrently,
            tblstaff.seniority as seniority,
            tblstaff.sales as sales,
            tblstaff.phone as phone,
            tblstaff.gasonline_cars as gasonline_cars,
            tblstaff.motel as motel,
            coalesce((tb_count_business_fee_overtime.weekday),0) as weekday,
            coalesce((tb_count_business_fee_overtime.sunday),0) as sunday,
            coalesce((tb_count_business_fee_overtime.holiday),0) as holiday,
            coalesce((tb_count_business_fee_overtime.go_night),0) as go_night,
            coalesce((tb_count_business_fee_overtime.back_night),0) as back_night,
            coalesce((tb_count_business_fee_overtime.allowance_survey),0) as allowance_survey,
            coalesce((tb_count_business_fee_overtime.construction_allowance_province),0) as construction_allowance_province,
        ", false);
        $this->db->from('tblstaff');
        $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->join("$countBusinessFeeOvertime", 'tb_count_business_fee_overtime.staff_id = tblstaff.staffid',
            'left');
        $this->db->where('(tblstaff.check_salary = 0 AND tblstaff.status_work != 2)');
        $this->db->where('tblstaff.branch_salary', $branch_search);
        $this->db->where("($isPayroll = 0)");
        $personnel = $this->db->get()->result_array();

        $listDate = getAllDateInMonth($month, $year, 'd/m');
        $countDate = 0;
        foreach ($listDate as $k => $value) {
            $day = date("d", strtotime($k));
            $format = 'D';
            $time = mktime(12, 0, 0, $month, $day, $year);
            $date_word = '';
            if (date('m', $time) == $month) {
                $date_word = date($format, $time);
            }
            if ($date_word != 'Sun') {
                $countDate++;
            }
        }

        $countDate = get_option('day_work');

        $index = 0;
        if (!empty($personnel)) {
            foreach ($personnel as $key => $value) {
                $staffid = $value['staff_id'];

                $coefficient_responsibility = $value['coefficient_responsibility'];
                $coefficient_position = $value['coefficient_position'];
                $responsibility_salary = $value['responsibility_salary'];
                $gasonline_cars = $value['gasonline_cars'];
                $sales = $value['sales'];
                $phone = $value['phone'];
                $motel = $value['motel'];
                $concurrently = $value['concurrently'];
                $seniority = $value['seniority'];
                $salary_responsibility = $salary_minimum_new * $coefficient_responsibility;
                $salary_position = $salary_minimum_new * $coefficient_position;

                $tdNumber = '<div class="text-center td-number">'.(++$key).'</div>';
                $tdCode = '<div class="td-code-staff">
                    '.$value['code'].'
                </div>';
                $tdFullname = '<div class="td-name-staff">
                    '.$value['fullname'].'
                </div>';
                $tdRole = '<div class="td-role-staff">
                    '.$value['role'].'
                </div>';
                $tdSalaryAll = '<div class="td-salary-staff">
                    '.(!empty($value['salary_bhxh'] + $concurrently + $sales + $seniority) ? formatMoney($value['salary_bhxh'] + $concurrently + $sales + $seniority) : '').'
                </div>';
                $tdSalary = '<div class="td-salary-staff">
                    '.(!empty($value['salary_bhxh']) ? formatMoney($value['salary_bhxh']) : '').'
                </div>';

                $tdSalaryConcurrently = '<div class="td-salary-concurrently-staff">
                    '.(!empty($concurrently) ? formatMoney($concurrently) : '').'
                </div>';
                $tdSalarySales = '<div class="td-salary-sales-staff">
                    '.(!empty($sales) ? formatMoney($sales) : '').'
                </div>';
                $tdSalarySeniority = '<div class="td-salary-seniority-staff">
                    '.(!empty($seniority) ? formatMoney($seniority) : '').'
                </div>';

                $weekday = !empty($value['weekday']) ? ($value['weekday']) : '';
                $sunday = !empty($value['sunday']) ? ($value['sunday']) : '';
                $holiday = !empty($value['holiday']) ? ($value['holiday']) : '';
                $total_night = $value['back_night'] + $value['go_night'];
                $total_night = !empty($total_night) ? formatNumber($total_night * 8) : '';
                $construction_allowance = !empty($value['allowance_survey']) ? formatNumber($value['allowance_survey']) : '';
                $construction_allowance_province = !empty($value['construction_allowance_province']) ? formatNumber($value['construction_allowance_province']) : '';
                $total_km = !empty($value['total_km']) ? formatNumber($value['total_km']) : '';


                $html .= '<tr>';
                $html .= '<td style="width: 50px;height:50px">'.$tdNumber.'</td>';

                $html .= '<td style="min-width: 80px;">'.$tdCode.'</td>';
                $html .= '<td style="min-width: 150px;">'.$tdFullname.'</td>';
                $html .= '<td style="min-width: 100px;">'.$tdRole.'</td>';
                $html .= '<td style="min-width: 100px;text-align: right">'.$tdSalaryAll.'</td>';
                $html .= '<td style="min-width: 100px;text-align: right">'.$tdSalary.'</td>';
                $html .= '<td style="min-width: 100px;text-align: right">'.$tdSalaryConcurrently.'</td>';
                $html .= '<td style="min-width: 100px;text-align: right">'.$tdSalarySales.'</td>';
                $html .= '<td style="min-width: 100px;text-align: right">'.$tdSalarySeniority.'</td>';
                $html .= '<td style="min-width: 100px;text-align: center"><input type="text" name="sunday[]" class="form-control sunday" style="width: 100px" value="'.$sunday.'"></td>';
                $html .= '<td style="min-width: 100px;" class="sunday_money"></td>';
                $html .= '<td style="min-width: 100px;text-align: center"><input type="text" name="holiday[]" class="form-control holiday" style="width: 100px" value="'.$holiday.'"></td>';
                $html .= '<td style="min-width: 100px;" class="holiday_money"></td>';
                $html .= '<td style="min-width: 100px;text-align: center"><input type="text" name="weekday[]" class="form-control weekday" style="width: 100px" value="'.$weekday.'"></td>';
                $html .= '<td style="min-width: 100px;" class="weekday_money"></td>';
                $html .= '<td style="min-width: 100px;text-align: center"><input type="text" name="weekday_night[]" class="form-control weekday_night" style="width: 100px" value=""></td>';
                $html .= '<td style="min-width: 100px;" class="weekday_night_money"></td>';
                $html .= '<td style="min-width: 100px;text-align: center"><input type="text" name="sunday_night[]" class="form-control sunday_night" style="width: 100px" value=""></td>';
                $html .= '<td style="min-width: 100px;" class="sunday_night_money"></td>';

                $weekday_new = $value['weekday'];
                $sunday_new = $value['sunday'];
                $holiday_new = $value['holiday'];
                $total_night_new = ($value['back_night'] + $value['go_night']) * 8;
                $construction_allowance_province_new = $value['construction_allowance_province'];
                $allowance_survey_new = $value['allowance_survey'];

                $html .= '<td style="min-width: 100px;text-align:right"><div class="total"></div>
                <input type="hidden" name="counter[]" class="form-control counter" value="'.$index.'">
                <input type="hidden" name="salary[]" class="form-control salary" value="'.($value['salary_bhxh'] + $concurrently + $sales + $seniority).'">
                <input type="hidden" name="sales[]" class="form-control sales" value="'.($sales).'">
                <input type="hidden" name="gasonline_cars[]" class="form-control gasonline_cars" value="'.($gasonline_cars).'">
                <input type="hidden" name="phone[]" class="form-control phone" value="'.($phone).'">
                <input type="hidden" name="motel[]" class="form-control motel" value="'.($motel).'">
                <input type="hidden" name="salary_bhxh[]" class="form-control salary_bhxh" value="'.$value['salary_bhxh'].'">
                <input type="hidden" name="concurrently[]" class="form-control concurrently" value="'.$concurrently.'">
                <input type="hidden" name="seniority[]" class="form-control seniority" value="'.$seniority.'">
                <input type="hidden" name="salary_responsibility[]" class="form-control salary_responsibility" value="'.$salary_responsibility.'">
                <input type="hidden" name="salary_position[]" class="form-control salary_position" value="'.$salary_position.'">
                <input type="hidden" name="allowance[]" class="form-control allowance" value="'.$value['allowance'].'">
                <input type="hidden" name="total_date[]" class="form-control total_date" value="'.$countDate.'">
                <input type="hidden" name="to_go_noight[]" class="form-control to_go_noight" value="'.$total_night_new.'">
                <input type="hidden" name="allowance_survey[]" class="form-control allowance_survey" value="'.$allowance_survey_new.'">
                <input type="hidden" name="construction_allowance_province[]" class="form-control construction_allowance_province" value="'.$construction_allowance_province_new.'">
                <input type="hidden" name="total_km[]" class="form-control total_km" value="0">
                <input type="hidden" name="staff_id[]" class="form-control staff_id" value="'.$staffid.'">
                </td>';

                $html .= '</tr>';
                $index++;
            }
        }

        $tfoot = '';
        if (empty($personnel)) {

            $tbDepartment = "(
                SELECT
                    tblstaff_departments.staffid as staffid,
                    GROUP_CONCAT(tbldepartments.name) as name_department
                FROM tbldepartments
                JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
                GROUP BY tblstaff_departments.staffid
            ) tb_department";

            $this->db->select("
                tblstaff.staffid as staff_id,
                tblstaff.code as code,
                CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as fullname,
                tblroles.name as role,
                tblstaff.salary_bhxh as salary_bhxh,
                coalesce((tb_count_business_fee_overtime.weekday),0) as weekday,
                coalesce((tb_count_business_fee_overtime.sunday),0) as sunday,
                coalesce((tb_count_business_fee_overtime.holiday),0) as holiday,
                coalesce((tb_count_business_fee_overtime.go_night),0) as go_night,
                coalesce((tb_count_business_fee_overtime.back_night),0) as back_night,
                coalesce((tb_count_business_fee_overtime.construction_allowance),0) as construction_allowance,
                coalesce((tb_count_business_fee_overtime.construction_allowance_province),0) as construction_allowance_province,
            ", false);
            $this->db->from('tblstaff');
            $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
            $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
            $this->db->join("$countBusinessFeeOvertime", 'tb_count_business_fee_overtime.staff_id = tblstaff.staffid',
                'left');
            $this->db->where('(tblstaff.check_salary = 0 AND tblstaff.status_work != 2)');
            $this->db->where('tblstaff.branch_salary', $branch_search);
            $this->db->where("($isPayroll = 0)");
            $personnelCheck = $this->db->get()->result_array();
            if (empty($personnelCheck)) {
                $data['month'] = $month;
                $data['year'] = $year;
                $data['branch'] = $branch_search;
                $this->load->view('admin/business_fee_other/load_view_empty', $data);
            } else {
                $data['tHead'] = $tHead;
                $data['tfoot'] = $tfoot;
                $data['html'] = $html;
                $data['coefficient'] = get_option('coefficient');
                $data['coefficient_sunday'] = get_option('coefficient_sunday');
                $data['coefficient_holiday'] = get_option('coefficient_holiday');
                $data['coefficient_default'] = get_option('coefficient_default');
                $data['coefficient_default_night'] = get_option('coefficient_default_night');
                $data['coefficient_sunday_night'] = get_option('coefficient_sunday_night');
                $data['day_work'] = COUNT_DAY_WORK;
                $data['hour_day'] = HOUR_DAY;
                $this->load->view('admin/business_fee_other/load_add_business_fee_boiler_calculate', $data);
            }
        } else {
            $data['tHead'] = $tHead;
            $data['tfoot'] = $tfoot;
            $data['html'] = $html;
            $data['coefficient'] = get_option('coefficient');
            $data['coefficient_sunday'] = get_option('coefficient_sunday');
            $data['coefficient_holiday'] = get_option('coefficient_holiday');
            $data['coefficient_default'] = get_option('coefficient_default');
            $data['coefficient_default_night'] = get_option('coefficient_default_night');
            $data['coefficient_sunday_night'] = get_option('coefficient_sunday_night');
            $data['day_work'] = COUNT_DAY_WORK;
            $data['hour_day'] = HOUR_DAY;
            $this->load->view('admin/business_fee_other/load_add_business_fee_boiler_calculate', $data);
        }
    }

    public function edit_business_fee_boiler_calculate()
    {
        if (!$this->perEditBusinessCalculate){
            access_denied();
        }
        $data = [];
        $coefficient = get_option('coefficient');
        $coefficient_sunday = get_option('coefficient_sunday');
        $coefficient_holiday = get_option('coefficient_holiday');
        $coefficient_default = get_option('coefficient_default');
        $coefficient_default_night = get_option('coefficient_default_night');
        $coefficient_sunday_night = get_option('coefficient_sunday_night');
        $day_work = get_option('day_work');
        $hour_day = get_option('hour_day');
        $survey_money = 0;
        $construction_province_money = 0;
        if ($this->input->post('save')) {
            $data = [];
            $this->form_validation->set_rules('month', lang("month"), 'required');
            $this->form_validation->set_rules('year', lang("year"), 'required');
            $this->form_validation->set_rules('staff_id[]', lang("Nhân viên"), 'required');
            if ($this->form_validation->run() == true) {
                $month = $this->input->post('month');
                $year = $this->input->post('year');
                $counter = $this->input->post('counter');
                $arrPayrollItem = [];
                if (!empty($counter)) {
                    $allowance_post = $this->input->post('allowance');
                    $salary_bhxh_post = $this->input->post('salary_bhxh');
                    $salary_responsibility_post = ($this->input->post('salary_responsibility'));
                    $salary_position_post = ($this->input->post('salary_position'));
                    $gasonline_cars_post = ($this->input->post('gasonline_cars'));
                    $sales_post = ($this->input->post('sales'));
                    $concurrently_post = ($this->input->post('concurrently'));
                    $seniority_post = ($this->input->post('seniority'));
                    $phone_post = ($this->input->post('phone'));
                    $motel_post = ($this->input->post('motel'));
                    $allowance_phone_post = $this->input->post('allowance_phone');
                    $allowance_bike_post = $this->input->post('allowance_bike');
                    $total_km_post = $this->input->post('total_km');
                    $weekday_post = ($this->input->post('weekday'));
                    $sunday_post = ($this->input->post('sunday'));
                    $holiday_post = ($this->input->post('holiday'));
                    $weekday_night_post = ($this->input->post('weekday_night'));
                    $sunday_night_post = ($this->input->post('sunday_night'));
                    $allowance_survey_post = $this->input->post('allowance_survey');
                    $construction_allowance_province_post = $this->input->post('construction_allowance_province');
                    $rice_post = $this->input->post('rice');
                    $day_work_post = $this->input->post('total_date');
                    $id_post = $this->input->post('id');

                    foreach ($counter as $key => $value) {
                        $staff_id = $this->input->post('staff_id')[$value];
                        $personnel = get_table_where('tblstaff', ['staffid' => $staff_id], '', 'row_array');
                        if (empty($personnel)) {
                            continue;
                        }

                        $id = $id_post[$key];

                        $this->db->from('tbl_business_fee_boiler_calculate');
                        $this->db->join('tbl_business_fee_boiler_calculate_item',
                            'tbl_business_fee_boiler_calculate_item.business_fee_boiler_calculate_id = tbl_business_fee_boiler_calculate.id',
                            'left');
                        $this->db->where('tbl_business_fee_boiler_calculate.month', $month);
                        $this->db->where('tbl_business_fee_boiler_calculate.year', $year);
                        $this->db->where('tbl_business_fee_boiler_calculate_item.staff_id', $staff_id);
                        $this->db->where('tbl_business_fee_boiler_calculate_item.id', $id);
                        $payRollItem = $this->db->get()->row_array();

                        $allowance = $allowance_post[$key];
                        $salary_bhxh = $salary_bhxh_post[$key];
                        $salary_responsibility = number_unformat($salary_responsibility_post[$key]);
                        $salary_position = number_unformat($salary_position_post[$key]);
                        $gasonline_cars = number_unformat($gasonline_cars_post[$key]);
                        $sales = number_unformat($sales_post[$key]);
                        $concurrently = number_unformat($concurrently_post[$key]);
                        $seniority = number_unformat($seniority_post[$key]);
                        $phone = number_unformat($phone_post[$key]);
                        $motel = number_unformat($motel_post[$key]);
                        $allowance_phone = $allowance_phone_post[$key];
                        $allowance_bike = $allowance_bike_post[$key];
                        $total_km = $total_km_post[$key];
                        $weekday = number_unformat($weekday_post[$key]);
                        $sunday = number_unformat($sunday_post[$key]);
                        $holiday = number_unformat($holiday_post[$key]);
                        $weekday_night = number_unformat($weekday_night_post[$key]);
                        $sunday_night = number_unformat($sunday_night_post[$key]);
                        $allowance_survey = $allowance_survey_post[$key];
                        $construction_allowance_province = $construction_allowance_province_post[$key];
                        $rice = $rice_post[$key];
                        $day_work = $day_work_post[$key];

                        $allowance_phone = empty($allowance_phone) ? 0 : $allowance_phone;
                        $allowance_bike = empty($allowance_bike) ? 0 : $allowance_bike;
                        $total_km = empty($total_km) ? 0 : $total_km;
                        $weekday = empty($weekday) ? 0 : $weekday;
                        $sunday = empty($sunday) ? 0 : $sunday;
                        $holiday = empty($holiday) ? 0 : $holiday;
                        $rice = empty($rice) ? 0 : $rice;

                        $salary = $salary_bhxh + $concurrently + $sales + $seniority;

                        $total_km_money = 0;
                        $sunday_money = ($salary / $day_work / $hour_day) * $coefficient_sunday * $sunday;
                        $holiday_money = ($salary / $day_work / $hour_day) * $coefficient_holiday * $holiday;
                        $weekday_money = ($salary / $day_work / $hour_day) * $coefficient * $weekday;
                        $weekday_night_money = ($salary / $day_work / $hour_day) * $coefficient_default_night * $weekday_night;
                        $sunday_night_money = ($salary / $day_work / $hour_day) * $coefficient_sunday_night * $sunday_night;
                        $allowance_survey_money = $allowance_survey * $survey_money;
                        $construction_allowance_province_money = $construction_allowance_province * $construction_province_money;
                        $rice_money_new = 0;
                        $allowance_phone_money = 0;
                        $allowance_bike_money = 0;
                        $total = $sunday_money + $holiday_money + $weekday_money + $weekday_night_money + $sunday_night_money;

                        $arrPayrollItem[] = [
                            'id' => $id,
                            'staff_id' => $staff_id,
                            'salary' => ($salary_bhxh + $concurrently + $sales + $seniority),
                            'salary_bhxh' => $salary_bhxh,
                            'salary_responsibility' => $salary_responsibility,
                            'salary_position' => $salary_position,
                            'responsibility_salary' => 0,
                            'sales' => $sales,
                            'concurrently' => $concurrently,
                            'seniority' => $seniority,
                            'gasonline_cars' => $gasonline_cars,
                            'phone' => $phone,
                            'motel' => $motel,
                            'allowance' => $allowance,
                            'total_km' => $total_km,
                            'total_km_money' => $total_km_money,
                            'total_weekday' => $weekday,
                            'total_weekday_money' => $weekday_money,
                            'total_sunday' => $sunday,
                            'total_sunday_money' => $sunday_money,
                            'total_holiday' => $holiday,
                            'total_holiday_money' => $holiday_money,
                            'total_weekday_night' => $weekday_night,
                            'total_weekday_night_money' => $weekday_night_money,
                            'total_sunday_night' => $sunday_night,
                            'total_sunday_night_money' => $sunday_night_money,
                            'allowance_survey' => $allowance_survey,
                            'allowance_survey_money' => $allowance_survey_money,
                            'construction_allowance_province' => $construction_allowance_province,
                            'construction_allowance_province_money' => $construction_allowance_province_money,
                            'allowance_phone' => $allowance_phone_money,
                            'allowance_bike' => $allowance_bike_money,
                            'allowance_rice' => $rice,
                            'allowance_rice_money' => $rice_money_new,
                            'total' => $total,
                            'allowance_phone_id' => $allowance_phone,
                            'allowance_bike_id' => $allowance_bike,
                            'business_fee_boiler_calculate_id' => $payRollItem['business_fee_boiler_calculate_id'],
                        ];

                    }
                }
                if (empty($arrPayrollItem)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Không có dữ liệu');
                    echo json_encode($data);
                    die;
                }

                $success = false;
                foreach ($arrPayrollItem as $key => $value) {
                    $this->db->where('id', $value['id']);
                    $success = $this->db->update('tbl_business_fee_boiler_calculate_item', $value);
                }
                if ($success) {

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
        } else {
            $year = $this->input->get('year');
            $month = $this->input->get('month');
            $branch_new = $this->input->get('branch');

            $ids = trim($this->input->get('ids'), ',');
            if (!$year || !$month || !$branch_new) {
                redirect(admin_url('business_fee_other/business_fee_other_calculate'));
            }
            $ids = explode(',', $ids);
            $result = [];
            $this->db->select('tbl_business_fee_boiler_calculate_item.*');
            $this->db->from('tbl_business_fee_boiler_calculate_item');
            $this->db->join('tbl_business_fee_boiler_calculate',
                'tbl_business_fee_boiler_calculate.id = tbl_business_fee_boiler_calculate_item.business_fee_boiler_calculate_id',
                'left');
            $this->db->join('tblstaff',
                'tblstaff.staffid = tbl_business_fee_boiler_calculate_item.staff_id',
                'inner');
            $this->db->where('tbl_business_fee_boiler_calculate.month', $month);
            $this->db->where('tbl_business_fee_boiler_calculate.year', $year);
            $this->db->where('tblstaff.branch_salary', $branch_new);
            $this->db->where('tbl_business_fee_boiler_calculate.type', $this->type);
            $payrollItems = $this->db->get()->result_array();
            $idss = [];
            if (!empty($payrollItems)) {
                foreach ($payrollItems as $key => $value) {
                    $idss [] = $value['id'];
                }
            }
            $data['month'] = $month;
            $data['year'] = $year;
            $data['branch_new'] = $branch_new;
            $idss = implode(',', $idss);
            $data['ids'] = $idss;
            $data['payroll'] = $result;
            $data['branch'] = getListBranch();
            $data['title'] = lang('Sửa tính tăng ca tháng');
            $data['breadcrumb'] = [
                array(
                    'link' => base_url('admin/business_fee_other/business_fee_other_calculate'),
                    'page' => lang('Bảng tính tăng ca'),
                ),
                array('link' => '#', 'page' => $data['title']),
            ];
            $this->load->view('admin/business_fee_other/edit_business_fee_boiler_calculate', $data);
        }
    }

    public function loadBusinessFeeCalculateEdit()
    {
        $data = [];
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $branch = $this->input->post('branch');
        $ids = $this->input->post('ids');
        $ids = explode(',', $ids);


        $tHead = '';
        $html = '';

        $tHead = '<tr>
            <th class="text-center">'.lang('STT').'</th>
            <th class="text-center">'.lang('Mã NV').'</th>
            <th class="text-center">'.lang('Họ tên').'</th>
            <th class="text-center">'.lang('Chức vụ').'</th>
            <th class="text-center">'.lang('Tổng lương').'</th>
            <th class="text-center">'.lang('Lương vị trí(LCB)').'</th>
            <th class="text-center">'.lang('Kiêm nhiệm').'</th>
            <th class="text-center">'.lang('Doanh số').'</th>
            <th class="text-center">'.lang('Thâm niên').'</th>
            <th class="text-center">'.lang('TC chủ nhật(H)').'</th>
            <th class="text-center">'.lang('LCB/26/8*2*H').'</th>
            <th class="text-center">'.lang('Tăng ca lễ (H)').'</th>
            <th class="text-center">'.lang('LCB/26/8*3*H').'</th>
            <th class="text-center">'.lang('TC thường (H)').'</th>
            <th class="text-center">'.lang('LCB/26/8*1.5*H').'</th>
            <th class="text-center">'.lang('TC đêm thường (H)').'</th>
            <th class="text-center">'.lang('LCB/26/8*'.get_option('coefficient_default_night').'*H').'</th>
            <th class="text-center">'.lang('TC đêm chủ nhật (H)').'</th>
            <th class="text-center">'.lang('LCB/26/8*'.get_option('coefficient_sunday_night').'*H').'</th>
            <th class="text-center">'.lang('Tổng').'</th>
        </tr>';


        $payRollItems = [];

        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";

        $this->db->select("
            tbl_business_fee_boiler_calculate_item.id as id,
            tbl_business_fee_boiler_calculate_item.staff_id as staff_id,
            tblstaff.code as code,
            CONCAT(TRIM(tblstaff.firstname),' ',TRIM(tblstaff.lastname)) as fullname,
            tblroles.name as role,
            tbl_business_fee_boiler_calculate_item.salary as salary_all,
            tbl_business_fee_boiler_calculate_item.salary_bhxh as salary_bhxh,
            tbl_business_fee_boiler_calculate_item.salary_responsibility as salary_responsibility,
            tbl_business_fee_boiler_calculate_item.salary_position as salary_position,
            tbl_business_fee_boiler_calculate_item.responsibility_salary as responsibility_salary,
            tbl_business_fee_boiler_calculate_item.sales as sales,
            tbl_business_fee_boiler_calculate_item.concurrently as concurrently,
            tbl_business_fee_boiler_calculate_item.seniority as seniority,
            tbl_business_fee_boiler_calculate_item.gasonline_cars as gasonline_cars,
            tbl_business_fee_boiler_calculate_item.concurrently as concurrently,
            tbl_business_fee_boiler_calculate_item.concurrently as concurrently,
            tbl_business_fee_boiler_calculate_item.phone as phone,
            tbl_business_fee_boiler_calculate_item.motel as motel,
            tbl_business_fee_boiler_calculate_item.allowance as allowance,
            coalesce((tbl_business_fee_boiler_calculate_item.total_km),0) as total_km,
            coalesce((tbl_business_fee_boiler_calculate_item.total_km_money),0) as total_km_money,
            coalesce((tbl_business_fee_boiler_calculate_item.total_weekday),0) as weekday,
            coalesce((tbl_business_fee_boiler_calculate_item.total_weekday_money),0) as total_weekday_money,
            coalesce((tbl_business_fee_boiler_calculate_item.total_sunday),0) as sunday,
            coalesce((tbl_business_fee_boiler_calculate_item.total_sunday_money),0) as total_sunday_money,
            coalesce((tbl_business_fee_boiler_calculate_item.total_holiday),0) as holiday,
            coalesce((tbl_business_fee_boiler_calculate_item.total_holiday_money),0) as total_holiday_money,
            coalesce((tbl_business_fee_boiler_calculate_item.total_weekday_night),0) as total_weekday_night,
            coalesce((tbl_business_fee_boiler_calculate_item.total_weekday_night_money),0) as total_weekday_night_money,
            coalesce((tbl_business_fee_boiler_calculate_item.total_sunday_night),0) as total_sunday_night,
            coalesce((tbl_business_fee_boiler_calculate_item.total_sunday_night_money),0) as total_sunday_night_money,
            coalesce((tbl_business_fee_boiler_calculate_item.to_go_noight),0) as go_night,
            coalesce((tbl_business_fee_boiler_calculate_item.to_go_noight_money),0) as to_go_noight_money,
            coalesce((tbl_business_fee_boiler_calculate_item.allowance_survey),0) as allowance_survey,
            coalesce((tbl_business_fee_boiler_calculate_item.allowance_survey_money),0) as allowance_survey_money,
            coalesce((tbl_business_fee_boiler_calculate_item.construction_allowance),0) as construction_allowance,
            coalesce((tbl_business_fee_boiler_calculate_item.construction_allowance_money),0) as construction_allowance_money,
            coalesce((tbl_business_fee_boiler_calculate_item.construction_allowance_province),0) as construction_allowance_province,
            coalesce((tbl_business_fee_boiler_calculate_item.construction_allowance_province_money),0) as construction_allowance_province_money,
            coalesce((tbl_business_fee_boiler_calculate_item.allowance_phone),0) as allowance_phone,
            coalesce((tbl_business_fee_boiler_calculate_item.allowance_bike),0) as allowance_bike,
            coalesce((tbl_business_fee_boiler_calculate_item.allowance_rice),0) as allowance_rice,
            coalesce((tbl_business_fee_boiler_calculate_item.allowance_rice_money),0) as allowance_rice_money,
            coalesce((tbl_business_fee_boiler_calculate_item.allowance_phone_id),0) as allowance_phone_id,
            coalesce((tbl_business_fee_boiler_calculate_item.allowance_bike_id),0) as allowance_bike_id,
            ", false);
        $this->db->from('tbl_business_fee_boiler_calculate_item');
        $this->db->join('tbl_business_fee_boiler_calculate',
            'tbl_business_fee_boiler_calculate.id = tbl_business_fee_boiler_calculate_item.business_fee_boiler_calculate_id',
            'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_business_fee_boiler_calculate_item.staff_id', 'left');
        $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->where_in('tbl_business_fee_boiler_calculate_item.id', $ids);
        $payRollItems = $this->db->get()->result_array();

        $listDate = getAllDateInMonth($month, $year, 'd/m');
        $countDate = 0;
        foreach ($listDate as $k => $value) {
            $day = date("d", strtotime($k));
            $format = 'D';
            $time = mktime(12, 0, 0, $month, $day, $year);
            $date_word = '';
            if (date('m', $time) == $month) {
                $date_word = date($format, $time);
            }
            if ($date_word != 'Sun') {
                $countDate++;
            }
        }

        $countDate = get_option('day_work');

        $index = 0;
        if (!empty($payRollItems)) {
            foreach ($payRollItems as $key => $value) {
                $staffid = $value['staff_id'];

                $tdNumber = '<div class="text-center td-number">'.(++$key).'</div>';
                $tdCode = '<div class="td-code-staff">
                    '.$value['code'].'
                </div>';
                $tdFullname = '<div class="td-name-staff">
                    '.$value['fullname'].'
                </div>';
                $tdRole = '<div class="td-role-staff">
                    '.$value['role'].'
                </div>';
                $tdSalaryAll = '<div class="td-salary-all">
                    '.(!empty($value['salary_all']) ? formatMoney($value['salary_all']) : '').'
                </div>';
                $tdSalary = '<div class="td-salary-staff">
                    '.(!empty($value['salary_bhxh']) ? formatMoney($value['salary_bhxh']) : '').'
                </div>';
                $tdSalaryConcurrently = '<div class="td-salary-seniority-staff">
                    '.(!empty($value['seniority']) ? formatMoney($value['seniority']) : '').'
                </div>';
                $tdSalarySales = '<div class="td-salary-concurrently-staff">
                    '.(!empty($value['sales']) ? formatMoney($value['sales']) : '').'
                </div>';
                $tdSalarySeniority = '<div class="td-salary-seniority-staff">
                    '.(!empty($value['seniority']) ? formatMoney($value['seniority']) : '').'
                </div>';

                $total_km = ($value['total_km']);
                $total_km_money = ($value['total_km_money']);
                $weekday = ($value['weekday']);
                $weekday_money = ($value['total_weekday_money']);
                $sunday = ($value['sunday']);
                $sunday_money = ($value['total_sunday_money']);
                $holiday = ($value['holiday']);
                $holiday_money = ($value['total_holiday_money']);
                $weekday_night = ($value['total_weekday_night']);
                $sunday_night = ($value['total_sunday_night']);
                $total_night = $value['go_night'];
                $total_night_money = $value['to_go_noight_money'];
                $construction_allowance = ($value['allowance_survey']);
                $construction_allowance_money = ($value['construction_allowance_money']);
                $construction_allowance_province = ($value['construction_allowance_province']);
                $construction_allowance_province_money = ($value['construction_allowance_province_money']);
                $allowance_phone = ($value['allowance_phone_id']);
                $allowance_bike = ($value['allowance_bike_id']);
                $allowance_rice = ($value['allowance_rice']);
                $allowance_rice_money = ($value['allowance_rice_money']);
                $id = $value['id'];

                $this->db->select('
                    coalesce(SUM(tbl_business_fee_boiler_overtime_detail.weekday),0) as weekday,
                    coalesce(SUM(tbl_business_fee_boiler_overtime_detail.sunday),0) as sunday,
                    coalesce(SUM(tbl_business_fee_boiler_overtime_detail.holiday),0) as holiday,
                    coalesce(SUM(tbl_business_fee_boiler_overtime_detail.go_night),0) + coalesce(SUM(tbl_business_fee_boiler_overtime_detail.back_night),0) as go_night,
                    coalesce(SUM(tbl_business_fee_boiler_overtime_detail.construction_allowance),0) as construction_allowance,
                    coalesce(SUM(tbl_business_fee_boiler_overtime_detail.allowance_survey),0) as allowance_survey,
                    coalesce(SUM(tbl_business_fee_boiler_overtime_detail.construction_allowance_province),0) as construction_allowance_province
                ');
                $this->db->from('tbl_business_fee_boiler_overtime');
                $this->db->join('tbl_business_fee_boiler_overtime_detail',
                    'tbl_business_fee_boiler_overtime_detail.business_fee_boiler_overtime_id = tbl_business_fee_boiler_overtime.id');
                $this->db->where('tbl_business_fee_boiler_overtime.month', $month);
                $this->db->where('tbl_business_fee_boiler_overtime.year', $year);
                $this->db->where('tbl_business_fee_boiler_overtime.staff_id', $staffid);
                $this->db->where('tbl_business_fee_boiler_overtime.status', 1);
                $this->db->where('tbl_business_fee_boiler_overtime.type', $this->type);
                $boilerOvertime = $this->db->get()->row_array();


                if ($weekday != $boilerOvertime['weekday'] || $sunday != $boilerOvertime['sunday']
                    || $holiday != $boilerOvertime['holiday'] || $construction_allowance != $value['allowance_survey']
                    || $construction_allowance_province != $value['construction_allowance_province']
                ) {

                    $boilerOvertime['weekday'] = $value['weekday'] > $boilerOvertime['weekday'] ? $value['weekday'] : $boilerOvertime['weekday'];
                    $boilerOvertime['sunday'] = $value['sunday'] > $boilerOvertime['sunday'] ? $value['sunday'] : $boilerOvertime['sunday'];
                    $boilerOvertime['holiday'] = $value['holiday'] > $boilerOvertime['holiday'] ? $value['holiday'] : $boilerOvertime['holiday'];

                    $weekday = $boilerOvertime['weekday'] > 0 ? formatNumber($boilerOvertime['weekday']) : '';
                    $sunday = $boilerOvertime['sunday'] > 0 ? formatNumber($boilerOvertime['sunday']) : '';
                    $holiday = $boilerOvertime['holiday'] > 0 ? formatNumber($boilerOvertime['holiday']) : '';
                    $construction_allowance = $boilerOvertime['allowance_survey'] > 0 ? formatNumber($boilerOvertime['allowance_survey']) : '';
                    $construction_allowance_province = $boilerOvertime['construction_allowance_province'] > 0 ? formatNumber($boilerOvertime['construction_allowance_province']) : '';
                    $allowance_rice = !empty($value['allowance_rice']) ? $value['allowance_rice'] : '';

                    $weekday_new = $boilerOvertime['weekday'];
                    $sunday_new = $boilerOvertime['sunday'];
                    $holiday_new = $boilerOvertime['holiday'];
                    $construction_allowance_new = $boilerOvertime['allowance_survey'];
                    $construction_allowance_province_new = $boilerOvertime['construction_allowance_province'];
                    $allowance_rice_new = 0;
                } else {
                    $weekday_new = $value['weekday'];
                    $sunday_new = $value['sunday'];
                    $holiday_new = $value['holiday'];
                    $construction_allowance_new = $value['allowance_survey'];
                    $construction_allowance_province_new = $value['construction_allowance_province'];
                    $allowance_rice_new = $value['allowance_rice'];


                    $total_km = $value['total_km'] > 0 ? formatNumber($value['total_km']) : '';
                    $weekday = $value['weekday'] > 0 ? formatNumber($value['weekday']) : '';
                    $sunday = $value['sunday'] > 0 ? formatNumber($value['sunday']) : '';
                    $holiday = $value['holiday'] > 0 ? formatNumber($value['holiday']) : '';
                    $construction_allowance = $value['allowance_survey'] > 0 ? formatNumber($value['allowance_survey']) : '';
                    $construction_allowance_province = $value['construction_allowance_province'] > 0 ? formatNumber($value['construction_allowance_province']) : '';
                    $allowance_rice = $value['allowance_rice'] > 0 ? formatNumber($value['allowance_rice']) : '';

                }

                $weekday = $value['weekday'] > 0 ? ($value['weekday']) : '';
                $sunday = $value['sunday'] > 0 ? ($value['sunday']) : '';
                $holiday = $value['holiday'] > 0 ? ($value['holiday']) : '';


                $html .= '<tr>';
                $html .= '<td style="width: 50px;height:50px">'.$tdNumber.'</td>';

                $html .= '<td style="min-width: 80px;">'.$tdCode.'</td>';
                $html .= '<td style="min-width: 150px;">'.$tdFullname.'</td>';
                $html .= '<td style="min-width: 100px;">'.$tdRole.'</td>';
                $html .= '<td style="min-width: 100px;text-align: right">'.$tdSalaryAll.'</td>';
                $html .= '<td style="min-width: 100px;text-align: right">'.$tdSalary.'</td>';
                $html .= '<td style="min-width: 100px;text-align: right">'.$tdSalaryConcurrently.'</td>';
                $html .= '<td style="min-width: 100px;text-align: right">'.$tdSalarySales.'</td>';
                $html .= '<td style="min-width: 100px;text-align: right">'.$tdSalarySeniority.'</td>';
                $html .= '<td style="min-width: 100px;text-align: center"><input type="text" name="sunday[]" class="form-control sunday" style="width: 100px" value="'.$sunday.'"></td>';
                $html .= '<td style="min-width: 100px;" class="sunday_money"></td>';
                $html .= '<td style="min-width: 100px;text-align: center"><input type="text" name="holiday[]" class="form-control holiday" style="width: 100px" value="'.$holiday.'"></td>';
                $html .= '<td style="min-width: 100px;" class="holiday_money"></td>';
                $html .= '<td style="min-width: 100px;text-align: center"><input type="text" name="weekday[]" class="form-control weekday" style="width: 100px" value="'.$weekday.'"></td>';
                $html .= '<td style="min-width: 100px;" class="weekday_money"></td>';
                $html .= '<td style="min-width: 100px;text-align: center"><input type="text" name="weekday_night[]" class="form-control weekday_night" style="width: 100px" value="'.$weekday_night.'"></td>';
                $html .= '<td style="min-width: 100px;" class="weekday_night_money"></td>';
                $html .= '<td style="min-width: 100px;text-align: center"><input type="text" name="sunday_night[]" class="form-control sunday_night" style="width: 100px" value="'.$sunday_night.'"></td>';
                $html .= '<td style="min-width: 100px;" class="sunday_night_money"></td>';

                $html .= '<td style="min-width: 100px;text-align:right"><div class="total"></div>
                <input type="hidden" name="counter[]" class="form-control counter" value="'.$index.'">
                <input type="hidden" name="id[]" class="form-control id" value="'.$id.'">
                <input type="hidden" name="total_date[]" class="form-control total_date" value="'.$countDate.'">
                <input type="hidden" name="salary[]" class="form-control salary" value="'.($value['salary_bhxh'] + $value['concurrently'] + $value['sales'] + $value['seniority']).'">
                <input type="hidden" name="sales[]" class="form-control sales" value="'.$value['sales'].'">
                <input type="hidden" name="concurrently[]" class="form-control concurrently" value="'.$value['concurrently'].'">
                <input type="hidden" name="seniority[]" class="form-control seniority" value="'.$value['seniority'].'">
                <input type="hidden" name="gasonline_cars[]" class="form-control gasonline_cars" value="'.$value['gasonline_cars'].'">
                <input type="hidden" name="phone[]" class="form-control phone" value="'.$value['phone'].'">
                <input type="hidden" name="motel[]" class="form-control motel" value="'.$value['motel'].'">
                <input type="hidden" name="salary_bhxh[]" class="form-control salary_bhxh" value="'.$value['salary_bhxh'].'">
                <input type="hidden" name="salary_responsibility[]" class="form-control salary_responsibility" value="'.$value['salary_responsibility'].'">
                <input type="hidden" name="salary_position[]" class="form-control salary_position" value="'.$value['salary_position'].'">
                <input type="hidden" name="allowance[]" class="form-control allowance" value="'.$value['allowance'].'">
                <input type="hidden" name="allowance_survey[]" class="form-control allowance_survey" value="'.$construction_allowance_new.'">
                <input type="hidden" name="construction_allowance_province[]" class="form-control construction_allowance_province" value="'.$construction_allowance_province_new.'">
                <input type="hidden" name="staff_id[]" class="form-control staff_id" value="'.$staffid.'">
                </td>';

                $html .= '</tr>';
                $index++;
            }
        }

        $tfoot = '';
        $data['tHead'] = $tHead;
        $data['tfoot'] = $tfoot;
        $data['html'] = $html;
        $data['coefficient'] = get_option('coefficient');
        $data['coefficient_sunday'] = get_option('coefficient_sunday');
        $data['coefficient_holiday'] = get_option('coefficient_holiday');
        $data['coefficient_default'] = get_option('coefficient_default');
        $data['coefficient_default_night'] = get_option('coefficient_default_night');
        $data['coefficient_sunday_night'] = get_option('coefficient_sunday_night');
        $data['day_work'] = get_option('day_work');
        $data['hour_day'] = get_option('hour_day');
        $this->load->view('admin/business_fee_other/load_edit_business_fee_boiler_calculate', $data);
    }

    public function load_view_edit_chose()
    {
//        if (!$this->perEditOwnPayrollSalary) {
//            accessDenied($js = true);
//        }
        $this->load->view('admin/business_fee_other/load_view_chose_edit_payroll');
    }

    public function deletePayroll()
    {
        if (!$this->perDeleteBusinessCalculate) {
            $data['result'] = 0;
            $data['message'] = lang('Truy cập bị từ chối');
            echo json_encode($data); die;
        }
        $data = [];

        if ($this->input->post()) {
            $ids = trim($this->input->post('ids'), ',');
            if (!$ids) {
                $data['result'] = 0;
                $data['message'] = lang('no_data_exists');
                echo json_encode($data);

                return;
            }
            $errors = '';
            $count = 0;
            $ids = explode(',', $ids);
            $ids = array_unique($ids);
            if (!empty($ids)) {
                foreach ($ids as $key => $id) {
                    $this->db->select('tbl_business_fee_boiler_calculate_item.staff_id,month,year');
                    $this->db->from('tbl_business_fee_boiler_calculate_item');
                    $this->db->join('tbl_business_fee_boiler_calculate',
                        'tbl_business_fee_boiler_calculate.id = tbl_business_fee_boiler_calculate_item.business_fee_boiler_calculate_id');
                    $this->db->where('tbl_business_fee_boiler_calculate_item.id', $id);
                    $payroll = $this->db->get()->row_array();

                    $month = $payroll['month'] < 10 ? '0'.$payroll['month'] : $payroll['month'];

                    $this->db->from('tbl_payroll_item');
                    $this->db->join('tbl_payroll',
                        'tbl_payroll.id = tbl_payroll_item.payroll_id');
                    $this->db->where('tbl_payroll.month', $month);
                    $this->db->where('tbl_payroll.year', $payroll['year']);
                    $this->db->where('tbl_payroll_item.staff_id', $payroll['staff_id']);
                    $payrollItem = $this->db->get()->row_array();
                    if (!empty($payrollItem)) {
                        $data['result'] = 0;
                        $data['message'] = 'Có nhân viên đã tính bảng lương không thể xóa !';
                        echo json_encode($data);
                        die();
                    }
                }
            }

            if (!empty($ids)) {
                foreach ($ids as $key => $id) {
                    $payroll = get_table_where('tbl_business_fee_boiler_calculate_item', ['id' => $id], '',
                        'row_array');
                    $this->db->where('id', $id);
                    $success = $this->db->delete('tbl_business_fee_boiler_calculate_item');
                }
            }
            if ($success) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
            $data['errors'] = $errors;
            echo json_encode($data);

            return;
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function print_pdf_new($id = '')
    {
        ob_start();
        $data = new stdClass();
        $paidHoliday = get_table_where('tbl_business_fee_boiler_overtime', array('id' => $id), '', 'row_array');

        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";

        $this->db->select('
            tbl_business_fee_boiler_overtime.id as id,
            tbl_business_fee_boiler_overtime.name as name,
            tbl_business_fee_boiler_overtime.date_created as date_created,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name_staff,
            tb_department.name_department as name_deparment,
            tblroles.name as name_roles,
            tblstaff.phonenumber as telephone,
            tblstaff.current_accommodation as current_accommodation
        ');
        $this->db->from('tbl_business_fee_boiler_overtime');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_business_fee_boiler_overtime.staff_id');
        $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->where('tbl_business_fee_boiler_overtime.id', $id);
        $paidHoliday = $this->db->get()->row_array();

        $table = '';
        $data->content = '';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;text-transform: uppercase">'._l('Bảng theo dõi tăng ca tháng').'</span><br>';

        $day = date('d', strtotime($paidHoliday['date_created']));
        $month = date('m', strtotime($paidHoliday['date_created']));
        $year = date('Y', strtotime($paidHoliday['date_created']));
        $date = _l('ch_day').' '.$day.' '._l('ch_month').' '.$month.' '._l('ch_year').' '.$year;
        $data->content .= '<span style="text-align: center;font-style: italic;">'.$date.'</span><br>';

        $name_roles = '';
        if (!empty($paidHoliday['name_roles'])) {
            $name_roles = ' ( '.$paidHoliday['name_roles'].' )';
        }

        $data->content .= '
            <span style="font-weight: bold;">'._l('Nhân viên').': </span><span>'.$paidHoliday['name_staff'].'</span><br><br>
            <span style="font-weight: bold;">'._l('Bộ phận').': </span><span>'.$paidHoliday['name_deparment'].$name_roles.'</span><br><br>
            <span style="font-weight: bold;">'._l('Địa chỉ liên lạc').': </span><span>'.$paidHoliday['current_accommodation'].'</span><br><br>
            <span style="font-weight: bold;">'._l('Số điện thoại').': </span><span>'.$paidHoliday['telephone'].'</span><br><br>';

        $trItems = '';
        $this->db->select('
                tbl_business_fee_boiler_overtime_detail.id as id,
                tbl_business_fee_boiler_overtime_detail.date as date,
                tbl_business_fee_boiler_overtime_detail.hour_start as hour_start,
                tbl_business_fee_boiler_overtime_detail.hour_end as hour_end,
                tbl_business_fee_boiler_overtime_detail.weekday as weekday,
                tbl_business_fee_boiler_overtime_detail.sunday as sunday,
                tbl_business_fee_boiler_overtime_detail.holiday as holiday,
                tbl_business_fee_boiler_overtime_detail.note as note
            ');
        $this->db->from('tbl_business_fee_boiler_overtime_detail');
        $this->db->where('tbl_business_fee_boiler_overtime_detail.business_fee_boiler_overtime_id', $id);
        $paidHolidayDetail = $this->db->get()->result_array();
        foreach ($paidHolidayDetail as $k => $v) {
            $trItems .= '<tr>
                        <td style="width: 5%;text-align: center" class="text-center">'.(++$k).'</td>
                        <td style="width: 20%" class="text-left">'._dhau($v['date']).'</td>
                        <td style="width: 15%;text-align:left" class="text-left">'.($v['hour_start']).'-'.($v['hour_end']).'</td>
                        <td style="width: 10%;text-align:center" class="text-center">'.(!empty($v['weekday']) ? $v['weekday'] : '').'</td>
                        <td style="width: 10%;text-align: center">'.(!empty($v['sunday']) ? $v['sunday'] : '').'</td>
                        <td style="width: 10%;text-align:center" class="text-center">'.(!empty($v['holiday']) ? $v['holiday'] : '').'</td>
                        <td style="width: 30%" class="text-left">'.($v['note']).'</td>
                    </tr>';
        }

        $data->content .= '<table class="table table-bordered" border="1" width="100%">
                <thead>
                    <tr>
                        <th style="text-align: center;width: 5%">STT</th>
                        <th style="text-align: center;width: 20%">Ngày</th>
                        <th style="text-align: center;width: 15%">Thời gian</th>
                        <th style="text-align: center;width: 10%">Ngày thường</th>
                        <th style="text-align: center;width: 10%">Chủ nhật</th>
                        <th style="text-align: center;width: 10%">Lễ tết</th>
                        <th style="text-align: center;width: 30%">Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    '.$trItems.'
                </tbody>
              </table><br><br>';
        $date_2 = _l('ch_day').' ........ '._l('ch_month').' ........ '._l('ch_year').' ........';
        $data->content .= '<span style="text-align: right;font-style: italic;">'.$date_2.'</span><br>';
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
                            <span style="font-weight: bold;">'._l('Người lập').'</span><br>
                            <span>'._l('ch_signature').'</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">'._l('Trưởng bộ phận').'</span><br>
                            <span>'._l('ch_signature').'</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">'._l('Phòng nhân sự').'</span><br>
                            <span>'._l('ch_signature').'</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">'._l('Giám đốc').'</span><br>
                            <span>'._l('ch_signature').'</span>
                        </td>
                    </tr>
                </tbody>
            </table>';
        $data->content .= $table;
        $pdf = print_pdf_P_ch($data);
        $type = 'I';
        $pdf->Output($paidHoliday['name'].'.pdf', $type);
    }

    public function report_business_fee_other_overtime()
    {
        if(!$this->perViewBusinessReportOvertime){
            access_denied();
        }
        $data = [];
        $data['staff'] = getPersonDeparmentdt(0);
        $data['title'] = lang('Thống kê giờ tăng ca');
        $this->load->view('admin/business_fee_other/report_business_fee_other_overtime', $data);
    }

    public function getOvertimeReport()
    {
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
            'tblstaff.staffid as id,
            tblstaff.code as code,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name_staff,
            tb_department.name_department as name_deparment,
            tbl_business_fee_boiler_overtime_detail.date as date,
            tbl_business_fee_boiler_overtime_detail.weekday as weekday,
            tbl_business_fee_boiler_overtime_detail.sunday as sunday,
            tbl_business_fee_boiler_overtime_detail.holiday as holiday
            ',
        ];
        $sIndexColumn = 'staffid';
        $sTable = 'tblstaff';
        $where = [
        ];
        $filter = [];
        $join = [
            'LEFT JOIN tblroles ON tblroles.roleid = tblstaff.role',
            'LEFT JOIN '.$tbDepartment.' ON tb_department.staffid = tblstaff.staffid',
            'INNER JOIN tbl_business_fee_boiler_overtime ON tbl_business_fee_boiler_overtime.staff_id = tblstaff.staffid',
            'INNER JOIN tbl_business_fee_boiler_overtime_detail ON tbl_business_fee_boiler_overtime_detail.business_fee_boiler_overtime_id = tbl_business_fee_boiler_overtime.id',
        ];

        if (!empty($staff_search)) {
            array_push($where,
                'AND ( tblstaff.staffid IN ('.implode(',', $staff_search).'))');
        }

        array_push($where,
            'AND ( tbl_business_fee_boiler_overtime.month = '.$month_search.')');

        array_push($where,
            'AND ( tbl_business_fee_boiler_overtime.year = '.$year_search.')');


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_business_fee_boiler_overtime_detail.hour_start as hour_start',
            'tbl_business_fee_boiler_overtime_detail.hour_end as hour_end',
        ], 'ORDER BY tblstaff.code asc', [], []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $stt = 1;

        $totalWeekday = 0;
        $totalSunday = 0;
        $totalHoliday = 0;
        $totalAll = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = array();

            $total_hour = 0;

            $row[] = '<div class="text-center">'.(++$key).'</div>';
            $row[] = $aRow['code'];
            $row[] = '<div style="width: 200px">'.$aRow['name_staff'].'</div>';
            $row[] = '<div style="width: 300px">'.$aRow['name_deparment'].'</div>';

            $row[] = '<div>'._dhau($aRow['date']).'</div>';
            $row[] = '<div>'.$aRow['hour_start'].' - '.$aRow['hour_end'].'</div>';
            $row[] = '<div class="text-center">'.(!empty($aRow['weekday']) ? $aRow['weekday'] : '').'</div>';
            $row[] = '<div class="text-center">'.(!empty($aRow['sunday']) ? $aRow['sunday'] : '').'</div>';
            $row[] = '<div class="text-center">'.(!empty($aRow['holiday']) ? $aRow['holiday'] : '').'</div>';
            $total_hour = $aRow['weekday'] + $aRow['sunday'] + $aRow['holiday'];
            $row[] = '<div class="text-center">'.(!empty($total_hour) ? $total_hour : '').'</div>';

            $totalWeekday += $aRow['weekday'];
            $totalSunday += $aRow['sunday'];
            $totalHoliday += $aRow['holiday'];
            $totalAll += $total_hour;
            $output['aaData'][] = $row;
            $stt++;

        }
        $output['totalWeekday'] = $totalWeekday;
        $output['totalSunday'] = $totalSunday;
        $output['totalHoliday'] = $totalHoliday;
        $output['totalAll'] = $totalAll;
        echo json_encode($output);
    }
}