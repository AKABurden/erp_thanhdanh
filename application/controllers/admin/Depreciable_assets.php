<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Depreciable_assets extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->hasView = has_permission('depreciable_assets', '', 'create');
        $this->hasEdit = has_permission('depreciable_assets', '', 'edit');
        $this->hasCreate = has_permission('depreciable_assets', '', 'create');
        $this->hasDelete = has_permission('depreciable_assets', '', 'delete');
        $this->hasExcel = has_permission('depreciable_assets', '', 'export');
    }

    public function index()
    {
        if(!$this->hasView) {
            access_denied();
        }
        $data = [];
        $data['title'] = lang('Danh Sách Tài Sản Khấu Hao');
        $data['status'] = 1;
        $this->load->view('admin/depreciable_assets/manage', $data);
    }

    public function table()
    {
        $aColumns = [
            'tbl_depreciable_assets.id as id',
            'tbl_machines.code as code_machines',
            'tbl_machines.name as name_machines',
            'tbl_depreciable_assets.name_short as name_short',
            'tbl_depreciable_assets.date_depreciation as date_depreciation',//thời gian bắt đầu sử dụng
            'tbl_depreciable_assets.depreciation_period as depreciation_period',//thời gian khấu hao (tháng)
            'DATEDIFF(DATE_ADD(date_depreciation, INTERVAL tbl_depreciable_assets.depreciation_period MONTH), now()) as remaining_day',//số ngày còn lại
            'tbl_depreciable_assets.asset_value as asset_value',// giá trị tài sản
            '2',
            '3',
            'tbl_depreciable_assets.note as note',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_depreciable_assets';
        $where        = [];
        if($this->input->post('filterStatus')) {
            $filterStatus = $this->input->post('filterStatus');
            if($filterStatus == 1) {
                $where[] = 'AND DATE_ADD(date_depreciation, INTERVAL tbl_depreciable_assets.depreciation_period MONTH) >= now()';
            }
            elseif($filterStatus == 2) {
                $where[] = 'AND DATE_ADD(date_depreciation, INTERVAL tbl_depreciable_assets.depreciation_period MONTH) < now()';
            }
        }
        $join = [
            'JOIN tbl_machines ON tbl_machines.id = tbl_depreciable_assets.id_machines'
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_depreciable_assets.depreciation_period'
        ]);
        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $date = new DateTime($aRow['date_depreciation']);
            $date_end = $date->modify("+".$aRow['depreciation_period']." month");
            $date_end_format = $date_end->format('Y-m-d');

            $date_depreciation = date_create($aRow['date_depreciation']);
            $date_end = date_create($date_end_format);
            $diff = date_diff($date_depreciation, $date_end);
            $allDay = $diff->days;

            $diffDayUse = $aRow['remaining_day'];
            if(strtotime($date_end_format) < strtotime(date('Y-m-d'))) {
                $diffDayUse = $allDay;
            }



            $row = [];
            $row[] = $aRow['id'];
            $row[] = $aRow['code_machines'];
            $row[] = $aRow['name_machines'];
            $row[] = $aRow['name_short'];
            $row[] = _dC($aRow['date_depreciation']);
            $row[] = '<div class="text-center">' . $aRow['depreciation_period'] . '</div>';
            $dayUsEnd = ($allDay >= $diffDayUse ?  ($allDay - $diffDayUse) : 0);
            $row[] = '<div class="text-center">' . $dayUsEnd . '</div>';
            $row[] = '<div class="text-right">' . number_format_data($aRow['asset_value']) . '</div>';


            $assetUse = (($aRow['asset_value'] / $allDay) * $diffDayUse);
            if(strtotime($aRow['date_depreciation']) > strtotime(date('Y-m-d'))) {
                $assetUse = 0;
            }
            $row[] = '<div class="text-right">' . number_format_data($assetUse) . '</div>';
            $row[] = '<div class="text-right">' . number_format_data($aRow['asset_value'] - $assetUse) . '</div>';
            $row[] = '<div class="text-left">'.$aRow['note'].'</div>';
            $options = '';
            if($this->hasEdit) {
                $options .= '<a href="' . admin_url('depreciable_assets/detail/' . $aRow['id']) . '" class="btn btn-icon btn-default c_modal"><i class="fa fa-edit"></i></a>';
            }
            if($this->hasDelete) {
                $options .= '<a href="' . admin_url('depreciable_assets/delete/' . $aRow['id']) . '" class="btn btn-danger btn-icon delete-remind"><i class="fa fa fa-remove"></i></a>';
            }
            $row[] = $options;
            $output['aaData'][] = $row;
        }
        $output['sums'][1] = $this->db->where('DATE_ADD(date_depreciation, INTERVAL tbl_depreciable_assets.depreciation_period MONTH) >= now()', false, false)->get('tbl_depreciable_assets')->num_rows();
        $output['sums'][2] = $this->db->where('DATE_ADD(date_depreciation, INTERVAL tbl_depreciable_assets.depreciation_period MONTH) < now()', false, false)->get('tbl_depreciable_assets')->num_rows();
        echo json_encode($output);die();
    }

    public function detail($id = '')
    {
        if($this->input->post()) {
            $data = $this->input->post();
            if(!empty($id)) {
                if(!$this->hasEdit) {
                    ajax_access_denied();
                }
                $dataUpdate = [
                    'id_machines' => $data['id_machines'],
                    'name_short' => !empty($data['name_short']) ? $data['name_short']  : '',
                    'date_depreciation' => to_sql_date($data['date_depreciation']),
                    'depreciation_period' => $data['depreciation_period'],
                    'asset_value' => number_format_data($data['asset_value'], false),
                    'note' => $this->input->post('note', false),
                ];
                $this->db->where('id', $id);
                $success = $this->db->update('tbl_depreciable_assets', $dataUpdate);
                if(!empty($success)) {
                    echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Cập nhật thành công']);return;
                }
                echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Cập nhật không thành công']);return;
            }
            else {
                if(!$this->hasCreate) {
                    ajax_access_denied();
                }
                $dataInsert = [
                    'id_machines' => $data['id_machines'],
                    'name_short' => !empty($data['name_short']) ? $data['name_short']  : '',
                    'date_depreciation' => to_sql_date($data['date_depreciation']),
                    'depreciation_period' => $data['depreciation_period'],
                    'asset_value' => number_format_data($data['asset_value'], false),
                    'create_by' => get_staff_user_id(),
                    'note' => $this->input->post('note', false),
                ];
                $success = $this->db->insert('tbl_depreciable_assets', $dataInsert);
                if(!empty($success)) {
                    echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Thêm dữ liệu thành công']);return;
                }
                echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Thêm dữ liệu không thành công']);return;
            }
        }
        else {
            $data['title'] = 'Thêm giá trị khấu hao cho tài sản';
            if (!empty($id)) {
                if(!$this->hasEdit) {
                    access_denied();
                }
                $data['title'] = 'Sửa giá trị khấu hao cho tài sản';
                $data['depreciable_assets'] = $this->db->get_where('tbl_depreciable_assets', ['id' => $id])->row();
            }
            else {
                if(!$this->hasCreate) {
                    access_denied();
                }
            }

            $this->db->select('id, code, name');
            $data['list_machines'] = $this->db->get_where('tbl_machines')->result_array();
            $this->load->view('admin/depreciable_assets/detail', $data);
        }
    }


    public function delete($id)
    {
        if(!$this->hasDelete) {
            ajax_access_denied();
        }
        $data = [];
        if(!empty($id)) {
            $success= true;
            $this->db->where('id', $id);
            $success = $this->db->delete('tbl_depreciable_assets');
            if (!empty($success)) {
                echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Xóa dữ liệu thành công']);
                die();
            }
        }
        echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Xóa dữ liệu không thành công']);die();
    }


    public function export_excel()
    {
        if (!$this->hasExcel) {
            access_denied();
        }
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $style_excel = style_excel();
        $cloumns_excel = cloumns_excel();
        $style_excel['Background_header_one'] = $style_excel['Background_header'];
        $style_excel['Background_header_one']['fill']['color']['rgb'] = '81dcf7';

        $style_excel['Background_header_two'] = $style_excel['Background_header'];
        $style_excel['Background_header_two']['fill']['color']['rgb'] = 'f79e83';

        $style_excel['Background_header_three'] = $style_excel['Background_header'];
        $style_excel['Background_header_three']['fill']['color']['rgb'] = '8ac78c';
        $style_excel['Background_header']['font']['bold'] = true;
        $style_excel['Background_header']['fill']['color']['rgb'] = 'fef7e2';


        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(45);
        $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(60);

        $s = 0;
        $numberRow = 1;
        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'STT')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
        $s++;

        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'MÃ THIẾT BỊ')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
        $s++;

        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'TÊN THIẾT BỊ')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
        $s++;

        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'TÊN GỌI RIÊNG CỦA THIẾT BỊ')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
        $s++;

        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'THỜI GIAN BẮT ĐẦU SỬ DỤNG')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
        $s++;

        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'THỜI GIAN KHẤU HAO (THÁNG)')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
        $s++;

        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'SỐ NGÀY CÒN LẠI')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
        $s++;

        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'GIÁ TRỊ TÀI SẢN')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
        $s++;

        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'GIÁ TRỊ SỬ DỤNG')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
        $s++;

        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'GIÁ TRỊ CÒN LẠI')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
        $s++;

        $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'GHI CHÚ')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
        $s++;
        $numberRow++;

        $this->db->select('tbl_depreciable_assets.*');
        $this->db->select([
            'tbl_depreciable_assets.id as id',
            'tbl_machines.code as code_machines',
            'tbl_machines.name as name_machines',
            'tbl_depreciable_assets.name_short as name_short',
            'tbl_depreciable_assets.date_depreciation as date_depreciation',//thời gian bắt đầu sử dụng
            'tbl_depreciable_assets.depreciation_period as depreciation_period',//thời gian khấu hao (tháng)
            'DATEDIFF(DATE_ADD(date_depreciation, INTERVAL tbl_depreciable_assets.depreciation_period MONTH), now()) as remaining_day',//số ngày còn lại
            'tbl_depreciable_assets.asset_value as asset_value',// giá trị tài sản
            'tbl_depreciable_assets.note as note',
        ]);
        $this->db->join('tbl_machines', 'tbl_machines.id = tbl_depreciable_assets.id_machines');
        $recommended_list_one = $this->db->get('tbl_depreciable_assets')->result_array();

        foreach ($recommended_list_one as $key => $value) {

            $date = new DateTime($value['date_depreciation']);
            $date_end = $date->modify("+".$value['depreciation_period']." month");
            $date_end_format = $date_end->format('Y-m-d');

            $date_depreciation = date_create($value['date_depreciation']);
            $date_end = date_create($date_end_format);
            $diff = date_diff($date_depreciation, $date_end);
            $allDay = $diff->days;

            $diffDayUse = $value['remaining_day'];
            if(strtotime($date_end_format) < strtotime(date('Y-m-d'))) {
                $diffDayUse = $allDay;
            }



            $row = [];
            $row[] = ($key + 1);
            $row[] = $value['code_machines'];
            $row[] = $value['name_machines'];
            $row[] = $value['name_short'];
            $row[] = _dC($value['date_depreciation']);
            $row[] = $value['depreciation_period'];
            $dayUsEnd = ($allDay >= $diffDayUse ?  ($allDay - $diffDayUse) : 0);
            $row[] = $dayUsEnd;
            $row[] =  number_format_data($value['asset_value']);


            $assetUse = (($value['asset_value'] / $allDay) * $diffDayUse);
            if(strtotime($value['date_depreciation']) > strtotime(date('Y-m-d'))) {
                $assetUse = 0;
            }
            $row[] = number_format_data($assetUse);
            $row[] = number_format_data($value['asset_value'] - $assetUse);
            $row[] = $value['note'];

            $s = 0;
            foreach($row as $k => $v) {
                if($k > 3 && $k < 10) {
                    $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", $v)->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);;
                }
                else {
                    $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", $v)->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);;
                }
                $s++;
            }
            $numberRow++;
        }



        $filename = lang('DS_tai_san_khau_hao') . '.xls';
        $objPHPExcel->getActiveSheet()->freezePane('A1');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
}
