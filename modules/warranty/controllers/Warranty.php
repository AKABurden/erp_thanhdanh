<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warranty extends AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('costs_model');

        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->image_types_pdf = 'gif|jpg|jpeg|png|tif|pdf';
        $this->allowed_file_size = '10240';
        $this->upload_path = 'modules/warranty/uploads/warranty/';
        $this->datetime_now = time();
    }
    public function confirm_warehous($id='',$warehouseman_id='')
    {
        $test_quantity = get_table_where('tblwarehouse_product',array('import_id'=>$id,'quantity_export >'=>0,'type_export'=>1),'','row');
        if (!$id) {
            die('ch_no_items');
        }

        $data_in = array(
            'warehouseman_id'=>get_staff_user_id(),
            'warehouseman_date'=>date('Y-m-d H:i:s')
        );
        if($warehouseman_id != '') {
            $data_in = array(
                'warehouseman_id'=>NULL,
                'warehouseman_date'=>NULL
            );
        }

        $success = $this->db->update('tblwarranty',$data_in,array('id'=>$id));
        
        $alert_type = 'warning';
        $message = _l('ch_no_successful_approval');
        if($warehouseman_id) {
            $message = _l('ch_no_successful_approval_cance');
        }
        if ($success) {
            $alert_type = 'success';
            $message    = _l('ch_successful_approval');
            if($warehouseman_id) {
                $message    = _l('ch_successful_approval_cance');
            }
            if(empty($warehouseman_id)) {
                $this->increaseWarehouse($id);
            }
            else {
                $import = get_table_where('tblwarranty',array('id'=>$id),'','row');
                $this->decreaseWarehouse($id,$import->suppliers_id);
            }
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }
    //tăng kho
    public function increaseWarehouse($id)
    {
        $get_detail = get_table_where('tblwarranty',array('id'=>$id),'','row');
        $count=0;
        if($get_detail) {
            $date_warehouse = date('Y-m-d H:i:s');
            $warehouse_id = 14;
            $date_import = $get_detail->date_create;
            $get_series = get_table_where('tblwarranty_items',array('id_warranty_receive'=>$get_detail->id_warranty_receive));
            foreach ($get_series as $key => $value) 
            {
                $get_detail_series = get_table_where('tblseries',array('id'=>$value['id_series']),'','row');
                if($get_detail_series->type_item == 'products') {
                    $get_detail_series->type_item = 'product';
                }
                $localtion =  $value['warehouse_localtion'];
                $product_id = $get_detail_series->id_item;
                $type_items = $get_detail_series->type_item;
                $quantity = 1;
                $pirce = 0;
                $series = $value['id_series'];
                $count = increaseProductQuantity_bh($warehouse_id,$id,$date_warehouse,$date_import,$product_id,$quantity,$localtion,$type_items,$pirce,$series);
                //tăng kho tổng
                increaseWarehuseQuantity_bh($warehouse_id,$localtion,$product_id,$quantity,$type_items,$series);
            }
        }        
        if ($count) {
            return true;
        }
        return false;
    }
    //giảm kho phiếu nhập xóa dữ liệu trong kho
    public function decreaseWarehouse($id,$suppliers_id='')
    {
        if(is_numeric($id))
        {
            $warehouse_product = get_table_where("tblwarehouse_product",array('import_id'=>$id,'type_export'=>35));
            $this->db->delete('tblwarehouse_product',array('import_id'=>$id,'type_export'=>35));
            //Giảm kho tổng
            foreach ($warehouse_product as $key => $value) {
                decreaseWarehuseQuantity_bh($value['warehouse_id'],$value['localtion'],$value['product_id'],$value['quantity'],$value['type_items'],$value['series']);
                // decreaseSuppliersQuantity($suppliers_id,1,$value['product_id'],$value['quantity'],$value['type_items']);
                $this->db->delete('tblwarehouse_items',array('series'=>$value['series'],'product_quantity'=>0));
            }
        }        
        return true;
    } 
    public function index()
    {
        $data['title'] = _l('warranty');
        $this->load->view('manager', $data);
    }

    public function table_warranty()
    {
        $aColumns = [
            'tblwarranty_receive.id',
            'tblwarranty_receive.code',
            'tblwarranty_receive.date',
            'tblclients.company',
            'CONCAT(tblcontacts.firstname, " ", COALESCE(tblcontacts.lastname, " "))',
            'tblwarranty_receive.service_type',
            'tblwarranty_receive.status',
            '7',
            'tblwarranty_receive.staff_create',
            '8'
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tblwarranty_receive';

        $join         = array(
            'LEFT JOIN tblclients on tblclients.userid = tblwarranty_receive.customer_id',
            'LEFT JOIN tblcontacts on tblcontacts.id = tblwarranty_receive.name_of_machine',
        );
        $where         = array();
        if($this->input->post('filterStatus')) {
            if($this->input->post('filterStatus') == 1) {
                array_push($where, 'AND tblwarranty_receive.status = 0');
            }
            else if($this->input->post('filterStatus') == 2) {
                array_push($where, 'AND tblwarranty_receive.status = 1');
            }
            else if($this->input->post('filterStatus') == 3) {
                array_push($where, 'AND tblwarranty_receive.id NOT IN (SELECT id_warranty_receive FROM tblwarranty)');
            }
            else if($this->input->post('filterStatus') == 4) {
                array_push($where, 'AND tblwarranty_receive.id IN (SELECT id_warranty_receive FROM tblwarranty)');
            }
        }
        if($this->input->post('search_date')) {
            $data_start = explode(' - ', $this->input->post('search_date'));
            array_push($where, 'AND tblwarranty_receive.date BETWEEN "' . to_sql_date($data_start[0]) . '" and "' . to_sql_date($data_start[1]) . '"');
        }

        if($this->input->post('search_code')) {
            array_push($where, 'AND tblwarranty_receive.id = ' . $this->input->post('search_code'));
        }
        if($this->input->post('search_client')) {
            array_push($where, 'AND tblwarranty_receive.customer_id = '.$this->input->post('search_client'));
        }

        $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
            'tblwarranty_receive.staff_status',
            'tblwarranty_receive.not_new_by_staff'
        ));
        $output  = $result['output'];
        $rResult = $result['rResult'];
        $currentPage = $this->input->post('start');
        $currentall = $output['iTotalRecords'];
        foreach ($rResult as $r => $aRow) {
            $row = [];
            for ($i = 0 ; $i < count($aColumns) ; $i++) {
                $_data = $aRow[$aColumns[$i]];
                if ($aColumns[$i] == 'tblwarranty_receive.id') {
                    $not_new_by_staff = explode(',',$aRow['not_new_by_staff']);
                    if(!in_array(get_staff_user_id(), $not_new_by_staff) && $aRow['tblwarranty_receive.status'] == 0) {
                        $_data = ($currentall+1)-($currentPage+$r+1).' <span class="wap-new">new</span>';
                    }
                    else {
                        $_data = ($currentall+1)-($currentPage+$r+1);
                    }
                }
                else if ($aColumns[$i] == 'tblwarranty_receive.code') {
                    $_data = '<a onclick="view_warranty_receive('.$aRow['tblwarranty_receive.id'].');return false;">'.$aRow['tblwarranty_receive.code'].'</a>';
                }
                else if ($aColumns[$i] == 'tblwarranty_receive.date') {
                    $_data = _d($aRow['tblwarranty_receive.date']);
                    $arr = explode(" ", $_data);
                    $_data = $arr[0];
                }
                else if ($aColumns[$i] == 'tblwarranty_receive.status') {
                    if($aRow['tblwarranty_receive.status'] == 0) {
                        $_data = '<span class="pointer label label-danger po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="<button data-id=\''.$aRow['tblwarranty_receive.id'].'\' data-status=\'1\' class=\'btn btn-success js-status\'>'.lang('approve').'</button><button class=\'btn btn-default po-close\'>'.lang('close').'</button>">'.lang('dont_approve').'</span>';
                    }
                    else {
                        $_data = '<span class="pointer label label-success po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="<button data-id=\''.$aRow['tblwarranty_receive.id'].'\' data-status=\'0\' class=\'btn btn-danger js-status\'>'.lang('dont_approve').'</button><button class=\'btn btn-default po-close\'>'.lang('close').'</button>">'.lang('tnh_approved').'</span>';
                        $_data .= '<br><br>';
                        $_data .= _l('tnh_user_agree') . ': ' . get_staff_full_name($aRow['staff_status']);
                    }
                }
                else if ($aColumns[$i] == '7') {
                    $checkExists = get_table_where('tblwarranty',array('id_warranty_receive'=>$aRow['tblwarranty_receive.id']),'','row');
                    if($checkExists) {
                        $_data = '<span class="label label-success">Đã tạo phiếu bảo hành</span>
                                <br><br>
                                <span>Mã phiếu: </span><a href="'.admin_url('warranty/detail/'.$aRow['tblwarranty_receive.id'].'/'.$checkExists->id).'">'.$checkExists->code.'</a>';
                    }
                    else {
                        $_data = '<span class="label label-warning">Chưa xử lý</span>';
                    }
                }
                else if ($aColumns[$i] == '8') {
                    $_data = '';
                    $_outputStatus = '<div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">'._l('action').'
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu h_right">';
                    $checkExists = get_table_where('tblwarranty',array('id_warranty_receive'=>$aRow['tblwarranty_receive.id']));
                    if($aRow['tblwarranty_receive.status'] == 1) {
                        if(!$checkExists) {
                            $_outputStatus .=       '<li><a href="'.admin_url('warranty/detail/'.$aRow['tblwarranty_receive.id']).'"><i class="fa fa-legal"></i> '._l('warranty_main_create').'</a></li>';
                        }
                    }
                    if(!$checkExists) {
                        $_outputStatus .=       '<li><a onclick="edit('.$aRow['tblwarranty_receive.id'].');return false;"><i class="fa fa-edit"></i> '._l('edit').'</a></li>';
                    }
                    $_outputStatus .=       '<li><a href="'.admin_url('warranty/print_pdf/'.$aRow['tblwarranty_receive.id']).'" target="_blank"><i class="fa fa-print"></i> '._l('print_p').'</a></li>';
                    $_outputStatus .=       '<li><a onclick="delete_warranty('.$aRow['tblwarranty_receive.id'].');return false;" class="delete-remind"><i class="fa fa-remove"></i> '._l('delete').'</a></li>';
                    $_outputStatus .=   '</ul>';
                    $_outputStatus .= '</div>';
                    $_data = $_outputStatus;
                }
                if ($aColumns[$i] == 'tblwarranty_receive.staff_create') {
                    $_data = get_staff_full_name($aRow['tblwarranty_receive.staff_create']);
                }
                $row[] = $_data;
                $not_new_by_staff = explode(',',$aRow['not_new_by_staff']);
                if(!in_array(get_staff_user_id(), $not_new_by_staff) && $aRow['tblwarranty_receive.status'] == 0) {
                    $row['DT_RowClass'] = 'alert-new';
                }
            }

            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function getDataEdit($id='')
    {
        $data = get_table_where('tblwarranty_receive',array('id'=>$id),'','row');
        $keyMain = 0;
        $dataResults['results'][0]['code'] = $data->code;
        $dataResults['results'][0]['date'] = _dt($data->date);
        $dataResults['results'][0]['customer_id'] = 'customers__'.$data->customer_id;
        $dataResults['results'][0]['name_of_machine'] = $data->name_of_machine;
        $dataResults['results'][0]['service_type'] = $data->service_type;

        $getSeriesItem = get_table_where('tblwarranty_items',array('id_warranty_receive'=>$id));
        $dataResults['results'][0]['seriesItem'] = array();
        foreach ($getSeriesItem as $key => $value) {
            $getSeries = get_table_where('tblseries',array('id'=>$value['id_series']),'','row');

            $dataResults['results'][0]['seriesItem'][$keyMain]['id_series'] = $value['id_series'];
            $dataResults['results'][0]['seriesItem'][$keyMain]['name_item'] = $getSeries->name_item .' ('. $getSeries->code_item .')'. '<br><span class="label label-warning">'.($getSeries->type_item == 'products' ? _l('products') : _l('ch_items')).'</span>';

            $img = '<img width="50" src="'.base_url('assets/images/tnh/no_image.png').'">';
            if($getSeries->type_item == "products") {
                $getImage = get_table_where('tbl_products',array('id'=>$getSeries->id_item),'','row');
                if($getImage && !empty($getImage->images)) {
                    $img = '<img width="50" src="'.base_url('uploads/products/'.$getImage->images).'">';
                }
            }
            else if($getSeries->type_item == "items") {
                $getImage = get_table_where('tblitems',array('id'=>$getSeries->id_item),'','row');
                if($getImage && !empty($getImage->avatar)) {
                    $img = '<img width="50" src="'.base_url($getImage->images).'">';
                }
            }
            $dataResults['results'][0]['seriesItem'][$keyMain]['img_item'] = $img;

            $get_item_warranty = get_table_where('tblwarranty_items',array('id_series'=>$value['id_series']));
            $dem_temp = 0;
            $date_temp = '';
            foreach ($get_item_warranty as $key => $value) {
                $get_warranty = get_table_where('tblwarranty',array('id_warranty_receive'=>$value['id_warranty_receive']),'','row');
                if($get_warranty) {
                    $dem_temp++;
                    if($key == 0) {
                        $date_temp = $get_warranty->date_create;
                    }
                    if(strtotime($date_temp) < strtotime($get_warranty->date_create)) {
                        $date_temp = $get_warranty->date_create;
                    }
                }
            }

            $dataDate = explode(" ", $data->date);
            if($getSeries->type_item == 'products') {
                $date_export_warehouses = explode(" ", $getSeries->date_export_warehouses);
                $getDetailItem = get_table_where('tbl_products',array('id'=>$getSeries->id_item),'','row');
                $date_deadline = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date_export_warehouses[0])) . " +".$getDetailItem->warranty." month"));
                if(strtotime($date_deadline) > strtotime($dataDate[0])) {
                    $date_1 = $dataDate[0];
                    $date_2 = $date_deadline;
                    $days = (strtotime($date_2) - strtotime($date_1)) / (60 * 60 * 24);
                    $dataResults['results'][0]['seriesItem'][$keyMain]['deadline_warranty'] = $days.' Ngày';
                }
                else {
                    $dataResults['results'][0]['seriesItem'][$keyMain]['deadline_warranty'] = 'Hết thời gian';
                }
            }
            else {
                $date_export_warehouses = explode(" ", $getSeries->date_export_warehouses);
                $getDetailItem = get_table_where('tblitems',array('id'=>$getSeries->id_item),'','row');
                $date_deadline = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date_export_warehouses[0])) . " +".$getDetailItem->warranty." month"));
                if(strtotime($date_deadline) > strtotime($dataDate[0])) {
                    $date_1 = $dataDate[0];
                    $date_2 = $date_deadline;
                    $days = (strtotime($date_2) - strtotime($date_1)) / (60 * 60 * 24);
                    $dataResults['results'][0]['seriesItem'][$keyMain]['deadline_warranty'] = $days.' Ngày';
                }
                else {
                    $dataResults['results'][0]['seriesItem'][$keyMain]['deadline_warranty'] = 'Hết thời gian';
                }
            }

            $dataResults['results'][0]['seriesItem'][$keyMain]['strCount'] = '<div class="text-center">
                                    <span class="label label-success">'.$dem_temp.' lần bảo hành</span>
                                    <br><span>'.($dem_temp > 0 ? _l('date_warranty_nearest').': '._d($date_temp) : '').'</span>
                                </div>';
            $keyMain++;
        }
        echo json_encode($dataResults);
    }

    public function add($id = '')
    {
        $data = $this->input->post();
        $customer_id = explode("__", $data['customer_id']);
        if($id == "") {
            $in = array(
                'code' => $data['code'],
                'date' => to_sql_date($data['date'], true),
                'customer_id' => $customer_id[1],
                'name_of_machine' => $data['name_of_machine'],
                'service_type' => $data['service_type'],
                'date_create' => date('Y-m-d'),
                'staff_create' => get_staff_user_id()
            );
            if(isset($data['select_series'])) {
                $checkItem = 0;
                foreach ($data['select_series'] as $key => $value) {
                    if($value['id_series'] == "") {
                        continue;
                    }
                    else {
                        $checkItem++;
                    }
                }
                if($checkItem > 0) {
                    $result = $this->db->insert('tblwarranty_receive',$in);
                    $insert_id = $this->db->insert_id();
                    if($result) {
                        //update stt
                        $old = get_table_where('tblwarranty_number',array(),'','row')->warranty_receive;
                        $number = $old + 1;
                        if($number >= 1000) {
                            $number = 1;
                        }
                        $this->db->set('warranty_receive', $number);
                        $this->db->update('tblwarranty_number');
                        //end
                        if(isset($data['select_series'])) {
                            foreach ($data['select_series'] as $key => $value) {
                                if($value['id_series'] == "") {
                                    continue;
                                }
                                else {
                                    $inSeries = array(
                                        'id_warranty_receive' => $insert_id,
                                        'id_series' => $value['id_series']
                                    );
                                    $this->db->insert('tblwarranty_items',$inSeries);
                                }
                            }
                        }
                        echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('add_warranty_success')));
                    }
                    else {
                        echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('add_warranty_false')));
                    }
                }
                else {
                    echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('item_is_empty')));
                }
            }
            else {
                echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('item_is_empty')));
            }
        }
        else {
            $in = array(
                'code' => $data['code'],
                'date' => to_sql_date($data['date'], true),
                'customer_id' => $customer_id[1],
                'name_of_machine' => $data['name_of_machine'],
                'service_type' => $data['service_type']
            );
            if(isset($data['select_series'])) {
                $checkItem = 0;
                foreach ($data['select_series'] as $key => $value) {
                    if($value['id_series'] == "") {
                        continue;
                    }
                    else {
                        $checkItem++;
                    }
                }
                if($checkItem > 0) {
                    $this->db->where('id',$id);
                    $result = $this->db->update('tblwarranty_receive',$in);
                    if($result) {
                        $arrID = array();
                        if(isset($data['select_series'])) {
                            foreach ($data['select_series'] as $key => $value) {
                                if($value['id_series'] == "") {
                                    continue;
                                }
                                else {
                                    $checkExists = get_table_where('tblwarranty_items',array('id_series'=>$value['id_series'], 'id_warranty_receive'=>$id),'','row');
                                    $inSeries = array(
                                        'id_warranty_receive' => $id,
                                        'id_series' => $value['id_series']
                                    );
                                    if(!$checkExists) {
                                        $this->db->insert('tblwarranty_items',$inSeries);
                                        $insert_id = $this->db->insert_id();
                                        $arrID[] = $insert_id;
                                    }
                                    else {
                                        $arrID[] = $checkExists->id;
                                    }
                                }
                            }
                        }
                        if(count($arrID) > 0) {
                            $this->db->where_not_in('id',$arrID);
                            $this->db->where('id_warranty_receive',$id);
                            $this->db->delete('tblwarranty_items');
                        }
                        else {
                            $this->db->where('id_warranty_receive',$id);
                            $this->db->delete('tblwarranty_items');
                        }
                        echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('edit_warranty_success')));
                    }
                    else {
                        echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('edit_warranty_false')));
                    }
                }
                else {
                    echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('item_is_empty')));
                }
            }
            else {
                echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('item_is_empty')));
            }
        }
    }

    public function update_status($id = '', $status = '')
    {
        $checkExists = get_table_where('tblwarranty',array('id_warranty_receive'=>$id));
        if($checkExists) {
            echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('not_change_status')));die;
        }
        $in = array(
            'status' => $status,
            'date_status' => date('Y-m-d H:i:s'),
            'staff_status' => get_staff_user_id()
        );
        $this->db->where('id', $id);
        $this->db->update('tblwarranty_receive', $in);
        echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('done_change_status')));die;
    }

    public function detail($id = '', $id_edit = '')
    {
        $dataMain = get_table_where('tblwarranty_receive',array('id'=>$id),'','row');
        $date = _d($dataMain->date);
        $arr = explode(" ", $date);
        $dataMain->date = $arr[0];
        $data['dataMain'] = $dataMain;
        $data['client'] = get_table_where('tblclients', array('userid'=>$dataMain->customer_id),'','row');
        $dataITem = get_table_where('tblwarranty_items', array('id_warranty_receive'=>$id));
        foreach ($dataITem as $key => $value) {
            $getSeries = get_table_where('tblseries',array('id'=>$value['id_series']),'','row');

            $data['dataITem'][$key]['id_warranty_item'] = $value['id'];
            $data['dataITem'][$key]['id_series'] = $value['id_series'];
            $data['dataITem'][$key]['series'] = $getSeries->series;
            $data['dataITem'][$key]['code_item'] = $getSeries->code_item . '<br><span class="label label-warning">'.($getSeries->type_item == 'products' ? _l('products') : _l('ch_items')).'</span>';
            $data['dataITem'][$key]['name_item'] = $getSeries->name_item;

            $img = base_url('assets/images/tnh/no_image.png');
            if($getSeries->type_item == "products") {
                $getImage = get_table_where('tbl_products',array('id'=>$getSeries->id_item),'','row');
                if($getImage && !empty($getImage->images)) {
                    $img = base_url('uploads/products/'.$getImage->images);
                }
            }
            else if($getSeries->type_item == "items") {
                $getImage = get_table_where('tblitems',array('id'=>$getSeries->id_item),'','row');
                if($getImage && !empty($getImage->avatar)) {
                    $img = base_url($getImage->images);
                }
            }
            $data['dataITem'][$key]['img_item'] = $img;

            $dataMainDate = explode(" ", $dataMain->date);
            if($getSeries->type_item == 'products') {
                $date_export_warehouses = explode(" ", $getSeries->date_export_warehouses);
                $getDetailItem = get_table_where('tbl_products',array('id'=>$getSeries->id_item),'','row');
                $data['dataITem'][$key]['month_warranty'] = $getDetailItem->warranty.' Tháng';

                $date_deadline = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date_export_warehouses[0])) . " +".$getDetailItem->warranty." month"));
                if(strtotime($date_deadline) > strtotime(to_sql_date($dataMain->date, true))) {
                    $date_1 = to_sql_date($dataMain->date);
                    $date_2 = $date_deadline;
                    $days = (strtotime($date_2) - strtotime($date_1)) / (60 * 60 * 24);
                    $data['dataITem'][$key]['deadline_warranty'] = $days.' Ngày';
                }
                else {
                    $data['dataITem'][$key]['deadline_warranty'] = 'Hết thời gian';
                }
            }
            else {
                $date_export_warehouses = explode(" ", $getSeries->date_export_warehouses);
                $getDetailItem = get_table_where('tblitems',array('id'=>$getSeries->id_item),'','row');
                $data['dataITem'][$key]['month_warranty'] = $getDetailItem->warranty.' Tháng';

                $date_deadline = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date_export_warehouses[0])) . " +".$getDetailItem->warranty." month"));
                if(strtotime($date_deadline) > strtotime(to_sql_date($dataMain->date))) {
                    $date_1 = to_sql_date($dataMain->date);
                    $date_2 = $date_deadline;
                    $days = (strtotime($date_2) - strtotime($date_1)) / (60 * 60 * 24);
                    $data['dataITem'][$key]['deadline_warranty'] = $days.' Ngày';
                }
                else {
                    $data['dataITem'][$key]['deadline_warranty'] = 'Hết thời gian';
                }
            }

            //vị trí kho mặc định
            if(empty($value['warehouse_localtion'])) {
                $getData = get_table_where('tbllocaltion_warehouses',array('warehouse'=>14, 'status_default'=>1),'','row');
                if($getData) {
                    $data['dataITem'][$key]['localtion_warehouse'] = $getData->id;
                }
                else {
                    $data['dataITem'][$key]['localtion_warehouse'] = '';
                }
            }
            else {
                $data['dataITem'][$key]['localtion_warehouse'] = $value['warehouse_localtion'];
            }
            //end
        }

        $this->db->select('tblstaff.staffid as staffid, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname');
        $this->db->where('active', 1);
        $data['staff'] = $this->db->get('tblstaff')->result_array();
        $data['id'] = $id;
        if($id_edit == '') {
            $data['expenses'] = array();
            $data['supplies'] = array();
            $data['id_edit'] = '';
            $data['dataWarranty'] = array();

            $number = get_table_where('tblwarranty_number',array(),'','row')->warranty;
            $str_number = '';
            if($number < 10) {
                $str_number = '00'.$number;
            }
            else if($number < 100) {
                $str_number = '0'.$number;
            }
            else if($number < 1000) {
                $str_number = $number;
            }
            $data['code'] = 'BH-'.date('dmy').$str_number;
        }
        else if(is_numeric($id_edit)) {
            $data['dataWarranty'] = get_table_where('tblwarranty',array('id'=>$id_edit),'','row');
            if($data['dataWarranty']->status_done == 1) {
                set_alert('danger', _l('browsed_cannot_be_edited'));
                redirect(admin_url('warranty/list_warranty'));
            }
            $data['expenses'] = get_table_where('tblwarranty_expenses',array('id_warranty'=>$id_edit));
            $data['supplies'] = get_table_where('tblwarranty_supplies',array('id_warranty'=>$id_edit));
            $data['id_edit'] = $id_edit;
        }

        $data['costs'] = array();
        $this->costs_model->get_by_id(0,$data['costs']);
        $data['title'] = lang('warranty_detail');
        $this->load->view('detail', $data);
    }

    public function loadCosts()
    {
        $data = array();
        $this->costs_model->get_by_id(0,$data);
        echo json_encode($data);
    }

    public function searchSeries($id = false)
    {
        $data = [];
        $customer_id = $this->input->get('customer_id');
        if(!empty($customer_id)) {
            $arr = explode("__", $customer_id);
            $customer_id = $arr[1];
        }
        else {
            if($id) {
                $getCustomer = get_table_where('tblseries',array('id'=>$id),'','row');
                if(!empty($getCustomer->customer_id)) {
                    $customer_id = $getCustomer->customer_id;
                }
            }
        }
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');

        $this->db->select('tblseries.id as id, CONCAT(tblseries.series, " (", tblseries.code_item, ")") as text', false);
        $this->db->from('tblseries');
        $this->db->where('tblseries.id_customer', $customer_id);
        $this->db->group_start();
        $this->db->where('tblseries.id NOT IN (SELECT id_series FROM tblwarranty_items WHERE id_warranty_receive IN (SELECT id_warranty_receive FROM tblwarranty WHERE status_done = 0))');
        $this->db->where('tblseries.id NOT IN (SELECT id_series FROM tblwarranty_items WHERE id_warranty_receive NOT IN (SELECT id_warranty_receive FROM tblwarranty))');
        $this->db->group_end();
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tblseries.series', $term);
            $this->db->or_like('tblseries.code_item', $term);
            $this->db->or_like('tblseries.name_item', $term);
            $this->db->group_end();
        }
        $results = $this->db->get()->result_array();

        if($results) {
            $data['results'] = [
                [
                    'text' => lang('manager_product'), 'children' => $results
                ],
            ];
        }
        else {
            $data['results'] = [
                [
                    'text' => lang('not_results_found'), 'children' => ''
                ],
            ];
        }

        if ($id) {
            $row = get_table_where('tblseries',array('id'=>$id),'','row');
            if ($row) {
                $data['row'] = ['id' => $row->id, 'text' => $row->series.' ('.$row->code_item.')'];
            } else {
                $data['row'] = ['id' => 0, 'text' => 'Not found!'];
            }
        }
        echo json_encode($data);
    }

    public function getDetail()
    {
        $data = $this->input->post();
        $result = get_table_where('tblseries',array('id'=>$data['id']),'','row');
        $result->name_item = $result->name_item .' ('. $result->code_item .')'. '<br><span class="label label-warning">'.($result->type_item == 'products' ? _l('products') : _l('ch_items')).'</span>';

        $img = '<img width="50" src="'.base_url('assets/images/tnh/no_image.png').'">';
        if($result->type_item == "products") {
            $getImage = get_table_where('tbl_products',array('id'=>$result->id_item),'','row');
            if($getImage && !empty($getImage->images)) {
                $img = '<img width="50" src="'.base_url('uploads/products/'.$getImage->images).'">';
            }
        }
        else if($result->type_item == "items") {
            $getImage = get_table_where('tblitems',array('id'=>$result->id_item),'','row');
            if($getImage && !empty($getImage->avatar)) {
                $img = '<img width="50" src="'.base_url($getImage->images).'">';
            }
        }
        $result->img_item = $img;

        $get_item_warranty = get_table_where('tblwarranty_items',array('id_series'=>$data['id']));
        $dem_temp = 0;
        $date_temp = '';
        foreach ($get_item_warranty as $key => $value) {
            $get_warranty = get_table_where('tblwarranty',array('id_warranty_receive'=>$value['id_warranty_receive']),'','row');
            if($get_warranty) {
                $dem_temp++;
                if($key == 0) {
                    $date_temp = $get_warranty->date_create;
                }
                if(strtotime($date_temp) < strtotime($get_warranty->date_create)) {
                    $date_temp = $get_warranty->date_create;
                }
            }
        }

        if($result->type_item == 'products') {
            $date_export_warehouses = explode(" ", $result->date_export_warehouses);
            $getDetailItem = get_table_where('tbl_products',array('id'=>$result->id_item),'','row');
            $date_deadline = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date_export_warehouses[0])) . " +".$getDetailItem->warranty." month"));
            if(strtotime($date_deadline) > strtotime(date('Y-m-d'))) {
                $date_1 = date('Y-m-d');
                $date_2 = $date_deadline;
                $days = (strtotime($date_2) - strtotime($date_1)) / (60 * 60 * 24);
                $result->deadline_warranty = $days.' Ngày';
            }
            else {
                $result->deadline_warranty = 'Hết thời gian';
            }
        }
        else {
            $date_export_warehouses = explode(" ", $result->date_export_warehouses);
            $getDetailItem = get_table_where('tblitems',array('id'=>$result->id_item),'','row');
            $date_deadline = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date_export_warehouses[0])) . " +".$getDetailItem->warranty." month"));
            if(strtotime($date_deadline) > strtotime(date('Y-m-d'))) {
                $date_1 = date('Y-m-d');
                $date_2 = $date_deadline;
                $days = (strtotime($date_2) - strtotime($date_1)) / (60 * 60 * 24);
                $result->deadline_warranty = $days.' Ngày';
            }
            else {
                $result->deadline_warranty = 'Hết thời gian';
            }
        }

        $result->strCount = '<div class="text-center">
                                <span class="label label-success">'.$dem_temp.' lần bảo hành</span>
                                <br><span>'.($dem_temp > 0 ? _l('date_warranty_nearest').': '._d($date_temp) : '').'</span>
                            </div>';
        echo json_encode($result);
    }

    public function searchIssue($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');

        $this->db->select('tblissue.id as id, tblissue.name as text', false);
        $this->db->from('tblissue');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tblissue.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $results = $this->db->get()->result_array();

        if($results) {
            $data['results'] = [
                [
                    'text' => 'Vấn đề', 'children' => $results
                ],
            ];
        }
        else {
            $data['results'] = [
                [
                    'text' => lang('not_results_found'), 'children' => ''
                ],
            ];
        }

        if ($id) {
            $row = get_table_where('tblissue',array('id'=>$id),'','row');
            if ($row) {
                $data['row'] = ['id' => $row->id, 'text' => $row->name];
            } else {
                $data['row'] = ['id' => 0, 'text' => 'Not found!'];
            }
        }
        echo json_encode($data);
    }

    public function add_detail($id = '', $id_edit = '')
    {
        $data = $this->input->post();
        if($id_edit == '') {
            //add localtion warehouse
            if(!isset($data['series'])) {
                $data['series'] = array();
            }
            foreach ($data['series'] as $key => $value) {
                if(!$value['localtion_warehouse']) {
                    $value['localtion_warehouse'] = null;
                }
                $update_warranty_items = array (
                    'warehouse_localtion' => $value['localtion_warehouse']
                );
                $this->db->where('id_warranty_receive', $id);
                $this->db->where('id_series', $value['id_series']);
                $this->db->update('tblwarranty_items', $update_warranty_items);
            }
            //end

            //add tblwarranty_issue
                $this->load->library('upload');
                if(!isset($data['employees_id'])) {
                    $data['employees_id'] = array();
                }
                if($data['localtion_warranty'] != 1) {
                    $data['localtion_warehouse'] = null;
                }
                $update_warranty = array (
                    'id_warranty_receive' => $id,
                    'code' => $data['code'],
                    'employees_id' => implode(",", $data['employees_id']),
                    'localtion_warranty' => $data['localtion_warranty'],
                    'localtion_warehouse' => $data['localtion_warehouse'],
                    'date_create' => date('Y-m-d H:i:s'),
                    'staff_create' => get_staff_user_id()
                );
                $this->db->insert('tblwarranty', $update_warranty);
                $insert_id_warranty = $this->db->insert_id();
                //update stt
                $old = get_table_where('tblwarranty_number',array(),'','row')->warranty;
                $number = $old + 1;
                if($number >= 1000) {
                    $number = 1;
                }
                $this->db->set('warranty', $number);
                $this->db->update('tblwarranty_number');
                //end

                //bổ xung lưu kho
                    $getDetailMain = get_table_where('tblwarranty',array('id'=>$insert_id_warranty),'','row');
                    if($data['localtion_warranty'] == 1) {
                        $this->confirm_warehous($insert_id_warranty);
                    }
                //end

                if(!isset($data['item'])) {
                    $data['item'] = array();
                }
                foreach ($data['item'] as $key => $value) {
                    if($value['issue'] == "") {
                        continue;
                    }
                    $in = array(
                        'id_warranty' => $insert_id_warranty,
                        'id_warranty_item' => $value['id_warranty_item'],
                        'id_issue' => $value['issue'],
                        'solution' => $value['solution']
                    );
                    //insert
                    $this->db->insert('tblwarranty_issue', $in);
                    $insert_id_issue = $this->db->insert_id();

                    $fileCount = count($_FILES['file']['name'][$key]);
                    for ($i = 0; $i < $fileCount; $i++) {
                        if ($_FILES['file']['name'][$key][$i] != "") {
                            $_FILES['file_data']['name'] = $_FILES['file']['name'][$key][$i];
                            $_FILES['file_data']['type'] = $_FILES['file']['type'][$key][$i];
                            $_FILES['file_data']['tmp_name'] = $_FILES['file']['tmp_name'][$key][$i];
                            $_FILES['file_data']['error'] = $_FILES['file']['error'][$key][$i];
                            $_FILES['file_data']['size'] = $_FILES['file']['size'][$key][$i];

                            if (!file_exists($this->upload_path . $insert_id_issue)) {
                                mkdir($this->upload_path . $insert_id_issue);
                            }
                            $config['upload_path'] = $this->upload_path . $insert_id_issue . '/';
                            $config['allowed_types'] = $this->image_types_pdf;
                            $config['file_name'] = vn_to_str($_FILES['file']['name'][$key][$i]);
                            $config['overwrite'] = TRUE;
                            $config['max_filename'] = 2500;
                            $config['encrypt_name'] = false;
                            $this->upload->initialize($config);
                            $this->upload->do_upload('file_data');

                            $in_file = array(
                                'id_warranty_issue' => $insert_id_issue,
                                'staff_create' => get_staff_user_id(),
                                'date_create' => date('Y-m-d H:i:s'),
                                'name' => $_FILES['file']['name'][$key][$i],
                                'type' => $_FILES['file']['type'][$key][$i]
                            );
                            $this->db->insert('tblwarranty_file', $in_file);
                        }
                    }
                }
            //end
            //add tblwarranty_expenses
                if(!isset($data['expenses'])) {
                    $data['expenses'] = array();
                }
                foreach ($data['expenses'] as $key => $value) {
                    if($value['name'] == "") {
                        continue;
                    }
                    $in_expenses = array(
                        'id_warranty' => $insert_id_warranty,
                        'name' => $value['name'],
                        'type' => $value['type'],
                        'amount' => str_replace(",", "", $value['amount'])
                    );
                    //insert
                    $this->db->insert('tblwarranty_expenses', $in_expenses);
                }
            //end
            //add tblwarranty_supplies
                if(!isset($data['supplies'])) {
                    $data['supplies'] = array();
                }
                $arrID = array();
                foreach ($data['supplies'] as $key => $value) {
                    if($value['id_item'] == "") {
                        continue;
                    }
                    $str_to_arr = explode("_", $value['id_item']);
                    $total = 0;
                    if($value['type_amount'] == 2) {
                        $total = str_replace(",", "", $value['quantity']) * str_replace(",", "", $value['amount']);
                    }
                    $in_supplies = array(
                        'id_warranty' => $insert_id_warranty,
                        'type_item' => $str_to_arr[0],
                        'id_item' => $str_to_arr[1],
                        'quantity' => str_replace(",", "", $value['quantity']),
                        'type_amount' => $value['type_amount'],
                        'amount' => str_replace(",", "", $value['amount']),
                        'total' => $total,
                        'note' => $value['note']
                    );
                    $checkExists = get_table_where('tblwarranty_supplies',array('id_warranty'=>$insert_id_warranty, 'type_item'=>$str_to_arr[0], 'id_item'=>$str_to_arr[1]),'','row');
                    if(!$checkExists) {
                        $this->db->insert('tblwarranty_supplies', $in_supplies);
                        $insert_id = $this->db->insert_id();
                        $arrID[] = $insert_id;
                    }
                    else {
                        $this->db->where('id', $checkExists->id);
                        $this->db->update('tblwarranty_supplies', $in_supplies);
                        $arrID[] = $checkExists->id;
                    }
                }
                if(count($arrID) > 0) {
                    $this->db->where_not_in('id',$arrID);
                    $this->db->where('id_warranty',$insert_id_warranty);
                    $this->db->delete('tblwarranty_supplies');
                }
                else {
                    $this->db->where('id_warranty',$insert_id_warranty);
                    $this->db->delete('tblwarranty_supplies');
                }
            //end
        }
        else {
            $getDetailMain = get_table_where('tblwarranty',array('id'=>$id_edit),'','row');
            //add localtion warehouse
            if(!isset($data['series'])) {
                $data['series'] = array();
            }
            foreach ($data['series'] as $key => $value) {
                if(!$value['localtion_warehouse']) {
                    $value['localtion_warehouse'] = null;
                }
                $update_warranty_items = array (
                    'warehouse_localtion' => $value['localtion_warehouse']
                );
                $this->db->where('id_warranty_receive', $id);
                $this->db->where('id_series', $value['id_series']);
                $this->db->update('tblwarranty_items', $update_warranty_items);
            }
            //end
            //bổ xung lưu kho
                if($data['localtion_warranty'] == 1) {
                    if($getDetailMain && !empty($getDetailMain->warehouseman_id)) {
                        $warehouseman_id = $getDetailMain->warehouseman_id;
                        $this->confirm_warehous($id_edit, $warehouseman_id);
                        $this->confirm_warehous($id_edit);
                    }
                    else {
                        $this->confirm_warehous($id_edit);
                    }
                }
                if($data['localtion_warranty'] == 2) {
                    if($getDetailMain && !empty($getDetailMain->warehouseman_id)) {
                        $warehouseman_id = $getDetailMain->warehouseman_id;
                        $this->confirm_warehous($id_edit, $warehouseman_id);
                    }
                }
            //end
            if(!isset($data['employees_id'])) {
                $data['employees_id'] = array();
            }
            if($data['localtion_warranty'] != 1) {
                $data['localtion_warehouse'] = null;
            }
            $update_warranty = array(
                'employees_id' => implode(",", $data['employees_id']),
                'localtion_warranty' => $data['localtion_warranty'],
                'localtion_warehouse' => $data['localtion_warehouse'],
            );
            $this->db->where('id', $id_edit);
            $this->db->update('tblwarranty', $update_warranty);
            //add tblwarranty_issue
                $this->load->library('upload');
                if(!isset($data['item'])) {
                    $data['item'] = array();
                }

                $arrID_issue = array();
                foreach ($data['item'] as $key => $value) {
                    if($value['issue'] == "") {
                        continue;
                    }
                    $in = array(
                        'id_warranty' => $id_edit,
                        'id_warranty_item' => $value['id_warranty_item'],
                        'id_issue' => $value['issue'],
                        'solution' => $value['solution']
                    );
                    //insert
                    if(isset($value['id']) && $value['id'] > 0) {
                        $this->db->where('id', $value['id']);
                        $this->db->update('tblwarranty_issue', $in);
                        $arrID_issue[] = $value['id'];

                        $fileCount = count($_FILES['file']['name'][$key]);
                        for ($i = 0; $i < $fileCount; $i++) {
                            if ($_FILES['file']['name'][$key][$i] != "") {
                                $_FILES['file_data']['name'] = $_FILES['file']['name'][$key][$i];
                                $_FILES['file_data']['type'] = $_FILES['file']['type'][$key][$i];
                                $_FILES['file_data']['tmp_name'] = $_FILES['file']['tmp_name'][$key][$i];
                                $_FILES['file_data']['error'] = $_FILES['file']['error'][$key][$i];
                                $_FILES['file_data']['size'] = $_FILES['file']['size'][$key][$i];

                                if (!file_exists($this->upload_path . $value['id'])) {
                                    mkdir($this->upload_path . $value['id']);
                                }
                                $config['upload_path'] = $this->upload_path . $value['id'] . '/';
                                $config['allowed_types'] = $this->image_types_pdf;
                                $config['file_name'] = vn_to_str($_FILES['file']['name'][$key][$i]);
                                $config['overwrite'] = TRUE;
                                $config['max_filename'] = 2500;
                                $config['encrypt_name'] = false;
                                $this->upload->initialize($config);
                                $this->upload->do_upload('file_data');

                                $in_file = array(
                                    'id_warranty_issue' => $value['id'],
                                    'staff_create' => get_staff_user_id(),
                                    'date_create' => date('Y-m-d H:i:s'),
                                    'name' => $_FILES['file']['name'][$key][$i],
                                    'type' => $_FILES['file']['type'][$key][$i]
                                );
                                $this->db->insert('tblwarranty_file', $in_file);
                            }
                        }
                    }
                    else {
                        $this->db->insert('tblwarranty_issue', $in);
                        $insert_id_issue = $this->db->insert_id();
                        $arrID_issue[] = $insert_id_issue;

                        $fileCount = count($_FILES['file']['name'][$key]);
                        for ($i = 0; $i < $fileCount; $i++) {
                            if ($_FILES['file']['name'][$key][$i] != "") {
                                $_FILES['file_data']['name'] = $_FILES['file']['name'][$key][$i];
                                $_FILES['file_data']['type'] = $_FILES['file']['type'][$key][$i];
                                $_FILES['file_data']['tmp_name'] = $_FILES['file']['tmp_name'][$key][$i];
                                $_FILES['file_data']['error'] = $_FILES['file']['error'][$key][$i];
                                $_FILES['file_data']['size'] = $_FILES['file']['size'][$key][$i];

                                if (!file_exists($this->upload_path . $insert_id_issue)) {
                                    mkdir($this->upload_path . $insert_id_issue);
                                }
                                $config['upload_path'] = $this->upload_path . $insert_id_issue . '/';
                                $config['allowed_types'] = $this->image_types_pdf;
                                $config['file_name'] = vn_to_str($_FILES['file']['name'][$key][$i]);
                                $config['overwrite'] = TRUE;
                                $config['max_filename'] = 2500;
                                $config['encrypt_name'] = false;
                                $this->upload->initialize($config);
                                $this->upload->do_upload('file_data');

                                $in_file = array(
                                    'id_warranty_issue' => $insert_id_issue,
                                    'staff_create' => get_staff_user_id(),
                                    'date_create' => date('Y-m-d H:i:s'),
                                    'name' => $_FILES['file']['name'][$key][$i],
                                    'type' => $_FILES['file']['type'][$key][$i]
                                );
                                $this->db->insert('tblwarranty_file', $in_file);
                            }
                        }
                    }
                }
                if(count($arrID_issue) > 0) {
                    //delete image
                    $get_all_issue = get_table_where('tblwarranty_issue',array('id_warranty'=>$id_edit));
                    foreach ($get_all_issue as $key => $value) {
                        if(!in_array($value['id'], $arrID_issue)) {
                            $folder = 'modules/warranty/uploads/warranty/'.$value['id'];
                            $files = glob($folder . '/*');
                            foreach($files as $file) {
                                if(is_file($file)){
                                    unlink($file);
                                }
                            }

                            $this->db->where('id_warranty_issue', $value['id']);
                            $this->db->delete('tblwarranty_file');
                        }
                    }
                    //end

                    $this->db->where_not_in('id',$arrID_issue);
                    $this->db->where('id_warranty',$id_edit);
                    $this->db->delete('tblwarranty_issue');
                }
                else {
                    //delete image
                    $get_all_issue = get_table_where('tblwarranty_issue',array('id_warranty'=>$id_edit));
                    foreach ($get_all_issue as $key => $value) {
                        $folder = 'modules/warranty/uploads/warranty/'.$value['id'];
                        $files = glob($folder . '/*');
                        foreach($files as $file) {
                            if(is_file($file)){
                                unlink($file);
                            }
                        }

                        $this->db->where('id_warranty_issue', $value['id']);
                        $this->db->delete('tblwarranty_file');
                    }
                    //end

                    $this->db->where('id_warranty',$id_edit);
                    $this->db->delete('tblwarranty_issue');
                }
            //end
            //add tblwarranty_expenses
                if(!isset($data['expenses'])) {
                    $data['expenses'] = array();
                }
                $this->db->where('id_warranty', $id_edit);
                $this->db->delete('tblwarranty_expenses');
                foreach ($data['expenses'] as $key => $value) {
                    if($value['name'] == "") {
                        continue;
                    }
                    $in_expenses = array(
                        'id_warranty' => $id_edit,
                        'name' => $value['name'],
                        'type' => $value['type'],
                        'amount' => str_replace(",", "", $value['amount'])
                    );
                    //insert
                    $this->db->insert('tblwarranty_expenses', $in_expenses);
                }
            //end
            //add tblwarranty_supplies
                if(!isset($data['supplies'])) {
                    $data['supplies'] = array();
                }
                $arrID = array();
                foreach ($data['supplies'] as $key => $value) {
                    if($value['id_item'] == "") {
                        continue;
                    }
                    $str_to_arr = explode("_", $value['id_item']);
                    $total = 0;
                    if($value['type_amount'] == 2) {
                        $total = str_replace(",", "", $value['quantity']) * str_replace(",", "", $value['amount']);
                    }
                    $in_supplies = array(
                        'id_warranty' => $id_edit,
                        'type_item' => $str_to_arr[0],
                        'id_item' => $str_to_arr[1],
                        'quantity' => str_replace(",", "", $value['quantity']),
                        'type_amount' => $value['type_amount'],
                        'amount' => str_replace(",", "", $value['amount']),
                        'total' => $total,
                        'note' => $value['note']
                    );
                    $checkExists = get_table_where('tblwarranty_supplies',array('id_warranty'=>$id_edit, 'type_item'=>$str_to_arr[0], 'id_item'=>$str_to_arr[1]),'','row');
                    if(!$checkExists) {
                        $this->db->insert('tblwarranty_supplies', $in_supplies);
                        $insert_id = $this->db->insert_id();
                        $arrID[] = $insert_id;
                    }
                    else {
                        //ràng buộc k bé hơn sl đã xuất
                        if($checkExists->export_warehouse > $in_supplies['quantity']) {
                            $in_supplies['quantity'] = $checkExists->export_warehouse;
                            $in_supplies['total'] = $checkExists->export_warehouse * str_replace(",", "", $value['amount']);
                        }
                        //end
                        $this->db->where('id', $checkExists->id);
                        $this->db->update('tblwarranty_supplies', $in_supplies);
                        $arrID[] = $checkExists->id;
                    }
                }
                if(count($arrID) > 0) {
                    $this->db->where_not_in('id',$arrID);
                    $this->db->where('export_warehouse',0);
                    $this->db->where('id_warranty',$id_edit);
                    $this->db->delete('tblwarranty_supplies');
                }
                else {
                    $this->db->where('id_warranty',$id_edit);
                    $this->db->where('export_warehouse',0);
                    $this->db->delete('tblwarranty_supplies');
                }
            //end
        }
        set_alert('success', _l('cong_update_true'));
        if($id_edit == '') {
            redirect(admin_url('warranty/detail/' . $id .'/'. $insert_id_warranty));
        }
        else {
            redirect(admin_url('warranty/detail/' . $id .'/'. $id_edit));
        }
    }

    public function add_issue()
    {
        $data = $this->input->post();
        $in = array(
            'name' => $data['name_issue']
        );
        $result = $this->db->insert('tblissue',$in);
        if($result) {
            echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('add_issue_success')));
        }
        else {
            echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('add_issue_false')));
        }
    }

    public function searchSupplies($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');

        $this->db->select('CONCAT("materials", "_", tbl_materials.id) as id, CONCAT(tbl_materials.code, " (", tbl_materials.name, ")") as text', false);
        $this->db->from('tbl_materials');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_materials.code', $term);
            $this->db->or_like('tbl_materials.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $results_materials = $this->db->get()->result_array();

        $this->db->select('CONCAT("supplies", "_", tbl_tools_supplies.id) as id, CONCAT(tbl_tools_supplies.code, " (", tbl_tools_supplies.name, ")") as text', false);
        $this->db->from('tbl_tools_supplies');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_tools_supplies.code', $term);
            $this->db->or_like('tbl_tools_supplies.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $results_tools_supplies = $this->db->get()->result_array();

        if($results_materials || $results_tools_supplies) {
            $data['results'] = [
                [
                    'text' => _l('tnh_item_materials'), 'children' => $results_materials
                ],
                [
                    'text' => _l('tnh_tools_supplies'), 'children' => $results_tools_supplies
                ],
            ];
        }
        else {
            $data['results'] = [
                [
                    'text' => lang('not_results_found'), 'children' => ''
                ],
            ];
        }

        if ($id) {
            $arr = explode("_", $id);
            if($arr[0] == 'materials') {
                $getDetail = get_table_where('tbl_materials',array('id'=>$arr[1]),'','row');
                $data['row'] = ['id' => 'materials_'.$getDetail->id, 'text' => $getDetail->code.' ('.$getDetail->name.')'];
            }
            else if($arr[0] == 'supplies') {
                $getDetail = get_table_where('tbl_tools_supplies',array('id'=>$arr[1]),'','row');
                $data['row'] = ['id' => 'supplies_'.$getDetail->id, 'text' => $getDetail->code.' ('.$getDetail->name.')'];
            }
            else {
                $data['row'] = ['id' => 0, 'text' => 'Not found!'];
            }
        }
        echo json_encode($data);
    }

    public function getDetail_ItemSupplies()
    {
        $data = $this->input->post();
        $arr = explode("_", $data['id_item']);
        $type = $arr[0];
        $id = $arr[1];
        $result = array();
        $img = '<img width="50" src="'.base_url('assets/images/tnh/no_image.png').'">';
        if($type == 'materials') {
            $getDetail = get_table_where('tbl_materials',array('id'=>$id),'','row');
            $result[0]['name'] = $getDetail->name . '<br><span class="label label-warning">'._l('tnh_item_materials').'</span>';
            $result[0]['price'] = number_format($getDetail->price_sell);
            if($getDetail && !empty($getDetail->images)) {
                $img = '<img width="50" src="'.base_url('uploads/materials/'.$getDetail->images).'">';
            }
        }
        else if($type == 'supplies') {
            $getDetail = get_table_where('tbl_tools_supplies',array('id'=>$id),'','row');
            $result[0]['name'] = $getDetail->name . '<br><span class="label label-warning">'._l('tnh_tools_supplies').'</span>';
            $result[0]['price'] = number_format($getDetail->price_import);
            if($getDetail && !empty($getDetail->images)) {
                $img = '<img width="50" src="'.base_url('uploads/tools_supplies/'.$getDetail->images).'">';
            }
        }
        $result[0]['img_item'] = $img;
        echo json_encode($result);
    }

    public function searchType_amount($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');

        $results = array(
            array(
                'id' => 1,
                'text' => _l('type_support')
            ),
            array(
                'id' => 2,
                'text' => _l('type_price')
            )
        );

        if($results) {
            $data['results'] = [
                [
                    'text' => _l('ch_costs'), 'children' => $results
                ],
            ];
        }

        if ($id) {
            foreach ($results as $key => $value) {
                if($value['id'] == $id) {
                    $data['row'] = ['id' => $value['id'], 'text' => $value['text']];
                }
            }
        }
        echo json_encode($data);
    }

    public function list_warranty()
    {
        $data['title'] = _l('warranty_main');
        $this->load->view('manager_list', $data);
    }

    public function table_warranty_list()
    {
        $aColumns = [
            'tblwarranty.id',
            'tblwarranty.code',
            'tblwarranty_receive.code',
            'tblwarranty.date_create',
            'tblclients.company',
            '5',
            '6',
            '66',
            'tblwarranty.status',
            'tblwarranty.staff_create',
            '7',
            '8',
            '9'
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tblwarranty';

        $join         = array(
            'LEFT JOIN tblwarranty_receive on tblwarranty_receive.id = tblwarranty.id_warranty_receive',
            'LEFT JOIN tblclients on tblclients.userid = tblwarranty_receive.customer_id',
        );
        $where         = array();
        if($this->input->post()) {
            if($this->input->post('filterStatus') == 1) {
                array_push($where, 'AND tblwarranty.status = 0');
            }
            else if($this->input->post('filterStatus') == 2) {
                array_push($where, 'AND tblwarranty.status = 1');
            }
            else if($this->input->post('filterStatus') == 3) {
                array_push($where, 'AND tblwarranty.id != 0');
            }
            else if($this->input->post('filterStatus') == 4) {
                array_push($where, 'AND tblwarranty.id IN (SELECT id_warranty FROM tblwarranty_export_supplies)');
            }
            else if($this->input->post('filterStatus') == 5) {
                array_push($where, 'AND (tblwarranty.id IN (SELECT tblwarranty_export_supplies.id_warranty FROM tblwarranty_export_supplies WHERE tblwarranty_export_supplies.id_purchases IS NOT NULL) OR tblwarranty.id IN (SELECT tblwarranty_export_supplies.id_warranty FROM tblwarranty_export_supplies WHERE tblwarranty_export_supplies.id IN (SELECT tblexport_different.id_warranty_export_supplies FROM tblexport_different)))');
            }
            else if($this->input->post('filterStatus') == 6) {
                array_push($where, 'AND tblwarranty.id IN (SELECT tblwarranty_export_supplies.id_warranty FROM tblwarranty_export_supplies WHERE tblwarranty_export_supplies.id IN (SELECT tblexport_different.id_warranty_export_supplies FROM tblexport_different))');
            }
            else if($this->input->post('filterStatus') == 7) {
                array_push($where, 'AND tblwarranty.status_done = 1');
            }

            if($this->input->post('search_date')) {
                $data_start = explode(' - ', $this->input->post('search_date'));
                array_push($where, 'AND tblwarranty.date_create BETWEEN "' . to_sql_date($data_start[0]) . '" and "' . to_sql_date($data_start[1]) . '"');
            }

            if($this->input->post('search_code')) {
                array_push($where, 'AND tblwarranty.id = ' . $this->input->post('search_code'));
            }
            if($this->input->post('search_client')) {
                array_push($where, 'AND tblwarranty.id_warranty_receive IN (SELECT id FROM tblwarranty_receive WHERE customer_id = '.$this->input->post('search_client').')');
            }
        }
        $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
            'tblwarranty.not_new_by_staff',
            'tblwarranty.status_done',
            'tblwarranty.staff_status_done',
            'tblwarranty.staff_status',
            'tblwarranty.id_warranty_receive',
            'tblwarranty.localtion_warranty',
            'tblwarranty_receive.id as id_warranty_receive'
        ));
        $output  = $result['output'];
        $rResult = $result['rResult'];
        $currentPage = $this->input->post('start');
        $currentall = $output['iTotalRecords'];

        $footer_data['total1'] = 0;
        $footer_data['total2'] = 0;
        $footer_data['total3'] = 0;
        foreach ($rResult as $r => $aRow) {
            $row = [];
            for ($i = 0 ; $i < count($aColumns) ; $i++) {
                $_data = $aRow[$aColumns[$i]];
                if ($aColumns[$i] == 'tblwarranty.id') {
                    $not_new_by_staff = explode(',',$aRow['not_new_by_staff']);
                    if(!in_array(get_staff_user_id(), $not_new_by_staff) && $aRow['tblwarranty.status'] == 0) {
                        $_data = ($currentall+1)-($currentPage+$r+1).' <span class="wap-new">new</span>';
                    }
                    else {
                        $_data = ($currentall+1)-($currentPage+$r+1);
                    }
                }
                else if ($aColumns[$i] == 'tblwarranty.code') {
                    $_data = '<a onclick="view_warranty_list('.$aRow['tblwarranty.id'].');return false;">'.$aRow['tblwarranty.code'].'</a>';
                }
                else if ($aColumns[$i] == 'tblwarranty_receive.code') {
                    $_data = '<a onclick="view_warranty_receive('.$aRow['id_warranty_receive'].');return false;">'.$aRow['tblwarranty_receive.code'].'</a>';
                }
                else if ($aColumns[$i] == 'tblwarranty.date_create') {
                    $_data = _dt($aRow['tblwarranty.date_create']);
                }
                else if ($aColumns[$i] == 'tblwarranty.staff_create') {
                    $_data = get_staff_full_name($aRow['tblwarranty.staff_create']);
                }
                else if ($aColumns[$i] == 'tblwarranty.status') {
                    if($aRow['tblwarranty.status'] == 0) {
                        $_data = '<span class="pointer label label-danger po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="<button data-id=\''.$aRow['tblwarranty.id'].'\' data-status=\'1\' class=\'btn btn-success js-status\'>'.lang('approve').'</button><button class=\'btn btn-default po-close\'>'.lang('close').'</button>">'.lang('dont_approve').'</span>';
                    }
                    else {
                        $_data = '<span class="pointer label label-success po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="<button data-id=\''.$aRow['tblwarranty.id'].'\' data-status=\'0\' class=\'btn btn-danger js-status\'>'.lang('dont_approve').'</button><button class=\'btn btn-default po-close\'>'.lang('close').'</button>">'.lang('tnh_approved').'</span>';
                        $_data .= '<br><br>';
                        $_data .= _l('tnh_user_agree') . ': ' . get_staff_full_name($aRow['staff_status']);
                    }
                }
                else if ($aColumns[$i] == '5') {
                    $expenses = get_table_where('tblwarranty_expenses',array('id_warranty'=>$aRow['tblwarranty.id']));                   
                    $total1 = 0;
                    foreach ($expenses as $key => $value) {
                        $total1 += $value['amount'];
                    }
                    $footer_data['total1'] += $total1;
                    $_data = '<div class="text-right">'.number_format($total1).'</div>';
                }
                else if ($aColumns[$i] == '6') {
                    $supplies = get_table_where('tblwarranty_supplies',array('id_warranty'=>$aRow['tblwarranty.id']));                    
                    $total2 = 0;
                    foreach ($supplies as $key => $value) {
                        $total2 += $value['amount'] * $value['quantity'];
                    }
                    $footer_data['total2'] += $total2;
                    $_data = '<div class="text-right">'.number_format($total2).'</div>';
                }
                else if ($aColumns[$i] == '66') {
                    $totalAll = $total1 + $total2;
                    $footer_data['total3'] += $totalAll;
                    $_data = '<div class="text-right">'.number_format($totalAll).'</div>';
                }
                else if ($aColumns[$i] == '7') {
                    $_data = '<div class="wrap-container-process">';
                    $_data .= '<div class="wrap-content-process active">
                                <div class="wrap-step-process line"></div>
                                <div class="wrap-title-process">
                                    '._l('warranty_process_create').'
                                </div>
                            </div>';
                    $check_export_supplies = get_table_where('tblwarranty_export_supplies',array('id_warranty'=>$aRow['tblwarranty.id']),'','row');
                    if($check_export_supplies) {
                        $check_export_different = get_table_where('tblexport_different',array('id_warranty_export_supplies'=>$check_export_supplies->id),'','row');
                    }
                    $getAllItem = get_table_where('tblwarranty_supplies',array('id_warranty'=>$aRow['tblwarranty.id']));
                    //check xuất kho
                    $checkExportWarehouse = true;
                    foreach ($getAllItem as $keyAllItem => $valueAllItem) {
                        if($valueAllItem['quantity'] > $valueAllItem['export_warehouse']) {
                            $checkExportWarehouse = false;
                            break;
                        }
                    }
                    if(!$getAllItem) {
                        $checkExportWarehouse = false;
                    }
                    //end
                    $_data .= '<div class="wrap-content-process '.($check_export_supplies ? 'active' : '').'">
                                <div class="wrap-step-process line"></div>
                                <div class="wrap-title-process">
                                    '._l('warranty_process_export_supplies').'
                                </div>
                                <br>
                                <div class="wrap-title-process">
                                    <span class="pointer label label-success" onclick="view_export_supplies('.($check_export_supplies ? $check_export_supplies->id : '').'); return fasle;">'.($check_export_supplies ? $check_export_supplies->code : '').'</span>
                                </div>
                            </div>';
                    $_data .= '<div class="wrap-content-process '.($check_export_supplies && !empty($check_export_supplies->id_purchases) ? 'active' : '').' '.($checkExportWarehouse == true ? 'active' : '').'">
                                <div class="wrap-step-process line"></div>
                                <div class="wrap-title-process">
                                    '._l('warranty_process_purchases').'
                                </div>
                            </div>';
                    $_data .= '<div class="wrap-content-process '.($checkExportWarehouse == true ? 'active' : '').'">
                                <div class="wrap-step-process line"></div>
                                <div class="wrap-title-process">
                                    '._l('warranty_process_export_warehouse').'
                                </div>
                            </div>';
                    $_data .= '<div class="wrap-content-process '.($checkExportWarehouse == true && $aRow['status_done'] == 1 ? 'active' : '').'">
                                <div class="wrap-step-process"></div>
                                <div class="wrap-title-process">
                                    '._l('warranty_process_done').'
                                </div>';
                    if($checkExportWarehouse == true && $aRow['status_done'] == 0) {
                        $_data .= '<div class="wrap-title-process">
                                        <span class="btn btn-success" onclick="update_process_done('.$aRow['tblwarranty.id'].', '.$aRow['localtion_warranty'].'); return false;">'._l('warranty_process_done').'</span>
                                    </div>';
                    }
                    else if($checkExportWarehouse == true && $aRow['status_done'] == 1) {
                        $_data .= '<div class="wrap-title-process">
                                        <span>'.get_staff_full_name($aRow['staff_status_done']).'</span>
                                        <br>
                                        <span class="text-danger">Trả khách</span>
                                    </div>';
                    }
                    else if($checkExportWarehouse == true && $aRow['status_done'] == 2) {
                        $_data .= '<div class="wrap-title-process">
                                        <span class="text-danger">Chờ duyệt kho</span>
                                    </div>';
                    }
                    // $aRow['status_done'] == 3 : thu hồi -> hoàn thành nhưng k cho bảo hành nữa
                    else if($checkExportWarehouse == true && $aRow['status_done'] == 3) {
                        $_data .= '<div class="wrap-title-process">
                                        <span>'.get_staff_full_name($aRow['staff_status_done']).'</span>
                                        <br>
                                        <span class="text-danger">Thu hồi</span>
                                    </div>';
                    }
                    $_data .= '</div>';
                    $_data .= '<div class="clearfix"></div>';
                    $_data .= '</div>';
                }
                else if ($aColumns[$i] == '8') {
                    $_data = '';
                    if($checkExportWarehouse == true && ($aRow['status_done'] == 1 || $aRow['status_done'] == 3)) {
                        $get_evaluate = get_table_where('tblwarranty_evaluate',array('id_warranty'=>$aRow['tblwarranty.id']),'','row');
                        if(!$get_evaluate) {
                            $_data = '<button class="btn btn-primary" onclick="add_evaluate('.$aRow['tblwarranty.id'].'); return false;">'._l('evaluate').'</button>';
                        }
                        else {
                            $_data = '<button class="btn btn-primary" onclick="edit_evaluate('.$aRow['tblwarranty.id'].'); return false;">'._l('update_evaluate').'</button>';
                        }
                    }
                }
                else if ($aColumns[$i] == '9') {
                    $_data = '';
                    $_outputStatus = '<div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">'._l('action').'
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu h_right">';
                    if($aRow['tblwarranty.status'] == 1) {
                        $checkExists = get_table_where('tblwarranty_export_supplies',array('id_warranty'=>$aRow['tblwarranty.id']),'','row');
                        if(!$checkExists) {
                            $_outputStatus .=   '<li><a onclick="add_export_supplies('.$aRow['tblwarranty.id'].'); return false;"><i class="fa fa-plus"></i> '._l('add_export_supplies').'</a></li>';
                        }
                    }
                    
                    if($aRow['status_done'] == 0) {
                        $_outputStatus .=       '<li><a href="'.admin_url('warranty/detail/'.$aRow['id_warranty_receive'].'/'.$aRow['tblwarranty.id']).'"><i class="fa fa-edit"></i> '._l('edit').'</a></li>';
                        $_outputStatus .=       '<li><a onclick="additional_supplies('.$aRow['tblwarranty.id'].'); return false;"><i class="fa fa-plus"></i> '._l('additional_supplies').'</a></li>';
                    }
                    $_outputStatus .=       '<li><a href="'.admin_url('warranty/print_pdf_warranty_issue/'.$aRow['tblwarranty.id']).'" target="_blank"><i class="fa fa-print"></i> '._l('In phiếu bảo hành').'</a></li>';
                    $_outputStatus .=       '<li><a href="'.admin_url('warranty/print_pdf_warranty_amount/'.$aRow['tblwarranty.id']).'" target="_blank"><i class="fa fa-print"></i> '._l('In phiếu chi phí').'</a></li>';
                    $_outputStatus .=       '<li><a href="'.admin_url('warranty/print_pdf_warranty_supplies/'.$aRow['tblwarranty.id']).'" target="_blank"><i class="fa fa-print"></i> '._l('In phiếu vật tư').'</a></li>';
                    $_outputStatus .=       '<li><a onclick="delete_warranty_list('.$aRow['tblwarranty.id'].');return false;" class="delete-remind"><i class="fa fa-remove"></i> '._l('delete').'</a></li>';
                    $_outputStatus .=   '</ul>';
                    $_outputStatus .= '</div>';
                    $_data = $_outputStatus;
                }
                $row[] = $_data;

                $not_new_by_staff = explode(',',$aRow['not_new_by_staff']);
                if(!in_array(get_staff_user_id(), $not_new_by_staff) && $aRow['tblwarranty.status'] == 0) {
                    $row['DT_RowClass'] = 'alert-new';
                }
            }

            $output['aaData'][] = $row;
        }
        $output['sums'] = $footer_data;
        foreach ($footer_data as $key => $total) {
            $footer_data[$key] = number_format($total);
        }
        $output['sums'] = $footer_data;
        echo json_encode($output);
    }

    public function update_status_list($id = '', $status = '')
    {
        $checkExists = get_table_where('tblwarranty_export_supplies',array('id_warranty'=>$id),'','row');
        if($checkExists) {
            echo json_encode(array('success' => true, 'alert_type' => 'danger', 'message' => _l('Đã tồn tại phiếu đề nghị xuất vật tư, không thể thay đổi trạng thái')));die;
        }

        $in = array(
            'status' => $status,
            'date_status' => date('Y-m-d H:i:s'),
            'staff_status' => get_staff_user_id()
        );
        $this->db->where('id', $id);
        $this->db->update('tblwarranty', $in);
        echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('cong_change_status_true')));die;
    }

    public function getCode()
    {
        $data = array();
        $number = get_table_where('tblwarranty_number',array(),'','row')->warranty_receive;
        $str_number = '';
        if($number < 10) {
            $str_number = '00'.$number;
        }
        else if($number < 100) {
            $str_number = '0'.$number;
        }
        else if($number < 1000) {
            $str_number = $number;
        }
        $data['code'] = 'TNBH-'.date('dmy').$str_number;
        $data['date'] = date('d/m/Y H:i');
        echo json_encode($data);
    }

    public function searchContact($id = false)
    {
        $data = [];
        $customer_id = $this->input->get('customer_id');
        if(!empty($customer_id)) {
            $arr = explode("__", $customer_id);
            $customer_id = $arr[1];
        }
        else {
            $customer_id = 0;
        }
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');

        $this->db->select('tblcontacts.id as id, CONCAT(tblcontacts.firstname, " ", COALESCE(tblcontacts.lastname, " ")) as text', false);
        $this->db->from('tblcontacts');
        $this->db->where('tblcontacts.userid', $customer_id);
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('CONCAT(tblcontacts.firstname, " ", tblcontacts.lastname)', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $results = $this->db->get()->result_array();

        if($results) {
            $data['results'] = [
                [
                    'text' => lang('contact_name'), 'children' => $results
                ],
            ];
        }
        else {
            $data['results'] = [
                [
                    'text' => lang('not_results_found'), 'children' => ''
                ],
            ];
        }

        if ($id) {
            $row = get_table_where('tblcontacts',array('id'=>$id),'','row');
            if ($row) {
                $data['row'] = ['id' => $row->id, 'text' => $row->firstname.(!empty($row->lastname) ? ' '.$row->lastname : '')];
            } else {
                $data['row'] = ['id' => 0, 'text' => 'Not found!'];
            }
        }
        echo json_encode($data);
    }

    public function delete_warranty($id='')
    {
        $checkExists = get_table_where('tblwarranty',array('id_warranty_receive'=>$id));
        if($checkExists) {
            echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('isset_warranty')));
        }
        else {
            $this->db->where('id', $id);
            $this->db->delete('tblwarranty_receive');

            $this->db->where('id_warranty_receive', $id);
            $this->db->delete('tblwarranty_items');

            echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('ch_delete_successfuly')));
        }
    }

    public function delete_warranty_list($id='')
    {
        $dataWarranty = get_table_where('tblwarranty',array('id'=>$id),'','row');
        if($dataWarranty->status == 1) {
            echo json_encode(array('success' => true, 'alert_type' => 'danger', 'message' => _l('browsed_cannot_be_deleted')));die;
        }

        $this->db->where('id', $id);
        $this->db->delete('tblwarranty');

        $this->db->where('id_warranty', $id);
        $this->db->delete('tblwarranty_expenses');

        //delete image
        $get_all_issue = get_table_where('tblwarranty_issue',array('id_warranty'=>$id));
        foreach ($get_all_issue as $key => $value) {
            $folder = 'uploads/warranty/'.$value['id'];
            $files = glob($folder . '/*');
            foreach($files as $file) {
                if(is_file($file)){
                    unlink($file);
                }
            }

            $this->db->where('id_warranty_issue', $value['id']);
            $this->db->delete('tblwarranty_file');
        }
        //end

        $this->db->where('id_warranty', $id);
        $this->db->delete('tblwarranty_issue');

        $this->db->where('id_warranty', $id);
        $this->db->delete('tblwarranty_supplies');

        echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('ch_delete_successfuly')));
    }

    public function delete_export_supplies($id='')
    {
        $check_export_supplies = get_table_where('tblwarranty_export_supplies',array('id'=>$id),'','row');
        $check_export_different = get_table_where('tblexport_different',array('id_warranty_export_supplies'=>$check_export_supplies->id),'','row');

        if($check_export_supplies && !empty($check_export_supplies->id_purchases)) {
            echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('Đã tồn tại phiếu [YCMH], không thể xóa!')));
        }
        else if($check_export_different && !empty($check_export_different->id_warranty_export_supplies)) {
            echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('Đã tồn tại phiếu [Xuất Kho Khác], không thể xóa!')));
        }
        else {
            $this->db->where('id', $id);
            $this->db->delete('tblwarranty_export_supplies');

            echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('ch_delete_successfuly')));
        }
    }

    public function getView_warranty_receive($id='')
    {
        $data = get_table_where('tblwarranty_receive',array('id'=>$id),'','row');
        $keyMain = 0;
        $dataResults['results'][0]['code'] = $data->code;
        $dataResults['results'][0]['date'] = _dt($data->date);
        $dataResults['results'][0]['customer_id'] = get_table_where('tblclients',array('userid'=>$data->customer_id),'','row')->company;
        $getContact = get_table_where('tblcontacts',array('id'=>$data->name_of_machine),'','row');
        $dataResults['results'][0]['name_of_machine'] = (!empty($getContact->firstname) ? $getContact->firstname : '') . (!empty($getContact->lastname) ? $getContact->lastname : '');
        $dataResults['results'][0]['service_type'] = $data->service_type;

        $getSeriesItem = get_table_where('tblwarranty_items',array('id_warranty_receive'=>$id));
        $dataResults['results'][0]['seriesItem'] = array();
        foreach ($getSeriesItem as $key => $value) {
            $getSeries = get_table_where('tblseries',array('id'=>$value['id_series']),'','row');

            $dataResults['results'][0]['seriesItem'][$keyMain]['id_series'] = $getSeries->series;
            $dataResults['results'][0]['seriesItem'][$keyMain]['name_item'] = $getSeries->name_item .' ('. $getSeries->code_item .')'. '<br><span class="label label-warning">'.($getSeries->type_item == 'products' ? _l('products') : _l('ch_items')).'</span>';

            $img = '<img width="50" src="'.base_url('assets/images/tnh/no_image.png').'">';
            if($getSeries->type_item == "products") {
                $getImage = get_table_where('tbl_products',array('id'=>$getSeries->id_item),'','row');
                if($getImage && !empty($getImage->images)) {
                    $img = '<img width="50" src="'.base_url('uploads/products/'.$getImage->images).'">';
                }
            }
            else if($getSeries->type_item == "items") {
                $getImage = get_table_where('tblitems',array('id'=>$getSeries->id_item),'','row');
                if($getImage && !empty($getImage->avatar)) {
                    $img = '<img width="50" src="'.base_url($getImage->images).'">';
                }
            }
            $dataResults['results'][0]['seriesItem'][$keyMain]['img_item'] = $img;

            $get_item_warranty = get_table_where('tblwarranty_items',array('id_series'=>$value['id_series']));
            $dem_temp = 0;
            $date_temp = '';
            foreach ($get_item_warranty as $key => $value) {
                $get_warranty = get_table_where('tblwarranty',array('id_warranty_receive'=>$value['id_warranty_receive']),'','row');
                if($get_warranty) {
                    $dem_temp++;
                    if($key == 0) {
                        $date_temp = $get_warranty->date_create;
                    }
                    if(strtotime($date_temp) < strtotime($get_warranty->date_create)) {
                        $date_temp = $get_warranty->date_create;
                    }
                }
            }

            $dataDate = explode(" ", $data->date);
            if($getSeries->type_item == 'products') {
                $date_export_warehouses = explode(" ", $getSeries->date_export_warehouses);
                $getDetailItem = get_table_where('tbl_products',array('id'=>$getSeries->id_item),'','row');
                $date_deadline = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date_export_warehouses[0])) . " +".$getDetailItem->warranty." month"));
                if(strtotime($date_deadline) > strtotime($dataDate[0])) {
                    $date_1 = $dataDate[0];
                    $date_2 = $date_deadline;
                    $days = (strtotime($date_2) - strtotime($date_1)) / (60 * 60 * 24);
                    $dataResults['results'][0]['seriesItem'][$keyMain]['deadline_warranty'] = $days.' Ngày';
                }
                else {
                    $dataResults['results'][0]['seriesItem'][$keyMain]['deadline_warranty'] = 'Hết thời gian';
                }
            }
            else {
                $date_export_warehouses = explode(" ", $getSeries->date_export_warehouses);
                $getDetailItem = get_table_where('tblitems',array('id'=>$getSeries->id_item),'','row');
                $date_deadline = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date_export_warehouses[0])) . " +".$getDetailItem->warranty." month"));
                if(strtotime($date_deadline) > strtotime($dataDate[0])) {
                    $date_1 = $dataDate[0];
                    $date_2 = $date_deadline;
                    $days = (strtotime($date_2) - strtotime($date_1)) / (60 * 60 * 24);
                    $dataResults['results'][0]['seriesItem'][$keyMain]['deadline_warranty'] = $days.' Ngày';
                }
                else {
                    $dataResults['results'][0]['seriesItem'][$keyMain]['deadline_warranty'] = 'Hết thời gian';
                }
            }

            $dataResults['results'][0]['seriesItem'][$keyMain]['strCount'] = '<div class="text-center">
                                    <span class="label label-success">'.$dem_temp.' lần bảo hành</span>
                                    <br><span>'.($dem_temp > 0 ? _l('date_warranty_nearest').': '._d($date_temp) : '').'</span>
                                </div>';
            $keyMain++;
        }
        $this->load->view('view_warranty_receive', $dataResults);
    }

    public function getView_warranty_list($id='')
    {
        $dataResults = array();
        $data = get_table_where('tblwarranty',array('id'=>$id),'','row');
        $data_receive = get_table_where('tblwarranty_receive',array('id'=>$data->id_warranty_receive),'','row');
        $keyMain = 0;
        $dataResults['customer_name'] = get_table_where('tblclients',array('userid'=>$data_receive->customer_id),'','row')->company;
        $getContact = get_table_where('tblcontacts',array('id'=>$data_receive->name_of_machine),'','row');
        $dataResults['contact'] = (!empty($getContact->firstname) ? $getContact->firstname : '') . (!empty($getContact->lastname) ? $getContact->lastname : '');
        $dataResults['service_type'] = $data_receive->service_type;
        $dataResults['code'] = $data->code;
        $arrID_staff = explode(',', $data->employees_id);
        $dataResults['employees'] = '';
        if($arrID_staff) {
            foreach ($arrID_staff as $key => $value) {
                $dataResults['employees'] .= get_staff_full_name($value).', ';
            }
            $dataResults['employees'] = trim($dataResults['employees'], ', ');
        }
        $dataResults['localtion_warranty'] = ($data->localtion_warranty == 1 ? 'Công ty' : 'Khách hàng');
        $dataITem = get_table_where('tblwarranty_items', array('id_warranty_receive'=>$data_receive->id));
        foreach ($dataITem as $key => $value) {
            $getSeries = get_table_where('tblseries',array('id'=>$value['id_series']),'','row');
            $dataResults['dataITem'][$key]['id_warranty_item'] = $value['id'];
            $dataResults['dataITem'][$key]['id_series'] = $value['id_series'];
            $dataResults['dataITem'][$key]['series'] = $getSeries->series;
            $dataResults['dataITem'][$key]['code_item'] = $getSeries->code_item . '<br><span class="label label-warning">'.($getSeries->type_item == 'products' ? _l('products') : _l('ch_items')).'</span>';
            $dataResults['dataITem'][$key]['name_item'] = $getSeries->name_item;

            $data_receiveDate = explode(" ", $data_receive->date);
            if($getSeries->type_item == 'products') {
                $date_export_warehouses = explode(" ", $getSeries->date_export_warehouses);
                $getDetailItem = get_table_where('tbl_products',array('id'=>$getSeries->id_item),'','row');
                $dataResults['dataITem'][$key]['month_warranty'] = $getDetailItem->warranty.' Tháng';
                $date_deadline = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date_export_warehouses[0])) . " +".$getDetailItem->warranty." month"));
                if(strtotime($date_deadline) > strtotime($data_receiveDate[0])) {
                    $date_1 = $data_receiveDate[0];
                    $date_2 = $date_deadline;
                    $days = (strtotime($date_2) - strtotime($date_1)) / (60 * 60 * 24);
                    $dataResults['dataITem'][$key]['deadline_warranty'] = $days.' Ngày';
                }
                else {
                    $dataResults['dataITem'][$key]['deadline_warranty'] = 'Hết thời gian';
                }
            }
            else {
                $date_export_warehouses = explode(" ", $getSeries->date_export_warehouses);
                $getDetailItem = get_table_where('tblitems',array('id'=>$getSeries->id_item),'','row');
                $dataResults['dataITem'][$key]['month_warranty'] = $getDetailItem->warranty.' Tháng';

                $date_deadline = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date_export_warehouses[0])) . " +".$getDetailItem->warranty." month"));
                if(strtotime($date_deadline) > strtotime($data_receiveDate[0])) {
                    $date_1 = $data_receiveDate[0];
                    $date_2 = $date_deadline;
                    $days = (strtotime($date_2) - strtotime($date_1)) / (60 * 60 * 24);
                    $dataResults['dataITem'][$key]['deadline_warranty'] = $days.' Ngày';
                }
                else {
                    $dataResults['dataITem'][$key]['deadline_warranty'] = 'Hết thời gian';
                }
            }

            $img = '<img width="50" src="'.base_url('assets/images/tnh/no_image.png').'">';
            if($getSeries->type_item == "products") {
                $getImage = get_table_where('tbl_products',array('id'=>$getSeries->id_item),'','row');
                if($getImage && !empty($getImage->images)) {
                    $img = '<img width="50" src="'.base_url('uploads/products/'.$getImage->images).'">';
                }
            }
            else if($getSeries->type_item == "items") {
                $getImage = get_table_where('tblitems',array('id'=>$getSeries->id_item),'','row');
                if($getImage && !empty($getImage->avatar)) {
                    $img = '<img width="50" src="'.base_url($getImage->images).'">';
                }
            }
            $dataResults['dataITem'][$key]['img_item'] = $img;
        }

        $dataResults['expenses'] = get_table_where('tblwarranty_expenses',array('id_warranty'=>$id));
        $dataResults['supplies'] = get_table_where('tblwarranty_supplies',array('id_warranty'=>$id));
        $dataResults['id_warranty'] = $id;
        $this->load->view('view_warranty_list', $dataResults);
    }

    public function getView_export_supplies($id='')
    {
        $dataResults = array();
        $dataResults['now'] = date('d/m/Y H:i:s');
        $dataResults['supplies'] = get_table_where('tblwarranty_supplies',array('id_warranty'=>$id));
        $dataResults['id'] = $id;
        $this->load->view('add_export_supplies', $dataResults);
    }

    public function add_export_supplies_form($id='')
    {
        $data = $this->input->post();
        $old = get_table_where('tblwarranty_number',array(),'','row')->export_supplies;
        $str_number = '';
        if($old < 10) {
            $str_number = '00'.$old;
        }
        else if($old < 100) {
            $str_number = '0'.$old;
        }
        else if($old < 1000) {
            $str_number = $old;
        }
        $code = 'VTBH-'.date('dmy').$str_number;

        $in = array(
            'id_warranty' => $id,
            'date' => to_sql_date($data['date_export_supplies'], true),
            'date_deadline' => to_sql_date($data['date_deadline']),
            'code' => $code,
            'name' => $data['name_export_supplies'],
            'note' => $data['note_export_supplies'],
            'date_create' => date('Y-m-d H:i:s'),
            'staff_create' => get_staff_user_id()
        );
        $this->db->insert('tblwarranty_export_supplies', $in);
        //update stt
        $number = $old + 1;
        if($number >= 1000) {
            $number = 1;
        }
        $this->db->set('export_supplies', $number);
        $this->db->update('tblwarranty_number');
        //end
        echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('cong_add_true')));
    }

    public function export_supplies()
    {
        $data['title'] = _l('add_export_supplies');
        $this->load->view('manager_export_supplies', $data);
    }

    public function table_warranty_export_supplies()
    {
        $aColumns = [
            'tblwarranty_export_supplies.id',
            'tblwarranty_export_supplies.code',
            'tblwarranty.code',
            'tblwarranty_export_supplies.name',
            'tblwarranty_export_supplies.date',
            'tblwarranty_export_supplies.date_deadline',
            'tblwarranty_export_supplies.status',
            '6',
            'tblwarranty_export_supplies.staff_create',
            '8'
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tblwarranty_export_supplies';

        $join         = array(
            'LEFT JOIN tblwarranty on tblwarranty.id = tblwarranty_export_supplies.id_warranty',
        );
        $where         = array();
        if($this->input->post('filterStatus')) {
            if($this->input->post('filterStatus') == 1) {
                array_push($where, 'AND tblwarranty_export_supplies.status = 0');
            }
            else if($this->input->post('filterStatus') == 2) {
                array_push($where, 'AND tblwarranty_export_supplies.status = 1');
            }
        }

        if($this->input->post('search_date')) {
            $data_start = explode(' - ', $this->input->post('search_date'));
            array_push($where, 'AND tblwarranty_export_supplies.date BETWEEN "' . to_sql_date($data_start[0]) . '" and "' . to_sql_date($data_start[1]) . '"');
        }

        if($this->input->post('search_code')) {
            array_push($where, 'AND tblwarranty_export_supplies.id = ' . $this->input->post('search_code'));
        }
        if($this->input->post('search_client')) {
            array_push($where, 'AND tblwarranty_export_supplies.id_warranty IN (SELECT id FROM tblwarranty WHERE tblwarranty.id_warranty_receive IN (SELECT id FROM tblwarranty_receive WHERE customer_id = '.$this->input->post('search_client').'))');
        }

        $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
            'tblwarranty_export_supplies.not_new_by_staff',
            'tblwarranty.id',
            'tblwarranty_export_supplies.date_status',
            'tblwarranty_export_supplies.staff_status',
        ));
        $output  = $result['output'];
        $rResult = $result['rResult'];
        $currentPage = $this->input->post('start');
        $currentall = $output['iTotalRecords'];
        foreach ($rResult as $r => $aRow) {
            $row = [];
            for ($i = 0 ; $i < count($aColumns) ; $i++) {
                $_data = $aRow[$aColumns[$i]];

                $result_checkWarehouse = true;
                $get_item = get_table_where('tblwarranty_supplies',array('id_warranty'=>$aRow['id']));
                $get_detail = get_table_where('tblwarranty_export_supplies',array('id'=>$aRow['tblwarranty_export_supplies.id']),'','row');
                $get_export_different = get_table_where('tblexport_different',array('id_warranty_export_supplies'=>$aRow['tblwarranty_export_supplies.id']),'','row');
                foreach ($get_item as $key => $value) {
                    if($value['type_item'] == 'materials') {
                        $this->db->select('SUM(tblwarehouse_items.product_quantity) as quantity_warehouse');
                        $this->db->where('tblwarehouse_items.id_items', $value['id_item']);
                        $this->db->where('tblwarehouse_items.type_items', 'nvl');
                        $quantity_warehouse = $this->db->get('tblwarehouse_items')->row();
                    }
                    else if($value['type_item'] == 'supplies') {
                        $this->db->select('SUM(tblwarehouse_items.product_quantity) as quantity_warehouse');
                        $this->db->where('tblwarehouse_items.id_items', $value['id_item']);
                        $this->db->where('tblwarehouse_items.type_items', 'tools');
                        $quantity_warehouse = $this->db->get('tblwarehouse_items')->row();
                    }

                    //không tính sl đã xuất
                    $quantity_rest = $value['quantity'] - $value['export_warehouse'];
                    //end
                    if($quantity_rest > $quantity_warehouse->quantity_warehouse) {
                        $result_checkWarehouse = false;
                        break;
                    }
                }

                $getAllItem = get_table_where('tblwarranty_supplies',array('id_warranty'=>$aRow['id']));
                //check xuất kho
                $checkExportWarehouse = true;
                foreach ($getAllItem as $keyAllItem => $valueAllItem) {
                    if($valueAllItem['quantity'] > $valueAllItem['export_warehouse']) {
                        $checkExportWarehouse = false;
                        break;
                    }
                }
                if(!$getAllItem) {
                    $checkExportWarehouse = false;
                }
                //end

                if ($aColumns[$i] == 'tblwarranty_export_supplies.id') {
                    $not_new_by_staff = explode(',',$aRow['not_new_by_staff']);
                    if(!in_array(get_staff_user_id(), $not_new_by_staff) && $aRow['tblwarranty_export_supplies.status'] == 0) {
                        $_data = ($currentall+1)-($currentPage+$r+1).' <span class="wap-new">new</span>';
                    }
                    else {
                        $_data = ($currentall+1)-($currentPage+$r+1);
                    }
                }
                else if ($aColumns[$i] == 'tblwarranty.code') {
                    $_data = '<a onclick="view_warranty_list('.$aRow['id'].');return false;">'.$aRow['tblwarranty.code'].'</a>';
                }
                else if ($aColumns[$i] == 'tblwarranty_export_supplies.code') {
                    $_data = '<a onclick="view_export_supplies('.$aRow['tblwarranty_export_supplies.id'].');return false;">'.$aRow['tblwarranty_export_supplies.code'].'</a>';
                }
                else if ($aColumns[$i] == 'tblwarranty_export_supplies.date') {
                    $_data = _dt($aRow['tblwarranty_export_supplies.date']);
                }
                else if ($aColumns[$i] == 'tblwarranty_export_supplies.date_deadline') {
                    if(!empty($aRow['tblwarranty_export_supplies.date_deadline'])) {
                        $_data = _d($aRow['tblwarranty_export_supplies.date_deadline']);
                    }
                    else {
                        $_data = '<span class="label label-warning">Không giới hạn</span>';
                    }
                }
                else if ($aColumns[$i] == 'tblwarranty_export_supplies.status') {
                    if($aRow['tblwarranty_export_supplies.status'] == 0) {
                        $_data = '<span class="pointer label label-danger po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="<button data-id=\''.$aRow['tblwarranty_export_supplies.id'].'\' data-status=\'1\' class=\'btn btn-success js-status\'>'.lang('approve').'</button><button class=\'btn btn-default po-close\'>'.lang('close').'</button>">'.lang('dont_approve').'</span>';
                    }
                    else {
                        $_data = '<span class="pointer label label-success po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="<button data-id=\''.$aRow['tblwarranty_export_supplies.id'].'\' data-status=\'0\' class=\'btn btn-danger js-status\'>'.lang('dont_approve').'</button><button class=\'btn btn-default po-close\'>'.lang('close').'</button>">'.lang('tnh_approved').'</span>';
                        $_data .= '<br><br>';
                        $_data .= _l('tnh_user_agree') . ': ' . get_staff_full_name($aRow['staff_status']);
                    }
                }
                else if ($aColumns[$i] == '6') {
                    if($result_checkWarehouse == false) {
                        $_data = '<span class="label label-danger">Chưa đủ kho</span>';
                    }
                    if($get_detail && !empty($get_detail->id_purchases)) {
                        $_data = '<span class="label label-danger">Đang thu mua</span>';
                    }
                    if($result_checkWarehouse == true && $checkExportWarehouse == false) {
                        $_data = '<span class="label label-success">Sẵn sàng xuất kho</span>';
                    }
                    if($checkExportWarehouse == true) {
                        $_data = '<span class="label label-primary">Đã xuất kho</span>';
                    }
                }
                else if ($aColumns[$i] == 'tblwarranty_export_supplies.staff_create') {
                    $_data = get_staff_full_name($aRow['tblwarranty_export_supplies.staff_create']);
                }
                else if ($aColumns[$i] == '8') {
                    $_data = '';
                    $_outputStatus = '<div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">'._l('action').'
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu h_right">';
                    $_outputStatus .=   '<li><a onclick="add_purchases('.$aRow['tblwarranty_export_supplies.id'].'); return false;"><i class="fa fa-plus"></i> '._l('purchases').'</a></li>';
                    if($aRow['tblwarranty_export_supplies.status'] == 1 && $result_checkWarehouse == true && $checkExportWarehouse == false) {
                        $_outputStatus .=   '<li><a onclick="add_export_warehouse('.$aRow['tblwarranty_export_supplies.id'].'); return false;"><i class="fa fa-plus"></i> '._l('add_export_warehouse').'</a></li>';
                    }
                    $_outputStatus .=       '<li><a onclick="delete_export_supplies('.$aRow['tblwarranty_export_supplies.id'].'); return false;"><i class="fa fa-remove"></i> '._l('delete').'</a></li>';
                    $_outputStatus .=   '</ul>';
                    $_outputStatus .= '</div>';
                    $_data = $_outputStatus;
                }

                $row[] = $_data;

                $not_new_by_staff = explode(',',$aRow['not_new_by_staff']);
                if(!in_array(get_staff_user_id(), $not_new_by_staff) && $aRow['tblwarranty_export_supplies.status'] == 0) {
                    $row['DT_RowClass'] = 'alert-new';
                }
            }

            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function update_status_export_supplies($id = '', $status = '')
    {
        $in = array(
            'status' => $status,
            'date_status' => date('Y-m-d H:i:s'),
            'staff_status' => get_staff_user_id()
        );
        $this->db->where('id', $id);
        $this->db->update('tblwarranty_export_supplies', $in);
    }

    public function getView_export_supplies_modal($id='')
    {
        $dataResults = array();
        $dataResults['dataMain'] = get_table_where('tblwarranty_export_supplies',array('id'=>$id),'','row');
        $dataResults['dataSub'] = get_table_where('tblwarranty',array('id'=>$dataResults['dataMain']->id_warranty),'','row');
        $dataResults['supplies'] = get_table_where('tblwarranty_supplies',array('id_warranty'=>$dataResults['dataMain']->id_warranty));
        $this->load->view('view_export_supplies', $dataResults);
    }

    public function getView_add_purchases($id='')
    {
        $dataResults = array();
        $dataResults['id'] = $id;
        $dataResults['id_warranty'] = get_table_where('tblwarranty_export_supplies',array('id'=>$id),'','row')->id_warranty;
        $this->load->view('add_purchases', $dataResults);
    }

    public function searchItemPurchase()
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $id_warranty = $this->input->get('id_warranty');
        $get_item_warranty = get_table_where('tblwarranty_supplies',array('id_warranty'=>$id_warranty));
        $arr = array();
        $arr_addtional = array();
        $keyMain = 0;
        $keyMain_addtional = 0;
        foreach ($get_item_warranty as $key => $value) {
            if($value['type_item'] == 'materials') {
                $getItem = get_table_where('tbl_materials',array('id'=>$value['id_item']),'','row');
                if($value['additional_supplies'] == 0) {
                    $arr[$keyMain]['id'] = 'materials_'.$getItem->id;
                    $arr[$keyMain]['text'] = $getItem->code . ' ('.$getItem->name.')';
                    $keyMain++;
                }
                else if($value['additional_supplies'] == 1) {
                    $arr_addtional[$keyMain_addtional]['id'] = 'materials_'.$getItem->id;
                    $arr_addtional[$keyMain_addtional]['text'] = $getItem->code . ' ('.$getItem->name.')';
                    $keyMain_addtional++;
                }
            }
            else if($value['type_item'] == 'supplies') {
                $getItem = get_table_where('tbl_tools_supplies',array('id'=>$value['id_item']),'','row');
                if($value['additional_supplies'] == 0) {
                    $arr[$keyMain]['id'] = 'supplies_'.$getItem->id;
                    $arr[$keyMain]['text'] = $getItem->code . ' ('.$getItem->name.')';
                    $keyMain++;
                }
                else if($value['additional_supplies'] == 1) {
                    $arr_addtional[$keyMain_addtional]['id'] = 'supplies_'.$getItem->id;
                    $arr_addtional[$keyMain_addtional]['text'] = $getItem->code . ' ('.$getItem->name.')';
                    $keyMain_addtional++;
                }
            }
        }

        if($arr != array() || $arr_addtional != array()) {
            $data['results'] = [
                [
                    'text' => _l('tnh_tools_supplies'), 'children' => $arr
                ],
                // [
                //     'text' => _l('Vật tư bổ sung'), 'children' => $arr_addtional
                // ],
            ];
        }
        else {
            $data['results'] = [
                [
                    'text' => lang('not_results_found'), 'children' => ''
                ],
            ];
        }
        echo json_encode($data);
    }

    public function getDetail_ItemWarranty()
    {
        $data = $this->input->post();
        $arr = explode("_", $data['id_item']);
        $type = $arr[0];
        $id = $arr[1];
        $result = array();
        $img = '<img width="50" src="'.base_url('assets/images/tnh/no_image.png').'">';
        if($type == 'materials') {
            $getDetail = get_table_where('tbl_materials',array('id'=>$id),'','row');
            $getUnit = get_table_where('tblunits',array('unitid'=>$getDetail->unit_id),'','row');
            $result[0]['id'] = 'materials_'.$id;
            $result[0]['code'] = $getDetail->code;
            $result[0]['name'] = $getDetail->name . '<br><span class="label label-warning">'._l('tnh_item_materials').'</span>';
            $result[0]['price'] = number_format($getDetail->price_sell);
            $result[0]['unit'] = $getUnit->unit;
            if($getDetail && !empty($getDetail->images)) {
                $img = '<img width="50" src="'.base_url('uploads/materials/'.$getDetail->images).'">';
            }
        }
        else if($type == 'supplies') {
            $getDetail = get_table_where('tbl_tools_supplies',array('id'=>$id),'','row');
            $getUnit = get_table_where('tblunits',array('unitid'=>$getDetail->unit_id),'','row');
            $result[0]['id'] = 'supplies_'.$id;
            $result[0]['code'] = $getDetail->code;
            $result[0]['name'] = $getDetail->name . '<br><span class="label label-warning">'._l('tnh_tools_supplies').'</span>';
            $result[0]['price'] = number_format($getDetail->price_import);
            $result[0]['unit'] = $getUnit->unit;
            if($getDetail && !empty($getDetail->images)) {
                $img = '<img width="50" src="'.base_url('uploads/tools_supplies/'.$getDetail->images).'">';
            }
        }
        $result[0]['img_item'] = $img;
        echo json_encode($result);
    }

    public function getAll_ItemWarranty()
    {
        $data = $this->input->post();
        $getAll_Item = get_table_where('tblwarranty_supplies',array('id_warranty'=>$data['id_warranty']));
        
        $result = array();
        foreach ($getAll_Item as $key => $value) {
            $img = '<img width="50" src="'.base_url('assets/images/tnh/no_image.png').'">';
            if($value['type_item'] == 'materials') {
                $getDetail = get_table_where('tbl_materials',array('id'=>$value['id_item']),'','row');
                $getUnit = get_table_where('tblunits',array('unitid'=>$getDetail->unit_id),'','row');
                $result[$key]['id'] = 'materials_'.$value['id_item'];
                $result[$key]['code'] = $getDetail->code;
                $result[$key]['name'] = $getDetail->name . '<br><span class="label label-warning">'._l('tnh_item_materials').'</span>';
                $result[$key]['price'] = number_format($getDetail->price_sell);
                $result[$key]['unit'] = $getUnit->unit;
                if($getDetail && !empty($getDetail->images)) {
                    $img = '<img width="50" src="'.base_url('uploads/materials/'.$getDetail->images).'">';
                }
            }
            else if($value['type_item'] == 'supplies') {
                $getDetail = get_table_where('tbl_tools_supplies',array('id'=>$value['id_item']),'','row');
                $getUnit = get_table_where('tblunits',array('unitid'=>$getDetail->unit_id),'','row');
                $result[$key]['id'] = 'supplies_'.$value['id_item'];
                $result[$key]['code'] = $getDetail->code;
                $result[$key]['name'] = $getDetail->name . '<br><span class="label label-warning">'._l('tnh_tools_supplies').'</span>';
                $result[$key]['price'] = number_format($getDetail->price_import);
                $result[$key]['unit'] = $getUnit->unit;
                if($getDetail && !empty($getDetail->images)) {
                    $img = '<img width="50" src="'.base_url('uploads/tools_supplies/'.$getDetail->images).'">';
                }
            }
            $result[$key]['img_item'] = $img;
        }
        echo json_encode($result);
    }

    public function add_purchases_form($id = '')
    {
        $data = $this->input->post();
        $purchase = array(
            'code'=>sprintf('%06d', ch_getMaxID('id', 'tblpurchases') + 1),
            'prefix'=>get_option('prefix_purchase'),
            'name_purchase'=>$data['name'],
            'explanation'=>$data['reason'],
            'date'=>to_sql_date($data['date'],true),
            'staff_create'=>get_staff_user_id(),
            'date_create'=>date('Y:m:d H:i:s'),
            'status'=>1,
            'type'=>1,
            'id_plan'=>0,
            'type_plan'=>0,
        );
        if($this->db->insert('tblpurchases',$purchase)) {
            $insert_id = $this->db->insert_id();
            $get_code = get_table_where('tblpurchases',array('id'=>$insert_id),'','row');
            activity_log_v2('purchase','tblpurchases',$id,$get_code->prefix.$get_code->code,'Thêm mới yêu cầu mua hàng ['.$get_code->prefix.$get_code->code.']');
        }

        $items = $data['item'];

        if($insert_id) {
            foreach ($items as $key => $item) {
                if(!empty($item['id_item'])) {
                    $arr = explode('_', $item['id_item']);
                    if($arr[0] == 'materials') {
                        $arr[0] = 'nvl';
                    }
                    else if($arr[0] == 'supplies') {
                        $arr[0] = 'tools';
                    }
                    $items = array(
                        'purchases_id'=>$insert_id,
                        'product_id'=>$arr[1],
                        'quantity'=>$item['quantity'],
                        'quantity_net'=>$item['quantity_net'],
                        'type'=>$arr[0],
                        'note'=>$item['note'],
                    );
                    $this->db->insert('tblpurchases_items',$items);
                }
            }

            $get_warranty_export_supplies = get_table_where('tblwarranty_export_supplies',array('id'=>$id),'','row');
            $old_string = !empty($get_warranty_export_supplies->id_purchases) ? $get_warranty_export_supplies->id_purchases : '';
            if($old_string == '') {
                $old_string = $insert_id;
            }
            else {
                $old_string .= ','.$insert_id;
            }
            $this->db->set('id_purchases', $old_string);
            $this->db->where('id', $id);
            $this->db->update('tblwarranty_export_supplies');
            create_purchase($insert_id);
            echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('cong_add_true')));
        }
        else {
            echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('cong_add_false')));
        }
    }

    public function getView_add_export_warehouse($id='')
    {
        $dataResults = array();
        $dataResults['id'] = $id;
        $dataMain = get_table_where('tblwarranty_export_supplies',array('id'=>$id),'','row');
        $dataWarranty = get_table_where('tblwarranty',array('id'=>$dataMain->id_warranty),'','row');
        if($dataWarranty) {
            $dataWarranty_receive = get_table_where('tblwarranty_receive',array('id'=>$dataWarranty->id_warranty_receive),'','row');
            if($dataWarranty_receive) {
                $dataResults['client'] = get_table_where('tblclients',array('userid'=>$dataWarranty_receive->customer_id),'','row');
            }
        }

        $getAll_Item = get_table_where('tblwarranty_supplies',array('id_warranty'=>$dataMain->id_warranty));
        $dataResults['item'] = array();
        foreach ($getAll_Item as $key => $value) {
            //không tính sl đã xuất
            $quantity_rest = $value['quantity'] - $value['export_warehouse'];
            //end
            if($quantity_rest <= 0) {
                continue;
            }

            $img = '<img width="50" src="'.base_url('assets/images/tnh/no_image.png').'">';
            if($value['type_item'] == 'materials') {
                $getDetail = get_table_where('tbl_materials',array('id'=>$value['id_item']),'','row');
                $getUnit = get_table_where('tblunits',array('unitid'=>$getDetail->unit_id),'','row');
                $dataResults['item'][$key]['id'] = $value['id_item'];
                $dataResults['item'][$key]['type'] = 'nvl';
                $dataResults['item'][$key]['name'] = $getDetail->name .' ('.$getDetail->code.')'. '<br><span class="label label-warning">'._l('tnh_item_materials').'</span>';
                $dataResults['item'][$key]['price'] = number_format($getDetail->price_sell);
                $dataResults['item'][$key]['unit'] = $getUnit->unit;
                if($getDetail && !empty($getDetail->images)) {
                    $img = '<img width="50" src="'.base_url('uploads/materials/'.$getDetail->images).'">';
                }
            }
            else if($value['type_item'] == 'supplies') {
                $getDetail = get_table_where('tbl_tools_supplies',array('id'=>$value['id_item']),'','row');
                $getUnit = get_table_where('tblunits',array('unitid'=>$getDetail->unit_id),'','row');
                $dataResults['item'][$key]['id'] = $value['id_item'];
                $dataResults['item'][$key]['type'] = 'tools';
                $dataResults['item'][$key]['name'] = $getDetail->name .' ('.$getDetail->code.')'. '<br><span class="label label-warning">'._l('tnh_tools_supplies').'</span>';
                $dataResults['item'][$key]['price'] = number_format($getDetail->price_import);
                $dataResults['item'][$key]['unit'] = $getUnit->unit;
                if($getDetail && !empty($getDetail->images)) {
                    $img = '<img width="50" src="'.base_url('uploads/tools_supplies/'.$getDetail->images).'">';
                }
            }
            $dataResults['item'][$key]['img_item'] = $img;
            $dataResults['item'][$key]['quantity'] = $quantity_rest;
        }

        $data['warehouse'] = get_table_where('tblwarehouse');
        $html='<option></option>';
        foreach ($data['warehouse'] as $key => $value) {
            $html.='<option value="'.$value['id'].'">'.$value['name'].'</option>';
        }
        $dataResults['html_warehouse'] = $html;
        $this->load->view('add_export_warehouse', $dataResults);
    }

    public function add_export_warehouse($id = '')
    {
        $data = $this->input->post();
        $export_different=array(
            'code'=>sprintf('%06d', ch_getMaxID('id', 'tblexport_different') + 1),
            'note'=>$data['reason_export_warehouse'],
            'object'=>$data['object_export_warehouse'],
            'id_object'=>$data['id_object_export_warehouse'],
            'object_text'=>'',
            'prefix'=>get_option('prefix_export_different'),
            'date'=>to_sql_date($data['date_export_warehouse'],true),
            'staff_id'=>get_staff_user_id(),
            'date_create'=>date('Y:m:d H:i:s'),
            'status'=>0,
            'id_warranty_export_supplies'=>$id
        );
        $this->db->insert('tblexport_different',$export_different);
        $insert_id = $this->db->insert_id();
        if($insert_id) {
            $items = $data['items'];
            $total = 0;
            foreach ($items as $key => $item) {
                if(!empty($item['id']))
                {
                    $_item['id_export_different'] = $insert_id;
                    $_item['product_id'] = $item['id'];
                    $_item['price'] = str_replace(',', '', $item['price']);
                    $_item['warehouses_id'] = $item['warehouses_id'];
                    $_item['localtion_warehouses_id'] = $item['localtion_warehouses_id'];
                    $_item['type'] = $item['type'];
                    $_item['note'] = $item['note'];
                    $_item['quantity_net'] = str_replace(',', '', $item['quantity_net']);
                    $amount = ($_item['quantity_net'] * $_item['price']);
                    $_item['amount'] = $amount;
                    $total += $amount;
                    $this->db->insert('tbltblexport_different_items',$_item);
                    $idd = $this->db->insert_id();
                }
            }
            $this->db->update('tblexport_different',array('subtotal'=>$total),array('id'=>$insert_id));
            echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('cong_add_true')));
        }
        else {
            echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('cong_add_false')));
        }
    }

    public function count_status()
    {
        $dataResults = array();
        $dataResults['dataWarranty_receive'] = total_rows('tblwarranty_receive',array('status'=>0));
        $dataResults['dataWarranty'] = total_rows('tblwarranty',array('status'=>0));
        $dataResults['dataWarranty_export_supplies'] = total_rows('tblwarranty_export_supplies',array('status'=>0));
        echo json_encode($dataResults);
    }

    public function searchCodeWarranty()
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = 5000;

        $this->db->select('tblwarranty.id as id, tblwarranty.code as text', false);
        $this->db->from('tblwarranty');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tblwarranty.code', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $results = $this->db->get()->result_array();

        if($results) {
            $data['results'] = [
                [
                    'text' => _l('ch_code_number'), 'children' => $results
                ],
            ];
        }
        else {
            $data['results'] = [
                [
                    'text' => lang('not_results_found'), 'children' => ''
                ],
            ];
        }
        echo json_encode($data);
    }

    public function searchCodeWarranty_receive()
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = 5000;

        $this->db->select('tblwarranty_receive.id as id, tblwarranty_receive.code as text', false);
        $this->db->from('tblwarranty_receive');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tblwarranty_receive.code', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $results = $this->db->get()->result_array();

        if($results) {
            $data['results'] = [
                [
                    'text' => _l('ch_code_number'), 'children' => $results
                ],
            ];
        }
        else {
            $data['results'] = [
                [
                    'text' => lang('not_results_found'), 'children' => ''
                ],
            ];
        }
        echo json_encode($data);
    }

    public function searchCodeWarranty_export_supplies()
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = 5000;

        $this->db->select('tblwarranty_export_supplies.id as id, tblwarranty_export_supplies.code as text', false);
        $this->db->from('tblwarranty_export_supplies');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tblwarranty_export_supplies.code', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $results = $this->db->get()->result_array();

        if($results) {
            $data['results'] = [
                [
                    'text' => _l('ch_code_number'), 'children' => $results
                ],
            ];
        }
        else {
            $data['results'] = [
                [
                    'text' => lang('not_results_found'), 'children' => ''
                ],
            ];
        }
        echo json_encode($data);
    }

    public function searchClientWarranty()
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = 500;

        $this->db->select('tblclients.userid as id, tblclients.company as text', false);
        $this->db->from('tblclients');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tblclients.company', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $results = $this->db->get()->result_array();

        if($results) {
            $data['results'] = [
                [
                    'text' => _l('clients'), 'children' => $results
                ],
            ];
        }
        else {
            $data['results'] = [
                [
                    'text' => lang('not_results_found'), 'children' => ''
                ],
            ];
        }
        echo json_encode($data);
    }

    public function print_pdf($id='')
    {
        ob_start();
        $data = new stdClass();
        $dataMain = get_table_where('tblwarranty_receive',array('id'=>$id),'','row');
        $dataClient = get_table_where('tblclients',array('userid'=>$dataMain->customer_id),'','row');
        $getContact = get_table_where('tblcontacts',array('id'=>$dataMain->name_of_machine),'','row');

        $getSeriesItem = get_table_where('tblwarranty_items',array('id_warranty_receive'=>$id));
        $table = '';
        $data->img = '';
        $data->content = '';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">TIẾP NHẬN BẢO HÀNH</span><br><br>';

        $data->content .= '<span style="text-align: left;font-size: 12px;">Mã phiếu: <span style="font-weight: bold">'.$dataMain->code.'</span></span><br>';
        $data->content .= '<span style="text-align: left;font-size: 12px;">Khách hàng: <span style="font-weight: bold">'.$dataClient->company.'</span></span><br>';
        $data->content .= '<span style="text-align: left;font-size: 12px;">Người liên hệ: <span style="font-weight: bold">'.(!empty($getContact->firstname) ? $getContact->firstname : '') . (!empty($getContact->lastname) ? $getContact->lastname : '').'</span></span><br>';
        $data->content .= '<span style="text-align: left;font-size: 12px;">Ngày liên hệ: <span style="font-weight: bold">'._dt($dataMain->date).'</span></span><br>';
        $data->content .= '<span style="text-align: left;font-size: 12px;">Hình thức liên hệ: <span style="font-weight: bold">'.$dataMain->service_type.'</span></span><br><br>';
        $data->content .= '<span style="text-align: left;font-size: 12px; font-weight: bold">Sản phẩm cần bảo hành:</span><br><br>';

        $table = '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1px">
                    <thead>
                        <tr>
                            <td style="width: 10%;text-align: center;font-weight: bold;">'._l('STT').'</td>
                            <td style="width: 30%;text-align: center;font-weight: bold;">'._l('series').'</td>
                            <td style="width: 30%;text-align: center;font-weight: bold;">'._l('tnh_product_code').'</td>
                            <td style="width: 30%;text-align: center;font-weight: bold;">'._l('tnh_product_name').'</td>
                ';
        $table .= '</tr>
                </thead>
                <tbody>';
        foreach ($getSeriesItem as $key => $value) {
            $getSeries = get_table_where('tblseries',array('id'=>$value['id_series']),'','row');
            $table .= '<tr nobr="true">';
            $table .= '<td style="width: 10%;text-align: center;">'.++$key.'</td>';
            $table .= '<td style="width: 30%;text-align: center;">'.$getSeries->series.'</td>';
            $table .= '<td style="width: 30%;text-align: center;">'.$getSeries->code_item.'</td>';
            $table .= '<td style="width: 30%;text-align: center;">'.$getSeries->name_item.'</td>';
            $table .= '</tr>';
        }
        $table .= '</tbody></table>';
        $data->content .= $table;

        $data->content .= '<table class="table table-bordered" width="100%">
                                <thead>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;">
                                            <span>Người duyệt</span>
                                            <br><span>(Ký, ghi rõ họ tên)</span>
                                        </td>
                                        <td style="text-align: center; font-weight: bold;">
                                            Người tiếp nhận
                                            <br><span>(Ký, ghi rõ họ tên)</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>';
        $pdf      = print_pdf_warranty($data);
        $type     = 'I';
        $pdf->Output($dataMain->code . '.pdf', $type);
    }

    public function print_pdf_warranty_issue($id='')
    {
        ob_start();
        $data = new stdClass();
        $dataMain = get_table_where('tblwarranty',array('id'=>$id),'','row');
        $dataReceive = get_table_where('tblwarranty_receive',array('id'=>$dataMain->id_warranty_receive),'','row');
        $dataClient = get_table_where('tblclients',array('userid'=>$dataReceive->customer_id),'','row');
        $getContact = get_table_where('tblcontacts',array('id'=>$dataReceive->name_of_machine),'','row');
        $getSeriesItem = get_table_where('tblwarranty_items',array('id_warranty_receive'=>$dataReceive->id));

        $html_employees_id = '';
        if($dataMain && !empty($dataMain->employees_id)) {
            $arrEmployees_id = explode(",", $dataMain->employees_id);
            foreach ($arrEmployees_id as $key => $value) {
                $html_employees_id .= get_staff_full_name($value).', ';
            }
            $html_employees_id = trim($html_employees_id, ', ');
        }
        $table = '';
        $data->img = '';
        $data->content = '';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">WARRANTY</span><br><br>';

        $data->content .= '<table class="table table-bordered" width="100%">
                                <thead>
                                    <tr>
                                        <td style="width: 60%;"></td>
                                        <td style="width: 40%;"></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="text-align: left;">Customer name: <span style="font-weight: bold;">'.$dataClient->company.'</span></td>
                                        <td style="text-align: left;">Warranty No: <span style="font-weight: bold;">'.$dataMain->code.'</span></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;">TKD PIC: <span style="font-weight: bold;">'.$html_employees_id.'</span></td>
                                        <td style="text-align: left;">Date: <span style="font-weight: bold;">'._d($dataReceive->date).'</span></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;">Name of Machine: <span style="font-weight: bold;">'.($dataReceive && !empty($dataReceive->name_of_machine) ? get_staff_full_name($dataReceive->name_of_machine) : '').'</span></td>
                                        <td style="text-align: left;">Service type: <span style="font-weight: bold;">'.$dataReceive->service_type.'</span></td>
                                    </tr>
                                </tbody>
                            </table>';

        $table = '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1px">
                    <thead>
                        <tr>
                            <td style="width: 10%; text-align: center;font-weight: bold;">NO.</td>
                            <td style="width: 30%; text-align: center;font-weight: bold;">ISSUE</td>
                            <td style="width: 30%; text-align: center;font-weight: bold;">SOLUTION</td>
                            <td style="width: 30%; text-align: center;font-weight: bold;">PHOTO</td>
                ';
        $table .= '</tr>
                </thead>
                <tbody>';
        foreach ($getSeriesItem as $key => $value) {
            $getSeries = get_table_where('tblseries',array('id'=>$value['id_series']),'','row');
            $table .= '<tr nobr="true">';
            $table .= '<td style="width: 10%; text-align: center; font-weight: bold;">'.++$key.'</td>';
            $table .= '<td style="width: 30%; text-align: left;"><span style="font-weight: bold;">'.$getSeries->name_item.' ('.$getSeries->code_item.')'.'</span></td>';
            $table .= '<td style="width: 30%; text-align: left;">Series: <span style="font-weight: bold;">'.$getSeries->series.'</span></td>';
            $table .= '<td style="width: 30%; text-align: center;"></td>';
            $table .= '</tr>';
            $getWarranty_issue = get_table_where('tblwarranty_issue',array('id_warranty'=>$id, 'id_warranty_item'=>$value['id']));
            $keySub = 1;
            foreach ($getWarranty_issue as $key_issue => $value_issue) {
                $getIssue = get_table_where('tblissue',array('id'=>$value_issue['id_issue']),'','row');
                $get_file = get_table_where('tblwarranty_file',array('id_warranty_issue'=>$value_issue['id']));
                $table .= '<tr nobr="true">';
                $table .= '<td style="width: 10%; text-align: center;">'.$key.'.'.$keySub.'</td>';
                $table .= '<td style="width: 30%; text-align: left;">'.$getIssue->name.'</td>';
                $table .= '<td style="width: 30%; text-align: left;">'.$value_issue['solution'].'</td>';
                $table .= '<td style="width: 30%; text-align: center;">';
                foreach ($get_file as $key_file => $value_file) {
                    $table .= '<img width="130" src="'.base_url('modules/warranty/uploads/warranty/'.$value_issue['id'].'/'.$value_file['name']).'">';
                }
                $table .= '</td>';
                $table .= '</tr>';
                $keySub++;
            }
        }
        $table .= '</tbody></table>';
        $data->content .= $table;

        $data->content .= '<p style="font-weight: bold;">Customer feedback: .......................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................</p>';
            $data->content .= '<span style="font-weight: bold;">Customer'."'".'s satisfaction: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Not Happy &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Happy &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Very Happy</span><br>';

            $data->content .= '<table class="table table-bordered" width="100%">
                                <thead>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;"><u>Customer'."'".'s confirmation</u></td>
                                        <td style="text-align: center; font-weight: bold;"><u>TKD staff</u></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;">Person name: .....................................................</td>
                                        <td style="text-align: center; font-weight: bold;">Name: .....................................................</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;">Title: ......................................................................</td>
                                        <td style="text-align: center; font-weight: bold;">Title: ........................................................</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;">Phone No: ............................................................</td>
                                        <td style="text-align: center; font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;">Signature</td>
                                        <td style="text-align: center; font-weight: bold;">Signature</td>
                                    </tr>
                                </tbody>
                            </table>';
        $pdf      = print_pdf_warranty($data);
        $type     = 'I';
        $pdf->Output($dataMain->code . '.pdf', $type);
    }

    public function print_pdf_warranty_amount($id='')
    {
        ob_start();
        $data = new stdClass();
        $dataMain = get_table_where('tblwarranty',array('id'=>$id),'','row');
        $dataReceive = get_table_where('tblwarranty_receive',array('id'=>$dataMain->id_warranty_receive),'','row');
        $dataClient = get_table_where('tblclients',array('userid'=>$dataReceive->customer_id),'','row');
        $getContact = get_table_where('tblcontacts',array('id'=>$dataReceive->name_of_machine),'','row');
        $getItem = get_table_where('tblwarranty_expenses',array('id_warranty'=>$id));

        $html_employees_id = '';
        if($dataMain && !empty($dataMain->employees_id)) {
            $arrEmployees_id = explode(",", $dataMain->employees_id);
            foreach ($arrEmployees_id as $key => $value) {
                $html_employees_id .= get_staff_full_name($value).', ';
            }
            $html_employees_id = trim($html_employees_id, ', ');
        }
        $table = '';
        $data->img = '';
        $data->content = '';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">WARRANTY</span><br><br>';

        $data->content .= '<table class="table table-bordered" width="100%">
                                <thead>
                                    <tr>
                                        <td style="width: 60%;"></td>
                                        <td style="width: 40%;"></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="text-align: left;">Customer name: <span style="font-weight: bold;">'.$dataClient->company.'</span></td>
                                        <td style="text-align: left;">Warranty No: <span style="font-weight: bold;">'.$dataMain->code.'</span></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;">TKD PIC: <span style="font-weight: bold;">'.$html_employees_id.'</span></td>
                                        <td style="text-align: left;">Date: <span style="font-weight: bold;">'._d($dataReceive->date).'</span></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;">Service type: <span style="font-weight: bold;">'.$dataReceive->service_type.'</span></td>
                                        <td style="text-align: left;">Serial number: <span style="font-weight: bold;"></span></td>
                                    </tr>
                                </tbody>
                            </table>';
        if($getItem) {
            $table = '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1px">
                        <thead>
                            <tr>
                                <th style="width: 10%;text-align: center;font-weight: bold;">'._l('STT').'</th>
                                <th style="width: 30%;text-align: center;font-weight: bold;">'._l('name_expenses').'</th>
                                <th style="width: 30%;text-align: center;font-weight: bold;">'._l('ch_costs').'</th>
                                <th style="width: 30%;text-align: center;font-weight: bold;">'._l('amount_expenses').'</th>
                    ';
            $table .= '</tr>
                    </thead>
                    <tbody>';
            $total = 0;
            foreach ($getItem as $key => $value) {
                if($value['type'] == 1) {
                    $str = 'Khách chịu';
                }
                else {
                    $str = 'Công ty chịu';
                }
                $table .= '<tr nobr="true">';
                $table .= '<td style="width: 10%;text-align: center;">'.++$key.'</td>';
                $table .= '<td style="width: 30%;text-align: center;">'.$value['name'].'</td>';
                $table .= '<td style="width: 30%;text-align: center;">'.$str.'</td>';
                $table .= '<td style="width: 30%;text-align: right;">'.number_format($value['amount']).'</td>';
                $table .= '</tr>';
                $total += $value['amount'];
            }
            $table .= '<tr nobr="true">';
            $table .= '<td colspan="3" style="text-align: left; font-weight: bold;">Tổng cộng</td>';
            $table .= '<td style="text-align: right; font-weight: bold;">'.number_format($total).'</td>';
            $table .= '</tr>';
            $table .= '</tbody></table>';
            $data->content .= $table;

            $data->content .= '<p style="font-weight: bold;">Customer feedback: .......................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................</p>';
            $data->content .= '<span style="font-weight: bold;">Customer'."'".'s satisfaction: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Not Happy &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Happy &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Very Happy</span><br>';

            $data->content .= '<table class="table table-bordered" width="100%">
                                <thead>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;"><u>Customer'."'".'s confirmation</u></td>
                                        <td style="text-align: center; font-weight: bold;"><u>TKD staff</u></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;">Person name: .....................................................</td>
                                        <td style="text-align: center; font-weight: bold;">Name: .....................................................</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;">Title: ......................................................................</td>
                                        <td style="text-align: center; font-weight: bold;">Title: ........................................................</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;">Phone No: ............................................................</td>
                                        <td style="text-align: center; font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;">Signature</td>
                                        <td style="text-align: center; font-weight: bold;">Signature</td>
                                    </tr>
                                </tbody>
                            </table>';
        }
        $pdf      = print_pdf_warranty($data);
        $type     = 'I';
        $pdf->Output($dataMain->code . '.pdf', $type);
    }

    public function print_pdf_warranty_supplies($id='')
    {
        ob_start();
        $data = new stdClass();
        $dataMain = get_table_where('tblwarranty',array('id'=>$id),'','row');
        $dataReceive = get_table_where('tblwarranty_receive',array('id'=>$dataMain->id_warranty_receive),'','row');
        $dataClient = get_table_where('tblclients',array('userid'=>$dataReceive->customer_id),'','row');
        $getContact = get_table_where('tblcontacts',array('id'=>$dataReceive->name_of_machine),'','row');
        $getItem = get_table_where('tblwarranty_supplies',array('id_warranty'=>$id));

        $html_employees_id = '';
        if($dataMain && !empty($dataMain->employees_id)) {
            $arrEmployees_id = explode(",", $dataMain->employees_id);
            foreach ($arrEmployees_id as $key => $value) {
                $html_employees_id .= get_staff_full_name($value).', ';
            }
            $html_employees_id = trim($html_employees_id, ', ');
        }
        $table = '';
        $data->img = '';
        $data->content = '';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">WARRANTY</span><br><br>';

        $data->content .= '<table class="table table-bordered" width="100%">
                                <thead>
                                    <tr>
                                        <td style="width: 60%;"></td>
                                        <td style="width: 40%;"></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="text-align: left;">Customer name: <span style="font-weight: bold;">'.$dataClient->company.'</span></td>
                                        <td style="text-align: left;">Warranty No: <span style="font-weight: bold;">'.$dataMain->code.'</span></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;">TKD PIC: <span style="font-weight: bold;">'.$html_employees_id.'</span></td>
                                        <td style="text-align: left;">Date: <span style="font-weight: bold;">'._d($dataReceive->date).'</span></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;">Service type: <span style="font-weight: bold;">'.$dataReceive->service_type.'</span></td>
                                        <td style="text-align: left;">Serial number: <span style="font-weight: bold;"></span></td>
                                    </tr>
                                </tbody>
                            </table>';
        if($getItem) {
            $table = '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1px">
                        <thead>
                            <tr>
                                <th style="width: 7%;text-align: center;font-weight: bold;">'._l('STT').'</th>
                                <th style="width: 23%;text-align: center;font-weight: bold;">'._l('name_supplies').'</th>
                                <th style="width: 7%;text-align: center;font-weight: bold;">'._l('SL').'</th>
                                <th style="width: 13%;text-align: center;font-weight: bold;">'._l('ch_costs').'</th>
                                <th style="width: 15%;text-align: center;font-weight: bold;">'._l('ch_price').'</th>
                                <th style="width: 15%;text-align: center;font-weight: bold;">'._l('tnh_subtotal').'</th>
                                <th style="width: 20%;text-align: center;font-weight: bold;">'._l('note').'</th>
                    ';
            $table .= '</tr>
                    </thead>
                    <tbody>';
            $total_quantity = 0;
            $total_amount = 0;
            $total = 0;
            foreach ($getItem as $key => $value) {
                if($value['type_item'] == 'materials') {
                    $getDetail = get_table_where('tbl_materials',array('id'=>$value['id_item']),'','row');
                }
                else if($value['type_item'] == 'supplies') {
                    $getDetail = get_table_where('tbl_tools_supplies',array('id'=>$value['id_item']),'','row');
                }

                if($value['type_amount'] == 1) {
                    $str = 'Hỗ trợ';
                }
                else {
                    $str = 'Tính phí';
                }
                $table .= '<tr nobr="true">';
                $table .= '<td style="width: 7%;text-align: center;">'.++$key.'</td>';
                $table .= '<td style="width: 23%;text-align: left;">'.$getDetail->name.' ('.$getDetail->code.')'.'</td>';
                $table .= '<td style="width: 7%;text-align: center;">'.number_format($value['quantity']).'</td>';
                $table .= '<td style="width: 13%;text-align: center;">'.$str.'</td>';
                $table .= '<td style="width: 15%;text-align: right;">'.number_format($value['amount']).'</td>';
                $table .= '<td style="width: 15%;text-align: right;">'.number_format($value['total']).'</td>';
                $table .= '<td style="width: 20%;text-align: center;">'.$value['note'].'</td>';
                $table .= '</tr>';
                $total_quantity += $value['quantity'];
                $total_amount += $value['amount'];
                $total += $value['total'];
            }
            $table .= '<tr nobr="true">';
            $table .= '<td colspan="2" style="text-align: left; font-weight: bold;">Tổng cộng</td>';
            $table .= '<td style="text-align: center; font-weight: bold;">'.number_format($total_quantity).'</td>';
            $table .= '<td style="text-align: right;"></td>';
            $table .= '<td style="text-align: right; font-weight: bold;">'.number_format($total_amount).'</td>';
            $table .= '<td style="text-align: right; font-weight: bold;">'.number_format($total).'</td>';
            $table .= '<td style="text-align: right;"></td>';
            $table .= '</tr>';
            $table .= '</tbody></table>';
            $data->content .= $table;

            $data->content .= '<p style="font-weight: bold;">Customer feedback: .......................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................</p>';
            $data->content .= '<span style="font-weight: bold;">Customer'."'".'s satisfaction: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Not Happy &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Happy &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Very Happy</span><br>';

            $data->content .= '<table class="table table-bordered" width="100%">
                                <thead>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;"><u>Customer'."'".'s confirmation</u></td>
                                        <td style="text-align: center; font-weight: bold;"><u>TKD staff</u></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;">Person name: .....................................................</td>
                                        <td style="text-align: center; font-weight: bold;">Name: .....................................................</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;">Title: ......................................................................</td>
                                        <td style="text-align: center; font-weight: bold;">Title: ........................................................</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;">Phone No: ............................................................</td>
                                        <td style="text-align: center; font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;">Signature</td>
                                        <td style="text-align: center; font-weight: bold;">Signature</td>
                                    </tr>
                                </tbody>
                            </table>';
        }
        $pdf      = print_pdf_warranty($data);
        $type     = 'I';
        $pdf->Output($dataMain->code . '.pdf', $type);
    }

    public function update_process_done($id='')
    {
        $data = $this->input->post();
        $this->db->set('status_done', 1);
        $this->db->set('type_status_done', $data['type_status_done']);
        $this->db->where('id', $id);
        $this->db->update('tblwarranty');

        //bổ xung lưu kho
            $getDetailMain = get_table_where('tblwarranty',array('id'=>$id),'','row');
            if($getDetailMain && !empty($getDetailMain->warehouseman_id)) {
                $warehouseman_id = $getDetailMain->warehouseman_id;
                $this->confirm_warehous($id, $warehouseman_id);
            }
        //end
        echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('cong_update_true')));
    }

    public function evaluate_modal($id_warranty = '')
    {
        $data['check'] = '';
        if($id_warranty != "") {
            $data['dataMain'] = get_table_where('tblwarranty_evaluate',array('id_warranty'=>$id_warranty),'','row');
        }
        $this->load->view('warranty/evaluate',$data);
    }

    public function add_evaluate($id_warranty = '')
    {
        $data = $this->input->post();
        $in = array(
            'id_warranty' => $id_warranty,
            'points' => $data['points'],
            'note' => $data['note'],
            'staff_create' => get_staff_user_id(),
            'date_create' => date('Y-m-d H:i:s')
        );
        $insert_id = $this->db->insert('tblwarranty_evaluate', $in);
        $alert_type = 'danger';
        $message = _l('edit_slide_false');
        $success = false;
        if($insert_id) {
            $alert_type = 'success';
            $message = _l('edit_slide_true');
            $success = true;
        }
        echo json_encode(array(
            'success' => $success, 
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function edit_evaluate($id_warranty = '')
    {
        $data = $this->input->post();
        $in = array(
            'points' => $data['points'],
            'note' => $data['note'],
            'staff_create' => get_staff_user_id(),
            'date_create' => date('Y-m-d H:i:s')
        );
        $this->db->where('id_warranty', $id_warranty);
        $insert_id = $this->db->update('tblwarranty_evaluate', $in);
        $alert_type = 'danger';
        $message = _l('edit_slide_false');
        $success = false;
        if($insert_id) {
            $alert_type = 'success';
            $message = _l('edit_slide_true');
            $success = true;
        }
        echo json_encode(array(
            'success' => $success, 
            'alert_type' => $alert_type,
            'message' => $message
        ));die;
    }

    public function getView_additional_supplies($id_warranty = '')
    {
        $dataResults = array();
        $dataResults['id'] = $id_warranty;

        $get_item_warranty = get_table_where('tblwarranty_supplies',array('id_warranty'=>$id_warranty, 'additional_supplies'=>0));
        $keyMain = 0;
        foreach ($get_item_warranty as $key => $value) {
            if($value['type_item'] == 'materials') {
                $getItem = get_table_where('tbl_materials',array('id'=>$value['id_item']),'','row');
                $dataResults['item_exists'][$keyMain]['id'] = 'materials_'.$getItem->id;
                $dataResults['item_exists'][$keyMain]['text'] = $getItem->name . ' ('.$getItem->code.')';
            }
            else if($value['type_item'] == 'supplies') {
                $getItem = get_table_where('tbl_tools_supplies',array('id'=>$value['id_item']),'','row');
                $dataResults['item_exists'][$keyMain]['id'] = 'supplies_'.$getItem->id;
                $dataResults['item_exists'][$keyMain]['text'] = $getItem->name . ' ('.$getItem->code.')';
            }
            $keyMain++;
        }
        $dataResults['supplies'] = get_table_where('tblwarranty_supplies',array('id_warranty'=>$id_warranty, 'additional_supplies'=>1));
        $this->load->view('additional_supplies', $dataResults);
    }

    public function additional_supplies($id_warranty = '')
    {
        $data = $this->input->post();
        if(!isset($data['supplies'])) {
            $data['supplies'] = array();
        }
        $arrID = array();
        foreach ($data['supplies'] as $key => $value) {
            if($value['id_item'] == "") {
                continue;
            }
            $str_to_arr = explode("_", $value['id_item']);
            $total = 0;
            if($value['type_amount'] == 2) {
                $total = str_replace(",", "", $value['quantity']) * str_replace(",", "", $value['amount']);
            }
            //'additional_supplies' => 1 : vật tư bổ xung -> hiện tại k cần
            $in_supplies = array(
                'id_warranty' => $id_warranty,
                'type_item' => $str_to_arr[0],
                'id_item' => $str_to_arr[1],
                'quantity' => str_replace(",", "", $value['quantity']),
                'type_amount' => $value['type_amount'],
                'amount' => str_replace(",", "", $value['amount']),
                'total' => $total,
                'note' => $value['note'],
                'additional_supplies' => 0
            );
            $checkExists = get_table_where('tblwarranty_supplies',array('id_warranty'=>$id_warranty, 'type_item'=>$str_to_arr[0], 'id_item'=>$str_to_arr[1]),'','row');
            if(!$checkExists) {
                $this->db->insert('tblwarranty_supplies', $in_supplies);
                $insert_id = $this->db->insert_id();
                $arrID[] = $insert_id;
            }
            else {
                $quantity = $checkExists->quantity + str_replace(",", "", $value['quantity']);
                $this->db->set('quantity', $quantity);
                $this->db->where('id', $checkExists->id);
                $this->db->update('tblwarranty_supplies');
                $arrID[] = $checkExists->id;
            }
        }
        //k xóa
        // if(count($arrID) > 0) {
        //     $this->db->where_not_in('id',$arrID);
        //     $this->db->where('id_warranty',$id_warranty);
        //     $this->db->where('additional_supplies',1);
        //     $this->db->delete('tblwarranty_supplies');
        // }
        // else {
        //     $this->db->where('id_warranty',$id_warranty);
        //     $this->db->where('additional_supplies',1);
        //     $this->db->delete('tblwarranty_supplies');
        // }

        echo json_encode(array(
            'success' => true, 
            'alert_type' => 'success',
            'message' => _l('additional_supplies_true')
        ));die;
    }

    public function getView_add_export_warehouse_done($id = '', $type_status_done = '')
    {
        $dataResults = array();
        $dataResults['id'] = $id;
        $dataResults['type_status_done'] = $type_status_done;
        $detailMainWarranty = get_table_where('tblwarranty',array('id'=>$id),'','row');
        $detailMainReceive = get_table_where('tblwarranty_receive',array('id'=>$detailMainWarranty->id_warranty_receive),'','row');
        $dataResults['client'] = get_table_where('tblclients',array('userid'=>$detailMainReceive->customer_id),'','row');

        $getAllItem = get_table_where('tblwarranty_items',array('id_warranty_receive'=>$detailMainWarranty->id_warranty_receive));
        $dataResults['item'] = array();
        foreach ($getAllItem as $key => $value) {
            $getItem = get_table_where('tblseries',array('id'=>$value['id_series']),'','row');
            $img = '<img width="50" src="'.base_url('assets/images/tnh/no_image.png').'">';

            if($getItem && $getItem->type_item == 'products') {
                $getDetail = get_table_where('tbl_products',array('id'=>$getItem->id_item),'','row');
                $getUnit = get_table_where('tblunits',array('unitid'=>$getDetail->unit_id),'','row');
                $dataResults['item'][$key]['id'] = $getItem->id;
                $dataResults['item'][$key]['type'] = 'products';
                $dataResults['item'][$key]['name'] = $getItem->series . '<br> ('.$getDetail->name.')';
                $dataResults['item'][$key]['price'] = 0;
                $dataResults['item'][$key]['unit'] = $getUnit->unit;
                if($getDetail && !empty($getDetail->images)) {
                    $img = '<img width="50" src="'.base_url('uploads/products/'.$getDetail->images).'">';
                }
            }
            else if($value['type_item'] == 'items') {
                $getDetail = get_table_where('tblitems',array('id'=>$getItem->id_item),'','row');
                $getUnit = get_table_where('tblunits',array('unitid'=>$getDetail->unit_id),'','row');
                $dataResults['item'][$key]['id'] = $getItem->id;
                $dataResults['item'][$key]['type'] = 'items';
                $dataResults['item'][$key]['name'] = $getItem->series . '<br> ('.$getDetail->name.')';
                $dataResults['item'][$key]['price'] = 0;
                $dataResults['item'][$key]['unit'] = $getUnit->unit;
                if($getDetail && !empty($getDetail->images)) {
                    $img = '<img width="50" src="'.base_url($getDetail->images).'">';
                }
            }
            $dataResults['item'][$key]['img_item'] = $img;
            $dataResults['item'][$key]['quantity'] = 1;
        }

        $dataWarehouse = get_table_where('tblwarehouse');
        $html = '<option></option>';
        foreach ($dataWarehouse as $key => $value) {
            if($value['id'] == 14) {
                $html .= '<option value="'.$value['id'].'" selected>'.$value['name'].'</option>';
            }
        }
        $dataResults['html_warehouse'] = $html;

        $this->load->view('add_export_warehouse_done', $dataResults);
    }

    public function getQuantityWarehouse($items = '', $warehouse_id = '', $localtion = '')
    {
        $quantity = get_table_where('tblwarehouse_items',array('warehouse_id'=>$warehouse_id, 'localtion'=>$localtion, 'series'=>$items),'','row');
        if(!empty($quantity)) {
            echo json_encode($quantity->product_quantity);die;
        } else {
            echo json_encode(0);die;
        }
    }

    public function add_export_warehouse_done($id_warranty = '')
    {
        $data = $this->input->post();
        $export_different=array(
            'code'=>sprintf('%06d', ch_getMaxID('id', 'tblexport_different') + 1),
            'note'=>$data['reason_export_warehouse'],
            'object'=>$data['object_export_warehouse'],
            'id_object'=>$data['id_object_export_warehouse'],
            'object_text'=>'',
            'prefix'=>get_option('prefix_export_different'),
            'date'=>to_sql_date($data['date_export_warehouse'],true),
            'staff_id'=>get_staff_user_id(),
            'date_create'=>date('Y:m:d H:i:s'),
            'status'=>0,
            'id_warranty_export_supplies_done'=>$id_warranty,
        );
        $this->db->insert('tblexport_different',$export_different);
        $insert_id = $this->db->insert_id();
        if($insert_id) {
            $items = $data['items'];
            $total = 0;
            foreach ($items as $key => $item) {
                if(!empty($item['id']))
                {
                    $_item['id_export_different'] = $insert_id;
                    $_item['product_id'] = $item['id'];
                    $_item['price'] = str_replace(',', '', $item['price']);
                    $_item['warehouses_id'] = $item['warehouses_id'];
                    $_item['localtion_warehouses_id'] = $item['localtion_warehouses_id'];
                    $_item['type'] = 'series';
                    $_item['note'] = $item['note'];
                    $_item['quantity_net'] = str_replace(',', '', $item['quantity_net']);
                    $amount = ($_item['quantity_net'] * $_item['price']);
                    $_item['amount'] = $amount;
                    $total += $amount;

                    $this->db->insert('tbltblexport_different_items',$_item);
                    $idd = $this->db->insert_id();
                }
            }
            $this->db->update('tblexport_different',array('subtotal'=>$total),array('id'=>$insert_id));

            $this->db->update('tblwarranty',array('status_done'=>2, 'type_status_done'=>$data['type_status_done'], 'staff_status_done'=>get_staff_user_id()), array('id' => $id_warranty));

            echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('cong_add_true')));
        }
        else {
            echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('cong_add_false')));
        }
    }

    public function getView_add_transfer_warehouse_done($id = '', $type_status_done = '')
    {
        $dataResults = array();
        $dataResults['id'] = $id;
        $dataResults['type_status_done'] = $type_status_done;
        $dataResults['warehouse'] = get_table_where('tblwarehouse');
        $this->load->view('add_transfer_warehouse_done', $dataResults);
    }

    public function get_localtion_default()
    {
        $data = $this->input->post();
        $warehouse = $data['warehouse'];
        $getData = get_table_where('tbllocaltion_warehouses',array('warehouse'=>$warehouse, 'status_default'=>1),'','row');
        if($getData) {
            $dataResults = $getData->id;
        }
        else {
            $dataResults = '';
        }
        echo json_encode(array('data' => $dataResults)); 
    }

    public function add_transfer_warehouse_done($id = '', $type_status_done = '')
    {
        $data = $this->input->post();
        $this->db->update('tblwarranty', array('status_done'=>3, 'type_status_done'=>$type_status_done, 'staff_status_done'=>get_staff_user_id()), array('id' => $id));

        $getWarranty = get_table_where('tblwarranty',array('id'=>$id),'','row');
        if($getWarranty) {
            $getSeries = get_table_where('tblwarranty_items',array('id_warranty_receive'=>$getWarranty->id_warranty_receive));
            $arrSeries = array();
            foreach ($getSeries as $key => $value) {
                $arrSeries[] = $value['id_series'];
            }
            if($arrSeries != array()) {
                $this->db->set('type_series', 2);
                $this->db->where_in('series', $arrSeries);
                $this->db->update('tblwarehouse_items');
            }
        }
        
        echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => 'Hoàn thành phiếu bảo hành'));
    }

    public function add_warehouse_done_by_customer($id = '')
    {
        $data = $this->input->post();
        $this->db->update('tblwarranty', array('status_done'=>1, 'staff_status_done'=>get_staff_user_id()), array('id' => $id));
        
        echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => 'Hoàn thành phiếu bảo hành'));
    }

    public function searchProductWarranty($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = 50;

        $this->db->select('tbl_products.id as id, CONCAT(tbl_products.name, "(", tbl_products.code,")") as text', false);
        $this->db->from('tbl_products');
        $this->db->where('tbl_products.type_products', 'products');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_products.name', $term);
            $this->db->or_like('tbl_products.code', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $results = $this->db->get()->result_array();

        if($results) {
            $data['results'] = [
                [
                    'text' => _l('products'), 'children' => $results
                ],
            ];
        }
        else {
            $data['results'] = [
                [
                    'text' => lang('not_results_found'), 'children' => ''
                ],
            ];
        }

        if ($id) {
            $row = get_table_where('tbl_products',array('id'=>$id),'','row');
            if ($row) {
                $data['row'] = ['id' => $row->id, 'text' => $row->name.' ('.$row->code.')'];
            } else {
                $data['row'] = ['id' => 0, 'text' => 'Not found!'];
            }
        }
        echo json_encode($data);
    }

    public function add_series()
    {
        $data = $this->input->post();
        $checkExists = get_table_where('tblseries',array('series'=>$data['number_series']),'','row');
        if($checkExists) {
            echo json_encode(array('success' => false, 'alert_type' => 'danger', 'type_error' => 1));
        }
        else {
            $customer_id = explode("__", $data['customer_id']);
            $getDetail = get_table_where('tbl_products',array('id'=>$data['product_by_series']),'','row');
            $in = array(
                'id_export_warehouses' => 0,
                'id_export_warehouse_item_id' => 0,
                'date_export_warehouses' => date('Y-m-d H:i:s'),
                'id_customer' => $customer_id[1],
                'type_item' => 'products',
                'id_item' => $data['product_by_series'],
                'code_item' => $getDetail->code,
                'name_item' => $getDetail->name,
                'series' => $data['number_series'],
                'date_create' => date('Y-m-d'),
                'staff_create' => get_staff_user_id(),
                'add_new_by_warranty' => 1
            );
            if($this->db->insert('tblseries', $in)) {
                $insert_id = $this->db->insert_id();
                echo json_encode(array('success' => true, 'alert_type' => 'success', 'type_error' => 2));
            }
            else {
                echo json_encode(array('success' => false, 'alert_type' => 'danger', 'type_error' => 3));
            }
        }
    }

    public function add_product()
    {
        $data = $this->input->post();
        $in = array(
            'category_id' => $data['category_product'],
            'type_products' => 'products',
            'code' => $data['code_product'],
            'name' => $data['name_product'],
            'unit_id' => $data['unit_product'],
            'created_by' => get_staff_user_id(),
            'date_created' => date('Y-m-d H:i:s')
        );
        if($this->db->insert('tbl_products', $in)) {
            $insert_id = $this->db->insert_id();
            echo json_encode(array('success' => true, 'id' => $insert_id));
        }
        else {
            echo json_encode(array('success' => false));
        }
    }
}