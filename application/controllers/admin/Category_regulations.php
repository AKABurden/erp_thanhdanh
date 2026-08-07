<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Category_regulations extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->preViewRoleLevel = true;
        $this->preAddRoleLevel = true;
        $this->preEditRoleLevel= true;
        $this->preDeleteRoleLevel = true;

        $this->preViewScoreMap = true;
        $this->preAddScoreMap = true;
        $this->preEditScoreMap = true;
        $this->preDeleteScoreMap = true;

        $this->preViewSalaryGrade = true;
        $this->preAddSalaryGrade = true;
        $this->preEditSalaryGrade = true;
        $this->preDeleteSalaryGrade = true;

        $this->preViewQuestionBank = true;
        $this->preAddQuestionBank = true;
        $this->preEditQuestionBank = true;
        $this->preDeleteQuestionBank = true;
    }

    public function role_level()
    {
        if (!$this->preViewRoleLevel) {
            access_denied('role_level');
        }
        $data['title'] = _l('Danh mục cấp bậc vai trò');
        $this->load->view('admin/category_regulations/role_level', $data);
    }

    public function getRoleLevel()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tbl_role_level.id as id',
            'tbl_role_level.name as name',
            'tbl_role_level_hiring_standards.min_degree as min_degree',
            'tbl_role_level_hiring_standards.min_english as min_english',
            'tbl_role_level_hiring_standards.min_chinese as min_chinese',
            'tbl_role_level_hiring_standards.min_years_exp as min_years_exp',
            'tbl_role_level_hiring_standards.min_it_skill as min_it_skill',
            'tbl_role_level_hiring_standards.pass_gtcl as pass_gtcl',
            'tbl_role_level_hiring_standards.pass_tc as pass_tc',
            'tbl_role_level_hiring_standards.pass_total as pass_total',
            'tbl_role_level_hiring_standards.w_gtcl as w_gtcl',
            'tbl_role_level_hiring_standards.w_tc as w_tc',
            'tbl_role_level_hiring_standards.w_cm as w_cm',
            'tbl_role_level_hiring_standards.w_sk as w_sk',
            'tbl_role_level_hiring_standards.w_td as w_td',
            'tbl_role_level_hiring_standards.is_ceo_required as is_ceo_required',
            'tbl_role_level_hiring_standards.ceo_pass_score as ceo_pass_score',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_role_level';
        $where = [

        ];
        $filter = [];
        $join = [
            'INNER JOIN tbl_role_level_hiring_standards ON tbl_role_level_hiring_standards.role_level_id = tbl_role_level.id',
        ];

        if (!empty($role_id_search)){
            $where[] = 'AND tbl_job_detail.role_id = '.$role_id_search.'';
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">'.$aRow['name'].'</a></div>';
            $row[] = '<div class="text-center">'.$aRow['min_degree'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['min_english'].'</div>';
            $row[] = '<div class="text-center">'.($aRow['min_chinese']).'</div>';
            $row[] = '<div class="text-center">'.($aRow['min_years_exp']).'</div>';
            $row[] = '<div class="text-center">'.$aRow['min_it_skill'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['pass_gtcl'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['pass_tc'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['pass_total'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['w_gtcl'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['w_tc'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['w_cm'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['w_sk'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['w_td'].'</div>';
            $htmlCEOR = '';
            if ($aRow['is_ceo_required'] == 1){
                $htmlCEOR = '<div  class="text-center">Có</div>';
            }else{
                $htmlCEOR = '<div class="text-center">Không</div>';
            }
            $row[] = '<div class="text-center">'.$htmlCEOR.'</div>';
            $row[] = '<div class="text-center">'.$aRow['ceo_pass_score'].'</div>';

            $edit = '<a class="tnh-modal" href="' . base_url('admin/category_regulations/detail_role_level/' . $aRow['id'].'') . '"><i class="fa fa-edit width-icon-actions"></i> ' . lang('Chỉnh sửa') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/category_regulations/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail_role_level($id = 0)
    {
        if ($this->input->post()){
            $this->form_validation->set_rules('code', lang("Mã cấp bậc vai trò"), 'required');
            $this->form_validation->set_rules('name', lang("Tên cấp bậc vai trò"), 'required');
            if ($this->form_validation->run() == true) {
                $code = $this->input->post('code');
                $name = $this->input->post('name');
                $min_degree = number_unformat($this->input->post('min_degree') ?? 0);
                $min_english = number_unformat($this->input->post('min_english') ?? 0);
                $min_chinese = number_unformat($this->input->post('min_chinese') ?? 0);
                $min_years_exp = number_unformat($this->input->post('min_years_exp') ?? 0);
                $min_it_skill = number_unformat($this->input->post('min_it_skill') ?? 0);
                $pass_gtcl = number_unformat($this->input->post('pass_gtcl') ?? 0);
                $pass_tc = number_unformat($this->input->post('pass_tc') ?? 0);
                $pass_total = number_unformat($this->input->post('pass_total') ?? 0);
                $w_gtcl = number_unformat($this->input->post('w_gtcl') ?? 0);
                $w_tc = number_unformat($this->input->post('w_tc') ?? 0);
                $w_cm = number_unformat($this->input->post('w_cm') ?? 0);
                $w_sk = number_unformat($this->input->post('w_sk') ?? 0);
                $w_td = number_unformat($this->input->post('w_td') ?? 0);
                $is_ceo_required = $this->input->post('is_ceo_required') ?? 0;
                $ceo_pass_score = number_unformat($this->input->post('ceo_pass_score') ?? 0);

                $this->db->where('code',$code);
                $this->db->from('tbl_role_level');
                if (!empty($id)){
                    $this->db->where('id !=', $id);
                }
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)){
                    $data['result'] = false;
                    $data['message'] = lang('Mã cấp bậc vai trò đã tồn tại');
                    echo json_encode($data);die();
                }

                $option = [
                    'code' => $code,
                    'name' => $name,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s')
                ];

                if(empty($id)){
                    $this->db->insert('tbl_role_level',$option);
                    $role_level_id = $this->db->insert_id();
                    if (!empty($role_level_id)){
                        $this->db->insert('tbl_role_level_hiring_standards',[
                            'min_degree' => $min_degree,
                            'min_english' => $min_english,
                            'min_chinese' => $min_chinese,
                            'min_years_exp' => $min_years_exp,
                            'min_it_skill' => $min_it_skill,
                            'pass_gtcl' => $pass_gtcl,
                            'pass_tc' => $pass_tc,
                            'pass_total' => $pass_total,
                            'w_gtcl' => $w_gtcl,
                            'w_tc' => $w_tc,
                            'w_cm' => $w_cm,
                            'w_sk' => $w_sk,
                            'w_td' => $w_td,
                            'is_ceo_required' => $is_ceo_required,
                            'ceo_pass_score' => $ceo_pass_score,
                            'role_level_id' => $role_level_id,
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thêm thất bị');
                    }
                    echo json_encode($data);die();
                } else {
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_role_level',$option);
                    $job_detail_id = $id;
                    if ($success){
                        $this->db->where('role_level_id',$id);
                        $this->db->delete('tbl_role_level_hiring_standards');

                        $this->db->insert('tbl_role_level_hiring_standards',[
                            'min_degree' => $min_degree,
                            'min_english' => $min_english,
                            'min_chinese' => $min_chinese,
                            'min_years_exp' => $min_years_exp,
                            'min_it_skill' => $min_it_skill,
                            'pass_gtcl' => $pass_gtcl,
                            'pass_tc' => $pass_tc,
                            'pass_total' => $pass_total,
                            'w_gtcl' => $w_gtcl,
                            'w_tc' => $w_tc,
                            'w_cm' => $w_cm,
                            'w_sk' => $w_sk,
                            'w_td' => $w_td,
                            'is_ceo_required' => $is_ceo_required,
                            'ceo_pass_score' => $ceo_pass_score,
                            'role_level_id' => $job_detail_id,
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Chỉnh sửa thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Chỉnh sửa thất bại');
                    }

                    echo json_encode($data);die();
                }

            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);die();
        }
        if(empty($id)){
            if (!$this->preAddRoleLevel){
                accessDenied($js = true);
            }
            $title = lang('Thêm mới cấp bậc vai trò');
        } else {
            if (!$this->preEditRoleLevel){
                accessDenied($js = true);
            }
            $title = lang('Chỉnh sửa cấp bậc vai trò');

            $this->db->select('tbl_role_level.*,tbl_role_level_hiring_standards.*,tbl_role_level_hiring_standards.id as role_level_hiring_standards');
            $this->db->from('tbl_role_level');
            $this->db->join('tbl_role_level_hiring_standards','tbl_role_level_hiring_standards.role_level_id = tbl_role_level.id');
            $this->db->where('tbl_role_level.id',$id);
            $dtData = $this->db->get()->row_array();
        }
        $data['title'] = $title;
        $data['dtData'] = $dtData ?? null;
        $data['id'] = $id;
        $this->load->view('admin/category_regulations/detail_role_level',$data);
    }

    public function delete($id){
        if (!$this->preEditRoleLevel){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->from('tbl_role_level');
        $this->db->where('tbl_role_level.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_role_level');
        if ($success){

            $this->db->where('role_level_id',$id);
            $this->db->delete('tbl_role_level_hiring_standards');

            insertActivityLog([
                'type_parent_obj' => 'role_level',
                'table_obj' => 'tbl_role_level',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa cấp bậc vai trò') . ' [' . $dtData['code'] . ']',
                'actions' => 'delete'
            ]);
            $data['result'] = 1;
            $data['message'] = lang('Xóa thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Xóa thất bại');
        }
        echo json_encode($data);
    }

    public function exportExcelRoleLevel()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            $room_search = $this->input->post('room_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $staff_id = get_staff_user_id();

            $this->db->select('
                tbl_role_level.id as id,
                tbl_role_level.name as name,
                tbl_role_level_hiring_standards.min_degree as min_degree,
                tbl_role_level_hiring_standards.min_english as min_english,
                tbl_role_level_hiring_standards.min_chinese as min_chinese,
                tbl_role_level_hiring_standards.min_years_exp as min_years_exp,
                tbl_role_level_hiring_standards.min_it_skill as min_it_skill,
                tbl_role_level_hiring_standards.pass_gtcl as pass_gtcl,
                tbl_role_level_hiring_standards.pass_tc as pass_tc,
                tbl_role_level_hiring_standards.pass_total as pass_total,
                tbl_role_level_hiring_standards.w_gtcl as w_gtcl,
                tbl_role_level_hiring_standards.w_tc as w_tc,
                tbl_role_level_hiring_standards.w_cm as w_cm,
                tbl_role_level_hiring_standards.w_sk as w_sk,
                tbl_role_level_hiring_standards.w_td as w_td,
                tbl_role_level_hiring_standards.is_ceo_required as is_ceo_required,
                tbl_role_level_hiring_standards.ceo_pass_score as ceo_pass_score
            ');
            $this->db->from('tbl_role_level');
            $this->db->join('tbl_role_level_hiring_standards','tbl_role_level_hiring_standards.role_level_id = tbl_role_level.id','inner');

            $this->db->order_by('tbl_role_level.id desc');
            $dtData = $this->db->get()->result_array();


            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $decimals_money = get_option('decimals_money');
            $decimals_number = get_option('decimals_number');
            $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);

            insertCompanyInfo($objPHPExcel, 'C1:L2', 'A1');

            $objPHPExcel->getActiveSheet()->setCellValue('A5',
                ('DANH MỤC CẤP BẬC VAI TRÒ'))->getStyle("A5")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A5:Q5');
            $sttRow = 2 + 4;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Tên cấp bậc')->getStyle("B$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Bằng cấp tối thiểu');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Tiếng anh tối thiểu');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Tiếng trung tối thiểu')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Số năm kinh nghiệm tối thiểu')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Kỹ năng IT')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Điểm sàn giá trị cốt lõi')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Điểm sàn kỹ năng tổ chức')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Điểm tổng tối thiếu (Đạt)')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Trọng số giá trị cốt lõi')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Trọng số kỹ năng tuân thủ')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Trọng số chuyên môn')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Trọng số kỹ năng mềm')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Trọng số tư duy')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'CEO duyệt')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Điểm tối thiểu CEO yêu cầu')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:Q$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '92D050'),
                ),
            ]);
            $this->load->library('ciqrcode');
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['name']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['min_degree']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['min_english'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $value['min_chinese'])->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['min_years_exp'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin",$value['min_it_skill'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",$value['pass_gtcl'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin",$value['pass_tc'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['pass_total'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['w_gtcl'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['w_tc'] )->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['w_cm'] )->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['w_sk'] )->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $value['w_td'] )->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    if ($value['is_ceo_required'] == 1){
                        $htmlCeo = 'Có';
                    } else {
                        $htmlCeo = 'Không';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $htmlCeo )->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", $value['ceo_pass_score'] )->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:Q$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('danh_muc_cap_bac_vai_tro') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(30);
            ob_start();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="$filename"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();
            $response = array(
                'result' => 1,
                'filename' => $filename,
                'message' => lang('success'),
                'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
            );
            die(json_encode($response));
        }
    }

    public function importRoleLevel()
    {
        $data = [];
        if (!empty($_FILES)){
            ini_set('max_execution_time', 800);
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->helper('security');
            $count = 0;
            $errors = '';
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

                $allSheetName = $objPHPExcel->getSheetNames();
                $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('Q');
                $arraydata = array();

                $fields = $this->input->post('fields');
                for ($row = 2; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                        $arraydata[$row - 2][$col] = $value;
                    }
                }
                $dataArray = [];
                $arrData = [];
                $statusMap = [
                    "Có" => 1,
                    "Không" => 0
                ];
                $count = 0;
                foreach ($arraydata as $key => $value) {

                    $code = (preg_replace('/\s+/', ' ', trim($value[1])));
                    $min_degree = (preg_replace('/\s+/', ' ', trim($value[2])));
                    $min_english = (preg_replace('/\s+/', ' ', trim($value[3])));
                    $min_chinese = (preg_replace('/\s+/', ' ', trim($value[4])));
                    $min_years_exp = (preg_replace('/\s+/', ' ', trim($value[5])));
                    $min_it_skill = (preg_replace('/\s+/', ' ', trim($value[6])));
                    $pass_gtcl = (preg_replace('/\s+/', ' ', trim($value[7])));
                    $pass_tc = (preg_replace('/\s+/', ' ', trim($value[8])));
                    $pass_total = (preg_replace('/\s+/', ' ', trim($value[9])));
                    $w_gtcl = (preg_replace('/\s+/', ' ', trim($value[10])));
                    $w_tc = (preg_replace('/\s+/', ' ', trim($value[11])));
                    $w_cm = (preg_replace('/\s+/', ' ', trim($value[12])));
                    $w_sk = (preg_replace('/\s+/', ' ', trim($value[13])));
                    $w_td = (preg_replace('/\s+/', ' ', trim($value[14])));
                    $is_ceo_required = (preg_replace('/\s+/', ' ', trim($value[15])));
                    $ceo_pass_score = (preg_replace('/\s+/', ' ', trim($value[16])));



                    $is_ceo_required = trim($is_ceo_required); // loại bỏ khoảng trắng đầu/cuối

                    $valueStatus = 1;
                    if (isset($statusMap[$is_ceo_required])) {
                        $valueStatus = $statusMap[$is_ceo_required];
                    }
                    $this->db->where('code',$code);
                    $this->db->from('tbl_role_level');
                    $checkExists = $this->db->get()->row_array();

                    if (empty($checkExists)) {
                        $option = [
                            'code' => $code,
                            'name' => $code,
                            'created_by' => get_staff_user_id(),
                            'date_created' => date('Y-m-d H:i:s')
                        ];
                        $this->db->insert('tbl_role_level',$option);
                        $role_level_id = $this->db->insert_id();
                    } else {
                        $role_level_id = $checkExists['id'];
                    }
                    if ($role_level_id){
                        $this->db->where('role_level_id',$role_level_id);
                        $this->db->delete('tbl_role_level_hiring_standards');

                        $this->db->insert('tbl_role_level_hiring_standards',[
                            'min_degree' => $min_degree,
                            'min_english' => $min_english,
                            'min_chinese' => $min_chinese,
                            'min_years_exp' => $min_years_exp,
                            'min_it_skill' => $min_it_skill,
                            'pass_gtcl' => $pass_gtcl,
                            'pass_tc' => $pass_tc,
                            'pass_total' => $pass_total,
                            'w_gtcl' => $w_gtcl,
                            'w_tc' => $w_tc,
                            'w_cm' => $w_cm,
                            'w_sk' => $w_sk,
                            'w_td' => $w_td,
                            'is_ceo_required' => $valueStatus,
                            'ceo_pass_score' => $ceo_pass_score,
                            'role_level_id' => $role_level_id,
                        ]);
                        $count ++;
                    }
                }
                echo json_encode(
                    [
                        'success' => true,
                        'errors' => $errors,
                        'alert_type' => 'success',
                        'message' => 'Thêm mới thành công ' . $count . ' cấp bậc vai trò',
                    ]
                );
                die();

            }
            echo json_encode([
                'success' => true,
                'errors' => $errors,
                'alert_type' => 'success',
                'message' => 'Import thành công ' . $count . ' dòng',
            ]);
            die();
        }
        $data['title'] = _l('Import danh mục cấp bậc vai trò');
        $this->load->view('admin/category_regulations/import_role_level', $data);
    }

    public function score_map()
    {
        if (!$this->preAddScoreMap) {
            access_denied('score_map');
        }
        $data['title'] = _l('Thang điểm cơ bản');
        $this->load->view('admin/category_regulations/score_map', $data);
    }

    public function getScoreMap()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tb_detail = "(
            SELECT 
                tbl_score_map_detail.score_map_id,
                GROUP_CONCAT(tbl_score_map_detail.title) as title
            FROM tbl_score_map_detail
            GROUP BY tbl_score_map_detail.score_map_id    
        ) tb_detail";

        $aColumns = [
            'tbl_score_map.id as id',
            'tbl_score_map.name as name',
            'tbl_score_map.point as point',
            'tb_detail.title as title',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_score_map';
        $where = [

        ];
        $filter = [];
        $join = [
            'LEFT JOIN '.$tb_detail.' ON tb_detail.score_map_id = tbl_score_map.id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">'.$aRow['name'].'</a></div>';
            $row[] = '<div class="text-center" style="width: 100px">'.$aRow['point'].' điểm</div>';
            $row[] = '<div class="text-center">'.$aRow['title'].'</div>';

            $edit = '<a class="tnh-modal" href="' . base_url('admin/category_regulations/detail_score_map/' . $aRow['id'].'') . '"><i class="fa fa-edit width-icon-actions"></i> ' . lang('Chỉnh sửa') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/category_regulations/delete_score_map/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail_score_map($id = 0)
    {
        if ($this->input->post()){
            $this->form_validation->set_rules('name', lang("Tên thang điểm"), 'required');
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $point = number_unformat($this->input->post('point') ?? 0);

                $this->db->where('name',$name);
                $this->db->from('tbl_score_map');
                if (!empty($id)){
                    $this->db->where('id !=', $id);
                }
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)){
                    $data['result'] = false;
                    $data['message'] = lang('Tên thang điểm đã tồn tại');
                    echo json_encode($data);die();
                }

                $dataPost = $this->input->post();
                $counterOther1 = $this->input->post('counterOther1') ?? [];
                $arrOther1 = [];
                if (!empty($counterOther1)){
                    foreach ($counterOther1 as $key => $value){
                        $title = $dataPost['title'][$key] ?? null;
                        $arrOther1[] = [
                            'title' => $title,
                        ];
                    }
                }
                $option = [
                    'point' => $point,
                    'name' => $name,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s')
                ];

                if(empty($id)){
                    $this->db->insert('tbl_score_map',$option);
                    $score_map_id = $this->db->insert_id();
                    if (!empty($score_map_id)){
                        if (!empty($arrOther1)){
                            foreach ($arrOther1 as $key => $value){
                                $value['score_map_id'] = $score_map_id;
                                $this->db->insert('tbl_score_map_detail',$value);
                            }
                        }
                        $data['result'] = 1;
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thêm thất bị');
                    }
                    echo json_encode($data);die();
                } else {
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_score_map',$option);
                    $score_map_id = $id;
                    if ($success){
                        $this->db->where('score_map_id',$id);
                        $this->db->delete('tbl_score_map_detail');

                        if (!empty($arrOther1)){
                            foreach ($arrOther1 as $key => $value){
                                $value['score_map_id'] = $score_map_id;
                                $this->db->insert('tbl_score_map_detail',$value);
                            }
                        }
                        $data['result'] = 1;
                        $data['message'] = lang('Chỉnh sửa thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Chỉnh sửa thất bại');
                    }

                    echo json_encode($data);die();
                }

            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);die();
        }
        if(empty($id)){
            if (!$this->preAddScoreMap){
                accessDenied($js = true);
            }
            $title = lang('Thêm mới thang điểm');
        } else {
            if (!$this->preEditScoreMap){
                accessDenied($js = true);
            }
            $title = lang('Chỉnh sửa thang điểm');

            $this->db->select('tbl_score_map.*');
            $this->db->from('tbl_score_map');
            $this->db->where('tbl_score_map.id',$id);
            $dtData = $this->db->get()->row_array();
        }
        $data['title'] = $title;
        $data['dtData'] = $dtData ?? null;
        $data['id'] = $id;
        $this->load->view('admin/category_regulations/detail_score_map',$data);
    }

    public function delete_score_map($id){
        if (!$this->preDeleteScoreMap){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->from('tbl_score_map');
        $this->db->where('tbl_score_map.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_score_map');
        if ($success){

            $this->db->where('score_map_id',$id);
            $this->db->delete('tbl_score_map_detail');

            insertActivityLog([
                'type_parent_obj' => 'score_map',
                'table_obj' => 'tbl_score_map',
                'id_obj' => $id,
                'name_obj' => $dtData['name'],
                'content' => lang('Xóa thang điểm') . ' [' . $dtData['name'] . ']',
                'actions' => 'delete'
            ]);
            $data['result'] = 1;
            $data['message'] = lang('Xóa thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Xóa thất bại');
        }
        echo json_encode($data);
    }

    public function exportExcelScoreMap()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            $room_search = $this->input->post('room_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $staff_id = get_staff_user_id();

            $tb_detail = "(
                SELECT 
                    tbl_score_map_detail.score_map_id,
                    GROUP_CONCAT(tbl_score_map_detail.title) as title
                FROM tbl_score_map_detail
                GROUP BY tbl_score_map_detail.score_map_id    
            ) tb_detail";

            $this->db->select('
                tbl_score_map.id as id,
                tbl_score_map.name as name,
                tbl_score_map.point as point,
                tb_detail.title as title
            ');
            $this->db->from('tbl_score_map');
            $this->db->join($tb_detail,'tb_detail.score_map_id = tbl_score_map.id','left');

            $this->db->order_by('tbl_score_map.id desc');
            $dtData = $this->db->get()->result_array();


            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $decimals_money = get_option('decimals_money');
            $decimals_number = get_option('decimals_number');
            $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);

            insertCompanyInfo($objPHPExcel, 'C1:D2', 'A1');

            $objPHPExcel->getActiveSheet()->setCellValue('A5',
                ('DANH MỤC THANG ĐIỂM'))->getStyle("A5")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A5:D5');
            $sttRow = 2 + 4;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Tên thang điểm')->getStyle("B$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Số điểm');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Tiêu chí');
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:D$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '92D050'),
                ),
            ]);
            $this->load->library('ciqrcode');
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['name']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['point']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['title'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:D$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('danh_muc_thang_diem') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(30);
            ob_start();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="$filename"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();
            $response = array(
                'result' => 1,
                'filename' => $filename,
                'message' => lang('success'),
                'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
            );
            die(json_encode($response));
        }
    }

    public function importScoreMap()
    {
        $data = [];
        if (!empty($_FILES)){
            ini_set('max_execution_time', 800);
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->helper('security');
            $count = 0;
            $errors = '';
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

                $allSheetName = $objPHPExcel->getSheetNames();
                $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('D');
                $arraydata = array();

                $fields = $this->input->post('fields');
                for ($row = 2; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                        $arraydata[$row - 2][$col] = $value;
                    }
                }
                $dataArray = [];
                $arrData = [];
                $statusMap = [
                    "Có" => 1,
                    "Không" => 0
                ];
                $count = 0;
                foreach ($arraydata as $key => $value) {

                    $name = (preg_replace('/\s+/', ' ', trim($value[1])));
                    $point = (preg_replace('/\s+/', ' ', trim($value[2])));
                    $title = (preg_replace('/\s+/', ' ', trim($value[3])));

                    $this->db->where('name',$name);
                    $this->db->from('tbl_score_map');
                    $checkExists = $this->db->get()->row_array();

                    $arrOther1 = [];
                    if (!empty($title)){
                        $title = explode(',',$title);
                        if (!empty($title)){
                            foreach ($title as $key => $value){
                                $arrOther1[] = [
                                    'title' => $value,
                                ];
                            }
                        }
                    }
                    if (empty($checkExists)) {
                        $option = [
                            'name' => $name,
                            'point' => $point,
                            'created_by' => get_staff_user_id(),
                            'date_created' => date('Y-m-d H:i:s')
                        ];
                        $this->db->insert('tbl_score_map',$option);
                        $score_map_id = $this->db->insert_id();
                    } else {
                        $score_map_id = $checkExists['id'];
                    }
                    if ($score_map_id){
                        $this->db->where('score_map_id',$score_map_id);
                        $this->db->delete('tbl_score_map_detail');

                        if (!empty($arrOther1)){
                            foreach ($arrOther1 as $key => $value){
                                $value['score_map_id'] = $score_map_id;
                                $this->db->insert('tbl_score_map_detail',$value);
                            }
                        }

                        $count ++;
                    }
                }
                echo json_encode(
                    [
                        'success' => true,
                        'errors' => $errors,
                        'alert_type' => 'success',
                        'message' => 'Thêm mới/ cập nhập thành công ' . $count . ' thang điểm',
                    ]
                );
                die();

            }
            echo json_encode([
                'success' => true,
                'errors' => $errors,
                'alert_type' => 'success',
                'message' => 'Import thành công ' . $count . ' dòng',
            ]);
            die();
        }
        $data['title'] = _l('Import danh mục cấp bậc vai trò');
        $this->load->view('admin/category_regulations/import_score_map', $data);
    }

    public function salary_grade()
    {
        if (!$this->preViewSalaryGrade) {
            access_denied('salary_grade');
        }
        $data['title'] = _l('Danh mục ngạch lương');
        $this->load->view('admin/category_regulations/salary_grade', $data);
    }

    public function getSalaryGrade()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tbl_salary_grade.id as id',
            'tbl_salary_grade.name as name',
            'tbl_salary_grade_hiring_standards.pass_total_score as pass_total_score',
            'tbl_salary_grade_hiring_standards.p1 as p1',
            'tbl_salary_grade_hiring_standards.p2 as p2',
            'tbl_salary_grade_hiring_standards.p3 as p3',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_salary_grade';
        $where = [

        ];
        $filter = [];
        $join = [
            'INNER JOIN tbl_salary_grade_hiring_standards ON tbl_salary_grade_hiring_standards.salary_grade_id = tbl_salary_grade.id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">'.$aRow['name'].'</a></div>';
            $row[] = '<div class="text-center">'.$aRow['pass_total_score'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['p1'].' %</div>';
            $row[] = '<div class="text-center">'.($aRow['p2']).' %</div>';
            $row[] = '<div class="text-center">'.($aRow['p3']).' %</div>';

            $edit = '<a class="tnh-modal" href="' . base_url('admin/category_regulations/detail_salary_grade/' . $aRow['id'].'') . '"><i class="fa fa-edit width-icon-actions"></i> ' . lang('Chỉnh sửa') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/category_regulations/delete_salary_grade/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail_salary_grade($id = 0)
    {
        if ($this->input->post()){
            $this->form_validation->set_rules('name', lang("Tên ngạch lương"), 'required');
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $pass_total_score = number_unformat($this->input->post('pass_total_score') ?? 0);
                $p1 = number_unformat($this->input->post('p1') ?? 0);
                $p2 = number_unformat($this->input->post('p2') ?? 0);
                $p3 = number_unformat($this->input->post('p3') ?? 0);

                $this->db->where('name',$name);
                $this->db->from('tbl_salary_grade');
                if (!empty($id)){
                    $this->db->where('id !=', $id);
                }
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)){
                    $data['result'] = false;
                    $data['message'] = lang('Tên ngạch lương đã tồn tại');
                    echo json_encode($data);die();
                }

                $option = [
                    'name' => $name,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s')
                ];

                if(empty($id)){
                    $this->db->insert('tbl_salary_grade',$option);
                    $salary_grade_id = $this->db->insert_id();
                    if (!empty($salary_grade_id)){
                        $this->db->insert('tbl_salary_grade_hiring_standards',[
                            'pass_total_score' => $pass_total_score,
                            'p1' => $p1,
                            'p2' => $p2,
                            'p3' => $p3,
                            'salary_grade_id' => $salary_grade_id,
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thêm thất bị');
                    }
                    echo json_encode($data);die();
                } else {
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_salary_grade',$option);
                    $salary_grade_id = $id;
                    if ($success){
                        $this->db->where('salary_grade_id',$id);
                        $this->db->delete('tbl_salary_grade_hiring_standards');

                        $this->db->insert('tbl_salary_grade_hiring_standards',[
                            'pass_total_score' => $pass_total_score,
                            'p1' => $p1,
                            'p2' => $p2,
                            'p3' => $p3,
                            'salary_grade_id' => $salary_grade_id,
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Chỉnh sửa thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Chỉnh sửa thất bại');
                    }

                    echo json_encode($data);die();
                }

            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);die();
        }
        if(empty($id)){
            if (!$this->preAddSalaryGrade){
                accessDenied($js = true);
            }
            $title = lang('Thêm mới danh mục ngạch lương');
        } else {
            if (!$this->preEditSalaryGrade){
                accessDenied($js = true);
            }
            $title = lang('Chỉnh sửa danh mục ngạch lương');

            $this->db->select('tbl_salary_grade.*,tbl_salary_grade_hiring_standards.*,tbl_salary_grade_hiring_standards.id as salary_grade_hiring_standard_id');
            $this->db->from('tbl_salary_grade');
            $this->db->join('tbl_salary_grade_hiring_standards','tbl_salary_grade_hiring_standards.salary_grade_id = tbl_salary_grade.id');
            $this->db->where('tbl_salary_grade.id',$id);
            $dtData = $this->db->get()->row_array();
        }
        $data['title'] = $title;
        $data['dtData'] = $dtData ?? null;
        $data['id'] = $id;
        $this->load->view('admin/category_regulations/detail_salary_grade',$data);
    }

    public function delete_salary_grade($id){
        if (!$this->preDeleteSalaryGrade){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->from('tbl_salary_grade');
        $this->db->where('tbl_salary_grade.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_salary_grade');
        if ($success){

            $this->db->where('salary_grade_id',$id);
            $this->db->delete('tbl_salary_grade_hiring_standards');

            insertActivityLog([
                'type_parent_obj' => 'salary_grade',
                'table_obj' => 'tbl_salary_grade',
                'id_obj' => $id,
                'name_obj' => $dtData['name'],
                'content' => lang('Xóa danh mục ngạch lương') . ' [' . $dtData['name'] . ']',
                'actions' => 'delete'
            ]);
            $data['result'] = 1;
            $data['message'] = lang('Xóa thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Xóa thất bại');
        }
        echo json_encode($data);
    }

    public function exportExcelSalaryGrade()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            $room_search = $this->input->post('room_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $staff_id = get_staff_user_id();

            $this->db->select('
                tbl_salary_grade.id as id,
                tbl_salary_grade.name as name,
                tbl_salary_grade_hiring_standards.pass_total_score as pass_total_score,
                tbl_salary_grade_hiring_standards.p1 as p1,
                tbl_salary_grade_hiring_standards.p2 as p2,
                tbl_salary_grade_hiring_standards.p3 as p3
            ');
            $this->db->from('tbl_salary_grade');
            $this->db->join('tbl_salary_grade_hiring_standards','tbl_salary_grade_hiring_standards.salary_grade_id = tbl_salary_grade.id','inner');

            $this->db->order_by('tbl_salary_grade.id desc');
            $dtData = $this->db->get()->result_array();


            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $decimals_money = get_option('decimals_money');
            $decimals_number = get_option('decimals_number');
            $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);

            insertCompanyInfo($objPHPExcel, 'C1:F2', 'A1');

            $objPHPExcel->getActiveSheet()->setCellValue('A5',
                ('DANH MỤC CẤP BẬC VAI TRÒ'))->getStyle("A5")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A5:F5');
            $sttRow = 2 + 4;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Tên ngạch lương')->getStyle("B$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Tổng điểm vượt qua');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Hưởng P1');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Hưởng P2')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Hưởng P3')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:F$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '92D050'),
                ),
            ]);
            $this->load->library('ciqrcode');
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['name']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['pass_total_score']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['p1'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $value['p2'])->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['p3'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:F$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('danh_muc_ngach_lương') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(30);
            ob_start();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="$filename"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();
            $response = array(
                'result' => 1,
                'filename' => $filename,
                'message' => lang('success'),
                'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
            );
            die(json_encode($response));
        }
    }

    public function importSalaryGrade()
    {
        $data = [];
        if (!empty($_FILES)){
            ini_set('max_execution_time', 800);
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->helper('security');
            $count = 0;
            $errors = '';
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

                $allSheetName = $objPHPExcel->getSheetNames();
                $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('F');
                $arraydata = array();

                $fields = $this->input->post('fields');
                for ($row = 2; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                        $arraydata[$row - 2][$col] = $value;
                    }
                }
                $dataArray = [];
                $arrData = [];
                $statusMap = [
                    "Có" => 1,
                    "Không" => 0
                ];
                $count = 0;
                foreach ($arraydata as $key => $value) {

                    $name = (preg_replace('/\s+/', ' ', trim($value[1])));
                    $pass_total_score = (preg_replace('/\s+/', ' ', trim($value[2])));
                    $p1 = (preg_replace('/\s+/', ' ', trim($value[3])));
                    $p2 = (preg_replace('/\s+/', ' ', trim($value[4])));
                    $p3 = (preg_replace('/\s+/', ' ', trim($value[5])));

                    $this->db->where('name',$name);
                    $this->db->from('tbl_salary_grade');
                    $checkExists = $this->db->get()->row_array();

                    if (empty($checkExists)) {
                        $option = [
                            'name' => $name,
                            'created_by' => get_staff_user_id(),
                            'date_created' => date('Y-m-d H:i:s')
                        ];
                        $this->db->insert('tbl_salary_grade',$option);
                        $salary_grade_id = $this->db->insert_id();
                    } else {
                        $salary_grade_id = $checkExists['id'];
                    }
                    if ($salary_grade_id){
                        $this->db->where('salary_grade_id',$salary_grade_id);
                        $this->db->delete('tbl_salary_grade_hiring_standards');

                        $this->db->insert('tbl_salary_grade_hiring_standards',[
                            'pass_total_score' => $pass_total_score,
                            'p1' => $p1,
                            'p2' => $p2,
                            'p3' => $p3,
                            'salary_grade_id' => $salary_grade_id,
                        ]);
                        $count ++;
                    }
                }
                echo json_encode(
                    [
                        'success' => true,
                        'errors' => $errors,
                        'alert_type' => 'success',
                        'message' => 'Thêm mới/ cập nhập thành công ' . $count . ' ngạch lương',
                    ]
                );
                die();

            }
            echo json_encode([
                'success' => true,
                'errors' => $errors,
                'alert_type' => 'success',
                'message' => 'Import thành công ' . $count . ' dòng',
            ]);
            die();
        }
        $data['title'] = _l('Import danh mục ngach lương');
        $this->load->view('admin/category_regulations/import_salary_grade', $data);
    }

    public function question_bank()
    {
        if (!$this->preAddQuestionBank) {
            access_denied('question_bank');
        }
        $data['title'] = _l('Ngân hàng câu hỏi');
        $this->load->view('admin/category_regulations/question_bank', $data);
    }

    public function getQuestionBank()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tb_criteria = "(
            SELECT 
                tbl_question_bank_criteria.question_bank_id,
                GROUP_CONCAT(tbl_question_bank_criteria.title) as title
            FROM tbl_question_bank_criteria
            GROUP BY tbl_question_bank_criteria.question_bank_id
        ) tb_criteria";

        $aColumns = [
            'tbl_question_bank.id as id',
            'tbl_question_bank.code as code',
            'tbl_question_bank.type as type',
            'tblroles.code_role as name_role',
            'tbl_role_level.name as name_role_level',
            'tbl_question_bank.question as question',
        ];
        foreach (getLevelAnswer() as $key => $value){
            $aColumns[] = '"'.$value['id'].'" as '.$value['id'].'';
        }
        $aColumns[] = 'tbl_question_bank.weight as weight';
        $aColumns[] = 'tbl_question_bank.point_max as point_max';
        $aColumns[] = 'tbl_question_bank.status as status';
        $aColumns[] = 'tbl_question_bank.affect_kpi as affect_kpi';
        $aColumns[] = 'tbl_question_bank.arise_report as arise_report';
        $aColumns[] = 'tbl_question_bank.version as version';
        $aColumns[] = '"" as actions';
        $sIndexColumn = 'id';
        $sTable = 'tbl_question_bank';
        $where = [

        ];
        $filter = [];
        $join = [
            'INNER JOIN tblroles ON tblroles.roleid = tbl_question_bank.role_id',
            'INNER JOIN tbl_role_level ON tbl_role_level.id = tbl_question_bank.role_level_id',
            'LEFT JOIN '.$tb_criteria.' ON tb_criteria.question_bank_id = tbl_question_bank.id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $arrId = array_column($rResult,'id');
        $dtAnswers = null;
        if (!empty($arrId)){
            $this->db->from('tbl_question_bank_answer');
            $this->db->where_in('tbl_question_bank_answer.question_bank_id',$arrId);
            $dtAnswers = $this->db->get()->result_array();
            if (!empty($dtAnswers)) {
                $dtAnswers = array_reduce($dtAnswers, function ($acc, $item) {
                    $acc[$item['question_bank_id']][$item['prefix']] = $item;
                    return $acc;
                });
            }
        }
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $answer = $dtAnswers[$aRow['id']] ?? null;
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">'.$aRow['code'].'</a></div>';
            $row[] = '<div class="text-center" style="width: 100px">'.$aRow['type'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['name_role'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['name_role_level'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['question'].'</div>';
            foreach (getLevelAnswer() as $k => $v){
                $row[] = '<div>'.($answer[$v['id']]['answer'] ?? '').'</div>';
            }
            $row[] = '<div class="text-center">'.$aRow['weight'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['point_max'].'</div>';
            $checked = '';
            if ($aRow['status'] == 1) {
                $checked = 'checked';
            }
            $_data = '<div class="onoffswitch">
                        <input type="checkbox" data-switch-url="' . admin_url() . 'category_regulations/changeStatusQuestionBank" name="onoffswitch_new" class="onoffswitch-checkbox" id="c_new' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $checked . '>
                        <label class="onoffswitch-label" for="c_new' . $aRow['id'] . '"></label>
                    </div>';
            $row[] = '<div class="text-center">'.$_data.'</div>';
            $affect_kpi = '';
            if ($aRow['affect_kpi'] == 1) {
                $affect_kpi = 'checked';
            }
            $_data = '<div class="onoffswitch">
                        <input type="checkbox" data-switch-url="' . admin_url() . 'category_regulations/changeAffectKpi" name="onoffswitch_new" class="onoffswitch-checkbox" id="c_affect_kpi' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $affect_kpi . '>
                        <label class="onoffswitch-label" for="c_affect_kpi' . $aRow['id'] . '"></label>
                    </div>';
            $row[] = '<div class="text-center">'.$_data.'</div>';
            $arise_report = '';
            if ($aRow['arise_report'] == 1) {
                $arise_report = 'checked';
            }
            $_data = '<div class="onoffswitch">
                        <input type="checkbox" data-switch-url="' . admin_url() . 'category_regulations/changeAriseReport" name="onoffswitch_new" class="onoffswitch-checkbox" id="c_arise_report' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $arise_report . '>
                        <label class="onoffswitch-label" for="c_arise_report' . $aRow['id'] . '"></label>
                    </div>';
            $row[] = '<div class="text-center">'.$_data.'</div>';
            $row[] = '<div class="text-left">'.$aRow['version'].'</div>';

            $edit = '<a class="tnh-modal" href="' . base_url('admin/category_regulations/detail_question_bank/' . $aRow['id'].'') . '"><i class="fa fa-edit width-icon-actions"></i> ' . lang('Chỉnh sửa') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/category_regulations/delete_question_bank/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail_question_bank($id = 0)
    {
        if ($this->input->post()){
            $this->form_validation->set_rules('code', lang("Mã câu hỏi"), 'required');
            $this->form_validation->set_rules('type', lang("Nhóm quy tắc"), 'required');
            $this->form_validation->set_rules('role_id', lang("Vị trí"), 'required');
            $this->form_validation->set_rules('role_level_id', lang("Cấp độ vai trò"), 'required');
            $this->form_validation->set_rules('version', lang("Version"), 'required');
            if ($this->form_validation->run() == true) {
                $code = $this->input->post('code');
                $type = $this->input->post('type');
                $role_id = $this->input->post('role_id');
                $role_level_id = $this->input->post('role_level_id');
                $version = $this->input->post('version');
                $question = $this->input->post('question');
                $weight = number_unformat($this->input->post('weight') ?? 0);
                $point_max = number_unformat($this->input->post('point_max') ?? 0);

                $this->db->where('code',$code);
                $this->db->from('tbl_question_bank');
                if (!empty($id)){
                    $this->db->where('id !=', $id);
                }
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)){
                    $data['result'] = false;
                    $data['message'] = lang('Mã câu hỏi đã tồn tại');
                    echo json_encode($data);die();
                }


                $dataPost = $this->input->post();
                $counterOther1 = $this->input->post('counterOther1') ?? [];
                $arrOther1 = [];
                if (!empty($counterOther1)){
                    foreach ($counterOther1 as $key => $value){
                        $title = $dataPost['title'][$key] ?? null;
                        $arrOther1[] = [
                            'title' => $title,
                        ];
                    }
                }
                $counterOther2 = $this->input->post('counterOther2') ?? [];
                $arrOther2 = [];
                if (!empty($counterOther2)){
                    foreach ($counterOther2 as $key => $value){
                        $answer = $dataPost['answer'][$value] ?? null;
                        $point = number_unformat($dataPost['point'][$value]) ?? 0;
                        $prefix = $dataPost['prefix'][$value] ?? null;
                        if (empty($answer)){
                            continue;
                        }
                        $arrOther2[] = [
                            'prefix' => $prefix,
                            'answer' => $answer,
                            'point' => $point,
                        ];
                    }
                }
                $option = [
                    'code' => $code,
                    'type' => $type,
                    'role_id' => $role_id,
                    'role_level_id' => $role_level_id,
                    'version' => $version,
                    'question' => $question,
                    'weight' => $weight,
                    'point_max' => $point_max,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s')
                ];

                if(empty($id)){
                    $this->db->insert('tbl_question_bank',$option);
                    $question_bank_id = $this->db->insert_id();
                    if (!empty($question_bank_id)){
                        if (!empty($arrOther1)){
                            foreach ($arrOther1 as $key => $value){
                                $value['question_bank_id'] = $question_bank_id;
                                $this->db->insert('tbl_question_bank_criteria',$value);
                            }
                        }
                        if (!empty($arrOther2)){
                            foreach ($arrOther2 as $key => $value){
                                $value['question_bank_id'] = $question_bank_id;
                                $this->db->insert('tbl_question_bank_answer',$value);
                            }
                        }
                        $data['result'] = 1;
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thêm thất bị');
                    }
                    echo json_encode($data);die();
                } else {
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_question_bank',$option);
                    $question_bank_id = $id;
                    if ($success){
                        $this->db->where('question_bank_id',$id);
                        $this->db->delete('tbl_question_bank_criteria');

                        $this->db->where('question_bank_id',$id);
                        $this->db->delete('tbl_question_bank_answer');

                        if (!empty($arrOther1)){
                            foreach ($arrOther1 as $key => $value){
                                $value['question_bank_id'] = $question_bank_id;
                                $this->db->insert('tbl_question_bank_criteria',$value);
                            }
                        }
                        if (!empty($arrOther2)){
                            foreach ($arrOther2 as $key => $value){
                                $value['question_bank_id'] = $question_bank_id;
                                $this->db->insert('tbl_question_bank_answer',$value);
                            }
                        }
                        $data['result'] = 1;
                        $data['message'] = lang('Chỉnh sửa thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Chỉnh sửa thất bại');
                    }

                    echo json_encode($data);die();
                }

            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);die();
        }
        if(empty($id)){
            if (!$this->preAddQuestionBank){
                accessDenied($js = true);
            }
            $title = lang('Thêm mới ngân hàng câu hỏi');
        } else {
            if (!$this->preEditQuestionBank){
                accessDenied($js = true);
            }
            $title = lang('Chỉnh sửa ngân hàng câu hỏi');

            $this->db->select('tbl_question_bank.*');
            $this->db->from('tbl_question_bank');
            $this->db->where('tbl_question_bank.id',$id);
            $dtData = $this->db->get()->row_array();
        }
        $data['dtType'] = getTypeQuestion();
        $data['title'] = $title;
        $data['dtData'] = $dtData ?? null;
        $data['id'] = $id;
        $this->load->view('admin/category_regulations/detail_question_bank',$data);
    }

    public function delete_question_bank($id){
        if (!$this->preDeleteQuestionBank){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->from('tbl_question_bank');
        $this->db->where('tbl_question_bank.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_question_bank');
        if ($success){

            $this->db->where('question_bank_id',$id);
            $this->db->delete('tbl_question_bank_criteria');

            $this->db->where('question_bank_id',$id);
            $this->db->delete('tbl_question_bank_answer');

            insertActivityLog([
                'type_parent_obj' => 'question_bank',
                'table_obj' => 'tbl_question_bank',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa ngân hàng câu hỏi') . ' [' . $dtData['code'] . ']',
                'actions' => 'delete'
            ]);
            $data['result'] = 1;
            $data['message'] = lang('Xóa thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Xóa thất bại');
        }
        echo json_encode($data);
    }

    public function exportExcelQuestionBank()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            $room_search = $this->input->post('room_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $staff_id = get_staff_user_id();

            $tb_detail = "(
                SELECT 
                    tbl_question_bank_criteria.question_bank_id,
                    GROUP_CONCAT(tbl_question_bank_criteria.title) as title
                FROM tbl_question_bank_criteria
                GROUP BY tbl_question_bank_criteria.question_bank_id    
            ) tb_detail";

            $this->db->select('
                tbl_question_bank.id as id,
                tbl_question_bank.code as code,
                tbl_question_bank.type as type,
                tblroles.code_role as name_role,
                tbl_role_level.name as name_role_level,
                tbl_question_bank.question as question,
                tbl_question_bank.weight as weight,
                tbl_question_bank.point_max as point_max,
                tbl_question_bank.status as status,
                tbl_question_bank.affect_kpi as affect_kpi,
                tbl_question_bank.arise_report as arise_report,
                tbl_question_bank.version as version,
            ');
            $this->db->from('tbl_question_bank');
            $this->db->join('tblroles','tblroles.roleid = tbl_question_bank.role_id','inner');
            $this->db->join('tbl_role_level','tbl_role_level.id = tbl_question_bank.role_level_id','inner');
            $this->db->join($tb_detail,'tb_detail.question_bank_id = tbl_question_bank.id','left');

            $this->db->order_by('tbl_question_bank.id desc');
            $dtData = $this->db->get()->result_array();
            $arrId = array_column($dtData,'id');
            $dtAnswers = null;
            if (!empty($arrId)){
                $this->db->from('tbl_question_bank_answer');
                $this->db->where_in('tbl_question_bank_answer.question_bank_id',$arrId);
                $dtAnswers = $this->db->get()->result_array();
                if (!empty($dtAnswers)) {
                    $dtAnswers = array_reduce($dtAnswers, function ($acc, $item) {
                        $acc[$item['question_bank_id']][$item['prefix']] = $item;
                        return $acc;
                    });
                }
            }

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $decimals_money = get_option('decimals_money');
            $decimals_number = get_option('decimals_number');
            $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);

            insertCompanyInfo($objPHPExcel, 'C1:Q2', 'A1');

            $objPHPExcel->getActiveSheet()->setCellValue('A5',
                ('NGÂN HÀNG CÂU HỎI'))->getStyle("A5")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A5:Q5');
            $sttRow = 2 + 4;
            $sttColum = 0;
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$sttColum].$sttRow.'', 'STT');
            $sttColum++;
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$sttColum].$sttRow.'', 'Mã câu hỏi')->getStyle("B$sttRow")->getAlignment()->setWrapText(true);
            $sttColum++;
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$sttColum].$sttRow.'', 'Nhóm quy tắc');
            $sttColum++;
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$sttColum].$sttRow.'', 'Mã vị trí');
            $sttColum++;
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$sttColum].$sttRow.'', 'Cấp độ vai trò');
            $sttColum++;
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$sttColum].$sttRow.'', 'Nội dung câu hỏi');
            foreach (getLevelAnswer() as $key => $value){
                $sttColum++;
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$sttColum].$sttRow.'', $value['name']);
            }
            $sttColum++;
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$sttColum].$sttRow.'', 'Trọng số');
            $sttColum++;
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$sttColum].$sttRow.'', 'Điểm tối đa');
            $sttColum++;
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$sttColum].$sttRow.'', 'Đang hoạt động');
            $sttColum++;
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$sttColum].$sttRow.'', 'Ảnh hưởng KPI');
            $sttColum++;
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$sttColum].$sttRow.'', 'Sinh BCKPH');
            $sttColum++;
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$sttColum].$sttRow.'', 'Phiên bản');
            $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[0]$sttRow:$cloumns_excel[$sttColum]$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '92D050'),
                ),
            ]);
            $this->load->library('ciqrcode');
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $colStt = 0;
                    $answer = $dtAnswers[$value['id']] ?? null;
                    $htmlStatus = $value['status'] == 1 ? 'Hoạt động' : 'Không hoạt động';
                    $htmlAffectKPI = $value['affect_kpi'] == 1 ? 'Có' : 'Không';
                    $htmlAriseReport = $value['arise_report'] == 1 ? 'Có' : 'Không';
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", (++$key));
                    $colStt ++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $value['code']);
                    $colStt ++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['type']));
                    $colStt ++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $value['name_role'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt ++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $value['name_role_level'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt ++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $value['question'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    foreach (getLevelAnswer() as $k => $v){
                        $colStt ++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($answer[$v['id']]['answer'] ?? ''))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    }
                    $colStt ++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $value['weight'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt ++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $value['point_max'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt ++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $htmlStatus)->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt ++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $htmlAffectKPI)->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt ++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $htmlAriseReport)->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt ++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $value['version'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:Q$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('ngan_hang_Cau_hoi') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            ob_start();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="$filename"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();
            $response = array(
                'result' => 1,
                'filename' => $filename,
                'message' => lang('success'),
                'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
            );
            die(json_encode($response));
        }
    }

    public function importQuestionBank()
    {
        $data = [];
        if (!empty($_FILES)){
            ini_set('max_execution_time', 800);
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->helper('security');
            $count = 0;
            $errors = '';
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

                $allSheetName = $objPHPExcel->getSheetNames();
                $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('Q');
                $arraydata = array();

                $fields = $this->input->post('fields');
                for ($row = 2; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                        $arraydata[$row - 2][$col] = $value;
                    }
                }
                $dataArray = [];
                $arrData = [];
                $statusMap = [
                    "Hoạt động" => 1,
                    "Không hoạt động" => 0
                ];
                $statusMapNew = [
                    "Có" => 1,
                    "Không" => 0
                ];
                $count = 0;
                foreach ($arraydata as $key => $value) {

                    $code = (preg_replace('/\s+/', ' ', trim($value[1])));
                    $type = (preg_replace('/\s+/', ' ', trim($value[2])));
                    $role = (preg_replace('/\s+/', ' ', trim($value[3])));
                    $role_level = (preg_replace('/\s+/', ' ', trim($value[4])));
                    $question = (preg_replace('/\s+/', ' ', trim($value[5])));
                    $answer_a = (preg_replace('/\s+/', ' ', trim($value[6])));
                    $answer_b = (preg_replace('/\s+/', ' ', trim($value[7])));
                    $answer_c = (preg_replace('/\s+/', ' ', trim($value[8])));
                    $answer_d = (preg_replace('/\s+/', ' ', trim($value[9])));
                    $answer_e = (preg_replace('/\s+/', ' ', trim($value[10])));
                    $weight = (preg_replace('/\s+/', ' ', trim($value[11])));
                    $point_max = (preg_replace('/\s+/', ' ', trim($value[12])));
                    $status = (preg_replace('/\s+/', ' ', trim($value[13])));
                    $affect_kpi = (preg_replace('/\s+/', ' ', trim($value[14])));
                    $arise_report = (preg_replace('/\s+/', ' ', trim($value[15])));
                    $version = (preg_replace('/\s+/', ' ', trim($value[16])));

                    $this->db->where('code',$code);
                    $this->db->from('tbl_question_bank');
                    $checkExists = $this->db->get()->row_array();

                    $arrOther2 = [];
                    if (!empty($answer_a)){
                        $answer_a = explode('|',$answer_a);
                        if (!empty($answer_a)){
                            $arrOther2[] = [
                                'prefix' => 'A',
                                'answer' => $answer_a[0],
                                'point' => $answer_a[1]
                            ];
                        }
                    }

                    if (!empty($answer_b)){
                        $answer_b = explode('|',$answer_b);
                        if (!empty($answer_b)){
                            $arrOther2[] = [
                                'prefix' => 'B',
                                'answer' => $answer_b[0],
                                'point' => $answer_b[1]
                            ];
                        }
                    }
                    if (!empty($answer_c)){
                        $answer_c = explode('|',$answer_c);
                        if (!empty($answer_c)){
                            $arrOther2[] = [
                                'prefix' => 'C',
                                'answer' => $answer_c[0],
                                'point' => $answer_c[1]
                            ];
                        }
                    }
                    if (!empty($answer_d)){
                        $answer_d = explode('|',$answer_d);
                        if (!empty($answer_d)){
                            $arrOther2[] = [
                                'prefix' => 'D',
                                'answer' => $answer_d[0],
                                'point' => $answer_d[1]
                            ];
                        }
                    }
                    if (!empty($answer_e)){
                        $answer_e = explode('|',$answer_e);
                        if (!empty($answer_e)){
                            $arrOther2[] = [
                                'prefix' => 'E',
                                'answer' => $answer_e[0],
                                'point' => $answer_e[1]
                            ];
                        }
                    }

                    $this->db->from('tblroles');
                    $this->db->where('code_role',$role);
                    $dtRole = $this->db->get()->row_array();
                    if (empty($dtRole)){
                        $errors .= lang('Mã '.$role.' vị trí không tồn tại');
                        continue;
                    }

                    $this->db->from('tbl_role_level');
                    $this->db->where('code',$role_level);
                    $dtRoleLevel = $this->db->get()->row_array();
                    if (empty($dtRoleLevel)){
                        $errors .= lang('Mã '.$role_level.' cấp độ vị trí không tồn tại');
                        continue;
                    }

                    $dtType = getTypeQuestion($type);
                    if (empty($dtType)){
                        $errors .= lang('Nhóm quy tắc '.$type.' không tồn tại');
                        continue;
                    }

                    $status = trim($status);

                    $valueStatus = 1;
                    if (isset($statusMap[$status])) {
                        $valueStatus = $statusMap[$status];
                    }

                    $affect_kpi = trim($affect_kpi);

                    $valueAffectKpi = 1;
                    if (isset($statusMapNew[$affect_kpi])) {
                        $valueAffectKpi = $statusMapNew[$affect_kpi];
                    }

                    $arise_report = trim($arise_report);

                    $valueAriseReport = 1;
                    if (isset($statusMapNew[$arise_report])) {
                        $valueAriseReport = $statusMapNew[$arise_report];
                    }

                    if (empty($checkExists)) {
                        $option = [
                            'code' => $code,
                            'type' => $type,
                            'role_id' => $dtRole['roleid'],
                            'role_level_id' => $dtRoleLevel['id'],
                            'version' => $version,
                            'question' => $question,
                            'weight' => $weight,
                            'point_max' => $point_max,
                            'status' => $valueStatus,
                            'affect_kpi' => $valueAffectKpi,
                            'arise_report' => $valueAriseReport,
                            'created_by' => get_staff_user_id(),
                            'date_created' => date('Y-m-d H:i:s')
                        ];
                        $this->db->insert('tbl_question_bank',$option);
                        $question_bank_id = $this->db->insert_id();
                    } else {
                        $option = [
                            'type' => $type,
                            'role_id' => $dtRole['roleid'],
                            'role_level_id' => $dtRoleLevel['id'],
                            'version' => $version,
                            'question' => $question,
                            'weight' => $weight,
                            'point_max' => $point_max,
                            'status' => $valueStatus,
                            'affect_kpi' => $valueAffectKpi,
                            'arise_report' => $valueAriseReport,
                        ];
                        $this->db->where('id',$checkExists['id']);
                        $this->db->update('tbl_question_bank',$option);
                        $question_bank_id = $checkExists['id'];
                    }
                    if ($question_bank_id){
                        $this->db->where('question_bank_id',$question_bank_id);
                        $this->db->delete('tbl_question_bank_criteria');

                        if (!empty($arrOther1)){
                            foreach ($arrOther1 as $key => $value){
                                $value['question_bank_id'] = $question_bank_id;
                                $this->db->insert('tbl_question_bank_criteria',$value);
                            }
                        }

                        $this->db->where('question_bank_id',$question_bank_id);
                        $this->db->delete('tbl_question_bank_answer');

                        if (!empty($arrOther2)){
                            foreach ($arrOther2 as $key => $value){
                                $value['question_bank_id'] = $question_bank_id;
                                $this->db->insert('tbl_question_bank_answer',$value);
                            }
                        }

                        $count ++;
                    }
                }
                echo json_encode(
                    [
                        'success' => true,
                        'errors' => $errors,
                        'alert_type' => 'success',
                        'message' => 'Thêm mới/ cập nhập thành công ' . $count . ' ngân hàng câu hỏi',
                    ]
                );
                die();

            }
            echo json_encode([
                'success' => true,
                'errors' => $errors,
                'alert_type' => 'success',
                'message' => 'Import thành công ' . $count . ' dòng',
            ]);
            die();
        }
        $data['title'] = _l('Import ngân hàng câu hỏi');
        $this->load->view('admin/category_regulations/import_question_bank', $data);
    }

    public function searchRoleLevel($id = 0)
    {
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_role_level.id as id, 
            tbl_role_level.name as text
        ', false);
        $this->db->from('tbl_role_level');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_role_level.code', $term);
            $this->db->or_like('tbl_role_level.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Cấp bậc vai trò'), 'children' => $pod];
        if (!empty($id)){
            $dtRole = get_table_where('tbl_role_level',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtRole['id'], 'text' => $dtRole['name']];
        }
        echo json_encode($data);
    }

    public function changeStatusQuestionBank($id,$status)
    {
        $data = [];
        $this->db->where('id', $id);
        $success = $this->db->update('tbl_question_bank', [
            'status' => $status
        ]);

        if ($success) {
            $data['result'] = 1;
            $data['message'] = lang('Thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Thất bại');
        }
        echo json_encode($data);
    }

    public function changeAffectKpi($id,$status)
    {
        $data = [];
        $this->db->where('id', $id);
        $success = $this->db->update('tbl_question_bank', [
            'affect_kpi' => $status
        ]);

        if ($success) {
            $data['result'] = 1;
            $data['message'] = lang('Thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Thất bại');
        }
        echo json_encode($data);
    }

    public function changeAriseReport($id,$status)
    {
        $data = [];
        $this->db->where('id', $id);
        $success = $this->db->update('tbl_question_bank', [
            'arise_report' => $status
        ]);

        if ($success) {
            $data['result'] = 1;
            $data['message'] = lang('Thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Thất bại');
        }
        echo json_encode($data);
    }
}