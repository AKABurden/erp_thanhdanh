<?php

defined('BASEPATH') or exit('No direct script access allowed');

use app\services\messages\Message;
use app\services\messages\PopupMessage;

if (!function_exists('noti_custom')) {
    function noti_custom($object_type, $object_id, $staffid, $pois_id = 0, $type_app = 'finishedPOD', $data = []) {
        $CI = &get_instance();
        $arrNotiWeb = [];
        $arrNotiApp = [];
        $notifiedUsers = [];
        if ($object_type == "pod") {
            $pois = $CI->manufactures_model->getProductionsOrderItemsStagesById($pois_id);
            $stage = get_table_where('tbl_stages', ['id' => $pois['stage_id']], '', 'row_array', '', 'name, id');
            $stage_name = $stage['name'];

            $CI->db->select('tbl_productions_orders_details.reference_no, tbl_products.name as item_name, tbl_products.code as item_code, tbl_productions_orders.location_id as id_branch, tbl_productions_orders_details.object_type as object_type, tbl_productions_orders_details.object_id as object_id', false);
            $CI->db->from('tbl_productions_orders_details');
            $CI->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
            $CI->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id');
            $CI->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
            $CI->db->where('tbl_productions_orders_details.id', $object_id);
            $pod = $CI->db->get()->row_array();
            if (!empty($pod)) {

                $object_typee = $pod['object_type'];
                $object_idd = $pod['object_id'];
                $reference_object = '';
                if ($object_typee == "orders") {
                    $CI->db->select('
                        tbl_orders.reference_no as reference_no,
                        tblclients.company as company
                    ', false);
                    $CI->db->from('tbl_orders');
                    $CI->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id');
                    $CI->db->where('tbl_orders.id', $object_idd);
                    $order = $CI->db->get()->row_array();
                    $reference_object = $order['reference_no'].' - '.$order['company'];
                } else {
                    $business_plan = get_table_where('tbl_business_plan', ['id' => $object_idd], '', 'row_array', '', 'reference_no');
                    $reference_object = $business_plan['reference_no'];
                }

                $userid = array();
                $getAllStaff = get_table_where('tblstaff', ['active' => 1], '', 'result_array', '', 'id_branch, staffid, firstname, lastname');
                if (!empty($getAllStaff)) {
                    $dataHtml = 'Sản phẩm '.$pod['item_name'].' thuộc lệnh sản xuất chi tiết '.$pod['reference_no'].'. Đơn hàng: '.$reference_object.' vừa được hoàn thành sản xuất công đoạn '.$stage_name;
                    foreach ($getAllStaff as $k => $val) {
                        if (has_permission('manufactures_order_production_details', $val['staffid'], 'notifications')) {
                            $id_branch = $val['id_branch'];
                            if ($id_branch == BRANCH_DEFAULT || $id_branch == $pod['id_branch']) {
                                $notification_data = [
                                    'date' => date('Y-m-d H:i:s'),
                                    'description' => "<a target='_blank' href='".base_url('admin/manufactures/detail_productions/'.$object_id)."'> ".$dataHtml .'</a>',
                                    'touserid' => $val['staffid'],
                                    'fromuserid' => $val['staffid'],
                                    'from_fullname' => $val['firstname'].' '.$val['lastname'],
                                    'link' => '',
                                    'type' => 1,
                                    'object_id' => $object_id,
                                    'object_type' => $object_type,
                                ];
                                $arrNotiWeb[] = $notification_data;
                                $notifiedUsers[] = $val['staffid'];

                                //noti app
                                $CI->db->select('player_id, staffid');
                                $CI->db->where('staffid', $val['staffid']);
                                $player_id = $CI->db->get('tblplayer_id')->result_array();
                                if (!empty($player_id)) {
                                    foreach ($player_id as $kP => $vP) {
                                        $userid[] = $vP['player_id'];
                                    }
                                }
                            }
                        }
                    }

                    if (!empty($userid)) {
                        $jsonData = [
                            'id' => $object_id,
                            'object_type' => $object_type,
                            'object_id' => $object_id,
                            'item_name' => $pod['item_name'],
                            'reference_no' => $pod['reference_no'],
                            'reference_object' => $reference_object,
                            'stage_name' => $stage_name,
                        ];

                        $staff = get_table_where('tblstaff', ['staffid' => $staffid],'','row_array', '', 'firstname, lastname');
                        $staff_name = $staff['firstname'].' '.$staff['lastname'];

                        $notification_app = [
                            'json_data' => json_encode($jsonData, JSON_UNESCAPED_UNICODE),
                            'message' => $dataHtml,
                            'title' => lang('Lệnh sản xuất chi tiết'),
                            'code' => $pod['reference_no'],
                            'user_id' => $userid,
                            'staff_name' => $staff_name,
                            'staff_id' => $staffid,
                            'created_by' => $staffid,
                            'item_name' => $pod['item_name'],
                            'object_type' => $object_type,
                            'pod_reference_no' => $pod['reference_no'],
                            'stage_name' => $stage_name,
                        ];
                        $arrNotiApp = $notification_app;
                    }
                }
            }
        } else if ($object_type == "qc_po") {
            $stage_id = $data['stage_id'];
            $po_id = $data['po_id'];
            $arrPOIS = $data['arrPOIS'];
            $arrCQIS = !empty($data['arrCQIS']) ? $data['arrCQIS'] : null;
            $nArrPois = count($arrPOIS) + count($arrCQIS);
            $stage = get_table_where('tbl_stages', ['id' => $stage_id], '', 'row_array', '', 'name, id, status_qc');
            $status_qc = $stage['status_qc'];
            if (!empty($status_qc)) {
                $stage_name = $stage['name'];
                $CI->db->select('
                    tbl_productions_orders.reference_no as reference_no,
                    tbl_productions_orders.location_id as id_branch
                ', false);
                $CI->db->from('tbl_productions_orders');
                $CI->db->where('tbl_productions_orders.id', $po_id);
                $po = $CI->db->get()->row_array();
                
                if (!empty($po)) {
                    $dataHtml = 'Công đoạn '.$stage_name.' của lệnh sản xuất tổng '.$po['reference_no'].' có '.$nArrPois.' sản phẩm vừa được hoàn thành, vui lòng QC để tiếp tục sản xuất tiếp theo !';

                    $userid = array();
                    $getAllStaff = get_table_where('tblstaff', ['active' => 1], '', 'result_array', '', 'id_branch, staffid, firstname, lastname');
                    if (!empty($getAllStaff)) {
                        foreach ($getAllStaff as $k => $val) {
                            if (has_permission('manufactures_productions_orders', $val['staffid'], 'notifications')) {
                                $id_branch = $val['id_branch'];
                                if ($id_branch == BRANCH_DEFAULT || $id_branch == $po['id_branch']) {
                                    //noti app
                                    $CI->db->select('player_id, staffid');
                                    $CI->db->where('staffid', $val['staffid']);
                                    $player_id = $CI->db->get('tblplayer_id')->result_array();
                                    if (!empty($player_id)) {
                                        foreach ($player_id as $kP => $vP) {
                                            $userid[] = $vP['player_id'];
                                        }
                                    }
                                }
                            }
                        }

                        if (!empty($userid)) {
                            $jsonData = [
                                'id' => $object_id,
                                'object_type' => $object_type,
                                'object_id' => $object_id,
                                'reference_no' => $po['reference_no'],
                                'stage_name' => $stage_name,
                                'arrPOIS' => $arrPOIS,
                                'arrCQIS' => $arrCQIS,
                            ];

                            $staff = get_table_where('tblstaff', ['staffid' => $staffid],'','row_array', '', 'firstname, lastname');
                            $staff_name = $staff['firstname'].' '.$staff['lastname'];

                            $notification_app = [
                                'json_data' => json_encode($jsonData, JSON_UNESCAPED_UNICODE),
                                'message' => $dataHtml,
                                'title' => lang('Lệnh sản xuất tổng'),
                                'code' => $po['reference_no'],
                                'user_id' => $userid,
                                'staff_name' => $staff_name,
                                'staff_id' => $staffid,
                                'created_by' => $staffid,
                                'item_name' => '',
                                'object_type' => $object_type,
                                'pod_reference_no' => $po['reference_no'],
                                'stage_name' => $stage_name,
                            ];
                            $arrNotiApp = $notification_app;
                        }
                    }

                }
            }
        } else if($object_type == "pod_new"){
            $stage_id = $data['stage_id'];
            $po_id = $data['po_id'];
            $arrPOIS = $data['arrPOIS'];
            $nArrPois = count($arrPOIS);
            $stage = get_table_where('tbl_stages', ['id' => $stage_id], '', 'row_array', '', 'name, id');
            $stage_name = $stage['name'];
            $CI->db->select('
                tbl_productions_orders.reference_no as reference_no,
                tbl_productions_orders.location_id as id_branch
            ', false);
            $CI->db->from('tbl_productions_orders');
            $CI->db->where('tbl_productions_orders.id', $po_id);
            $po = $CI->db->get()->row_array();
            
            if (!empty($po)) {
                $dataHtml = 'Lệnh sản xuất tổng '.$po['reference_no'].' có '.$nArrPois.' sản phẩm vừa được hoàn thành tại công đoạn '.$stage_name.'';

                $userid = array();
                $getAllStaff = get_table_where('tblstaff', ['active' => 1], '', 'result_array', '', 'id_branch, staffid, firstname, lastname');
                if (!empty($getAllStaff)) {
                    foreach ($getAllStaff as $k => $val) {
                        if (has_permission('manufactures_productions_orders', $val['staffid'], 'notifications')) {
                            $id_branch = $val['id_branch'];
                            if ($id_branch == BRANCH_DEFAULT || $id_branch == $po['id_branch']) {
                                $notification_data = [
                                    'date' => date('Y-m-d H:i:s'),
                                    'description' => "<a target='_blank' href='".base_url('admin/manufactures/detail_productions_orders/'.$object_id)."'> ".$dataHtml .'</a>',
                                    'touserid' => $val['staffid'],
                                    'fromuserid' => $val['staffid'],
                                    'from_fullname' => $val['firstname'].' '.$val['lastname'],
                                    'link' => '',
                                    'type' => 1,
                                    'object_id' => $object_id,
                                    'object_type' => $object_type,
                                ];
                                $arrNotiWeb[] = $notification_data;
                                $notifiedUsers[] = $val['staffid'];

                                //noti app
                                $CI->db->select('player_id, staffid');
                                $CI->db->where('staffid', $val['staffid']);
                                $player_id = $CI->db->get('tblplayer_id')->result_array();
                                if (!empty($player_id)) {
                                    foreach ($player_id as $kP => $vP) {
                                        $userid[] = $vP['player_id'];
                                    }
                                }
                            }
                        }
                    }


                    if (!empty($userid)) {
                        $jsonData = [
                            'id' => $object_id,
                            'object_type' => $object_type,
                            'object_id' => $object_id,
                            'reference_no' => $po['reference_no'],
                            'stage_name' => $stage_name,
                            'arrPOIS' => $arrPOIS
                        ];

                        $staff = get_table_where('tblstaff', ['staffid' => $staffid],'','row_array', '', 'firstname, lastname');
                        $staff_name = $staff['firstname'].' '.$staff['lastname'];

                        $notification_app = [
                            'json_data' => json_encode($jsonData, JSON_UNESCAPED_UNICODE),
                            'message' => $dataHtml,
                            'title' => lang('Lệnh sản xuất'),
                            'code' => $po['reference_no'],
                            'user_id' => $userid,
                            'staff_name' => $staff_name,
                            'staff_id' => $staffid,
                            'created_by' => $staffid,
                            'item_name' => '',
                            'object_type' => $object_type,
                            'pod_reference_no' => $po['reference_no'],
                            'stage_name' => $stage_name,
                        ];
                        $arrNotiApp = $notification_app;
                    }
                }
            }
        } else if ($object_type == "qc_po_detail") {
            $stage_id = $data['stage_id'];
            $pod_id = $data['pod_id'];
            $arrPOIS = $data['arrPOIS'];
            $nArrPois = count($arrPOIS);
            $stage = get_table_where('tbl_stages', ['id' => $stage_id], '', 'row_array', '', 'name, id, status_qc');
            $status_qc = $stage['status_qc'];
            if (!empty($status_qc)) {
                $stage_name = $stage['name'];
                $CI->db->select('
                    tbl_productions_orders_details.reference_no as reference_no,
                    tbl_productions_orders.location_id as id_branch
                ', false);
                $CI->db->from('tbl_productions_orders');
                $CI->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id');
                $CI->db->where('tbl_productions_orders_details.id', $pod_id);
                $po = $CI->db->get()->row_array();
                
                if (!empty($po)) {
                    $dataHtml = 'Công đoạn '.$stage_name.' của lệnh sản xuất chi tiết '.$po['reference_no'].' có '.$nArrPois.' sản phẩm vừa được hoàn thành, vui lòng QC để tiếp tục sản xuất tiếp theo !';

                    $userid = array();
                    $getAllStaff = get_table_where('tblstaff', ['active' => 1], '', 'result_array', '', 'id_branch, staffid, firstname, lastname');
                    if (!empty($getAllStaff)) {
                        foreach ($getAllStaff as $k => $val) {
                            if (has_permission('manufactures_productions_orders', $val['staffid'], 'notifications')) {
                                $id_branch = $val['id_branch'];
                                if ($id_branch == BRANCH_DEFAULT || $id_branch == $po['id_branch']) {
                                    //noti app
                                    $CI->db->select('player_id, staffid');
                                    $CI->db->where('staffid', $val['staffid']);
                                    $player_id = $CI->db->get('tblplayer_id')->result_array();
                                    if (!empty($player_id)) {
                                        foreach ($player_id as $kP => $vP) {
                                            $userid[] = $vP['player_id'];
                                        }
                                    }
                                }
                            }
                        }

                        if (!empty($userid)) {
                            $jsonData = [
                                'id' => $object_id,
                                'object_type' => $object_type,
                                'object_id' => $object_id,
                                'reference_no' => $po['reference_no'],
                                'stage_name' => $stage_name,
                                'arrPOIS' => $arrPOIS
                            ];

                            $staff = get_table_where('tblstaff', ['staffid' => $staffid],'','row_array', '', 'firstname, lastname');
                            $staff_name = $staff['firstname'].' '.$staff['lastname'];

                            $notification_app = [
                                'json_data' => json_encode($jsonData, JSON_UNESCAPED_UNICODE),
                                'message' => $dataHtml,
                                'title' => lang('Lệnh sản xuất tổng'),
                                'code' => $po['reference_no'],
                                'user_id' => $userid,
                                'staff_name' => $staff_name,
                                'staff_id' => $staffid,
                                'created_by' => $staffid,
                                'item_name' => '',
                                'object_type' => $object_type,
                                'pod_reference_no' => $po['reference_no'],
                                'stage_name' => $stage_name,
                            ];
                            $arrNotiApp = $notification_app;
                        }
                    }

                }
            }
        } else if ($object_type == "qc_po_detail_return") {
            $stage_id = $data['stage_id'];
            $pod_id = $data['pod_id'];
            $arrPOIS = $data['arrPOIS'];
            $arrCQIS = $data['arrCQIS'];
            $nArrPois = count($arrPOIS);
            $stage = get_table_where('tbl_stages', ['id' => $stage_id], '', 'row_array', '', 'name, id, status_qc');
            $status_qc = $stage['status_qc'];
            if (!empty($status_qc)) {
                $stage_name = $stage['name'];
                $CI->db->select('
                    tbl_productions_orders_details.reference_no as reference_no,
                    tbl_productions_orders.location_id as id_branch
                ', false);
                $CI->db->from('tbl_productions_orders');
                $CI->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id');
                $CI->db->where('tbl_productions_orders_details.id', $pod_id);
                $po = $CI->db->get()->row_array();
                
                if (!empty($po)) {
                    $dataHtml = 'Công đoạn '.$stage_name.' của lệnh sản xuất chi tiết '.$po['reference_no'].' có '.$nArrPois.' sản phẩm vừa được hoàn thành sản xuất lại, vui lòng QC lại để tiếp tục sản xuất tiếp theo !';

                    $userid = array();
                    $getAllStaff = get_table_where('tblstaff', ['active' => 1], '', 'result_array', '', 'id_branch, staffid, firstname, lastname');
                    if (!empty($getAllStaff)) {
                        foreach ($getAllStaff as $k => $val) {
                            if (has_permission('manufactures_productions_orders', $val['staffid'], 'notifications')) {
                                $id_branch = $val['id_branch'];
                                if ($id_branch == BRANCH_DEFAULT || $id_branch == $po['id_branch']) {
                                    //noti app
                                    $CI->db->select('player_id, staffid');
                                    $CI->db->where('staffid', $val['staffid']);
                                    $player_id = $CI->db->get('tblplayer_id')->result_array();
                                    if (!empty($player_id)) {
                                        foreach ($player_id as $kP => $vP) {
                                            $userid[] = $vP['player_id'];
                                        }
                                    }
                                }
                            }
                        }

                        if (!empty($userid)) {
                            $jsonData = [
                                'id' => $object_id,
                                'object_type' => $object_type,
                                'object_id' => $object_id,
                                'reference_no' => $po['reference_no'],
                                'stage_name' => $stage_name,
                                'arrPOIS' => $arrPOIS,
                                'arrCQIS' => $arrCQIS,
                            ];

                            $staff = get_table_where('tblstaff', ['staffid' => $staffid],'','row_array', '', 'firstname, lastname');
                            $staff_name = $staff['firstname'].' '.$staff['lastname'];

                            $notification_app = [
                                'json_data' => json_encode($jsonData, JSON_UNESCAPED_UNICODE),
                                'message' => $dataHtml,
                                'title' => lang('Lệnh sản xuất tổng'),
                                'code' => $po['reference_no'],
                                'user_id' => $userid,
                                'staff_name' => $staff_name,
                                'staff_id' => $staffid,
                                'created_by' => $staffid,
                                'item_name' => '',
                                'object_type' => $object_type,
                                'pod_reference_no' => $po['reference_no'],
                                'stage_name' => $stage_name,
                            ];
                            $arrNotiApp = $notification_app;
                        }
                    }

                }
            }
        } elseif ($object_type == "create_quotes") {
            $actions = $data['actions'];
            if ($actions == "add") {
                $arrManager = getParentStaff($staffid);

                $CI->db->select('*');
                $CI->db->from('tbl_quotes');
                $CI->db->where('tbl_quotes.id', $object_id);
                $quote = $CI->db->get()->row_array();


                $CI->db->select('
                    tblstaff.staffid as staffid,
                    tblstaff.firstname as firstname,
                    tblstaff.lastname as lastname,
                    tblstaff.profile_image as profile_image,
                ', false);
                $CI->db->from('tblstaff');
                $CI->db->where('tblstaff.staffid', $staffid);
                $staff = $CI->db->get()->row_array();
                $created_by = $staffid;
                $created_name = $staff['firstname'] . ' ' . $staff['lastname'];
                $customer = $CI->site_model->rowCustomer($quote['customer_id']);

                $arrId = [];
                $orders_staffs = [];
                $existStaff = 0;
                if (!empty($arrManager)) {
                    foreach ($arrManager as $key => $value) {
                        if ($value == $created_by) {
                            $existStaff = 1;
                        }

                        $orders_staffs[] = [
                            'staff_id' => $value,
                            'staff_name' => get_staff_full_name($value),
                        ];
                        $arrId[] = $value;
                    }
                }

                if (!$existStaff) {
                    $orders_staffs = array_merge([['staff_id' => $created_by, 'staff_name' => $created_name]],
                        $orders_staffs);
                    $arrId[] = $created_by;
                }

                $dataHtml = '
                    Vừa mới tạo báo giá <b>' . $quote['reference_no'] . '</b> thuộc khách hàng <b>' . $customer['company'] . '</b>. Vui lòng xem xét và duyệt báo giá sớm nhất
                ';

                $CI->db->select('tblstaff.staffid as staff_id, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name',
                    false);
                $CI->db->from('tblstaff');
                if (!empty($arrId)) {
                    $CI->db->where_not_in('tblstaff.staffid', $arrId);
                }

                $CI->db->where('tblstaff.admin', 1);
                $staffAdmin = $CI->db->get()->result_array();
                $orders_staffs = array_merge($staffAdmin, $orders_staffs);

                if (!empty($orders_staffs)) {
                    foreach ($orders_staffs as $kS => $vS) {
                        $notification_data = [
                            'date' => date('Y-m-d H:i:s'),
                            'description' => $dataHtml,
                            'touserid' => $vS['staff_id'],
                            'fromuserid' => $created_by,
                            'from_fullname' => $created_name,
                            'link' => 'quotes/view_quotes/' . $object_id,
                            'type' => 2,
                            'object_id' => $object_id,
                            'object_type' => $object_type,
                            'onclick' => 'agreeQuotes(' . $object_id . ', \'approved\')',
                        ];
                        $arrNotiWeb[] = $notification_data;
                        $notifiedUsers[] = $vS['staff_id'];
                    }
                }
            } elseif ($actions == "delete") {
                $CI->db->where('tblnotifications.object_id', $object_id);
                $CI->db->where('tblnotifications.object_type', $object_type);
                $CI->db->delete('tblnotifications');
                return true;
            }
        } elseif ($object_type == "create_orders") {
            $actions = $data['actions'];
            if ($actions == "add") {
                $arrManager = getParentStaff($staffid);

                $CI->db->select('*');
                $CI->db->from('tbl_orders');
                $CI->db->where('tbl_orders.id', $object_id);
                $order = $CI->db->get()->row_array();


                $CI->db->select('
                    tblstaff.staffid as staffid,
                    tblstaff.firstname as firstname,
                    tblstaff.lastname as lastname,
                    tblstaff.profile_image as profile_image,
                ', false);
                $CI->db->from('tblstaff');
                $CI->db->where('tblstaff.staffid', $staffid);
                $staff = $CI->db->get()->row_array();
                $created_by = $staffid;
                $created_name = $staff['firstname'] . ' ' . $staff['lastname'];
                $customer = $CI->site_model->rowCustomer($order['customer_id']);

                $arrId = [];
                $orders_staffs = [];
                $existStaff = 0;
                if (!empty($arrManager)) {
                    foreach ($arrManager as $key => $value) {
                        if ($value == $created_by) {
                            $existStaff = 1;
                        }

                        $orders_staffs[] = [
                            'staff_id' => $value,
                            'staff_name' => get_staff_full_name($value),
                        ];
                        $arrId[] = $value;
                    }
                }

                if (!$existStaff) {
                    $orders_staffs = array_merge([['staff_id' => $created_by, 'staff_name' => $created_name]],
                        $orders_staffs);
                    $arrId[] = $created_by;
                }

                $dataHtml = '
                    Vừa mới tạo đơn hàng <b>' . $order['reference_no'] . '</b> thuộc khách hàng <b>' . $customer['company'] . '</b>. Vui lòng xem xét và duyệt đơn hàng sớm nhất
                ';

                $CI->db->select('tblstaff.staffid as staff_id, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name',
                    false);
                $CI->db->from('tblstaff');
                if (!empty( $arrId)) {
                    $CI->db->where_not_in('tblstaff.staffid', $arrId);
                }
                $CI->db->where('tblstaff.admin', 1);
                $staffAdmin = $CI->db->get()->result_array();
                $orders_staffs = array_merge($staffAdmin, $orders_staffs);

                if (!empty($orders_staffs)) {
                    foreach ($orders_staffs as $kS => $vS) {
                        $notification_data = [
                            'date' => date('Y-m-d H:i:s'),
                            'description' => $dataHtml,
                            'touserid' => $vS['staff_id'],
                            'fromuserid' => $created_by,
                            'from_fullname' => $created_name,
                            'link' => 'orders/view_order/' . $object_id,
                            'type' => 2,
                            'object_id' => $object_id,
                            'object_type' => $object_type,
                            'onclick' => 'agreeOrders(' . $object_id . ', \'approved\')',
                        ];
                        $arrNotiWeb[] = $notification_data;
                        $notifiedUsers[] = $vS['staff_id'];
                    }
                }
            } elseif ($actions == "delete") {
                $CI->db->where('tblnotifications.object_id', $object_id);
                $CI->db->where('tblnotifications.object_type', $object_type);
                $CI->db->delete('tblnotifications');
                return true;
            }
        } else if ($object_type == "create_productions_orders") {
            $actions = $data['actions'];
            if ($actions == "add") {
                // $arrManager = getParentStaff($staffid);

                // $CI->db->select('*');
                // $CI->db->from('tbl_orders');
                // $CI->db->where('tbl_orders.id', $object_id);
                // $order = $CI->db->get()->row_array();


                $CI->db->select('
                    tblstaff.staffid as staffid,
                    tblstaff.firstname as firstname,
                    tblstaff.lastname as lastname,
                    tblstaff.profile_image as profile_image,
                ', false);
                $CI->db->from('tblstaff');
                $CI->db->where('tblstaff.staffid', $staffid);
                $staff = $CI->db->get()->row_array();
                $created_by = $staffid;
                $created_name = $staff['firstname'] . ' ' . $staff['lastname'];
                // $customer = $CI->site_model->rowCustomer($order['customer_id']);

                // $arrId = [];
                // $orders_staffs = [];
                // $existStaff = 0;
                // if (!empty($arrManager)) {
                //     foreach ($arrManager as $key => $value) {
                //         if ($value == $created_by) {
                //             $existStaff = 1;
                //         }

                //         $orders_staffs[] = [
                //             'staff_id' => $value,
                //             'staff_name' => get_staff_full_name($value),
                //         ];
                //         $arrId[] = $value;
                //     }
                // }

                // if (!$existStaff) {
                //     $orders_staffs = array_merge([['staff_id' => $created_by, 'staff_name' => $created_name]],
                //         $orders_staffs);
                //     $arrId[] = $created_by;
                // }

                // $dataHtml = '
                //     Vừa mới tạo đơn hàng <b>' . $order['reference_no'] . '</b> thuộc khách hàng <b>' . $customer['company'] . '</b>. Vui lòng xem xét và duyệt đơn hàng sớm nhất
                // ';

                // $CI->db->select('tblstaff.staffid as staff_id, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name',
                //     false);
                // $CI->db->from('tblstaff');
                // if (!empty( $arrId)) {
                //     $CI->db->where_not_in('tblstaff.staffid', $arrId);
                // }
                // $CI->db->where('tblstaff.admin', 1);
                // $staffAdmin = $CI->db->get()->result_array();
                // $orders_staffs = array_merge($staffAdmin, $orders_staffs);
                
                $CI->db->select('
                    tbl_productions_orders.reference_no,
                    tbl_productions_orders.productions_plan_reference_no as productions_plan_reference_no
                ', false);
                $CI->db->from('tbl_productions_orders');
                $CI->db->where('tbl_productions_orders.id', $object_id);
                $productions_orders = $CI->db->get()->row_array();
                if (!$productions_orders) {
                    return false;
                }

                $dataHtml = '<span>Số lệnh sản xuất <b>'.$productions_orders['reference_no'].'</b> thuộc đơn <b>'.$productions_orders['productions_plan_reference_no'].'</b> vừa được nhân viên <b>'.$created_name.'</b> tạo</span>';   

                $CI->db->select('
                    tblstaff.staffid as staff_id, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name
                ', false);
                $CI->db->from('tblstaff');
                $CI->db->where('tblstaff.active', 1);
                $productions_orders_staffs = $CI->db->get()->result_array();
                if (!empty($productions_orders_staffs)) {
                    foreach ($productions_orders_staffs as $kS => $vS) {
                        if (!has_permission('manufactures_productions_orders', $vS['staff_id'], 'view') && !has_permission('manufactures_productions_orders', $vS['staff_id'], 'view_own')) {
                            continue;
                        }

                        $notification_data = [
                            'date' => date('Y-m-d H:i:s'),
                            'description' => $dataHtml,
                            'touserid' => $vS['staff_id'],
                            'fromuserid' => $created_by,
                            'from_fullname' => $created_name,
                            'link' => 'manufactures/detail_productions_orders/' . $object_id,
                            'type' => 1,
                            'object_id' => $object_id,
                            'object_type' => $object_type,
                            'onclick' => '',
                        ];
                        $arrNotiWeb[] = $notification_data;
                        $notifiedUsers[] = $vS['staff_id'];
                    }
                }
            } elseif ($actions == "delete") {
                $CI->db->where('tblnotifications.object_id', $object_id);
                $CI->db->where('tblnotifications.object_type', $object_type);
                $CI->db->delete('tblnotifications');
                return true;
            }
        } elseif ($object_type == "kpi") {
            $actions = $data['actions'];
            if ($actions == "add") {

                $CI->db->select('*');
                $CI->db->from('tbl_kpi');
                $CI->db->where('tbl_kpi.id', $object_id);
                $kpi = $CI->db->get()->row_array();
                if (empty($kpi)) {
                    return false;
                }

                $type_kpi = $kpi['type_kpi'];
                $arrStaffNoti = [];

                $dataHtml = '';
                if ($type_kpi == 1) {
                    $CI->db->select('
                        tblstaff.staffid as staff_id, 
                        CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name
                    ', false);
                    $CI->db->from('tblstaff');
                    $CI->db->where('tblstaff.staffid', $kpi['staff']);
                    $staff = $CI->db->get()->row_array();
                    $dataHtml = 'Vừa mới tạo KPI <b>'.$kpi['reference_no'].' cho nhân viên <b>'.$staff['staff_name'].'</b></b>';

                    $arrStaffNoti[] = $staff;
                    $arrManager = getParentStaff($kpi['staff']);
                    if (!empty($arrManager)) {
                        $CI->db->select('
                            tblstaff.staffid as staff_id, 
                            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name
                        ', false);
                        $CI->db->from('tblstaff');
                        $CI->db->where_in('tblstaff.staffid', $arrManager);
                        $dtStaffManager = $CI->db->get()->result_array();
                        $arrStaffNoti = array_merge($arrStaffNoti, $dtStaffManager);
                    }

                } else if ($type_kpi == 2) {
                    $CI->db->select('
                        tbldepartments.departmentid as departmentid, 
                        tbldepartments.name as department_name
                    ', false);
                    $CI->db->from('tbldepartments');
                    $CI->db->where('tbldepartments.departmentid', $kpi['staff']);
                    $department = $CI->db->get()->row_array();
                    $dataHtml = 'Vừa mới tạo KPI <b>'.$kpi['reference_no'].' cho phòng ban <b>'.$department['department_name'].'</b>';

                    $CI->db->select('
                        tblstaff.staffid as staff_id, 
                        CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name
                    ', false);
                    $CI->db->from('tblstaff_departments');
                    $CI->db->join('tblstaff', 'tblstaff.staffid = tblstaff_departments.staffid');
                    $CI->db->where('tblstaff_departments.departmentid', $kpi['staff']);
                    $arrStaffDepartments = $CI->db->get()->result_array();
                    $arrStaffNoti = array_merge($arrStaffNoti, $arrStaffDepartments);
                }

                $arrId = [];

                $CI->db->select('tblstaff.staffid as staff_id, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name',
                    false);
                $CI->db->from('tblstaff');
                $CI->db->where('tblstaff.admin', 1);
                $staffAdmin = $CI->db->get()->result_array();
                $arrStaffNoti = array_merge($arrStaffNoti, $staffAdmin);
                $arrStaffNoti = array_unique_multidimensional($arrStaffNoti);

                $created_by = $staffid;
                $created_name = get_staff_full_name($created_by);

                if (!empty($arrStaffNoti)) {
                    foreach ($arrStaffNoti as $kS => $vS) {
                        $notification_data = [
                            'date' => date('Y-m-d H:i:s'),
                            'description' => $dataHtml,
                            'touserid' => $vS['staff_id'],
                            'fromuserid' => $created_by,
                            'from_fullname' => $created_name,
                            'link' => 'kpi/view_kpi/' . $object_id,
                            'type' => 14,
                            'object_id' => $object_id,
                            'object_type' => $object_type,
                            'onclick' => '',
                        ];
                        $arrNotiWeb[] = $notification_data;
                        $notifiedUsers[] = $vS['staff_id'];
                    }
                }
            } elseif ($actions == "delete") {
                $CI->db->where('tblnotifications.object_id', $object_id);
                $CI->db->where('tblnotifications.object_type', $object_type);
                $CI->db->delete('tblnotifications');
                return true;
            }
        }

        if (!empty($arrNotiWeb)) {
            $CI->db->insert_batch('tblnotifications', $arrNotiWeb);
        }

        if (!empty($arrNotiApp)) {
            SendOnesignal($arrNotiApp, $type_app, $object_id);
        }

        if (!empty($notifiedUsers)) {
            pusher_trigger_notification($notifiedUsers);
        }
        return true;
    }
}
