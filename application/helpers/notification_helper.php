<?php
defined('BASEPATH') or exit('No direct script access allowed');
function check_view_price_po($userid)
{
    $view_price = array();
    $no_view_price = array();

    foreach ($userid as $key => $value) {
        $ktr = get_table_where('tblplayer_id', array('player_id' => $value), '', 'row');
        if (has_permission('purchase_order', $ktr->staffid, 'view_price') && has_permission('purchase_order', $ktr->staffid, 'view_price_onsumable')) {
            $view_price[]  = $value;
        } else {
            $no_view_price[]  = $value;
        }
    }
    $data['view_price'] = $view_price;
    $data['no_view_price'] = $no_view_price;
    return $data;
}
function Add_notification_app($data = array(), $userid = array())
{
    $CI = &get_instance();
    $userid = is_array($userid) ? $userid : explode(',', $userid);
    foreach ($userid as $key => $value) {
        $data['player_id'] = $value;
        $data['time'] = date('Y-m-d H:i:s');
        $ktr = get_table_where('tblplayer_id', array('player_id' => $value), '', 'row');
        $data['staff_id'] = $ktr->staffid;
        $CI->db->insert('tblnotification_app', $data);
    }
}
function SendOnesignal($data = array(), $type = '', $id_old = '')
{

    if (!empty($data)) {
        $message = $data['message'];
        $url = (!empty($data['url']) ? $data['url'] : ''); // không bắt buộc
        $title = $data['title'];
        $icon = (!empty($data['icon']) ? $data['icon'] : '');
        $ios_badgeType = "Increase"; // tùy theo api  trong tài liệu
        $ios_badgeCount = "1"; // tùy theo api // trong tài liệu
        $__data['title'] = $data['title'];
        $__data['type'] = $type;
        $__data['id_old'] = $id_old;
        $__data['user_name'] = '';
        if (!empty($data['user_name'])) {
            $__data['user_name'] = $data['user_name'];
        }

        $user_id = $data['user_id'];
        $app_id = get_option('onesignal_id');
        $keyapp = get_option('onesignal_key');
        $curl_onesignal = 'https://onesignal.com/api/v1/notifications';
        if (!empty($user_id) && !empty($app_id) && !empty($keyapp) && !empty($curl_onesignal)) {

            if (!empty($message)) {
                $content = ["en" => "$message"];
            }
            if (!empty($title)) {
                $headings = ["en" => "$title"];
            }
            $fields = array(
                'app_id' => $app_id,
                'include_player_ids' => is_array($user_id) ? $user_id : [$user_id],
                'chrome_web_icon' => $icon,
                'ios_badgeType' => $ios_badgeType,
                'ios_badgeCount' => $ios_badgeCount,
                'mutable_content' => true
            );
            if (!empty($url)) {
                $fields['url'] = $url;
            }
            if (!empty($content)) {
                $fields['contents'] = $content;
            }
            if (!empty($headings)) {
                $fields['headings'] = $headings;
            }
            $fields['data'] = $__data;
            $fields = json_encode($fields);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $curl_onesignal);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Basic ' . $keyapp
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response);
            if (!empty($response->id)) {
                $_data['notificationID'] = $response->id;
            } else {
                $_data['notificationID'] = time() . '_' . $data['staff_id'];
            }

            $user_id = $data['user_id'];
            $_data['body'] = $message;
            $_data['notificationID'] = $response->id;
            $_data['title'] = $title;
            $_data['url'] = $url;
            $_data['type'] = $type;
            $_data['id_old'] = $id_old;
            $_data['created_by'] = $data['created_by'];
            $_data['staff_name'] =  !empty($data['staff_name']) ? $data['staff_name'] : (!empty($data['created_by']) ? get_staff_full_name($data['created_by']) : "");
            $_data['code'] = (!empty($data['code']) ? $data['code'] : '');
            $_data['suppliers'] = (!empty($data['suppliers']) ? $data['suppliers'] : 0);
            $_data['total'] = (!empty($data['total']) ? $data['total'] : 0);
            $_data['suppliers_name'] = (!empty($data['suppliers_name']) ? $data['suppliers_name'] : '');
            $_data['item_name'] = (!empty($data['item_name']) ? $data['item_name'] : '');
            $_data['stage_name'] = (!empty($data['stage_name']) ? $data['stage_name'] : '');
            $_data['pod_reference_no'] = (!empty($data['pod_reference_no']) ? $data['pod_reference_no'] : '');
            $_data['json_data'] = (!empty($data['json_data']) ? $data['json_data'] : null);
            $_data['object_type'] = (!empty($data['object_type']) ? $data['object_type'] : '');

            Add_notification_app($_data, $user_id);

            if (!empty($response->id)) {
                return true;
            }
        }
    }
    return false;
}
function get_list_admin()
{
    $CI = &get_instance();
    $customer = array();
    $userid = array();
    $CI->db->select('staffid');
    $CI->db->where('admin', 1);
    $admin = $CI->db->get('tblstaff')->result_array();
    foreach ($admin as $key => $value) {
        $customer[] = $value['staffid'];
    }
    return $customer;
}
function get_customeradmins($customer_id = '')
{
    $CI = &get_instance();
    $customer = array();
    $userid = array();

    if (!empty($customer_id)) {
        $CI->db->select('staff_id');
        $CI->db->where('customer_id', $customer_id);
        $customeradmins = $CI->db->get('tblcustomer_admins')->result_array();
        foreach ($customeradmins as $key => $value) {
            $customer[] = $value['staff_id'];
        }
        $CI->db->select('staffid');
        $CI->db->where('admin', 1);
        $admin = $CI->db->get('tblstaff')->result_array();
        foreach ($admin as $key => $value) {
            $customer[] = $value['staffid'];
        }
    }
    $customer = array_unique($customer);
    if ($customer) {
        $CI->db->select('player_id,staffid');
        $CI->db->where_in('staffid', $customer);
        $player_id = $CI->db->get('tblplayer_id')->result_array();
        foreach ($player_id as $key => $value) {
            $userid[] = $value['player_id'];
        }
    }
    return $userid;
}
function notificationQCNotAchieved($id, $staffid, $valueItems = array())
{
    $CI = &get_instance();
    $userid = array();
    $checkQuantity =  get_table_where('tbl_check_quality', array('id' => $id), '', 'row');
    $CI->db->select('player_id,staffid');
    $player_id = $CI->db->get('tblplayer_id')->result_array();
    foreach ($player_id as $key => $value) {
        $isAdmin = is_admin($value['staffid']);
        if (!empty($isAdmin)) {
            $userid[] = $value['player_id'];
        } else {
            $perViewNotifications = has_permission('quality_control', $value['staffid'], 'notifications');
            if ($perViewNotifications && !$isAdmin) {
                $branchID = get_staff_user_id_branch_app($value['staffid']);
                if ($branchID == 1 || ($checkQuantity->id_branch ==  $branchID)) {
                    $userid[] = $value['player_id'];
                }
            }
        }
    }

    $pod = get_table_where('tbl_productions_orders_details', ['id' => $valueItems['pod_id']], '', 'row_array');
    $stage = get_table_where('tbl_stages', ['id' => $valueItems['id_stage']], '', 'row_array');
    $reference_no = $checkQuantity->reference_no;
    $staff = get_table_where('tblstaff', ['staffid' => $staffid], '', 'row_array');
    $staff_name = $staff['firstname'] . ' ' . $staff['lastname'];
    $_data = array();
    $_data['message'] =  'Sản phẩm ' . $valueItems['item_name'] . ' (' . $pod['reference_no'] . ') Không đạt chất lượng tại công đoạn ' . $stage['name'] . ' cần sản xuất lại! Vui lòng kiểm tra ' . $reference_no;
    $_data['title'] = 'Phiếu QC';
    $_data['code'] = $reference_no;
    $_data['total'] = 0;
    $_data['user_id'] = $userid;
    $_data['staff_name'] = $staff_name;
    $_data['created_by'] = $staffid;
    $_data['item_name'] = $valueItems['item_name'];
    $_data['stage_name'] = $stage['name'];
    $_data['pod_reference_no'] = $pod['reference_no'];
    SendOnesignal($_data, 'QCNotAchieved', $id);
}
function notificationImportOutsource($id, $staffid, $data = [])
{
    $CI = &get_instance();
    $userid = array();
    $importOutsource =  get_table_where('tbl_import_outsource', array('id' => $id), '', 'row');
    $CI->db->select('player_id,staffid');
    $player_id = $CI->db->get('tblplayer_id')->result_array();
    foreach ($player_id as $key => $value) {
        $isAdmin = is_admin($value['staffid']);
        if (!empty($isAdmin)) {
            $userid[] = $value['player_id'];
        } else {
            $perViewNotifications = has_permission('import_outsource', $value['staffid'], 'notifications');
            if ($perViewNotifications && !$isAdmin) {
                $branchID = get_staff_user_id_branch_app($value['staffid']);
                if ($branchID == 1 || ($importOutsource->id_branch ==  $branchID)) {
                    $userid[] = $value['player_id'];
                }
            }
        }
    }

    $po_id = $data['po_id'];
    $stage_id = $data['stage_id'];
    $arrPois = $data['arrPois'];
    $nArrPois = count($arrPois);
    $po = get_table_where('tbl_productions_orders', ['id' => $po_id], '', 'row_array', '', 'reference_no,location_id');
    $stage = get_table_where('tbl_stages', ['id' => $stage_id], '', 'row_array');
    $reference_no = $importOutsource->reference_no;
    $jsonData = [
        'id' => $po_id,
        'object_type' => 'import_outsource',
        'object_id' => $po_id,
        'reference_no' => $po['reference_no'],
        'stage_name' => $stage['name'],
        'arrPOIS' => $arrPois
    ];
    $staff = get_table_where('tblstaff', ['staffid' => $staffid], '', 'row_array');
    $staff_name = $staff['firstname'] . ' ' . $staff['lastname'];
    $_data = array();
    $_data['message'] =  'Công đoạn ' . $stage['name'] . ' của lệnh sản xuất tổng ' . $po['reference_no'] . ' có ' . $nArrPois . ' sản phẩm vừa được nhập gia công ' . $reference_no . '.Vui lòng QC để tiếp tục sản xuất';
    $_data['title'] = 'Phiếu nhập gia công';
    $_data['code'] = $reference_no;
    $_data['total'] = 0;
    $_data['user_id'] = $userid;
    $_data['staff_name'] = $staff_name;
    $_data['created_by'] = $staffid;
    $_data['item_name'] = '';
    $_data['stage_name'] = $stage['name'];
    $_data['pod_reference_no'] = $po['reference_no'];
    $_data['json_data'] = json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    SendOnesignal($_data, 'ImportOutsouce', $id);
}

