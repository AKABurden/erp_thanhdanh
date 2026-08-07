<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sms extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('sms_model');
    }
    /* List all clients */
    public function index()
    {
        if (!has_permission('sms', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('sms', '', 'create')) {
                access_denied('sms');
            }
        }
        $data['title'] = _l('sms_marketing');
        $this->load->view('admin/sms/manage', $data);
    }

    public function table()
    {
        if (!has_permission('customers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                ajax_access_denied();
            }
        }

        $this->app->get_table_data('sms');
    }
    public function send_sms($id = "")
    {
        if (!empty($this->input->post())) {
                $data = $this->input->post();
                $phone_excel = explode(',', $data['phone']);
                $phone_client = explode(',', $data['phone_client']);

                $create_by = get_staff_user_id();
                $this->db->insert(db_prefix().'sms', array(
                    'datecreated' => date('Y-m-d H:i:s'),
                    'create_by' => $create_by,
                    'brandname' => $data['brand_name']
                ));
                $id = $this->db->insert_id();


                $Count_DataSend = 0;
                $Count_AllDataSend = 0;
                if (!empty($id)) {

                    //Xử lý danh sách khách hàng theo Group
                    if (!empty($data['group_client'])) {
                        $this->db->select(db_prefix().'clients.*');
                        $this->db->where_in('groupid', $data['group_client']);
                        $this->db->join(db_prefix().'customer_groups', db_prefix().'customer_groups.customer_id = '.db_prefix().'clients.userid');
                        $this->db->group_by('customer_id');
                        $client = $this->db->get(db_prefix().'clients')->result_array();
                        foreach ($client as $key => $value) {
                            if (!empty($value['phonenumber'])) {
                                /*
                                 * Lấy nội dung theo khách hàng
                                 */
                                $content = $this->get_content($value['userid'], $data['content'], 'client');
                                $status = 0;
                                $date_send = NULL;
                                if ($data['type'] == 0) {
                                    $Status_Send = $this->FSendSms($value['phonenumber'], $content, $data['brand_name']);
                                    $status = (!empty($Status_Send) ? 2 : 3);
                                } else {
                                    if (!empty($data['date_send'])) {
                                        $date_send = to_sql_date($data['date_send'], true);
                                    }
                                }

                                $this->db->insert(db_prefix().'send_sms', array(
                                    'id_sms' => $id,
                                    'phone' => $value['phonenumber'],
                                    'message' => $content,
                                    'status' => $status,
                                    'date_send' => $date_send,
                                    'userid' => $value['userid'],
                                ));
                                ++$Count_AllDataSend;
                                if ($this->db->insert_id()) {
                                    ++$Count_DataSend;
                                    if (($kp = array_search($value['phonenumber'], $phone_excel)) !== false) {
                                        unset($phone_excel[$kp]);
                                    }

                                }


                            }

                            if (!empty($data['send_all_client'])) {
                                $this->db->where('userid', $value['userid']);
                                if (!empty($data['contact_is_primary'])) {
                                    $this->db->where('is_primary', 1);
                                }
                                $client[$key]['contact'] = $this->db->get(db_prefix().'contacts')->result_array();
                                foreach ($client[$key]['contact'] as $k => $v) {
                                    if (!empty($v['phonenumber'])) {
                                        $content = $this->get_content($v['id'], $data['content'], 'contact');
                                        $status = 0;
                                        $date_send = NULL;
                                        if ($data['type'] == 0) {
                                            $Status_Send = $this->FSendSms($v['phonenumber'], $content, $data['brand_name']);
                                            $status = (!empty($Status_Send) ? 2 : 3);
                                        } else {
                                            if (!empty($data['date_send'])) {
                                                $date_send = to_sql_date($data['date_send'], true);
                                            }
                                        }

                                        $this->db->insert(db_prefix().'send_sms', array(
                                            'id_sms' => $id,
                                            'phone' => (string)$v['phonenumber'],
                                            'message' => $content,
                                            'status' => $status,
                                            'date_send' => $date_send,
                                            'userid' => $value['userid'],
                                            'id_contact' => $v['id']
                                        ));
                                        ++$Count_AllDataSend;
                                        if ($this->db->insert_id()) {
                                            ++$Count_DataSend;
                                            if (($kp = array_search($v['phonenumber'], $phone_excel)) !== false) {
                                                unset($phone_excel[$kp]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    //Xử lý danh sách khách hàng theo khách hàng bổ sung
                    if (!empty($phone_client) && !empty($data['group_client'])) {

                        $this->db->select(db_prefix().'clients.*');
                        $this->db->where_in('concat(company," {",userid,"}")', $data['group_client']);
                        $client_add = $this->db->get(db_prefix().'clients')->result_array();
                        if (!empty($id)) {
                            foreach ($client_add as $key => $value) {
                                if (!empty($value['phonenumber'])) {
                                    /*
                                     * Lấy nội dung theo khách hàng
                                     */
                                    $content = $this->get_content($value['userid'], $data['content'], 'client');
                                    $status = 0;
                                    $date_send = NULL;
                                    if ($data['type'] == 0) {
                                        $Status_Send = $this->FSendSms($value['phonenumber'], $content, $data['brand_name']);
                                        $status = (!empty($Status_Send) ? 2 : 3);
                                    } else {
                                        if (!empty($data['date_send'])) {
                                            $date_send = to_sql_date($data['date_send'], true);
                                        }
                                    }

                                    $this->db->insert(db_prefix().'send_sms', array(
                                        'id_sms' => $id,
                                        'phone' => (string)$value['phonenumber'],
                                        'message' => $content,
                                        'status' => $status,
                                        'date_send' => $date_send,
                                        'userid' => $value['userid']
                                    ));
                                    ++$Count_AllDataSend;
                                    if ($this->db->insert_id()) {
                                        ++$Count_DataSend;
                                        if (($kp = array_search($value['phonenumber'], $phone_excel)) !== false) {
                                            unset($phone_excel[$kp]);
                                        }
                                    }
                                }


                                if (!empty($data['send_all_client'])) {
                                    $this->db->where('userid', $value['userid']);
                                    if (!empty($data['contact_is_primary'])) {
                                        $this->db->where('is_primary', 1);
                                    }
                                    $client[$key]['contact'] = $this->db->get(db_prefix().'contacts')->result_array();
                                    foreach ($client[$key]['contact'] as $k => $v) {
                                        if (!empty($v)) {
                                            $content = $this->get_content($v['id'], $data['content'], 'contact');
                                            $status = 0;
                                            $date_send = NULL;
                                            if ($data['type'] == 0) {
                                                $Status_Send = $this->FSendSms($v['phonenumber'], $content, $data['brand_name']);
                                                $status = (!empty($Status_Send) ? 2 : 3);
                                            } else {
                                                if (!empty($data['date_send'])) {
                                                    $date_send = to_sql_date($data['date_send'], true);
                                                }
                                            }

                                            $this->db->insert(db_prefix().'send_sms', array(
                                                'id_sms' => $id,
                                                'phone' => (string)$v['phonenumber'],
                                                'message' => $content,
                                                'status' => $status,
                                                'date_send' => $date_send,
                                                'userid' => $value['userid'],
                                                'id_contact' => $v['id']
                                            ));
                                            if ($this->db->insert_id()) {
                                                if (($kp = array_search($v['phonenumber'], $phone_excel)) !== false) {
                                                    unset($phone_excel[$kp]);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }


                    //Gửi sms danh dánh từ excel còn lại
                    if (!empty($phone_excel)) {
                        $status = 0;
                        $date_send = NULL;
                        if ($data['type'] == 0) {
                            $Status_Send = $this->FSendSms($value['phonenumber'], $content, $data['brand_name']);
                            $status = (!empty($Status_Send) ? 2 : 3);
                        } else {
                            if (!empty($data['date_send'])) {
                                $date_send = to_sql_date($data['date_send'], true);
                            }
                        }

                        foreach ($phone_excel as $key => $value) {
                            if (!empty($value)) {
                                $this->db->insert(db_prefix().'send_sms', array(
                                    'id_sms' => $id,
                                    'phone' => (string)$value,
                                    'message' => $data['content'],
                                    'status' => $status,
                                    'date_send' => $date_send
                                ));
                                ++$Count_AllDataSend;
                                if ($this->db->insert_id()) {
                                    ++$Count_DataSend;
                                    if (($kp = array_search($value['phonenumber'], $phone_excel)) !== false) {
                                        unset($phone_excel[$kp]);
                                    }

                                }
                            }
                        }
                    }

                    set_alert('success', (_l('send_true_sms') . $Count_DataSend . '/' . $Count_AllDataSend));
                    redirect(admin_url('sms'));
                }
                set_alert('success', (_l('send_false_sms')));
                redirect(admin_url('sms/send_sms'));
        }





        if(!empty($id))
        {
            $data['title'] = _l('view_infomation_sms');
            $this->db->select('company,concat(lastname," ",firstname) as fullname,'.db_prefix().'clients.userid,'.db_prefix().'send_sms.*,'.db_prefix().'sms.*');
            $this->db->where(db_prefix().'send_sms.id', $id);
            $this->db->join(db_prefix().'clients',db_prefix().'clients.userid = '.db_prefix().'send_sms.userid', 'left');
            $this->db->join(db_prefix().'contacts',db_prefix().'contacts.id = '.db_prefix().'send_sms.id_contact', 'left');
            $this->db->join(db_prefix().'sms',db_prefix().'sms.id = '.db_prefix().'send_sms.id_sms');
            $data['sms'] = $this->db->get(db_prefix().'send_sms')->row();
            $this->load->view('admin/sms/view_sms', $data);
        }
        else
        {
            /*
             * View data
             */
            $this->db->select('*,concat(lastname," ",firstname) as fullname');
            $data['contacts'] = $this->db->get(db_prefix().'contacts')->result_array();
            $data['clients'] = $this->db->get(db_prefix().'clients')->result_array();
            $data['title'] = _l('cong_send_sms');

            $data['template'] = get_table_where(db_prefix().'template_sms');

            $this->db->select(db_prefix().'customers_groups.*,concat('.db_prefix().'customers_groups.name," (",count('.db_prefix().'customer_groups.customer_id), ")") as full_option');
            $this->db->join(db_prefix().'customer_groups', db_prefix().'customer_groups.groupid = '.db_prefix().'customers_groups.id', 'left');
            $this->db->group_by(db_prefix().'customer_groups.groupid');
            $data['group_client'] = $this->db->get(db_prefix().'customers_groups')->result_array();

            $data['country'] = get_table_where(db_prefix().'countries');
            $customer_default_country = get_option('customer_default_country');
            $data['city'] = [];
            if(!empty($customer_default_country))
            {
                $data['city'] = get_table_where(db_prefix().'province', array('countries' => $customer_default_country));
            }
            $this->load->view('admin/sms/send_sms', $data);
        }
    }

    public function read_excel_phone()
    {
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');

        if (!empty($_FILES['file_csv']))
        {

            $fullfile = $_FILES['file_csv']['tmp_name'];
            $extension = strtoupper(pathinfo($_FILES['file_csv']['name'], PATHINFO_EXTENSION));
            if($extension != 'XLSX' && $extension != 'XLS'){
                echo json_encode(array('alert_type' => 'danger', 'success' => true, 'message' => _l('cong_not_format_excel')));die();
            }

            $inputFileType = PHPExcel_IOFactory::identify($fullfile); $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('H');
            $array_colum = array();
            for ($row = 2; $row <= $highestRow; ++$row)
            {
                for ($col = 0; $col < $highestColumnIndex; ++$col)
                {
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $array_colum[$row - 1][$col] = $value;
                }
            }
            foreach ($array_colum as $key => $row)
            {
                if($key > 1)
                {
                    if(!empty($row[0]))
                    {
                        $array_result[] = $row[0];
                    }
                }
            }

            echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => (_l('find_number_phone').' '.count($array_result).' '._l('cong_phone')), 'result' => ($array_result)));die();

        }
        echo json_encode(array('alert_type' => 'danger', 'success' => false, 'message' => _l('cong_err_add')));die();
    }

    public function get_content($id = "", $content = "", $object = "")
    {
        if(!empty($id) && ($content != "") && !empty($object) )
        {
            $field_customer = [
                'fullcode_client',
                'company',
                'vat',
                'birtday',
                'address'
            ];
            if($object == 'client')
            {
                $this->db->select('concat(prefix_client,code_client) as fullcode_client, '.db_prefix().'clients.*');
                $this->db->where('userid', $id);
                $client = $this->db->get(db_prefix().'clients')->row();
                foreach($field_customer as $key => $value)
                {
                    $content = preg_replace('"{'.$value.'}"', $client->{$value}, $content);
                }
            }
            $field_contact = [
                'company',
                'birtday'
            ];
            if($object == 'contact')
            {
                $this->db->select(db_prefix().'contact.*');
                $this->db->where('id', $id);
                $contact = $this->db->get(db_prefix().'contacts')->row();
                foreach($field_contact as $key => $value)
                {
                    $content = preg_replace('"{'.$value.'}"', $contact->{$value}, $content);
                }
            }
        }
        return $content;
    }

    /*
     *
     * Hàm gửi tin nhắn
     *
     */
    private function FSendSms($phone = "", $content = "", $brand_name = "")
    {
        if(!empty($phone) && !empty($content) && !empty($brand_name))
        {
            return true;
        }
        return false;
    }
    /*
     *
     * end Hàm gửi tin nhắn
     *
     */

    public function get_table_client()
    {
        if($this->input->is_ajax_request())
        {
            $this->app->get_table_data('customer_sms');
        }
    }


    public function get_modal_template($id = "")
    {
        if(!empty($id))
        {
            $this->db->where('id', $id);
            $data['template'] = $this->db->get(db_prefix().'template_sms')->row();
            $this->load->view('admin/sms/modal_template', $data);
        }
        else
        {
            $this->load->view('admin/sms/modal_template');
        }
    }
    public function AddTemplate()
    {
        if(!empty($this->input->post()))
        {
            $data = $this->input->post();
            if(!empty($data['id']))
            {
                $this->db->where('id', $data['id']);
                $array_update = array(
                    'name' => $data['name'],
                    'content' => $data['content']
                );
                if($this->db->update(db_prefix().'template_sms',$array_update))
                {
                    echo json_encode(array(
                        'success' => true,
                        'alert_type' => 'success',
                        'name' => $data['name'],
                        'content' => $data['content'],
                        'id' => $data['id'],
                        'type' => 'update',
                        'message' => _l('cong_update_true')
                        )
                    );die();
                }
                echo json_encode(array(
                        'success' => false,
                        'alert_type' => 'danger',
                        'name' => $data['name'],
                        'content' => $data['content'],
                        'id' => $data['id'],
                        'type' => 'update',
                        'message' => _l('cong_false_true')
                    )
                );die();
            }
            else
            {
                $create_by = get_staff_user_id();
                $this->db->insert(db_prefix().'template_sms', array(
                    'name' => $data['name'],
                    'content' => $data['content'],
                    'date_create' => date('Y-m-d H:i:s'),
                    'create_by' =>  $create_by
                    )
                );
                $id = $this->db->insert_id();
                if(!empty($id))
                {
                    echo json_encode(array(
                        'success' => true,
                        'alert_type' => 'success',
                        'name' => $data['name'],
                        'content' => $data['content'],
                        'id' => $id,
                        'type' => 'add',
                        'message' => _l('cong_add_true')
                    ));die();
                }
                echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('cong_add_false')));die();

            }
        }
    }
    public function DeleteTemplate($id = "")
    {
        if(!empty($id))
        {
            $this->db->where('id', $id);
            if($this->db->delete(db_prefix().'template_sms'))
            {
                echo json_encode(array(
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('cong_delete_true')
                    )
                );die();
            }
            echo json_encode(array(
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => _l('cong_delete_false')
                )
            );die();
        }
    }

}
