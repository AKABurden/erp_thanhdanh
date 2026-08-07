<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Violation_group extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['title'] = _l('dt_violation_group');
        $data['dtType'] = get_table_where('tbl_type_violation_group');
        $this->load->view('admin/violation_group/index', $data);
    }

    public function detail($id = 0){
        $data = [];
        $dtData = get_table_where('tbl_violation_group',['id'=>$id],'','row_array');
        if ($this->input->post()){
            if ((!empty($dtData) && $dtData['code'] != $this->input->post('code'))) {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_violation_group.code]');
            }
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('type', lang("Loại"), 'required');
            if ($this->form_validation->run() == true) {
                $type = _string($this->input->post('type'));
                $name = _string($this->input->post('name'));
                $code = _string($this->input->post('code'));
                $detail = _string($this->input->post('detail'));
                if (empty($type)){
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng chọn loại');
                    echo json_encode($data);
                    return;
                }

                $option = [
                    'type' => $type,
                    'name' => $name,
                    'code' => $code,
                    'detail' => $detail,
                ];

                if ($id) {
                    $this->db->where('id',$id);
                    $ins = $this->db->update('tbl_violation_group',$option);
                    $violation_group_id = $id;
                } else {
                    $ins = $this->db->insert('tbl_violation_group',$option);
                    $violation_group_id = $ins;
                }

                if (!empty($violation_group_id)) {
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
            return;
        }
        if (empty($id)) {
            $data['title'] = _l('dt_add_violation_group');
        } else {
            $data['title'] = _l('dt_edit_violation_group');
        }
        $data['dtData'] = $dtData;
        $data['id'] = $id;
        $data['dtType'] = get_table_where('tbl_type_violation_group');
        $this->load->view('admin/violation_group/detail',$data);
    }


    public function delete_violation_group($id)
    {
        $data = [];
        $this->db->from('tblproduction_report');
        $this->db->where('violation_group',$id);
        $checkExists = $this->db->count_all_results();
        if (!empty($checkExists)){
            $data['result'] = 0;
            $data['message'] = lang('Nhóm vi phạm đã được sử dụng !');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_violation_group');
        if ($success) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function get_exsit($id = '')
    {
        $items = get_table_where('tblpay_slip', array('id_costs' => $id), '', 'row');
        $itemss = get_table_where('tblother_payslips', array('id_costs' => $id), '', 'row');
        if (!empty($items) || !empty($itemss)) {
            echo json_encode(true);
            die;
        } else {
            $parent = get_table_where('tblcosts', array('costs_parent' => $id), '', 'row');
            if (!empty($parent)) {
                echo json_encode(true);
                die;
            }
            $success = $this->db->delete('tblcosts', array('id' => $id));
            if ($success) {
                $success = true;
                $message = _l('ch_delete_successfuly');
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
            die;
        }
    }

    public function modal_excel_import()
    {
        $data['title'] = _l('dt_import_violation_group');
        $this->load->view('admin/violation_group/excel_import', $data);
    }

    public function excel_import()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $count = 0;
        $data = [];
        if (!empty($_FILES['file'])) {
            $fullfile = $_FILES['file']['tmp_name'];
            $nameFile = $_FILES['file']['name'];
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
                die();
            }
            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            // $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName       = $objPHPExcel->getSheetNames();
            $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow         = $objWorksheet->getHighestRow();
            $highestColumn      = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('G');
            $arraydata          = array();

            $fields = $this->input->post('fields');
            for ($row = 2; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }
            foreach ($arraydata as $key => $value) {

                $code_type = $value[0];
                $name_type = $value[1];
                $code = $value[2];
                $name = $value[3];
                $detail = $value[4];
                $code_old = $value[5];

                if (empty($code) || empty($name) || empty($code_type) || empty($name_type)) {
                    continue;
                }

                $dtType = get_table_where('tbl_type_violation_group',['code' => $code_type],'','row_array');
                $type = 0;
                if (!empty($dtType)){
                    $type = $dtType['id'];
                } else {
                    $this->db->insert('tbl_type_violation_group', [
                        'code' => $code_type,
                        'name' => $name_type,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                    ]);
                    $type = $this->db->insert_id();
                }

                $dtViolationGroup = get_table_where('tbl_violation_group',['code' => $code],'','row_array');
                if (!empty($dtViolationGroup)) {
                    $options = [
                        'code' => $code,
                        'name' => $name,
                        'detail' => $detail,
                        'type' => $type,
                        'code_old' => $code_old,
                    ];
                    $this->db->where('id',$dtViolationGroup['id']);
                    $rs = $this->db->update('tbl_violation_group',$options);
                } else {
                    $options = [
                        'code' => $code,
                        'name' => $name,
                        'active' => 1,
                        'detail' => $detail,
                        'type' => $type,
                        'code_old' => $code_old,
                    ];
                    $this->db->insert('tbl_violation_group',$options);
                    $rs = $this->db->insert_id();
                }
                if ($rs) {
                    $count++;
                }
            }
        }
        echo json_encode(
            [
                'success' => true,
                'alert_type' => 'success',
                'message' => 'Import thành công ' . $count . ' dòng',
            ]
        );
        die();
    }

    public function excel_export()
    {
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(45);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(45);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(45);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(45);
        $colum_array = array('I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '111112'),
                'size' => 11,
                'name' => 'Times New Roman'
            )
        );
        $BStyleHeader = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '111112'),
                'size' => 14,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $BStyle1 = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                'color' => array('rgb' => '111112'),
                'size' => 11,
                'name' => 'Times New Roman'
            )
        );
        $BStyleCenter = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                'color' => array('rgb' => '111112'),
                'size' => 11,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );
        for ($row = 1; $row <= 100; $row++) {
            $styleArray = [
                'font' => [
                    'size' => 12
                ]
            ];
            $objPHPExcel->getActiveSheet()
                ->getStyle("A1:N2")
                ->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'DANH SÁCH NHÓM VI PHẠM');
            $objPHPExcel->getActiveSheet()->mergeCells('A1:F1')->getStyle('A1:F1')->applyFromArray($BStyleHeader);;
        }
        $objPHPExcel->getActiveSheet()->setCellValue('A2', 'MÃ LOẠI')->getStyle('A2')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('B2', 'TÊN LOẠI')->getStyle('B2')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('C2', 'MÃ VI PHẠM')->getStyle('C2')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('D2', 'TÊN VI PHẠM')->getStyle('D2')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('E2', 'STT')->getStyle('E2')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('F2', 'MÔ TẢ')->getStyle('F2')->applyFromArray($BStyle);
        $this->db->select('
		    tbl_violation_group.*,
		    tbl_type_violation_group.code as code_type,
		    tbl_type_violation_group.name as name_type,
		');
        $this->db->join('tbl_type_violation_group', 'tbl_type_violation_group.id = tbl_violation_group.type');
        $this->db->where('tbl_violation_group.active', 1);
        $this->db->order_by('tbl_type_violation_group.id asc,tbl_violation_group.name asc');
        $ktr = $this->db->get('tbl_violation_group')->result();
        $stt = 1;
        $group = "";
        foreach ($ktr as $rom => $item) {
            if ($group != $item->name){
                $stt = 1;
            }
            $group = $item->name;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . ($rom + 3), $item->code_type)->getStyle('A' . ($rom + 3))->applyFromArray($BStyle1);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . ($rom + 3), $item->name_type)->getStyle('B' . ($rom + 3))->applyFromArray($BStyle1);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . ($rom + 3), $item->code)->getStyle('C' . ($rom + 3))->applyFromArray($BStyle1);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . ($rom + 3), $item->name)->getStyle('D' . ($rom + 3))->applyFromArray($BStyle1);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . ($rom + 3), $stt)->getStyle('E' . ($rom + 3))->applyFromArray($BStyleCenter);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . ($rom + 3), $item->detail)->getStyle('F' . ($rom + 3))->applyFromArray($BStyle1);
            $stt++;
        }
        // $objPHPExcel->getActiveSheet()->freezePane('A4');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="ds_nhom_vi_pham.xls"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        exit();
    }

    public function getViolationGroup()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tbl_type_violation_group.code as code_type',
            'tbl_type_violation_group.name as name_type',
            'tbl_violation_group.code as code',
            'tbl_violation_group.name as name',
            '"" as stt',
            'tbl_violation_group.detail as detail',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_violation_group';
        $where        = [
            'AND tbl_violation_group.active = 1'
        ];
        $filter = [];
        $type_cost = $this->input->post('type_cost');
        if (!empty($type_cost)) {
            $where[]        = 'AND tbl_violation_group.type = ' . $type_cost;
        }
        $join = [
            'INNER JOIN tbl_type_violation_group ON tbl_type_violation_group.id = tbl_violation_group.type',
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbl_violation_group.id','tbl_violation_group.type'], 'ORDER BY tbl_type_violation_group.id asc,tbl_violation_group.name asc', []);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $group = "";
        $stt = 1;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $violation_group_id = $aRow['id'];
            $row = array();
            if ($group != $aRow['name']){
                $stt = 1;
            }
            $group = $aRow['name'];
            $row[] = '<div>' . $aRow['code_type'] . '</div>';
            $row[] = '<div>' . $aRow['name_type'] . '</div>';
            $row[] = $aRow['code'];
            $row[] = $aRow['name'];
            $row[] = '<div class="text-center">'.$stt.'</div>';
            $row[] = $aRow['detail'];
            $html = '<div><a class="tnh-modal btn btn-default btn-icon" href="'.base_url('admin/violation_group/detail/'.$violation_group_id.'').'"><i class="fa fa-pencil"></i></a>';
            $html .= ' <button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                        <button href=\'' . base_url('admin/violation_group/delete_violation_group/'.$violation_group_id.'') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove"></i></button></div>';
            $stt++;
            $row[] = $html;
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }
}