function notificationQCAchieved($id, $staffid, $po_id, $arr_stage = array(), $arrPod = array())
{
    $CI = &get_instance();
    $userid = array();
    $checkQuantity =  get_table_where('tbl_check_quality', array('id' => $id), '', 'row');
    $CI->db->select('player_id,staffid');
    $player_id = $CI->db->get('tblplayer_id')->result_array();
    foreach ($player_id as $key => $value) {
        $isAdmin = is_admin($value['staffid']);
        if (!empty($isAdmin)) {
            $userid[] = $value['player_id'];
        } else {
            $perViewNotifications = has_permission('quality_control', $value['staffid'], 'notifications');
            if ($perViewNotifications && !$isAdmin) {
                $branchID = get_staff_user_id_branch_app($value['staffid']);
                if ($branchID == 1 || ($checkQuantity->id_branch ==  $branchID)) {
                    $userid[] = $value['player_id'];
                }
            }
        }
    }

    $po = get_table_where('tbl_productions_orders', ['id' => $po_id], '', 'row_array');
    $CI->db->select('tbl_stages.name as name');
    $CI->db->from('tbl_stages');
    $CI->db->where_in('tbl_stages.id', $arr_stage);
    $stages = $CI->db->get()->result_array();
    $stage_name = '';
    if (!empty($stages)) {
        foreach ($stages as $key => $value) {
            $stage_name .= $value['name'] . ', ';
        }
    }
    $stage_name = trim($stage_name, ', ');
    $reference_no = $checkQuantity->reference_no;
    $staff = get_table_where('tblstaff', ['staffid' => $staffid], '', 'row_array');
    $staff_name = $staff['firstname'] . ' ' . $staff['lastname'];
    $countPod = count($arrPod);
    $_data = array();
    $_data['message'] =  'Lệnh sản xuất tổng ' . $po['reference_no'] . ' vừa mới được QC ' . $countPod . ' mặt hàng ở công đoạn ' . $stage_name . ' Vui lòng tiến hành các bước sản xuất tiếp theo !';
    $_data['title'] = 'Phiếu QC';
    $_data['code'] = $reference_no;
    $_data['total'] = 0;
    $_data['user_id'] = $userid;
    $_data['staff_name'] = $staff_name;
    $_data['created_by'] = $staffid;
    $_data['item_name'] = '';
    $_data['stage_name'] = $stage_name;
    $_data['pod_reference_no'] = $po['reference_no'];
    SendOnesignal($_data, 'QCAchieved', $po_id);
}


