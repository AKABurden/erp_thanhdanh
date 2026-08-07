<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Internal_proposal_export extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        //		$this->load->model('suggestion_type_model');
        $this->load->model('internal_proposal_model');
        $this->load->model('purchases_model');
        $this->load->model('recommended_list_model');
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->perView = has_permission('internal_proposal', '', 'view');
        $this->perViewOwn = has_permission('internal_proposal', '', 'view_own');
        $this->perAdd = has_permission('internal_proposal', '', 'create');
        $this->perEdit = has_permission('internal_proposal', '', 'edit');
        $this->perDelete = has_permission('internal_proposal', '', 'delete');
        $this->perApprove = has_permission('internal_proposal', '', 'approve_accept');
        $this->approvett = has_permission('internal_proposal', '', 'approve');
        $this->perPdf = has_permission('internal_proposal', '', 'print');
        // $this->type_plan_propose = array(
        //     array(
        //         'id' => 'train',
        //         'name' => 'KẾ HOẠCH ĐÀO TẠO'
        //     ),
        //     array(
        //         'id' => 'repair',
        //         'name' => 'KẾ HOẠCH SỬA CHỮA'
        //     ),
        //     array(
        //         'id' => 'quality',
        //         'name' => 'KẾ HOẠCH KIỂM TRA CHẤT LƯỢNG SẢN PHẨM'
        //     ),
        //     array(
        //         'id' => 'performance',
        //         'name' => 'KẾ HOẠCH BẢO DƯỠNG ĐỊNH KỲ'
        //     ),
        //     array(
        //         'id' => 'calibration',
        //         'name' => 'KẾ HOẠCH HIỆU CHUẨN ĐỊNH KỲ'
        //     ),
        //     array(
        //         'id' => 'replace',
        //         'name' => 'KẾ HOẠCH VẬT TƯ THAY THẾ ĐỊNH KỲ'
        //     ),
        //     // array(
        //     // 	'id' => 'check',
        //     // 	'name' => 'KẾ HOẠCH KIỂM TRA CHẤT LƯỢNG SẢN PHẨM'
        //     // ),
        //     array(
        //         'id' => 'npl',
        //         'name' => 'KẾ HOẠCH MUA NPL'
        //     ),
        //     array(
        //         'id' => 'tools',
        //         'name' => 'KẾ HOẠCH MUA VĂN PHÒNG PHẨM'
        //     ),
        //     array(
        //         'id' => 'sanxuat',
        //         'name' => 'KẾ HOẠCH MUA VẬT TƯ SẢN XUẤT'
        //     ),
        //     array(
        //         'id' => 'vouchers_coupon',
        //         'name' => 'KẾ HOẠCH THU'
        //     ),
        //     array(
        //         'id' => 'pay_slip',
        //         'name' => 'KẾ HOẠCH CHI'
        //     ),
        //     array(
        //         'id' => 'purchases',
        //         'name' => 'MUA NGOÀI KẾ HOẠCH'
        //     ),
        //     array(
        //         'id' => 'recruit',
        //         'name' => 'KẾ HOẠCH TUYỂN DỤNG'
        //     ),
        //     array(
        //         'id' => 'machining',
        //         'name' => 'KẾ HOẠCH GIA CÔNG'
        //     ),
        //     array(
        //         'id' => 'system',
        //         'name' => 'KẾ HOẠCH CẬP NHẬT HỆ THỐNG'
        //     ),
        //     array(
        //         'id' => 'kpi',
        //         'name' => 'KẾ HOẠCH ĐÁNH GIÁ KPI'
        //     ),
        //     array(
        //         'id' => 'reward_discipline',
        //         'name' => 'KẾ HOẠCH KHEN THƯỞNG - KỸ LUẬT'
        //     ),
        //     array(
        //         'id' => 'reports',
        //         'name' => 'KẾ HOẠCH BÁO CÁO'
        //     )
        // );
        $this->type_plan_propose = type_plan_propose();
        $this->type_title_plan_propose = [];
        foreach ($this->type_plan_propose as $key => $value) {
            $this->type_title_plan_propose[$value['id']] = $value['name'];
        }
        $this->type_object = [
            'productions_plan' => 'Kế hoạch NPL',
            'orders' => 'Đơn đặt hàng bán',
            'customer' => 'Khách hàng',
            'supplier' => 'Nhà cung cấp',
            'quotes' => 'Báo giá',
            'import' => 'Nhập kho',
            'releases' => 'Giao hàng',
        ];
        $this->code_departments = [
            '1.BOD-CFO',
            '1.BOD-PRE',
            '1.CEO',
            '1.KH',
            '1.KH',
        ];
        $this->is_branch = true;
        $this->arrayStatus = [
            'approved' => 'Đã Duyệt',
            'rejected' => 'Không duyệt',
            'pending' => 'Chưa duyệt'
        ];
    }

    public function export_excel()
    {
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
        $objPHPExcel->getDefaultStyle()->applyFromArray([
            'font' => array(
                'name'  => 'Times New Roman'
            ),
        ]);

        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');

        $tbrecommended_detail = '(SELECT
            tblinternal_proposal_recommended.id_internal_proposal AS id_internal_proposal,
            GROUP_CONCAT(tbl_recommended_list.code SEPARATOR "; ") as code,
            GROUP_CONCAT(tbl_recommended_list.name SEPARATOR "; ") as name
        FROM tblinternal_proposal_recommended
        INNER JOIN tbl_recommended_list ON tbl_recommended_list.id = tblinternal_proposal_recommended.recommended_list_detail_id
        GROUP BY tblinternal_proposal_recommended.id_internal_proposal
        ) AS tbrecommended_detail';

        $tbstaff_assigned = '(SELECT
            tblinternal_proposal_assigned.id_internal_proposal AS id_internal_proposal,
            GROUP_CONCAT(CONCAT(tblstaff.firstname, " ", tblstaff.lastname) SEPARATOR "; ") as staff_name
            FROM tblinternal_proposal_assigned
            INNER JOIN tblstaff ON tblstaff.staffid = tblinternal_proposal_assigned.id_staff
            GROUP BY tblinternal_proposal_assigned.id_internal_proposal
        ) AS tbstaff_assigned';

        $tbstaff_bod = '(SELECT
            tblinternal_proposal_staff_pod.id_internal_proposal AS id_internal_proposal,
            GROUP_CONCAT(CONCAT(tblstaff.firstname, " ", tblstaff.lastname) SEPARATOR "; ") as staff_name
            FROM tblinternal_proposal_staff_pod
            INNER JOIN tblstaff ON tblstaff.staffid = tblinternal_proposal_staff_pod.id_staff
            GROUP BY tblinternal_proposal_staff_pod.id_internal_proposal
        ) AS tbstaff_bod';

        // $tbpurchase = '(SELECT
        //     tblinternal_proposal_purchase.id_internal_proposal AS id_internal_proposal,
        //     GROUP_CONCAT(CONCAT(tblpurchases.prefix, "-", tblpurchases.code) SEPARATOR "; ") as purchase
        //     FROM tblinternal_proposal_purchase
        //     INNER JOIN tblpurchases ON tblpurchases.id = tblinternal_proposal_purchase.id_purchases
        //     GROUP BY tblinternal_proposal_purchase.id_internal_proposal
        // ) AS tbpurchase';

        $tblpurchase_order = '(SELECT
            tblpurchase_order.id_internal_proposal AS id_internal_proposal,
            GROUP_CONCAT(CONCAT(tblpurchase_order.prefix, tblpurchase_order.code) SEPARATOR "; ") as code
        FROM tblpurchase_order
        GROUP BY tblpurchase_order.id_internal_proposal
        ) AS tblpurchase_order';

        $tblsuggestion = '(SELECT
            tblsuggestion.id_internal_proposal AS id_internal_proposal,
            GROUP_CONCAT(tblsuggestion.code SEPARATOR "; ") as code
        FROM tblsuggestion
        GROUP BY tblsuggestion.id_internal_proposal
        ) AS tblsuggestion';

        $tblplan_propose = '(SELECT
            tblplan_propose.id_internal_proposal AS id_internal_proposal,
            GROUP_CONCAT(tblplan_propose.code SEPARATOR "; ") as code
        FROM tblplan_propose
        GROUP BY tblplan_propose.id_internal_proposal
        ) AS tblplan_propose';

        $this->db->select('
            tblinternal_proposal.id as id,
            tblinternal_proposal.date as date,
            tblinternal_proposal.date_finish as date_finish,
            tblinternal_proposal.code as code,
            tblinternal_proposal.type_plan_propose as plan_type,
            tb_type.name as type,
            tb_group.name as group,
            tbrecommended_detail.name as detail,
            tblbranch.name as branch,
            tbl_room.name as department,
            tblcategory_tasks.content as task,
            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff,
            tbstaff_assigned.staff_name as staff_assigned,
            tbstaff_bod.staff_name as staff_bod,
            CONCAT(tblpurchases.prefix, tblpurchases.code) as purchase,
            CONCAT(tbl_services.prefix, tbl_services.code) as service,
            tblpurchase_order.code as purchase_order,
            tblsuggestion.code as financial_proposal,
            tblplan_propose.code as plan,
            IF(tbl_internal_proposal_purchase_items.id_purchases_items > 0,(tbl_internal_proposal_purchase_items.quantity_payment*tbl_internal_proposal_purchase_items.price*(IF(tbl_internal_proposal_purchase_items.tax_rate > 0,(1 +(tbl_internal_proposal_purchase_items.tax_rate/100)),1))),tblinternal_proposal.money) as money,
            tblinternal_proposal.content as content,
            tbl_materials.code as item_code,
            tbl_materials.name as item_name,
            tblunits.unit as item_unit,
            tblpurchases_items.quantity as item_all_quanliti,
            tblpurchases_items.quantity_net as item_all_quanliti_net,
            (tbl_internal_proposal_purchase_items.quantity) as item_all_quanliti_order,
            (tblpurchases_items.quantity_net - tbl_internal_proposal_purchase_items.quantity) as item_all_quanliti_left,
            tblsuppliers.company as company_supp
        ');

        // 'item_code' => 'Mặt hàng',
        //     'item_name' => 'Tên hàng',
        //     'item_unit' => 'ĐVT',
        //     'item_all_quanliti' => 'Tổng số lượng',
        //     'item_all_quanliti_net' => 'Số lượng duyệt',
        //     'item_all_quanliti_order' => 'Số lượng đặt',
        //     'item_all_quanliti_left' => 'Số lượng còn lại',
        $this->db->from('tblinternal_proposal');
        $this->db->join('tbl_recommended_list tb_type', 'tb_type.id = tblinternal_proposal.recommended_list_group_id', 'left');
        $this->db->join('tbl_recommended_list tb_group', 'tb_group.id = tblinternal_proposal.recommended_list_id', 'left');
        $this->db->join($tbrecommended_detail, 'tbrecommended_detail.id_internal_proposal = tblinternal_proposal.id', 'left');
        $this->db->join('tblbranch', 'tblbranch.id = tblinternal_proposal.id_branch', 'left');
        $this->db->join('tbl_room', 'tbl_room.id = tblinternal_proposal.id_departments', 'left');
        $this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblinternal_proposal.category_tasks', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblinternal_proposal.staff', 'left');
        $this->db->join($tbstaff_assigned, 'tbstaff_assigned.id_internal_proposal = tblinternal_proposal.id', 'left');
        $this->db->join($tbstaff_bod, 'tbstaff_bod.id_internal_proposal = tblinternal_proposal.id', 'left');
        // $this->db->join($tbpurchase, 'tbpurchase.id_internal_proposal = tblinternal_proposal.id', 'left');
        $this->db->join('tbl_services', 'tbl_services.id = tblinternal_proposal.id_service', 'left');
        $this->db->join($tblsuggestion, 'tblsuggestion.id_internal_proposal = tblinternal_proposal.id', 'left');
        $this->db->join($tblpurchase_order, 'tblpurchase_order.id_internal_proposal = tblinternal_proposal.id', 'left');
        $this->db->join($tblplan_propose, 'tblplan_propose.id_internal_proposal = tblinternal_proposal.id', 'left');
        $this->db->join('tblinternal_proposal_purchase', 'tblinternal_proposal_purchase.id_internal_proposal = tblinternal_proposal.id', 'left');
        $this->db->join('tbl_internal_proposal_purchase_items', 'tbl_internal_proposal_purchase_items.id_internal_proposal = tblinternal_proposal.id', 'left');

        $this->db->join('tblpurchases', 'tblpurchases.id = tblinternal_proposal_purchase.id_purchases', 'left');
        $this->db->join('tblpurchases_items', 'tblpurchases_items.id = tbl_internal_proposal_purchase_items.id_purchases_items', 'left');
        $this->db->join('tbl_materials', 'tbl_materials.id = tblpurchases_items.product_id', 'left');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_internal_proposal_purchase_items.suppliers_id', 'left');


        if (!empty($start_date_search)) {
            $this->db->where('tblinternal_proposal.date >= "' . to_sql_date($start_date_search) . ' 00:00:00' . '"');
        }
        if (!empty($end_date_search)) {
            $this->db->where('tblinternal_proposal.date <= "' . to_sql_date($end_date_search) . ' 23:59:59' . '"');
        }

        $this->db->group_by('tblpurchases_items.id,tbl_internal_proposal_purchase_items.id,tblinternal_proposal.id');
        $this->db->order_by('tblinternal_proposal.id', 'desc');
        $this->db->order_by('tblpurchases.id', 'desc');
        $result = $this->db->get()->result_array();
        // $result = [];

        $styleTitle = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                'bold' => true,
                'size' => 18,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];

        $styleHeader = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                // 'bold' => true,
                // 'color' => array('rgb' => '111112'),
                'size' => 12,
                'name' => 'Times New Roman'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '4BACC6'),
                'size' => 12,
                // 'bold' => true
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];
        $styleHeaderChild = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                // 'bold' => true,
                // 'color' => array('rgb' => '111112'),
                'size' => 12,
                'name' => 'Times New Roman'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '4BACC6'),
                'size' => 12,
                // 'bold' => true
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];

        $stylePlain = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                // 'bold' => false,
                // 'color' => array('rgb' => '111112'),
                'size' => 11,
                'name' => 'Times New Roman'
            ),
        ];

        $headerFillColor = [
            'A' => array('rgb' => 'BDD7EE'),
        ];

        $cloumns_excel = cloumns_excel();
        $colName = [
            'stt' => "STT",
            'date' => 'Ngày Đề Xuất',
            'code' => 'Mã Đề Xuất',
            'plan_type' => 'Loại Kế Hoạch',
            'type' => 'Loại Đề Xuất',
            'group' => 'Nhóm Đề Xuất',
            // 'detail' => 'Chi Tiết Đề Xuất',
            'item_code' => 'Mặt hàng',
            'item_name' => 'Tên hàng',
            'item_unit' => 'ĐVT',
            'item_all_quanliti' => 'Tổng số lượng',
            'item_all_quanliti_net' => 'Số lượng duyệt',
            'item_all_quanliti_order' => 'Số lượng đặt',
            'item_all_quanliti_left' => 'Số lượng còn lại',
            'branch' => 'Chi Nhánh',
            'department' => 'Khối Phòng Ban',
            'task' => 'Mã Công Việc',
            'staff' => 'Người Đề Xuất',
            'staff_assigned' => 'Quản Lý Duyệt',
            'staff_bod' => 'BOD Duyệt',
            'purchase' => 'Phiếu YCMH',
            'service' => 'Phiếu Dịch Vụ',
            'financial_proposal' => 'Phiếu ĐXTC',
            'purchase_order' => 'Phiếu PO',
            'plan' => 'Phiếu Kế Hoạch',
            'money' => 'Số Tiền',
            'company_supp' => 'Nhà Cung Cấp',
            'content' => 'Nội Dung',
            'checkis' => 'Hoàn thành quy trình',
        ];
        $aColumns = array_keys($colName);

        insertCompanyInfo($objPHPExcel, 'C1:AA2', 'B1');

        $excelRowNum = 1 + 4;
        $maxCol = count($colName) - 1;
        $objPHPExcel->getActiveSheet()->mergeCells('A' . ($excelRowNum) . ':' . $cloumns_excel[$maxCol] . $excelRowNum);
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $excelRowNum, ('PHIẾU ĐỀ XUẤT NỘI BỘ'))->getStyle("A" . $excelRowNum)->applyFromArray($styleTitle);
        // $objPHPExcel->getActiveSheet()->freezePane('A1');

        $RowNumStart = $excelRowNum = 2 + 4;
        foreach ($aColumns as $key => $value) {
            foreach ($headerFillColor as $colIndex => $color) {
                if ($cloumns_excel[$key] == $colIndex) {
                    $styleHeader['fill']['color'] = $color;
                    unset($headerFillColor[$colIndex]);
                    break;
                }
            }
            $ColumsHeader = $cloumns_excel[$key] . $excelRowNum.':'.$cloumns_excel[$key] . ($excelRowNum + 1);
            $objPHPExcel->getActiveSheet()->mergeCells("$ColumsHeader")
                ->getStyle("$ColumsHeader")
                ->applyFromArray($styleHeader);

//            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$key] . $excelRowNum, ($colName[$value]))->getStyle($cloumns_excel[$key] . ($excelRowNum))->applyFromArray($styleHeader);
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$key] . $excelRowNum, ($colName[$value]))->getStyle($ColumsHeader)->applyFromArray($styleHeader);
        }


        $excelRowNum = 3 + 5;
        $countAppend = 0;
        foreach ($result as $key => $aRow) {
            $aRow['stt'] = ($key + 1);

            if (!empty($aRow['plan_type'])) {
                foreach ($this->type_plan_propose as $type_plan_propose) {
                    if ($aRow['plan_type'] == $type_plan_propose['id']) {
                        $aRow['plan_type'] = $type_plan_propose['name'];
                    }
                }
            }

            if (!empty($aRow['content'])) {
                $aRow['content'] = html_entity_decode(strip_tags($aRow['content']), ENT_QUOTES, 'UTF-8');
            }
            $this->db->select('tbl_internal_proposal_process.*,tbl_internal_proposal_process_child.id as childs');
            $this->db->where('tbl_internal_proposal_process.id_internal_proposal', $aRow['id']);
            $this->db->join('tbl_internal_proposal_process_child', 'tbl_internal_proposal_process_child.recommended_list_id = tbl_internal_proposal_process.id_process AND tbl_internal_proposal_process_child.id_internal_proposal = tbl_internal_proposal_process.id_internal_proposal', 'left');
            $this->db->order_by('tbl_internal_proposal_process.id_process asc');
            $this->db->group_by('tbl_internal_proposal_process.id');
            $data_checklist_items = $this->db->get('tbl_internal_proposal_process')->result_array();

            // NEW: xác định trạng thái thời hạn theo yêu cầu
            $date_start = $aRow['date']; // ngày bắt đầu
            $date_end = $aRow['date_finish']; // ngày kết thúc
            if (empty($date_end)) {
                $date_end = $date_start;
            }
            $now = date('Y-m-d H:i:s');

            $hasPending = false;
            $allDone = true;
            $max_date_status = null;

            foreach ($data_checklist_items as $v) {
                if (empty($v['status']) || $v['status'] == 0) {
                    $hasPending = true;
                    $allDone = false;
                    // không break ở đây nếu muốn thu thập thêm thông tin; nhưng pending đủ để đánh dấu chưa hoàn thành/trễ
                    break;
                } else {
                    // status == 1
                    if (!empty($v['date_status'])) {
                        // lấy max date_status
                        if ($max_date_status === null || strtotime($v['date_status']) > strtotime($max_date_status)) {
                            $max_date_status = $v['date_status'];
                        }
                    }
                }
            }

            if ($hasPending) {
                // có bước chưa xong
                if (strtotime($now) > strtotime($date_end)) {
                    $aRow['checkis'] = 'Trễ';
                } else {
                    $aRow['checkis'] = 'Chưa hoàn thành';
                }
            } elseif ($allDone) {
                // tất cả đã xong (status == 1)
                if (empty($max_date_status)) {
                    // đã hoàn thành nhưng không có date_status -> coi là đã hoàn thành (không xác định thời gian)
                    $aRow['checkis'] = 'Đã hoàn thành';
                } else {
                    if (strtotime($max_date_status) > strtotime($date_end)) {
                        $aRow['checkis'] = 'Trễ';
                    } elseif (strtotime($max_date_status) < strtotime($date_start)) {
                        $aRow['checkis'] = 'Sớm';
                    } else {
                        // max_date_status nằm trong [date_start, date_end]
                        $aRow['checkis'] = 'Đúng';
                    }
                }
            } else {
                // fallback
                $aRow['checkis'] = 'Chưa hoàn thành';
            }

            foreach ($aColumns as $colIndex => $colCode) {
                if (str_contains($colCode, 'date')) {
                    // $cellValue = (isset($aRow[$colCode]) ? _d($aRow[$colCode]) : '');
                    // $newDate = date($newDateFormat, strtotime($oldDate));
                    $cellValue = (isset($aRow[$colCode]) ? date("Y/m/d H:i:s", strtotime($aRow[$colCode])) : '');
                } else {
                    $cellValue = (isset($aRow[$colCode]) ? $aRow[$colCode] : '');
                }

                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$colIndex] . $excelRowNum, $cellValue)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
            }




            $Proposal = $this->getProposalProcessData($aRow['id'], $aRow['date_finish'], $data_checklist_items);
            $columsNext = count($aColumns);

            if(!empty($Proposal)) {
                foreach ($Proposal['steps'] as $kStep => $vStep) {
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        $cloumns_excel[$columsNext] . $excelRowNum,
                        $vStep['process_name']
                    )->getStyle($cloumns_excel[$columsNext] . $excelRowNum)->applyFromArray($stylePlain);
                    $columsNext++;
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        $cloumns_excel[$columsNext] . $excelRowNum,
                        $vStep['staff_name']
                    )->getStyle($cloumns_excel[$columsNext] . $excelRowNum)->applyFromArray($stylePlain);
                    $columsNext++;
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        $cloumns_excel[$columsNext] . $excelRowNum,
                        $this->arrayStatus[$vStep['status_text']]
                    )->getStyle($cloumns_excel[$columsNext] . $excelRowNum)->applyFromArray($stylePlain);
                    $columsNext++;



                    $objPHPExcel->getActiveSheet()->setCellValue(
                        $cloumns_excel[$columsNext] . $excelRowNum,
                        (!empty($vStep['date_status']) ? _dt($vStep['date_status']) : '')
                    )->getStyle($cloumns_excel[$columsNext] . $excelRowNum)->applyFromArray($stylePlain);
                    $columsNext++;
                }

                if(!empty($Proposal['steps'])) {
                    $countAppend = max($countAppend, count($Proposal['steps']));
                }
            }

            $excelRowNum++;
        }

        $keyEndColumd = count($aColumns);
        for($i = 0; $i < $countAppend; $i++) {
            $startColums = $cloumns_excel[$keyEndColumd] . $RowNumStart;
            $endColums = $cloumns_excel[$keyEndColumd + 3] . $RowNumStart;
            $objPHPExcel->getActiveSheet()->mergeCells("$startColums:$endColums")
                ->getStyle("$startColums:$endColums")
                ->applyFromArray($styleHeader);
            $objPHPExcel->getActiveSheet()->setCellValue($startColums, 'Bước ' . ($i + 1));
            $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$keyEndColumd + $i])->setAutoSize(true);


            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$keyEndColumd] . ($RowNumStart + 1), 'Tên bước')
                ->getStyle($cloumns_excel[$keyEndColumd] . ($RowNumStart + 1))
                ->applyFromArray($styleHeaderChild);;
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$keyEndColumd + 1] . ($RowNumStart + 1), 'Người duyệt')
                ->getStyle($cloumns_excel[$keyEndColumd + 1] . ($RowNumStart + 1))
                ->applyFromArray($styleHeaderChild);
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$keyEndColumd + 2] . ($RowNumStart + 1), 'Trạng thái')
                ->getStyle($cloumns_excel[$keyEndColumd + 2] . ($RowNumStart + 1))
                ->applyFromArray($styleHeaderChild);
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$keyEndColumd + 3] . ($RowNumStart + 1), 'Thời gian duyệt')
                ->getStyle($cloumns_excel[$keyEndColumd + 3] . ($RowNumStart + 1))
                ->applyFromArray($styleHeaderChild);

            $keyEndColumd = $keyEndColumd + 4;
        }

        $filename = 'Phieu_DXNB' . '.xls';
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="$filename"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response =  array(
            'result' => 1,
            'message' => lang('success'),
            'filename' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        die(json_encode($response));
    }


    function test() {
        print_arrays($this->getProposalProcessData(12316, '2024-06-15'));
    }

    function getProposalProcessData($proposalId, $dateFinish = null, $data_checklist_items = [])
    {
        $dateFinish = date('Y-m-d');
        $CI =& get_instance();

        // 1) Query thô dữ liệu process + child flag
        if(empty($data_checklist_items)) {
            $CI->db->select(
                '
                    tbl_internal_proposal_process.*,
                    tbl_internal_proposal_process_child.id as child_id
                '
            );
            $CI->db->from('tbl_internal_proposal_process');
            $CI->db->join(
                'tbl_internal_proposal_process_child',
                'tbl_internal_proposal_process_child.recommended_list_id = tbl_internal_proposal_process.id_process
         AND tbl_internal_proposal_process_child.id_internal_proposal = tbl_internal_proposal_process.id_internal_proposal',
                'left'
            );
            $CI->db->where('tbl_internal_proposal_process.id_internal_proposal', $proposalId);
            $CI->db->order_by('tbl_internal_proposal_process.id_process', 'asc');
            $CI->db->group_by('tbl_internal_proposal_process.id'); // để tránh nhân bản khi có nhiều child
            $rows = $CI->db->get()->result_array();
        }
        else {
            $rows = $data_checklist_items;
        }

        $result = [
            'proposal_id'  => $proposalId,
            'date_finish'  => $dateFinish,
            'has_steps'    => !empty($rows),
            'steps'        => [],
        ];

        if (empty($rows)) {
            return $result; // không có bước nào
        }

        // 2) Map vai trò từ trường 'bod' -> label (bạn có thể điều chỉnh text tùy ngôn ngữ)
        $roleMap = [
            1 => 'BOD duyệt',
            2 => 'Người duyệt đề xuất',
            3 => 'Người Hoàn Thành 2',
            4 => 'Người kiểm soát hoàn thành',
            5 => 'Người lập đề xuất',
            6 => 'Người hoàn thành 2',
            7 => 'Người kiểm toán hoàn thành',
            8 => 'Người kiểm soát hoàn thành',
            9 => 'Người kiểm toán hoàn thành',
        ];

        // 3) Hàm phụ: status -> text
        $statusText = function (int $s): string {
            if ($s === 1) return 'approved';
            if ($s === 2) return 'rejected';
            return 'pending';
        };

        // 4) Chuẩn hóa deadline để so sánh
        $deadlineStart = null;
        $deadlineEnd   = null;
        if (!empty($dateFinish)) {
            $deadlineStart = $dateFinish . ' 00:00:00';
            $deadlineEnd   = $dateFinish . ' 23:59:59';
        }

        // 5) Duyệt từng step và gom dữ liệu con (inspection children + production reports)
        foreach ($rows as $v) {
            $dateStatus = !empty($v['date_status']) ? $v['date_status'] : null;

            // Tính overdue theo logic gốc:
            // - pending: quá hạn nếu $dateFinish < hôm nay (00:00:00)  => đỏ
            // - approved: nếu date_status > deadlineEnd => cảnh báo muộn
            $isOverdue = false;
            if (!empty($deadlineStart)) {
                if ((int)$v['status'] === 0) {
                    // chưa duyệt: nếu đã qua hạn
                    $isOverdue = (date('Y-m-d 00:00:00') > $deadlineStart);
                } elseif ((int)$v['status'] === 1 && $dateStatus) {
                    // đã duyệt: kiểm tra có duyệt sau hạn không
                    $isOverdue = ($dateStatus > $deadlineEnd);
                }
            }

            // Lấy thông tin nhân sự (nếu cần)
            $staffId   = !empty($v['staff_id']) ? (int)$v['staff_id'] : null;
            $staffName = $staffId ? get_staff_full_name($staffId) : null;

            // Lấy children kiểm quy trình (nếu có)
            $children = get_table_where(
                'tbl_tinternal_proposal_inspection_criteria_process',
                [
                    'id_internal_proposal'          => $proposalId,
                    'process_id'                    => $v['id_process'],
                    'id_internal_proposal_process'  => $v['id'],
                ]
            );

            $inspectionChildren = [];
            if (!empty($children)) {
                foreach ($children as $c) {
                    $inspectionChildren[] = [
                        'inspection_id' => (int)$c['inspection_criteria'],
                        'is_check_not'  => (int)$c['isCheckNot'], // giữ nguyên theo DB của bạn
                    ];
                }
            }

            // Lấy production reports:
            // - Khi status = 1 và có child: duyệt theo từng child
            // - Khi status = 2 (không duyệt): report gắn child = 0
//            $productionReports = [];

//            if ((int)$v['status'] === 1) {
//                if (!empty($inspectionChildren)) {
//                    foreach ($inspectionChildren as $child) {
//                        $pr = get_table_where(
//                            'tblproduction_report',
//                            [
//                                'id_internal_proposal'             => $proposalId,
//                                'id_internal_proposal_process'     => $v['id'],
//                                'id_internal_proposal_process_child'=> $child['inspection_id'],
//                            ],
//                            '',
//                            'row_array'
//                        );
//                        if (!empty($pr)) {
//                            $productionReports[] = [
//                                'id'          => (int)$pr['id'],
//                                'reference_no'=> (string)$pr['reference_no'],
//                                'for_child'   => (int)$child['inspection_id'],
//                            ];
//                        }
//                    }
//                }
//            } elseif ((int)$v['status'] === 2) {
//                $pr = get_table_where(
//                    'tblproduction_report',
//                    [
//                        'id_internal_proposal'             => $proposalId,
//                        'id_internal_proposal_process'     => $v['id'],
//                        'id_internal_proposal_process_child'=> 0,
//                    ],
//                    '',
//                    'row_array'
//                );
//                if (!empty($pr)) {
//                    $productionReports[] = [
//                        'id'          => (int)$pr['id'],
//                        'reference_no'=> (string)$pr['reference_no'],
//                        'for_child'   => 0,
//                    ];
//                }
//            }

            // Quyền hành động (tùy quy tắc hệ thống – ở đây đặt mặc định, FE/BE nơi khác sẽ quyết định)
            $canApprove    = ((int)$v['status'] === 0);
            $canUnapprove  = ((int)$v['status'] === 1);
            $canReject     = ((int)$v['status'] === 0);


            $this->db->where('id_internal_proposal', $proposalId);
            $assigned_pod = $this->db->get('tblinternal_proposal_staff_pod')->result_array();
            if (!empty($assigned_pod)) {
                foreach ($assigned_pod as $k => $value) {
                    if (!empty($value['id_staff'])) {
                        $FullName = get_staff_full_name($value['id_staff']);
                        if ($bod_process == '') {
                            $bod_process = '<div>' . staff_profile_image(
                                    $value['id_staff'],
                                    array('staff-profile-image-small mright5'),
                                    'small',
                                    array('data-toggle' => 'tooltip', 'data-title' => $FullName)
                                ) . $FullName . '</div>';
                        }
                    }
                }
                // $column[12] = '<div style="white-space: nowrap;">' . $column[12] . '</div>';
            }

            $result['steps'][] = [
                'id'                  => (int)$v['id'],
                'id_process'          => (int)$v['id_process'],
                'process_name'        => (string)$v['name'],
                'status'              => (int)$v['status'],
                'status_text'         => $statusText((int)$v['status']),
                'staff_id'            => $staffId,
                'staff_name'          => $staffName,
                'date_status'         => $dateStatus,
                'is_overdue'          => $isOverdue,
                'can_approve'         => $canApprove,
                'can_unapprove'       => $canUnapprove,
                'can_reject'          => $canReject,
                'reason'              => !empty($v['reason']) ? (string)$v['reason'] : null,
                'has_children'        => !empty($v['child_id']),
                'inspection_children' => $inspectionChildren,
//                'production_reports'  => $productionReports,
                'role_label'          => isset($roleMap[(int)$v['bod']]) ? $roleMap[(int)$v['bod']] : 'Khác',
            ];
        }
        return $result;
    }

}
