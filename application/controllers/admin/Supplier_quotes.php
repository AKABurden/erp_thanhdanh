<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Supplier_quotes extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('supplier_quotes_model');
        $this->load->model('invoice_items_model');
        $this->load->model('purchases_model');
    }
    public function index()
    {
        if (!has_permission('supplier_quotes', '', 'view')&&!has_permission('supplier_quotes', '', 'view_own')) {
                access_denied('supplier_quotes');
            }
        $this->db->select('tblstaff.staffid, CONCAT(firstname," ",lastname) as name');
        $this->db->from('tblstaff');
        $data['dataStaff'] = $this->db->get()->result_array();

        $this->db->select('tblsuppliers.id, tblsuppliers.company, CONCAT(prefix,"-",code) as code');
        $this->db->from('tblsuppliers');
        $data['dataSupplier'] = $this->db->get()->result_array();

        $data['title']          = _l('ch_supplier_quotes');
        $this->load->view('admin/supplier_quotes/manage', $data);
    }
    public function table()
    {
        
        $this->app->get_table_data('supplier_quotes');
    }

    public function get_items($id='',$type='',$load = 0,$id_supplier='')
    {
        
        $items = $this->invoice_items_model->get_full_item($id,$type);
        if(!empty($id_supplier))
        {
        $ktr_supp = get_table_where('tblmainstream_goods',array('id_suppliers'=>$id_supplier,'id_items'=>$id,'type'=>$type),'','row');
        if(!empty($ktr_supp))
        {
          $items->price_ch =   $ktr_supp->price;
        }else
        {
          $items->price_ch =   0;  
        }
        }else
        {
          $items->price_ch =   0;  
        }
        echo json_encode($items);

    }
    public function get_items_order_import($id='',$type='',$id_quote='')
    {
        
        $items = $this->supplier_quotes_model->get_items_order($id,$type,$id_quote);
        
        $items->avatar = (!empty($items->avatar)?(file_exists($items->avatar) ? base_url($items->avatar) : (file_exists('uploads/materials/'.$items->avatar) ? base_url('uploads/materials/'.$items->avatar) : (file_exists('uploads/products/'.$items->avatar) ? base_url('uploads/products/'.$items->avatar) : base_url('assets/images/preview-not-available.jpg')))):base_url('assets/images/preview-not-available.jpg'));
        
        echo json_encode($items);

    }    
    public function detail($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('supplier_quotes', '', 'create')) {
                    access_denied('supplier_quotes');
                }

                $data                 = $this->input->post();
                
                if(isset($data['items']) && count($data['items']) > 0)
                {
                    $id = $this->supplier_quotes_model->add($data);
                }
                
                if ($id) {
                    $get_code = get_table_where('tblsupplier_quotes',array('id'=>$id),'','row');
                    activity_log_v2('purchase','tblsupplier_quotes',$id,$get_code->prefix.'-'.$get_code->code,'Thêm mới báo giá nhà cung cấp ['.$get_code->prefix.'-'.$get_code->code.']');
                    set_alert('success', _l('ch_added_successfuly'));
                    redirect(admin_url('supplier_quotes'));
                }
            } else {
                if (!has_permission('supplier_quotes', '', 'edit')) {
                        access_denied('supplier_quotes');
                }
                $success = $this->supplier_quotes_model->update($this->input->post(), $id);
                if ($success == true) {
                    $this->load->model('misc_model');
                    $this->misc_model->changeRowNew_model('tblsupplier_quotes',$id);
                    
                    $get_code = get_table_where('tblsupplier_quotes',array('id'=>$id),'','row');
                    activity_log_v2('purchase','tblsupplier_quotes',$id,$get_code->prefix.'-'.$get_code->code,'Cập nhật báo giá nhà cung cấp ['.$get_code->prefix.'-'.$get_code->code.']');
                    set_alert('success', _l('ch_updated_successfuly'));
                }
                redirect(admin_url('supplier_quotes/detail/' . $id));
            }
        }
        if ($id == '') {
            if (!has_permission('supplier_quotes', '', 'create')) {
                    access_denied('supplier_quotes');
                }
            $title = _l('ch_supplier_quotes_add');
        } else {
             if (!has_permission('supplier_quotes', '', 'edit')) {
                        access_denied('supplier_quotes');
                }
            $title = _l('ch_supplier_quotes_edit');
            $item = $this->supplier_quotes_model->get_full_edit($id);
            $data['items'] = $item;
        }
        $data['taxes'] = get_taxes_dropdown_template('',0);
        $type_items = get_table_where('tbltype_items',array('active'=>1));
        $count = 0;
        $data['type_items'][0] = array('type'=>'-1','name'=>_l('task_list_all'));
        foreach ($type_items as $key => $value) {
            $count++;
            $data['type_items'][$count] = $value;
        }
        $data['suppliers'] = get_table_where('tblsuppliers',array('type'=>0));
        $data['cell_excel'] = get_table_where('tbl_cell_excel');
        $data['title'] = $title;
        $this->load->view('admin/supplier_quotes/detail', $data);
    }
    public function delete($id)
    {
        if (!has_permission('supplier_quotes', '', 'delete')) {
            access_denied('Delete Supplier quotes');
        }
        $order = get_table_where('tblpurchase_order',array('id_quotes'=>$id),'','row');
        if(!empty($order))
        {
        echo json_encode(array(
            'alert_type' => 'warning',
            'message' => 'ch_delete_not'
            ));die;    
        }
        $get_code = get_table_where('tblsupplier_quotes',array('id'=>$id),'','row');
        activity_log_v2('purchase','tblsupplier_quotes',$id,$get_code->prefix.'-'.$get_code->code,'Xóa báo giá nhà cung cấp ['.$get_code->prefix.'-'.$get_code->code.']');
        $response = $this->supplier_quotes_model->delete_supplier_quotes($id);
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');
        if ($response) {
            $alert_type = 'success';
            $message    = _l('ch_delete');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
            ));
    } 
    public function update_status($value='')
    {
        if (!has_permission('supplier_quotes', '', 'approve')) {
            echo json_encode(array(
                'success' => false,
                'message' => _l('ch_approve_not')
            ));die;
            }
        if ($this->input->post()) {
            
            $id=$this->input->post('id');
            $status=$this->input->post('status');
            $supplier_quotes = get_table_where('tblsupplier_quotes',array('id'=>$id),'','row');
            $staff_id=get_staff_user_id();
            $date=date('Y-m-d H:i:s');
            $history_status = $supplier_quotes->history_status;
            $history_status.='|'.$staff_id.','.$date;
            $data =array(
                'history_status'=>$history_status,
                'status' => ($status+1),
            );
            $success=$this->supplier_quotes_model->update_status($id,$data);
        }
        if($success) {
            $get_code = get_table_where('tblsupplier_quotes',array('id'=>$id),'','row');
            activity_log_v2('purchase','tblsupplier_quotes',$id,$get_code->prefix.'-'.$get_code->code,'Cập nhật trạng thái báo giá nhà cung cấp ['.$get_code->prefix.'-'.$get_code->code.']');
            echo json_encode(array(
                'success' => $success,
                'message' => _l('ch_successful_approval')
            ));
        }
        else
        {
            echo json_encode(array(
                'success' => $success,
                'message' => _l('ch_successful_approval')
            ));
        }
        die;
    }  
    public function view_supplier_quotes($id = '')
    {
        $data['items'] = $this->supplier_quotes_model->get_full_edit($id);
        $data['dataLog'] = get_table_where('tblactivity_log_v2',array('table_obj'=>'tblsupplier_quotes','id_obj'=>$id),'id DESC');
        $this->load->view('admin/supplier_quotes/view_modal',$data);
    }
    public function detail_v2($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('supplier_quotes', '', 'create')) {
                    access_denied('supplier_quotes');
                }

                $data                 = $this->input->post();
                
                if(isset($data['items']) && count($data['items']) > 0)
                {
                    $id = $this->supplier_quotes_model->add($data);
                }
                
                if ($id) {
                    $get_code = get_table_where('tblsupplier_quotes',array('id'=>$id),'','row');
                    activity_log_v2('purchase','tblsupplier_quotes',$id,$get_code->prefix.'-'.$get_code->code,'Tạo mới báo giá nhà cung cấp ['.$get_code->prefix.'-'.$get_code->code.']');

                    set_alert('success', _l('ch_added_successfuly'));
                    redirect(admin_url('supplier_quotes/detail/' . $id));
                }
            } else {
                if (!has_permission('supplier_quotes', '', 'edit')) {
                        access_denied('supplier_quotes');
                }
                $success = $this->supplier_quotes_model->update($this->input->post(), $id);
                if ($success == true) {
                    $get_code = get_table_where('tblsupplier_quotes',array('id'=>$id),'','row');
                    activity_log_v2('purchase','tblsupplier_quotes',$id,$get_code->prefix.'-'.$get_code->code,'Cập nhật báo giá nhà cung cấp ['.$get_code->prefix.'-'.$get_code->code.']');

                    $this->load->model('misc_model');
                    $this->misc_model->changeRowNew_model('tblsupplier_quotes',$id);
                    
                    set_alert('success', _l('ch_updated_successfuly'));
                }
                redirect(admin_url('supplier_quotes/detail_v2/' . $id));
            }
        }
        if ($id == '') {
            if (!has_permission('supplier_quotes', '', 'create')) {
                    access_denied('supplier_quotes');
                }
            $title = _l('ch_supplier_quotes_add');
        } else {
            if (!has_permission('supplier_quotes', '', 'edit')) {
                        access_denied('supplier_quotes');
                }
            $title = _l('ch_supplier_quotes_edit');
            $item = $this->supplier_quotes_model->get_full_edit($id);
            $data['items'] = $item;
            if(!empty($item->id_purchases))
            {
                $data['itemssss'] = $this->purchases_model->get_items_purchases($item->id_purchases);
                $data['load_html'] = $this->supplier_quotes_model->get_items_quotes_order($item->id_purchases);
                $html='<option></option>';
                foreach ($data['load_html'] as $key => $value) {
                    if($key == 0)
                        {
                        $html.='<optgroup label="'.$value['name'].'">';
                        }else if($value['id'] == 'h')
                        {
                            $html.='</optgroup>';
                            $html.='<optgroup label="'.$value['name'].'">'; 
                        }else{
                        $value['name'] = str_replace('"','', $value['name']);
                        $value['name'] = str_replace("'",'', $value['name']);
                        $html.='<option data-id='.$value['type_items'].' value="' .$value['id']. '">'  .$value['name']. '</option>';
                        }
                }
                $html.='</optgroup>';
                $data['html'] = $html; 
            }if(!empty($item->id_ask_price))
            {
                    $data['itemssss'] = $this->purchases_model->get_items_ask_price_suppliers($item->id_ask_price,$item->suppliers_id);
                    $data['load_html'] = $this->supplier_quotes_model->get_items_rfq_order($item->id_ask_price,$item->suppliers_id);
                    $html='<option></option>';
                    foreach ($data['load_html'] as $key => $value) {
                        if($key == 0)
                            {
                            $html.='<optgroup label="'.$value['name'].'">';
                            }else if($value['id'] == 'h')
                            {
                                $html.='</optgroup>';
                                $html.='<optgroup label="'.$value['name'].'">'; 
                            }else{
                            $value['name'] = str_replace('"','', $value['name']);
                            $value['name'] = str_replace("'",'', $value['name']);
                            $html.='<option data-id='.$value['type_items'].' value="' .$value['id']. '">'  .$value['name']. '</option>';
                            }
                    }
                    $html.='</optgroup>';
                    $data['html'] = $html;
            }
        }
        $data['taxes'] = get_taxes_dropdown_template('',0);
        $type_items = get_table_where('tbltype_items',array('active'=>1));
        $count = 0;
        $data['type_items'][0] = array('type'=>'-1','name'=>_l('task_list_all'));
        foreach ($type_items as $key => $value) {
            $count++;
            $data['type_items'][$count] = $value;
        }
        $data['suppliers'] = get_table_where('tblsuppliers',array('type'=>0));
        $data['cell_excel'] = get_table_where('tbl_cell_excel');
        $data['title'] = $title;
        $this->load->view('admin/supplier_quotes/detail_v2', $data);
    }
    public function detail_create($id_rfq = '',$supplier_id = '',$type ='0')
    {
        if (!has_permission('supplier_quotes', '', 'create')) {
                    access_denied('supplier_quotes');
            }
        if ($this->input->post()) {

                

                $data                 = $this->input->post();
                
                if(isset($data['items']) && count($data['items']) > 0)
                {
                    if(!empty($supplier_id)&&($supplier_id > 0))
                    {
                        $data['suppliers_id']=$supplier_id;
                    }
                    $id = $this->supplier_quotes_model->add($data,$id_rfq);
                }
                
                if ($id) {

                    $get_code = get_table_where('tblsupplier_quotes',array('id'=>$id),'','row');
                    activity_log_v2('purchase','tblsupplier_quotes',$id,$get_code->prefix.'-'.$get_code->code,'Thêm mới báo giá nhà cung cấp ['.$get_code->prefix.'-'.$get_code->code.']');
                    set_alert('success', _l('ch_added_successfuly'));
                    redirect(admin_url('supplier_quotes/detail_v2/' . $id));
                }
        }
        
        $title = _l('ch_supplier_quotes_add');
        if($type == 1)
        {
        $data['id_purchases'] = $id_rfq;   
        $data['items_purchases'] = get_table_where('tblpurchases_items',array('purchases_id'=>$id_rfq));
        $data['load_html'] = $this->supplier_quotes_model->get_items_quotes_order($id_rfq);
        $html='<option></option>';
        foreach ($data['load_html'] as $key => $value) {
            if($key == 0)
                {
                $html.='<optgroup label="'.$value['name'].'">';
                }else if($value['id'] == 'h')
                {
                    $html.='</optgroup>';
                    $html.='<optgroup label="'.$value['name'].'">'; 
                }else{
                $value['name'] = str_replace('"','', $value['name']);
                $value['code_name'] = str_replace('"','', $value['code_name']);
                $value['name'] = str_replace("'",'', $value['name']);
                $value['code_name'] = str_replace("'",'', $value['code_name']);
                $html.='<option data-id='.$value['type_items'].' value="' .$value['id']. '">('.$value['code_name'].') '.$value['name']. '</option>';
                }
        }
        $html.='</optgroup>';
        $data['html'] = $html; 
        }
        else
        {
        $data['items_ask'] = get_table_where('tblrfq_ask_price_items',array('id_rfq_ask_price'=>$id_rfq,'suppliers_id'=>$supplier_id));
        $data['id_rfq'] = $id_rfq;
        $data['supplier_id'] = $supplier_id;
                    $data['load_html'] = $this->supplier_quotes_model->get_items_rfq_order($id_rfq,$supplier_id);
                    $html='<option></option>';
                    foreach ($data['load_html'] as $key => $value) {
                        if($key == 0)
                            {
                            $html.='<optgroup label="'.$value['name'].'">';
                            }else if($value['id'] == 'h')
                            {
                                $html.='</optgroup>';
                                $html.='<optgroup label="'.$value['name'].'">'; 
                            }else{
                            $value['name'] = str_replace('"','', $value['name']);
                            $value['code_name'] = str_replace('"','', $value['code_name']);
                            $value['name'] = str_replace("'",'', $value['name']);
                            $value['code_name'] = str_replace("'",'', $value['code_name']);
                            $html.='<option data-id='.$value['type_items'].' value="' .$value['id']. '">('.$value['code_name'].') '.$value['name']. '</option>';
                            }
                    }
                    $html.='</optgroup>';
                    $data['html'] = $html; 

        }
        $data['taxes'] = get_taxes_dropdown_template('',0);
        $type_items = get_table_where('tbltype_items',array('active'=>1));
        $count = 0;
        $data['type_items'][0] = array('type'=>'-1','name'=>_l('task_list_all'));
        foreach ($type_items as $key => $value) {
            $count++;
            $data['type_items'][$count] = $value;
        }
        $data['suppliers'] = get_table_where('tblsuppliers',array('type'=>0));
        $data['title'] = $title;
        $data['type_quote'] = $type;
        $this->load->view('admin/supplier_quotes/detail_create', $data);
    }
    public function add_tagert($id='',$idquote='')
    {
       if ($this->input->post()) {
       $success = $this->supplier_quotes_model->add_targert($this->input->post(),$id,$idquote);
            if($success)
            {
                $success = true;
                $message = _l('ch_added_successfuly', _l('ch_evaluation_criteria'));
            }else
            {
                $success = false;
                $message = _l('ch_added_successfuly_not', _l('ch_evaluation_criteria'));
            }
        }else
        {
                $success = false;
                $message = _l('ch_added_successfuly_not', _l('ch_evaluation_criteria'));
        }
        echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));die;
    }
    public function evaluate_modal_view($id='',$supplier_id='',$idquote='')
    {
        $data['ask_price'] = $id;
        $data['idquote'] = $idquote;
        $data['targert'] = $this->purchases_model->get_targert($id);
        $data['supplier_id'] = $supplier_id;
        $data['title'] = 'ch_evaluation_criteria';
        $this->load->view('admin/supplier_quotes/evaluate', $data);
    }
    public function import_items()
    {
        if ($this->input->post()) {
            $message='';
            $items = $this->input->post();
            var_dump($_FILES['file_import']['name']);die;
                if (isset($_FILES['file_import']['name']) && $_FILES['file_import']['name'] != '') {
                $tmpFilePath = $_FILES['file_import']['tmp_name'];
                $ext = strtolower(pathinfo($_FILES['file_import']['name'], PATHINFO_EXTENSION));
                $type = $_FILES["file_import"]["type"];
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $newFilePath = TEMP_FOLDER . $_FILES['file_import']['name'];
                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 777);
                    }                   
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $import_result = true;
                        $fd            = fopen($newFilePath, 'r');
                        $rows          = array();
                        if($ext == 'csv') {
                            while ($row = fgetcsv($fd)) {
                                $rows[] = $row;
                            }
                        }
                        else if($ext == 'xlsx' || $ext == 'xls') {
                            if($type == "application/octet-stream" || $type == "application/vnd.ms-excel" || $type == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
                                require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
                                $inputFileType = PHPExcel_IOFactory::identify($newFilePath);

                                $objReader = PHPExcel_IOFactory::createReader($inputFileType);

                                $objReader->setReadDataOnly(true);

                                /**  Load $inputFileName to a PHPExcel Object  **/
                                $objPHPExcel =           $objReader->load($newFilePath);
                                $allSheetName       = $objPHPExcel->getSheetNames();
                                $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
                                $highestRow         = $objWorksheet->getHighestRow();
                                $highestColumn      = $objWorksheet->getHighestColumn();

                                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                                if($items['number_end'] < $highestRow)
                                {
                                    $highestRow = $items['number_end'];
                                }
                                for ($row = $items['number']; $row <= $highestRow; ++$row) {
                                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                                        $value                     = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                                        $rows[$row - 2][$col] = $value;
                                    }
                                }
                            }
                        }
                        else {
                            fclose($fd);
                            unlink($newFilePath);
                            redirect('/');
                        }
                        fclose($fd);
                        $data['total_rows_post'] = count($rows);
                        unlink($newFilePath);
                        $backup_rows = $rows;
                        $fetch_columns_step = true;
                        $fetch_product_step = false;
                        $columns_found = 0;
                        $product_count = 0;
                        $data = [];
                        $data_ok = true;
                        $reason = "";
                        $dem_temp = 0;
                        $alert['success'] = 0;
                        $alert['fail'] = 0;
                        $fail=array();;
                                foreach ($rows as $key => $row) {
                                 $_data[$key]['type'] = $row[($items['type_itemss']-1)];
                                 $_data[$key]['name'] = $row[($items['name']-1)];
                                 $_data[$key]['unit'] = $row[($items['unit']-1)];
                                     if($_data[$key]['type'] == 1)
                                     {
                                        $item = get_table_where('tblitems',array('name'=>trim($_data[$key]['name'])),'','row');
                                        if($item)
                                        {
                                        $_data[$key]['id_product'] = $item->id;
                                        $_data[$key]['unit'] = $item->id; 
                                        $_data[$key]['quantity'] = $row[($items['quantity']-1)];
                                        $_data[$key]['cost'] = $row[($items['cost']-1)];
                                        $_data[$key]['tax'] = $row[($items['tax']-1)];
                                        $dem_temp++;
                                        }else
                                        {
                                            $fail[$key]['type'] = $row[($items['type_itemss']-1)];
                                            $fail[$key]['name'] = $row[($items['name']-1)];
                                            $fail[$key]['unit'] = $row[($items['unit']-1)];
                                            $fail[$key]['quantity'] = $row[($items['quantity']-1)];
                                            $fail[$key]['cost'] = $row[($items['cost']-1)];
                                            $fail[$key]['tax'] = $row[($items['tax']-1)];
                                            continue;
                                        }
                                     }
                                  }
                        $message = "Có  " . $dem_temp . " được import.";
                        }
                    }
                }
                echo json_encode(array(
                    'message' => $message
                ));die;
        }
    }
    public function print_pdf($id='')
    {
        ob_start();
        $data = new stdClass();
        $dataField = get_table_where('tbl_field_pdf',array('parent_field'=>'supplier_quotes'),'','row');
        $dataMain = get_table_where('tblsupplier_quotes',array('id'=>$id),'','row');
        $dataSub = get_table_where('tblsupplier_quote_items',array('id_supplier_quotes'=>$id));
        $table = '';
        $data->img = '';
        $data->content = '';
        // $data->content .= '<span style="text-align: center;">______________________________________________________________________________________________</span><br><br>';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">BÁO GIÁ NHÀ CUNG CẤP</span><br><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">'._l('ch_code_p').': '.$dataMain->prefix.'-'.$dataMain->code.'</span><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">'._l('ch_date_p').': '._d($dataMain->date).'</span><br><br>';
        $data->content .= '
            <span style="font-weight: bold;">'._l('ch_staff_p').': </span><span>'.get_staff_full_name($dataMain->staff_create).'</span><br>
            <span style="font-weight: bold;">'._l('ch_note_t').': </span><span>'.$dataMain->note.'</span><br><br>
        ';

        $widthStt = '';
        $widthItem = '';
        $widthUnit = '';
        $widthQuantity = '';
        $widthPrice = '';
        $widthTax = '';
        $widthEstimate = '';
        $widthNote = '';
        $dem_temp = 2;
        if(isset($dataField->arr_field)) {
            $arr = explode(',', $dataField->arr_field);
            foreach ($arr as $key => $value) {
                if($value == 'item_unit_supplier_quotes') {
                    $item_unit_supplier_quotes = true;
                    $dem_temp = 3;
                }
                if($value == 'item_quantity_supplier_quotes') {
                    $item_quantity_supplier_quotes = true;
                }
                if($value == 'item_price_supplier_quotes') {
                    $item_price_supplier_quotes = true;
                }
                if($value == 'item_tax_supplier_quotes') {
                    $item_tax_supplier_quotes = true;
                }
                if($value == 'item_estimate_total_supplier_quotes') {
                    $item_estimate_total_supplier_quotes = true;
                }
                if($value == 'item_note_supplier_quotes') {
                    $item_note_supplier_quotes = true;
                }
            }
            if(isset($item_unit_supplier_quotes) && isset($item_quantity_supplier_quotes) && isset($item_price_supplier_quotes) && isset($item_tax_supplier_quotes) && isset($item_estimate_total_supplier_quotes) && isset($item_note_supplier_quotes)) {
                $widthStt = 'width: 5%;';
                $widthItem = 'width: 35%;';
                $widthUnit = 'width: 10%;';
                $widthQuantity = 'width: 10%;';
                $widthPrice = 'width: 10%;';
                $widthTax = 'width: 10%;';
                $widthEstimate = 'width: 10%;';
                $widthNote = 'width: 10%;';
            }
        }
        $table = '
            <table class="table table-bordered" border="1" width="100%">
                <thead>
                    <tr>
                        <td style="'.$widthStt.'text-align: center;font-weight: bold;">'._l('STT').'</td>
        ';
        $table .= '<td style="'.$widthItem.'text-align: center;font-weight: bold;">'._l('ch_items_name_t').'</td>';
        
        if(isset($item_unit_supplier_quotes)) {
            $table .= '<td style="'.$widthUnit.'text-align: center;font-weight: bold;">'._l('item_unit').'</td>';
        }
        if(isset($item_quantity_supplier_quotes)) {
            $table .= '<td style="'.$widthQuantity.'text-align: center;font-weight: bold;">'._l('item_quantity').'</td>';
        }
        if(isset($item_price_supplier_quotes)) {
            $table .= '<td style="'.$widthPrice.'text-align: center;font-weight: bold;">'._l('ch_price').'</td>';
        }
        if(isset($item_tax_supplier_quotes)) {
            $table .= '<td style="'.$widthTax.'text-align: center;font-weight: bold;">'._l('tax').'</td>';
        }
        if(isset($item_estimate_total_supplier_quotes)) {
            $table .= '<td style="'.$widthEstimate.'text-align: center;font-weight: bold;">'._l('estimate_total').'</td>';
        }
        if(isset($item_note_supplier_quotes)) {
            $table .= '<td style="'.$widthNote.'text-align: center;font-weight: bold;">'._l('note').'</td>';
        }
        $table .= '</tr>
                </thead>
                <tbody>';
        $sum_quantity = 0;
        $sum_price = 0;
        $sum_tax = 0;
        $sum_estimate = 0;
        foreach ($dataSub as $key => $value) {
            $table .= '<tr nobr="true">';
            $dataItem = $this->invoice_items_model->get_full_item($value['product_id'],$value['type']);
            $table .= '<td style="'.$widthStt.'text-align: center;">'.++$key.'</td>';
            $table .= '<td style="'.$widthItem.'text-align: left;">'.$dataItem->name.'</td>';
            
            if(isset($item_unit_supplier_quotes)) {
                $table .= '<td style="'.$widthUnit.'text-align: center;">'.$dataItem->unit_name.'</td>';
            }
            if(isset($item_quantity_supplier_quotes)) {
                $table .= '<td style="'.$widthQuantity.'text-align: center;">'.formatNumber($value['quantity']).'</td>';
                $sum_quantity += $value['quantity'];
            }
            if(isset($item_price_supplier_quotes)) {
                $table .= '<td style="'.$widthPrice.'text-align: center;">'.number_format($value['unit_cost']).'</td>';
                $sum_price += $value['unit_cost'];
            }
            if(isset($item_tax_supplier_quotes)) {
                $table .= '<td style="'.$widthTax.'text-align: center;">'.number_format($value['tax_rate']).' %</td>';
                $sum_tax += $value['subtotal'] - $value['unit_cost'];
            }
            if(isset($item_estimate_total_supplier_quotes)) {
                $table .= '<td style="'.$widthEstimate.'text-align: right;">'.number_format($value['subtotal']).'</td>';
                $sum_estimate += $value['subtotal'];
            }
            if(isset($item_note_supplier_quotes)) {
                $table .= '<td style="'.$widthNote.'text-align: center;">'.$value['note'].'</td>';
            }
            $table .= '</tr>';
        }
        $table .= '<tr>
                <td colspan="'.$dem_temp.'" style="text-align: center;font-weight: bold;">'._l('invoice_dt_table_heading_amount').'</td>';
        if(isset($item_quantity_supplier_quotes)) {
            $table .= '<td style="text-align: center;">'.formatNumber($sum_quantity).'</td>';
        }
        if(isset($item_price_supplier_quotes)) {
            $table .= '<td style="text-align: center;">'.number_format($sum_price).'</td>';
        }
        if(isset($item_tax_supplier_quotes)) {
            $table .= '<td style="text-align: center;">'.number_format($sum_tax).'</td>';
        }
        if(isset($item_estimate_total_supplier_quotes)) {
            $table .= '<td style="text-align: right;">'.number_format($sum_estimate).'</td>';
        }
        if(isset($item_note_supplier_quotes)) {
            $table .= '<td></td>';
        }
        $table .= '</tr>';
        $table .= '</tbody>
            </table>';
        $data->content .= $table;


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
                            <span style="font-weight: bold;">Người Đề Nghị</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Trưởng Bộ Phận</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Lãnh Đạo</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Nhà Cung Cấp</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                    </tr>
                </tbody>
            </table>';
        $data->content .= $table;
        $pdf      = print_pdf($data);
        $type     = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }

    public function count_all()
    {
        if(has_permission('supplier_quotes','','view_own')&&!is_admin())
        {
        $count = get_table_where_select('count(*) as alls','tblsupplier_quotes',array('tblsupplier_quotes.staff_create'=>get_staff_user_id()),'','row');
        $dont_approve = get_table_where_select('count(*) as dont_approve','tblsupplier_quotes',array('status'=>1,'tblsupplier_quotes.staff_create'=>get_staff_user_id()),'','row');
        $do_approve = get_table_where_select('count(*) as do_approve','tblsupplier_quotes',array('status'=>2,'tblsupplier_quotes.staff_create'=>get_staff_user_id()),'','row');
        }else
        {
        $count = get_table_where_select('count(*) as alls','tblsupplier_quotes',array(),'','row');
        $dont_approve = get_table_where_select('count(*) as dont_approve','tblsupplier_quotes',array('status'=>1),'','row');
        $do_approve = get_table_where_select('count(*) as do_approve','tblsupplier_quotes',array('status'=>2),'','row');  
        }
        
        $data['all'] = $count->alls;
        $data['dont_approve'] = $dont_approve->dont_approve;
        $data['do_approve'] = $do_approve->do_approve;
        echo json_encode($data);
    }
}