function notificationTaskAssigned($id, $staffid, $assigned_id)
{
    $CI = &get_instance();
    $userid = array();
    $task = get_table_where('tbltasks', array('id' => $id), '', 'row');
    $CI->db->select('player_id,staffid');
    $player_id = $CI->db->get('tblplayer_id')->result_array();
    foreach ($player_id as $key => $value) {
        if (is_array($assigned_id)) {
            if (in_array($value['staffid'], $assigned_id)) {
                $userid[] = $value['player_id'];
            }
        } else {
            if ($assigned_id == $value['staffid']) {
                $userid[] = $value['player_id'];
            }
        }
    }
	$rowName = '';

	$CI->db->select('GROUP_CONCAT(tbldepartments.name) as list_name');
	$CI->db->join('tbldepartments', 'tbldepartments.departmentid = tbltask_department.department_id');
	$departments_task_name = $CI->db->get_where('tbltask_department', ['task_id' => $task->id])->row('list_name');
	if(!empty($departments_task_name)) {
		$rowName .= $departments_task_name;
	}

    $task_rel_data = get_relation_data($task->rel_type, $task->rel_id);
    $task_rel_value = get_relation_values($task_rel_data, $task->rel_type);
	if(!empty($task_rel_value['type'])) {
		if(!empty($rowName)) {
			$rowName .= ' và ';
		}
		$rowName .=  _l('c_tasks_' . $task_rel_value['type']) . ' ' . $task_rel_value['name'];
	}

	if(!empty($rowName)) {
		$rowName = 'Liên quan đến: ' . $rowName;
	}


//    if (!empty($task_rel_value['type'])) {
//        $rowName = ' Liên quan đến ' . _l('c_tasks_' . $task_rel_value['type']) . ' : ' . $task_rel_value['name'];
//    } else {
//        $rowName = '';
//    }
    $staff = get_table_where('tblstaff', ['staffid' => $staffid], '', 'row_array');
    $staff_name = (!empty($staff['firstname']) ? $staff['firstname'] : '')  . ' ' . (!empty($staff['lastname']) ? $staff['lastname'] : '');
    $_data = array();
    $_data['message'] = $staff_name . ' Vừa phân công công việc ' . $rowName . ' vào ngày ' . _dhau($task->startdate) . ' Vui lòng theo dõi và tiến hành cập nhật công việc !';
    $_data['title'] = 'Phiếu Công Việc';
    $_data['code'] = $task->name;
    $_data['total'] = 0;
    $_data['user_id'] = $userid;
    $_data['staff_name'] = $staff_name;
    $_data['created_by'] = $staffid;
    $_data['staff_id'] = $staffid;
    $_data['object_type'] = 'tasks';
    SendOnesignal($_data, 'TaskAssigned', $id);
}

