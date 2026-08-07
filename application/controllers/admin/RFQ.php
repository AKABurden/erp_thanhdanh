<?php
defined('BASEPATH') or exit('No direct script access allowed');
class RFQ extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchases_model');
        $this->load->model('invoice_items_model');
        $this->load->model('suppliers_model');
    }
    public function index()
    {
        if (!has_permission('RFQ', '', 'view')&&!has_permission('RFQ', '', 'view_own')) {
                access_denied('RFQ');
        }
        $this->db->select('tblstaff.staffid, CONCAT(firstname," ",lastname) as name');
        $this->db->from('tblstaff');
        $data['dataStaff'] = $this->db->get()->result_array();
        
        $this->db->select('tblsuppliers.id, tblsuppliers.company, CONCAT(prefix,"-",code) as code');
        $this->db->from('tblsuppliers');
        $data['dataSupplier'] = $this->db->get()->result_array();
        
        $data['title']          = _l('ask_the_supplier_price');
        $this->load->view('admin/RFQ/manage', $data);
    }
    public function count_all()
    {
        if(has_permission('RFQ','','view_own')&&!is_admin())
        {
        $count = get_table_where_select('count(*) as alls','tblrfq_ask_price',array('tblrfq_ask_price.staff_create'=>get_staff_user_id()),'','row');
        $dont_approve = get_table_where_select('count(*) as dont_approve','tblrfq_ask_price',array('status'=>1,'tblrfq_ask_price.staff_create'=>get_staff_user_id()),'','row');
        $do_approve = get_table_where_select('count(*) as do_approve','tblrfq_ask_price',array('status'=>2,'tblrfq_ask_price.staff_create'=>get_staff_user_id()),'','row');
        }else
        {
        $count = get_table_where_select('count(*) as alls','tblrfq_ask_price',array(),'','row');
        $dont_approve = get_table_where_select('count(*) as dont_approve','tblrfq_ask_price',array('status'=>1),'','row');
        $do_approve = get_table_where_select('count(*) as do_approve','tblrfq_ask_price',array('status'=>2),'','row');  
        }
        
        $data['all'] = $count->alls;
        $data['dont_approve'] = $dont_approve->dont_approve;
        $data['do_approve'] = $do_approve->do_approve;
        echo json_encode($data);
    }
    public function table()
    {
        
        $this->app->get_table_data('rqf_table');
    }
    public function detail($id='')
    {
        if ($this->input->post()) {
            if ($id == '') {

                if (!has_permission('purchases', '', 'create')) {
                    access_denied('purchases');
                }

                $data                 = $this->input->post();
                
                if(isset($data['items']) && count($data['items']) > 0)
                {
                    
                    $id = $this->purchases_model->add($data);
                }
                
                if ($id) {
                    set_alert('success', _l('ch_added_successfuly'));
                    redirect(admin_url('purchases/detail/' . $id));
                }
            } else {
                if (!has_permission('purchases', '', 'edit')) {
                        access_denied('purchases');
                }
                $success = $this->purchases_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('ch_updated_successfuly'));
                }
                redirect(admin_url('purchases/detail/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('ask_the_supplier_price'));

        } else {
            $title = _l('edit', _l('ask_the_supplier_price'));
            $data['purchase'] = $this->purchases_model->get($id);
        }
        $data['type_items'] = get_table_where('tbltype_items',array('active'=>1));
        $data['title'] = $title;
        $this->load->view('admin/purchases/detail', $data);
    } 
    public function add_rfq($id='')
     {  
         if ($this->input->post()) {
            $data = $this->input->post();
            if(empty($data['id']))
            {
                unset($data['id']);
            $success = $this->purchases_model->add_rfq($data,$id);
            if($success)
            {
                $get_code = get_table_where('tblrfq_ask_price',array('id'=>$success),'','row');
                activity_log_v2('purchase','tblrfq_ask_price',$success,$get_code->prefix.'-'.$get_code->code,'Thêm mới phiếu hỏi giá nhà cung cấp ['.$get_code->prefix.'-'.$get_code->code.']');
                set_alert('success', _l('ch_added_successfuly', _l('ch_evaluation_criteria')));
                redirect(admin_url('purchases'));
            }else
            {
                set_alert('danger', _l('ch_added_successfuly_not', _l('ch_evaluation_criteria')));
            }
            }else
            {
                $success = $this->purchases_model->update_rfq($data,$id);
                $get_code = get_table_where('tblrfq_ask_price',array('id'=>$data['id']),'','row');
                activity_log_v2('purchase','tblrfq_ask_price',$data['id'],$get_code->prefix.'-'.$get_code->code,'Cập nhật phiếu hỏi giá nhà cung cấp ['.$get_code->prefix.'-'.$get_code->code.']');

                set_alert('success', _l('updated_successfully', _l('ch_evaluation_criteria')));
                redirect(admin_url('purchases'));
            }
         }
     } 
    public function views_purchases($id = '')
    {
        $data['purchase'] = $this->purchases_model->get($id);
        $this->load->view('admin/purchases/view_modal',$data);
    }
    public function send_quote_suppliers($suppliers_id = '',$id)
    {
        $data['id'] = $id;
        if($suppliers_id > 0)
        {
        $data['suppliers_id'] = $suppliers_id;
        $data['title']=_l('ch_seen_quote');
        // $data['get_gmail'] = $this->suppliers_model->get_gmail($suppliers_id);
        }else
        {   
            $data['title']=_l('ch_seen_quote_all');
            $ask = get_table_where('tblrfq_ask_price',array('id'=>$id),'','row')->suppliers_id;
            $data['suppliers_id'] = $ask;
        }
        $data['emailtemplates'] = get_table_where('tblemailtemplates',array('emailtemplateid'=>1535),'','row');
        echo json_encode($data);die;
        // $this->load->view('admin/RFQ/send_quote',$data);
    } 
    public function rfq_modal($id = '')
    {
        $data['purchase'] = $this->purchases_model->get_items_purchases($id);
        $data['id'] = $id;
        $data['suppliers'] = get_table_where('tblsuppliers',array('type'=>0));
        $ktr = get_table_where('tblrfq_ask_price',array('id_purchases'=>$id),'','row');
        if($ktr)
        {
            $data['ask_price'] = $this->purchases_model->get_ask_price($id);
            $suppliers_id = explode(',', $data['ask_price']->suppliers_id);
            foreach ($suppliers_id as $key => $value) {
                $data['suppliers_id'][$key]['id']=$value;
                $data['suppliers_id'][$key]['company']=get_table_where('tblsuppliers',array('id'=>$value),'','row')->company;
            }
            $data['suppliers_id'] = array_reverse($data['suppliers_id'], true);
        }
        $this->load->view('admin/purchases/rfq_modal',$data);
    }     
    public function get_items_supplier()
    {
        if ($this->input->post()) {
            $data =$this->input->post();
            $mainstream_goods = $this->purchases_model->get_items_supplier($data['id'],$data['supplier_id']);
            echo json_encode($mainstream_goods);
        }
    }
    public function update_status()
    {
        if ($this->input->post()) {
            $id=$this->input->post('id');
            $status=$this->input->post('status');
            $staff_id=get_staff_user_id();
            $date=date('Y-m-d H:i:s');
            $history_status = '';
            $history_status.='|'.$staff_id.','.$date;
            $data =array(
                'history_status'=>$history_status,
                'status' => ($status+1),
            );
            $this->db->where('id',$id);
            $this->db->update('tblrfq_ask_price',$data);
        }
        if ($this->db->affected_rows() > 0) {
            $get_code = get_table_where('tblrfq_ask_price',array('id'=>$id),'','row');
            activity_log_v2('purchase','tblrfq_ask_price',$id,$get_code->prefix.'-'.$get_code->code,'Cập nhật trạng thái phiếu hỏi giá nhà cung cấp ['.$get_code->prefix.'-'.$get_code->code.']');
            echo json_encode(array(
                'success' => true,
                'message' => _l('Xác nhận thành công')
            ));
        }
        else
        {
            echo json_encode(array(
                'success' => false,
                'message' => _l('Không thể cập nhật dữ liệu')
            ));
        }
        die;
    }
    public function items($type='')
    {
    //HAU
        if($type == 'items')
        {
            echo json_encode($this->purchases_model->get_items_ch());
        }
    }  
            public function get_items($id='')
    {
        echo json_encode($this->invoice_items_model->get_full_edit($id));
    }
    public function delete($id)
    {
        if (!has_permission('RFQ', '', 'delete')) {
            access_denied('Delete Ask price');
        }
        $ktr = get_table_where('tblsupplier_quotes',array('id_ask_price'=>$id),'','row');
        if($ktr) {
            $response = false;
            $alert_type = 'warning';
            $message    = _l('Đã tồn tại phiếu báo giá!');
        } else {
            $get_code = get_table_where('tblrfq_ask_price',array('id'=>$id),'','row');
            activity_log_v2('purchase','tblrfq_ask_price',$id,$get_code->prefix.'-'.$get_code->code,'Xóa phiếu hỏi giá nhà cung cấp ['.$get_code->prefix.'-'.$get_code->code.']');

            $response = $this->purchases_model->delete_rfq($id);
            $alert_type = 'warning';
            $message    = _l('Không thể xóa dữ liệu');
        }
        if ($response) {
            $alert_type = 'success';
            $message    = _l('Xóa dữ liệu thành công');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
            ));
    }