// yct start
function notificationTaskComplete($id, $staffid, $arrStaffReceive = [])
{
    $CI = &get_instance();
    $userid = array();
    $task = get_table_where('tbltasks', array('id' => $id), '', 'row');
    $CI->db->select('player_id,staffid');
    $player_id = $CI->db->get('tblplayer_id')->result_array();
    foreach ($player_id as $key => $value) {
        if (is_array($arrStaffReceive)) {
            if (in_array($value['staffid'], $arrStaffReceive)) {
                $userid[] = $value['player_id'];
            }
        } else {
            if ($arrStaffReceive == $value['staffid']) {
                $userid[] = $value['player_id'];
            }
        }
    }
    // $task_rel_data = get_relation_data($task->rel_type, $task->rel_id);
    // $task_rel_value = get_relation_values($task_rel_data, $task->rel_type);
    // if (!empty($task_rel_value['type'])) {
    //     $rowName = ' Liên quan đến ' . _l('c_tasks_' . $task_rel_value['type']) . ' : ' . $task_rel_value['name'];
    // } else {
    //     $rowName = '';
    // }
    $staff = get_table_where('tblstaff', ['staffid' => $staffid], '', 'row_array');
    $staff_name = (!empty($staff['firstname']) ? $staff['firstname'] : '')  . ' ' . (!empty($staff['lastname']) ? $staff['lastname'] : '');
    $_data = array();
    // $_data['message'] = $staff_name . ' Vừa phân công công việc ' . $rowName . ' vào ngày ' . _dhau($task->startdate) . ' Vui lòng theo dõi và tiến hành cập nhật công việc !';
    $_data['message'] = 'Phiếu công việc ' .$task->name. ' được hoàn thành vào ngày ' ._dhau($task->startdate). ' bởi ' .$staff_name ;
    $_data['title'] = 'Phiếu Công Việc';
    $_data['code'] = $task->name;
    $_data['total'] = 0;
    $_data['user_id'] = $userid;
    $_data['staff_name'] = $staff_name;
    $_data['created_by'] = $staffid;
    $_data['staff_id'] = $staffid;
    $_data['object_type'] = 'tasks';
    SendOnesignal($_data, 'TaskComplete', $id);
}
// yct end

function send_notification_app_c($id = '', $data = [], $list_staff = [], $staffid = '')
{
    $CI = &get_instance();
    $userid = array();
    //    $arrAssigned = array();
    if (!empty($list_staff)) {
        $CI->db->where_in('staffid', $list_staff);
    }
    $CI->db->select('player_id, staffid');
    $player_id = $CI->db->get('tblplayer_id')->result_array();
    foreach ($player_id as $key => $value) {
        //        $isAdmin = is_admin($value['staffid']);
        //        if (!empty($isAdmin)) {
        //            $userid[] = $value['player_id'];
        //        } else {
        //            if (in_array($value['staffid'], $arrAssigned)) {
        //                $userid[] = $value['player_id'];
        //            }
        //        }
        $userid[] = $value['player_id'];
    }
    $staff = get_table_where('tblstaff', ['staffid' => $staffid], '', 'row_array');
    $staff_name = $staff['firstname'] . ' ' . $staff['lastname'];
    $_data = array();
    $_data['message'] = $data['description'];
    $_data['title'] = $data['title'];
    $_data['code'] = $data['code'];
    $_data['total'] = 0;
    $_data['user_id'] = $userid;
    $_data['staff_name'] = $staff_name;
    $_data['created_by'] = $staffid;
    $_data['staff_id'] = $staffid;
    $_data['object_type'] = !empty($data['object_type']) ? $data['object_type'] : 'tasks';
    SendOnesignal($_data, $_data['object_type'], $id);
}
//end