public function excel($id = '',$suppliers_id)
    {
        if(empty($id) || empty($suppliers_id))
        {
            die;
        }
        $this->db->select('tblrfq_ask_price.*,tblstaff.email as email,tblstaff.phonenumber as phonenumber')->distinct();
        $this->db->from('tblrfq_ask_price');
        $this->db->join('tblstaff','tblstaff.staffid=tblrfq_ask_price.staff_create','left');
        $this->db->where('id',$id);
        $ask_price = $this->db->get()->row();

        // $this->db->select('tblrfq_ask_price_items.*,tblitems.name as name_item,tblitems.code as code_item')->distinct();
        // $this->db->from('tblrfq_ask_price_items');
        // $this->db->join('tblitems','tblitems.id=tblrfq_ask_price_items.product_id','left');
        // $this->db->where('id_rfq_ask_price',$id);
        // $this->db->where('suppliers_id',$suppliers_id);
        $items = $this->purchases_model->get_items_ask_price_suppliers($id,$suppliers_id);
        // $this->db->get()->result_array();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setShowGridlines(False);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $colum_array=array('I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $BStyleheader = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 16,
                'name'  => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal'=> 'center',
            ),
        );
        $BStylenumber = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal'=> 'center',
            ),
        );
        $BStylewhite = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            )
        );
        $BStylewhiteV2 = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal'=> 'center',
            ),
        );
        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '20b24c')
            ),
            'alignment' => array(
                'horizontal'=> 'center',
            ),
        );
        $BStylerleft = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '20b24c')
            ),
            'alignment' => array(
                'horizontal'=> 'left',
            ),
        );
        $BStyleright = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal'=> 'right',
            ),
        );

        for($row = 1; $row <= 100; $row++)
        {
            $styleArray = [
                'font' => [
                    'size' => 12
                ]
            ];
            $objPHPExcel->getActiveSheet()
            ->getStyle("A1:N2")
            ->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->SetCellValue('A2',_l('ch_excel_ask_price'))->getStyle('A2')->applyFromArray($BStyleheader); 
            $objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
            $objPHPExcel->getActiveSheet()->mergeCells('D3:F3');
            $objPHPExcel->getActiveSheet()->mergeCells('D4:F4');
            $objPHPExcel->getActiveSheet()->mergeCells('D5:F5');
            $objPHPExcel->getActiveSheet()->mergeCells('D6:F6');

        }
        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
        $objDrawing1->setName('Sample image');
        $objDrawing1->setDescription('Sample image');
        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
        $objDrawing1->setPath('uploads/company/'.get_option('company_logo'));
        $objDrawing1->setWidth(100);
        $objDrawing1->setCoordinates('B3');
        $objPHPExcel->getActiveSheet()->setCellValue('C3',_l('supplier'))->getStyle('C3')->applyFromArray($BStylerleft);
        $objPHPExcel->getActiveSheet()->setCellValue('C4',_l('ch_staff_crate_rfq'))->getStyle('C4')->applyFromArray($BStylerleft);
        $objPHPExcel->getActiveSheet()->setCellValue('C5',_l('email'))->getStyle('C5')->applyFromArray($BStylerleft);
        $objPHPExcel->getActiveSheet()->setCellValue('C6',_l('ch_phone_number'))->getStyle('C6')->applyFromArray($BStylerleft);
        $objPHPExcel->getActiveSheet()->setCellValue('E3','')->getStyle('E3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('E4','')->getStyle('E4')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('E5','')->getStyle('E5')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('E6','')->getStyle('E6')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('F3','')->getStyle('F3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('F4','')->getStyle('F4')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('F5','')->getStyle('F5')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('F6','')->getStyle('F6')->applyFromArray($BStyle);
        $suppliers = get_table_where('tblsuppliers',array('id'=>$suppliers_id),'','row');
        $objPHPExcel->getActiveSheet()->setCellValue('D3',$suppliers->company)->getStyle('D3')->applyFromArray($BStyleright);
        $objPHPExcel->getActiveSheet()->setCellValue('D4',get_staff_full_name($ask_price->staff_create))->getStyle('D4')->applyFromArray($BStyleright);
        $objPHPExcel->getActiveSheet()->setCellValue('D5',$ask_price->email)->getStyle('D5')->applyFromArray($BStyleright);
        $objPHPExcel->getActiveSheet()->setCellValue('D6',$ask_price->phonenumber)->getStyle('D6')->applyFromArray($BStyleright); 

        $objPHPExcel->getActiveSheet()->setCellValue('B8','#')->getStyle('B8')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('C8',_l('ch_items'))->getStyle('C8')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('D8',_l('item_quantity'))->getStyle('D8')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('E8',_l('ch_price'))->getStyle('E8')->applyFromArray($BStyle); 
        $objPHPExcel->getActiveSheet()->setCellValue('F8',_l('invoice_total'))->getStyle('F8')->applyFromArray($BStyle);
        $number = 8;
        foreach ($items as $key => $value) {
        $number++;
        $objPHPExcel->getActiveSheet()->setCellValue('B'.($number),($key+1))->getStyle('B'.($number))->applyFromArray($BStylenumber);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.($number),$value['name_item'])->getStyle('C'.($number))->applyFromArray($BStylewhite);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.($number),$value['quantity'])->getStyle('D'.($number))->applyFromArray($BStylewhiteV2);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.($number),0)->getStyle('E'.($number))->applyFromArray($BStylewhite)->getNumberFormat()->setFormatCode('#,##0.00');
        $objPHPExcel->getActiveSheet()->setCellValue('F'.($number),'=E'.($number).'*'.'D'.($number))->getStyle('F'.($number))->applyFromArray($BStylewhite)->getNumberFormat()->setFormatCode('#,##0.00');
        }
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="RFQ'.$suppliers->prefix.'-'.$suppliers->code.'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();

    }
    public function send_to_email()
    {
      if(!empty($this->input->post()))
        {
            $data = $this->input->post();
            $suppliers = explode(',', $data['suppliers_mail']);
            $count = 0;
            foreach ($suppliers as $key => $value) {
                $get_gmail = $this->suppliers_model->get_gmail($value);
                $_data['suppliers'] = $value;
                $_data['content'] = $this->input->post('content', false);
                $_data['email'] = $get_gmail;
                $_data['subject'] = $data['subject'];
                $_data['id'] = $data['id'];
                $_data['cc'] = $data['cc'];
                $success =  $this->send_email_ch($_data);
                if($success)
                {
                    $count++;
                }
            }
            if($count > 0)
            {
              echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('send_true_email') ));die();  
          }else
          {
            echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => $this->email->print_debugger() ));die();
          }
        }  
    }
    public function send_email_ch($data)
    {
        $content ='';

        $this->db->select('tblrfq_ask_price.*,tblstaff.email as email,tblstaff.phonenumber as phonenumber')->distinct();
        $this->db->from('tblrfq_ask_price');
        $this->db->join('tblstaff','tblstaff.staffid=tblrfq_ask_price.staff_create','left');
        $this->db->where('id',$data['id']);
        $ask_price = $this->db->get()->row();
        $items = $this->purchases_model->get_items_ask_price_suppliers($data['id'],$data['suppliers']);
        // $this->db->select('tblrfq_ask_price_items.*,tblitems.name as name_item,tblitems.code as code_item')->distinct();
        // $this->db->from('tblrfq_ask_price_items');
        // $this->db->join('tblitems','tblitems.id=tblrfq_ask_price_items.product_id','left');
        // $this->db->where('id_rfq_ask_price',$data['id']);
        // $this->db->where('suppliers_id',$data['suppliers']);
        // $items = $this->db->get()->result_array();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setShowGridlines(False);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $colum_array=array('I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $BStyleheader = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 16,
                'name'  => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal'=> 'center',
            ),
        );
        $BStylenumber = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal'=> 'center',
            ),
        );
        $BStylewhite = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            )
        );
        $BStylewhiteV2 = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal'=> 'center',
            ),
        );
        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '20b24c')
            ),
            'alignment' => array(
                'horizontal'=> 'center',
            ),
        );
        $BStylerleft = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '20b24c')
            ),
            'alignment' => array(
                'horizontal'=> 'left',
            ),
        );
        $BStyleright = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal'=> 'right',
            ),
        );

        for($row = 1; $row <= 100; $row++)
        {
            $styleArray = [
                'font' => [
                    'size' => 12
                ]
            ];
            $objPHPExcel->getActiveSheet()
            ->getStyle("A1:N2")
            ->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->SetCellValue('A2',_l('ch_excel_ask_price'))->getStyle('A2')->applyFromArray($BStyleheader); 
        // Chưa Gắn _l()
            $objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
            $objPHPExcel->getActiveSheet()->mergeCells('D3:F3');
            $objPHPExcel->getActiveSheet()->mergeCells('D4:F4');
            $objPHPExcel->getActiveSheet()->mergeCells('D5:F5');
            $objPHPExcel->getActiveSheet()->mergeCells('D6:F6');

        }
        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
        $objDrawing1->setName('Sample image');
        $objDrawing1->setDescription('Sample image');
        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
        $objDrawing1->setPath('uploads/company/'.get_option('company_logo'));
        $objDrawing1->setWidth(100);
        $objDrawing1->setCoordinates('B3');
        $objPHPExcel->getActiveSheet()->setCellValue('C3',_l('supplier'))->getStyle('C3')->applyFromArray($BStylerleft);
        $objPHPExcel->getActiveSheet()->setCellValue('C4',_l('ch_staff_crate_rfq'))->getStyle('C4')->applyFromArray($BStylerleft);
        $objPHPExcel->getActiveSheet()->setCellValue('C5',_l('email'))->getStyle('C5')->applyFromArray($BStylerleft);
        $objPHPExcel->getActiveSheet()->setCellValue('C6',_l('ch_phone_number'))->getStyle('C6')->applyFromArray($BStylerleft);
        $objPHPExcel->getActiveSheet()->setCellValue('E3','')->getStyle('E3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('E4','')->getStyle('E4')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('E5','')->getStyle('E5')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('E6','')->getStyle('E6')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('F3','')->getStyle('F3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('F4','')->getStyle('F4')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('F5','')->getStyle('F5')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('F6','')->getStyle('F6')->applyFromArray($BStyle);
        $suppliers = get_table_where('tblsuppliers',array('id'=>$data['suppliers']),'','row');
        $objPHPExcel->getActiveSheet()->setCellValue('D3',$suppliers->company)->getStyle('D3')->applyFromArray($BStyleright);
        $objPHPExcel->getActiveSheet()->setCellValue('D4',get_staff_full_name($ask_price->staff_create))->getStyle('D4')->applyFromArray($BStyleright);
        $objPHPExcel->getActiveSheet()->setCellValue('D5',$ask_price->email)->getStyle('D5')->applyFromArray($BStyleright);
        $objPHPExcel->getActiveSheet()->setCellValue('D6',$ask_price->phonenumber)->getStyle('D6')->applyFromArray($BStyleright); 

        $objPHPExcel->getActiveSheet()->setCellValue('B8','#')->getStyle('B8')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('C8',_l('ch_items'))->getStyle('C8')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('D8',_l('item_quantity'))->getStyle('D8')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('E8',_l('ch_price'))->getStyle('E8')->applyFromArray($BStyle); 
        $objPHPExcel->getActiveSheet()->setCellValue('F8',_l('invoice_total'))->getStyle('F8')->applyFromArray($BStyle);
        $number = 8;
        foreach ($items as $key => $value) {
        $number++;
        $objPHPExcel->getActiveSheet()->setCellValue('B'.($number),($key+1))->getStyle('B'.($number))->applyFromArray($BStylenumber);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.($number),$value['name_item'])->getStyle('C'.($number))->applyFromArray($BStylewhite);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.($number),$value['quantity'])->getStyle('D'.($number))->applyFromArray($BStylewhiteV2);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.($number),0)->getStyle('E'.($number))->applyFromArray($BStylewhite)->getNumberFormat()->setFormatCode('#,##0.00');
        $objPHPExcel->getActiveSheet()->setCellValue('F'.($number),'=E'.($number).'*'.'D'.($number))->getStyle('F'.($number))->applyFromArray($BStylewhite)->getNumberFormat()->setFormatCode('#,##0.00');
        }
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="RFQ'.$suppliers->prefix.'-'.$suppliers->code.'.xls"');
        header('Cache-Control: max-age=0');
       
            $objWriter->save('php://output');
            $content = ob_get_clean();
            ob_end_clean();
            // var_dump($content);die;
            set_mailing_constant();
            $this->load->config('email');
            $this->email->initialize();
            $this->email->clear(true);
            if(!empty($data['suppliers']))
            {
                $list_field = ['company' => 'company'];
                $this->db->where('id', $data['suppliers']);
                $suppliers = $this->db->get(db_prefix().'suppliers')->row();
                if(!empty($suppliers))
                {
                    foreach($list_field as $key => $value)
                    {
                        $data['content'] = str_replace('{'.$value.'}', $suppliers->{$key}, $data['content']);
                    }
                }
            }
            if (get_option('mail_engine') == 'phpmailer') {
                $this->email->set_debug_output(function ($err) {
                    if (!isset($GLOBALS['debug'])) {
                        $GLOBALS['debug'] = '';
                    }
                    $GLOBALS['debug'] .= $err . '<br />';

                    echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => $err ));die();
                });
                $this->email->set_smtp_debug(3);
            }
            $company = get_option('companyname');
            $this->email->set_newline(config_item('newline'));
            $this->email->set_crlf(config_item('crlf'));
            $this->email->from(get_option('smtp_email'), $company);
            $this->email->to($data['email']);
            if(!empty($data['cc']))
            {
                $this->email->cc($data['cc']);
            }
            $systemBCC = get_option('bcc_emails');

            if ($systemBCC != '') {
                $this->email->bcc($systemBCC);
            }

            $this->email->subject($data['subject']);
            $this->email->message($data['content']);

            $this->email->attach($content, 'attachment', 'RFQ'.$suppliers->prefix.'-'.$suppliers->code.'.xls','application/vnd.ms-excel');
            
            if ($this->email->send(true))
            {
                $create_by = get_staff_user_id();
                $this->db->insert(db_prefix().'log_send_email', array(
                    'email' => $data['email'],
                    'bcc' => $systemBCC,
                    'subject' => $data['subject'],
                    'mesage' => $data['content'],
                    'create_by' => $create_by,
                    'type' => 'send_rfq_suppliers',
                    'date_create' => date('Y-m-d H:i:s')
                ));
                return true;
            }
            else
            {
                return false;
            }
     
    }
    public function compare_supplier($id = '')
    {
        $data['idMain'] = $id;
        $data['title'] = _l('compare_supplier');
        $data['dataMain'] = get_table_where('tblrfq_ask_price',array('id'=>$id),'','row');
        $arrSupplier = [];
        $Supplier = get_table_where('tblevaluate_suppliers',array('id_rfq_ask_price'=>$id),'','result_array','suppliers_id');
        foreach ($Supplier as $key => $value) {
            $arrSupplier[] = $value['suppliers_id'];
        }
        if(count($arrSupplier) > 0) {
            $this->db->select('tblsuppliers.*');
            $this->db->from('tblsuppliers');
            $this->db->where_in('id',$arrSupplier);
            $data['arrSupplier'] = $this->db->get()->result_array();
        }

        $parent = get_table_where('tblevaluate_suppliers',array('id_rfq_ask_price'=>$id),'','result_array','id_evaluation_criteria');
        $stt = 0;
        foreach ($parent as $key => $value) {
            $data['bodyMain'][$stt]['parent'] = get_table_where('tblevaluation_criteria',array('id'=>$value['id_evaluation_criteria']),'','row')->name;
            $data['bodyMain'][$stt]['idParent'] = $value['id_evaluation_criteria'];
            $data['bodyMain'][$stt]['child'] = get_table_where('tblevaluate_suppliers',array('id_rfq_ask_price'=>$id,'id_evaluation_criteria'=>$value['id_evaluation_criteria']));
            $stt++;
        }

        $this->load->view('admin/RFQ/compare_supplier',$data);
    }
}