function notificationAddPaidHoliday($id, $staffid)
{
    $CI = &get_instance();
    $userid = array();
    $paidHoliday =  get_table_where('tbl_paid_holiday_leave', array('id' => $id), '', 'row');
    $CI->db->select('player_id,staffid');
    $player_id = $CI->db->get('tblplayer_id')->result_array();
    foreach ($player_id as $key => $value) {
        $isAdmin = is_admin($value['staffid']);
        if (!empty($isAdmin)) {
            $userid[] = $value['player_id'];
        } else {
            if ($paidHoliday->staff_agree == $value['staffid']){
                $userid[] = $value['player_id'];
            }
        }
    }

    $staff = get_table_where('tblstaff', ['staffid' => $staffid], '', 'row_array');
    $staff_name = $staff['firstname'] . ' ' . $staff['lastname'];
    $_data = array();
    $_data['message'] =  'Nhân viên '.$staff_name.' vừa tạo đơn xin nghỉ phép '.$paidHoliday->name.' vào ngày '._dt(date('Y-m-d H:i:s')).'.Vui lòng theo dõi và duyệt phiếu !';
    $_data['title'] = 'Đơn xin nghỉ phép';
    $_data['code'] = $paidHoliday->name;
    $_data['total'] = 0;
    $_data['user_id'] = $userid;
    $_data['staff_name'] = $staff_name;
    $_data['created_by'] = $staffid;
    $_data['item_name'] = '';
    $_data['stage_name'] = '';
    $_data['pod_reference_no'] = '';
    SendOnesignal($_data, 'AddPaidHoliday', $id);
}

function notificationAgreePaidHoliday($id,$paid_holiday_detail_id,$staffid,$status)
{
    $CI = &get_instance();
    $userid = array();
    $paidHoliday =  get_table_where('tbl_paid_holiday_leave', array('id' => $id), '', 'row');
    $CI->db->select('player_id,staffid');
    $player_id = $CI->db->get('tblplayer_id')->result_array();
    foreach ($player_id as $key => $value) {
        $isAdmin = is_admin($value['staffid']);
        if (!empty($isAdmin)) {
            $userid[] = $value['player_id'];
        } else {
            if ($paidHoliday->staff_id == $value['staffid']){
                $userid[] = $value['player_id'];
            }
        }
    }
    $CI->db->select('
                    tbl_type_magic.name as name_magic,
                    date_start,
                    date_end,
                    tbl_type_magic.id as type_magic_id,
                    paid_holiday_leave_id,
                    tbl_paid_holiday_leave_detail.id as id
                    ');
    $CI->db->from('tbl_paid_holiday_leave_detail');
    $CI->db->join('tbl_type_magic', 'tbl_type_magic.id = tbl_paid_holiday_leave_detail.type_magic_id');
    $CI->db->where('tbl_paid_holiday_leave_detail.id', $paid_holiday_detail_id);
    $get_code_child = $CI->db->get()->row_array();
    $staff = get_table_where('tblstaff', ['staffid' => $staffid], '', 'row_array');
    $staff_name = $staff['firstname'] . ' ' . $staff['lastname'];
    $_data = array();
    if ($status == 1) {
        $_data['message'] = 'Nhân viên ' . $staff_name . ' vừa duyệt đơn xin nghỉ phép ' . $paidHoliday->name . '['.$get_code_child['name_magic'].'] ngày nghỉ phép '._dhau($get_code_child['date_start']).' của nhân viên '.get_staff_full_name($paidHoliday->staff_id).' vào ngày ' . _dt(date('Y-m-d H:i:s')) . '.';
    } elseif ($status == 2){
        $_data['message'] = 'Nhân viên ' . $staff_name . ' vừa không duyệt đơn xin nghỉ phép ' . $paidHoliday->name . '['.$get_code_child['name_magic'].'] ngày nghỉ phép '._dhau($get_code_child['date_start']).' của nhân viên '.get_staff_full_name($paidHoliday->staff_id).' vào ngày ' . _dt(date('Y-m-d H:i:s')) . '.';
    }
    $_data['title'] = 'Đơn xin nghỉ phép';
    $_data['code'] = $paidHoliday->name;
    $_data['total'] = 0;
    $_data['user_id'] = $userid;
    $_data['staff_name'] = $staff_name;
    $_data['created_by'] = $staffid;
    $_data['item_name'] = '';
    $_data['stage_name'] = '';
    $_data['pod_reference_no'] = '';
    SendOnesignal($_data, 'AgreePaidHoliday', $id);
}