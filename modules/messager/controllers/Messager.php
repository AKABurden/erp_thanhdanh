<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Messager extends AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('messager_model');
    }
    public function index()
    {
        if(!empty($_COOKIE['access_token_page_active']))
        {
            $data['title']  =   'Quản lý tin nhắn Fanpage Facebook';

            $VersionAppFB = get_option('VersionAppFB');
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://graph.facebook.com/".$VersionAppFB."/".$_COOKIE['page_active']."/conversations?access_token=".$_COOKIE['access_token_page_active'].'&fields=updated_time,senders&limit=500&suppress_http_code=1',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Accept: */*",
                    "Cache-Control: no-cache",
                    "Connection: keep-alive",
                    "Content-Type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            $data_messager = curl_error($curl);

            curl_close($curl);
            $kt_data = json_decode($response);
            if(empty($kt_data->error->code))
            {
                $this->db->where('active', 1);
                $data['staff'] = $this->db->get(db_prefix().'staff')->result_array();
                $data['list_data'] = $response;
                $this->load->view('messagers/manage/manage', $data);
            }
            else
            {
                redirect('admin/messager/login');
            }
        }
        else
        {
            redirect('admin/messager/login');
        }
    }

    public function login()
    {
        $data['title']  =   'Đăng nhập FB';
        $this->load->view('messagers/login', $data);
    }
    /*
     * LẤY NỘI DUNG TIN NHẮN CUỘC TRÒ CHUYỆN
     */
    public function getJson_message()
    {
        $id = $this->input->get('id');
        if(!empty($id))
        {
            if(!empty($_COOKIE['access_token_page_active'])) {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://graph.facebook.com/v3.1/" . $id . "/messages?access_token=" . $_COOKIE['access_token_page_active'] . "&fields=message,from,to,created_time,tags,attachments&limit=16&pretty=0&suppress_http_code=1",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "Accept: */*",
                        "Cache-Control: no-cache",
                        "Connection: keep-alive",
                        "Content-Type: application/json"
                    ),
                ));

                $response = curl_exec($curl);
                $data_messager = curl_error($curl);
                curl_close($curl);
                $data['message'] = $response;
                $data['chat_area'] = true;
                $data['id_chat'] = $id;
                $this->load->view('messagers/manage/content_mid', $data);
            }
            else
            {
                redirect('messager/login');
            }
        }
    }
    /*
    *
    * Upload để gửi file từ PC
    */
    public function uploadfilepc()
    {
        if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '' && !empty($this->input->post('userid')))
        {
            $userid = $this->input->post('userid');
            if (!file_exists(FCPATH . 'uploads/Messager')) {
                @mkdir(FCPATH . 'uploads/Messager');
                fopen(FCPATH . 'uploads/Messager' . '/index.html', 'w');
            }
            $path        = FCPATH . 'uploads/Messager' . '/'.$userid;
            $tmpFilePath = $_FILES['file']['tmp_name'];
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                $path_parts         = pathinfo($_FILES["file"]["name"]);
                $extension          = $path_parts['extension'];
                $extension = strtolower($extension);
                $allowed_extensions = array(
                    'jpg',
                    'jpeg',
                    'png'
                );
                if (!in_array($extension, $allowed_extensions)) {
                    echo json_encode(array('success' => false));die();
                }
                if (!file_exists($path)) {
                    mkdir($path);
                    fopen($path . '/index.html', 'w');
                }
                $_FILES["file"]["name"] = time().'_'.$_FILES["file"]["name"];
                $filename    = unique_filename($path, $_FILES["file"]["name"]);
                $newFilePath = $path . '/' . $filename;
                if(move_uploaded_file($tmpFilePath, $newFilePath))
                {
                    echo json_encode(array('success' => true,'name' => $filename, 'url' => base_url('uploads/Messager/'.$userid.'/'.$filename), 'newfile' => $newFilePath));die();
                }
            }
        }
        echo json_encode(array('success' => false));die();
    }
    /*
    *
    *   Xóa file khi gửi xong tin nhắn
    *
    */
    public function deleteFile()
    {
        if(!empty($this->input->post('url')))
        {
            unlink($this->input->post('url'));
        }
    }

    public function get_lead_to_phone()
    {
        $phone_name = $this->input->post('phone_number');
        $phone_name = explode(' - ', $phone_name);
        $search = false;
        if(!empty($phone_name))
        {
            if(count($phone_name) == 1)
            {
                if(!empty($phone_name[0]))
                {
                    $this->db->select('tblclients.*, group_concat(leadid) as list_leadid');
                    $this->db->like('phonenumber', $phone_name[0])
                        ->or_like('company', $phone_name[0])
                        ->group_by('tblclients.userid');
                    $clients = $this->db->get('tblclients')->result_array();
                    if(!empty($clients[0]['list_leadid']))
                    {
                        $this->db->where_not_in('id', $clients[0]['list_leadid']);
                    }
                    $this->db->group_start()
                        ->like('phonenumber', $phone_name[0])
                        ->or_like('name', $phone_name[0])
                    ->group_end();
                    $lead = $this->db->get('tblleads')->result_array();

                    echo json_encode([
                        'lead' => $lead,
                        'client' => $clients,
                        'search' => $search
                    ]);die();
                }
            }
            else if(count($phone_name) == 4)
            {

                $search = true;
                if($phone_name[3] == 'KH')
                {
                    $this->db->where('userid', $phone_name[2]);
                    $clients = $this->db->get('tblclients')->row();
                    if(!empty($clients->client_image))
                    {
                        $clients->img = base_url('download/preview_image?path=uploads/clients/'.$clients->userid.'/thumb_'.$clients->client_image);
                    }
                    $clients = !empty($clients) ? $clients : [];
                    if(!empty($clients))
                    {
                        $Advisory = $this->GetAdvisoryClient($clients->userid);
                        //Lấy dơn hàng
                        $this->load->model('orders_model');
                        $orders = $this->orders_model->getClientOrder($clients->userid);
                    }


                    echo json_encode([
                        'type_data' => 'KH',
                        'data' => $this->load->view('messagers/manage/right/tab_content_customer', ['data' => $clients], true),
                        'advisory' => $this->load->view('messagers/manage/right/advisory_client', ['data' => (!empty($Advisory) ? $Advisory : [])], true),
                        'orders' => $this->load->view('messagers/manage/right/tab_orders_client', ['data' => (!empty($orders) ? $orders : [])], true),
                        'countOrder' => (!empty($orders) ? count($orders) : 0),
                        'countAdvisory' => (!empty($Advisory) ? count($Advisory) : 0),
                        'search' => $search,
                        'id_facebook' => !empty($clients->id_facebook) ? $clients->id_facebook : false
                    ]);die();
                }
                else {
                    $this->db->where('name', $phone_name[0])
                        ->where('phonenumber', $phone_name[1])
                        ->where('id', $phone_name[2]);
                    $lead = $this->db->get('tblleads')->row();
                    $lead = !empty($lead) ? $lead : [];

                    if(!empty($lead))
                    {
                        $Advisory = $this->GetAdvisoryLead($lead->id);
                    }

                    if (!empty($lead->lead_image)) {
                        $lead->img = base_url('download/preview_image?path=uploads/leads/' . $lead->id . '/thumb_' . $lead->lead_image);
                    }
                    echo json_encode([
                        'type_data' => 'KHTN',
                        'data' => $this->load->view('messagers/manage/right/tab_content_lead', ['data' => $lead], true),
                        'advisory' => $this->load->view('messagers/manage/right/advisory_lead', ['data' => !empty($Advisory) ? $Advisory : []], true),
                        'orders' => $this->load->view('messagers/manage/right/tab_orders_client', ['data' => []], true),
                        'countAdvisory' => (!empty($Advisory) ? count($Advisory) : 0),
                        'search' => $search,
                        'id_facebook' => !empty($lead->id_facebook) ? $lead->id_facebook : false
                    ]);
                    die();
                }
            }
        }
        echo json_encode(['search' => $search, 'lead' => [], 'client' => []]);die();
    }

    public function get_lead_to_facebook()
    {
        $id_facebook = $this->input->post('id_facebook');
        if(!empty($id_facebook))
        {
            $this->db->where('id_facebook', $id_facebook);
            $client = $this->db->get('tblclients')->row();
            if(!empty($client))
            {
                if(!empty($client->client_image))
                {
                    $client->img = base_url('download/preview_image?path=uploads/clients/'.$client->userid.'/thumb_'.$client->client_image);
                }

                $Advisory = $this->GetAdvisoryClient($client->userid);

                //Lấy dơn hàng
                $this->load->model('orders_model');
                $orders = $this->orders_model->getClientOrder($client->userid);
                echo json_encode([
                    'type_data' => 'KH',
                    'data' => $this->load->view('messagers/manage/right/tab_content_customer', ['data' => $client], true),
                    'advisory' => $this->load->view('messagers/manage/right/advisory_client', ['data' => (!empty($Advisory) ? $Advisory : [])], true),
                    'orders' => $this->load->view('messagers/manage/right/tab_orders_client', ['data' => (!empty($orders) ? $orders : [])], true),
                    'countOrder' => (!empty($orders) ? count($orders) : 0),
                    'countAdvisory' => (!empty($Advisory) ? count($Advisory) : 0)
                ]);die();
            }
            else if(empty($client))
            {
                $this->db->where('id_facebook', $id_facebook);
                $lead = $this->db->get('tblleads')->row();
                if(!empty($lead->lead_image))
                {
                    $lead->img = base_url('download/preview_image?path=uploads/leads/'.$lead->id.'/thumb_'.$lead->lead_image);
                }

                if(!empty($lead))
                {
                    $Advisory = $this->GetAdvisoryLead($lead->id);

                    echo json_encode([
                        'type_data' => 'KHTN',
                        'data' => $this->load->view('messagers/manage/right/tab_content_lead', ['data' => $lead], true),
                        'advisory' => $this->load->view('messagers/manage/right/advisory_lead', ['data' => (!empty($Advisory) ? $Advisory : [])], true),
                        'orders' => $this->load->view('messagers/manage/right/tab_orders_client', ['data' => []], true),
                        'countOrder' => 0,
                        'countAdvisory' => (!empty($Advisory) ? count($Advisory) : 0)
                    ]);die();
                }
                else
                {
                    $this->db->where('id_facebook', $id_facebook);
                    $data = $this->db->get('tbllist_fb')->row();
                    if(empty($data))
                    {
                        $arrayAdd = ['id_facebook' => $id_facebook];
                        $name = $this->input->post('name');
                        if(!empty($name))
                        {
                            $arrayAdd['name'] = trim($name);
                        }
                        $arrayAdd['prefix'] = date('dmY');

                        $this->db->select('max(orders_day) as max_orders_day');
                        $this->db->where('prefix', $arrayAdd['prefix']);
                        $list_fb = $this->db->get('tbllist_fb')->row();
                        if(!empty($list_fb->max_orders_day))
                        {
                            $arrayAdd['orders_day'] = $list_fb->max_orders_day+1;
                        }
                        else
                        {
                            $arrayAdd['orders_day'] = 1;
                        }

                        $arrayAdd['create_by'] = get_staff_user_id();
                        $arrayAdd['date_create'] = date('Y-m-d H:i:s');
                        $this->db->insert('tbllist_fb', $arrayAdd);
                        if($this->db->insert_id())
                        {
                            $id = $this->db->insert_id();

                            $paste_img = FCPATH . 'uploads/avatarFB' . '/' . $id . '/';
                            _maybe_create_upload_path($paste_img);

                            $image_small = 'https://graph.facebook.com/' . $id_facebook . '/picture?height=24&width=32&access_token=' . $_COOKIE['access_token_page_active'];
                            $image_thumb = 'https://graph.facebook.com/' . $id_facebook . '/picture?height=240&width=320&access_token=' . $_COOKIE['access_token_page_active'];
                            $time = time();
                            @copy($image_small, $paste_img . 'small_' . $time . '.jpg');
                            @copy($image_thumb, $paste_img . 'thumb_' . $time . '.jpg');
                            $avatar = $time . '.jpg';
                            $this->db->where('id', $id);
                            $this->db->update('tbllist_fb', ['avatar' => $avatar]);

                            $this->db->where('id', $id);
                            $data = $this->db->get('tbllist_fb')->row();
                            if(!empty($data->avatar))
                            {
                                $paste_img = base_url('download/preview_image?path=uploads/avatarFB/'.$data->id.'/thumb_'.$data->avatar);
                                $data->img = $paste_img;
                            }
                            echo json_encode(['data' => $this->load->view('messagers/manage/right/tab_addFB', [
                                'data' => $data
                            ], true)]);die();
                        }
                    }
                    else
                    {
                        if(!empty($data->avatar))
                        {
                            $paste_img = base_url('download/preview_image?path=uploads/avatarFB/'.$data->id.'/thumb_'.$data->avatar);
                            $data->img = $paste_img;
                        }
                        echo json_encode(['data' => $this->load->view('messagers/manage/right/tab_addFB', [
                            'data' => $data
                        ], true)]);die();
                    }
                }
            }

        }
//        echo json_encode(['data' => $this->load->view('messagers/manage/right/tab_addFB', ['data' => []], true)]);die();
        echo json_encode(['data' => '']);die();
    }

    public function detail_Listfb()
    {
        if($this->input->post()) {

            $data = $this->input->post();
            if (empty($data['id_facebook'])) {
                echo json_encode([
                    'message' => _l('cong_check_facebook'),
                    'alert_type' => 'danger',
                    'success' => false
                ]);
                die();
            }
            $_data = [];
            if(isset($data['phonenumber']))
            {
                $_data['phonenumber'] = $data['phonenumber'];
            }
            if(isset($data['address']))
            {
                $_data['address'] = $data['address'];
            }
            if(isset($data['gender']))
            {
                $_data['gender'] = $data['gender'];
            }
            if(isset($data['email']))
            {
                $_data['email'] = $data['email'];
            }
            if(isset($data['note']))
            {
                $_data['description'] = $data['note'];
            }
            if(isset($data['birtday']))
            {
                $_data['birtday'] = to_sql_date($data['birtday']);
            }
            $_data['id_facebook'] = $data['id_facebook'];
            if(isset($data['company']))
            {
                $_data['company'] = trim($data['company']);
            }
            if(isset($data['name']))
            {
                $_data['name'] = trim($data['name']);
            }
            if(isset($data['zcode']))
            {
                $_data['zcode'] = trim($data['zcode']);
            }
            $this->db->where('id', $data['id']);
            $this->db->update('tbllist_fb', $_data);

            echo json_encode([
                'message' => _l('cong_update_true'),
                'alert_type' => 'success',
                'success' => true
            ]);
            die();
        }
        echo json_encode([
            'message' => _l('cong_add_false'),
            'alert_type' => 'danger',
            'success' => false
        ]);die();
    }

    public function detail_lead()
    {
        if($this->input->post()) {

            $data = $this->input->post();
            if (empty($data['id_facebook'])) {
                echo json_encode([
                    'message' => _l('cong_check_facebook'),
                    'alert_type' => 'danger',
                    'success' => false
                ]);
                die();
            }
            $_data = [];
            if(isset($data['phonenumber']))
            {
                $_data['phonenumber'] = $data['phonenumber'];
            }
            if(isset($data['address']))
            {
                $_data['address'] = $data['address'];
            }
            if(isset($data['gender']))
            {
                $_data['gender'] = $data['gender'];
            }
            if(isset($data['email']))
            {
                $_data['email'] = $data['email'];
            }
            if(isset($data['note']))
            {
                $_data['description'] = $data['note'];
            }
            if(isset($data['birtday']))
            {
                $_data['birtday'] = to_sql_date($data['birtday']);
            }
            $_data['id_facebook'] = $data['id_facebook'];
            if(isset($data['company']))
            {
                $_data['company'] = trim($data['company']);
            }
            if(isset($data['name']))
            {
                $_data['name'] = trim($data['name']);
            }
            if (empty($data['id'])) {
                $_data['prefix_lead'] = date('ymd');
                $_data['dateadded'] = date('Y-m-d H:i:s');
                $_data['addedfrom'] = get_staff_user_id();
                if (!empty($data['zcode'])) {
                    $_data['zcode'] = $data['zcode'];
                    $_data['code_type'] = 'TN';
                }

                $this->db->insert(db_prefix() . 'leads', $_data);
                if ($this->db->insert_id()) {

                    $id = $this->db->insert_id();
                    $paste_lead = FCPATH . 'uploads/leads' . '/' . $id . '/';
                    _maybe_create_upload_path($paste_lead);

                    $code_lead = sprintf("%06s", $id);
                    $array_code = ['code_lead' => $code_lead];

                    if (!empty($_data['id_facebook'])) {
                        $image_small = 'https://graph.facebook.com/' . $_data['id_facebook'] . '/picture?height=24&width=32&access_token=' . $_COOKIE['access_token_page_active'];
                        $image_thumb = 'https://graph.facebook.com/' . $_data['id_facebook'] . '/picture?height=240&width=320&access_token=' . $_COOKIE['access_token_page_active'];
                        $time = time();
                        @copy($image_small, $paste_lead . 'small_' . $time . '.jpg');
                        @copy($image_thumb, $paste_lead . 'thumb_' . $time . '.jpg');
                        $array_code['lead_image'] = $time . '.jpg';
                    }

                    $this->db->where('name', 'Facebook');
                    $leads_sources = $this->db->get(db_prefix().'leads_sources')->row();
                    if(!empty($leads_sources))
                    {
                        $source = $leads_sources->id;
                    }
                    else
                    {
                        $this->db->insert(db_prefix().'leads_sources', ['name' => 'Facebook']);
                        $source = $this->db->insert_id();
                    }

                    $array_code['source'] = $source;

                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'leads', $array_code);
                    $this->updateDataTag($id, $data['tag'], 'lead');
                    echo json_encode([
                        'message' => _l('cong_add_true'),
                        'alert_type' => 'success',
                        'success' => true
                    ]);
                    die();
                }
            }
            else {

                if (!empty($data['zcode'])) {
                    $_data['code_type'] = 'TN';
                }
                $_data['zcode'] = $data['zcode'];
                $this->db->where('id', $data['id']);
                $this->db->update(db_prefix() . 'leads', $_data);
                echo json_encode([
                    'message' => _l('cong_update_true'),
                    'alert_type' => 'success',
                    'success' => true
                ]);
                die();
            }
        }
        echo json_encode([
            'message' => _l('cong_add_false'),
            'alert_type' => 'danger',
            'success' => false
        ]);die();
    }

    public function detail_customer()
    {
        if($this->input->post())
        {

            $data = $this->input->post();
            if(empty($data['id_facebook']))
            {
                echo json_encode([
                    'message' => _l('cong_check_facebook'),
                    'alert_type' => 'danger',
                    'success' => false
                ]);die();
            }
            $_data = [];

            if(isset($data['phonenumber']))
            {
                $_data['phonenumber'] = $data['phonenumber'];
            }
            if(isset($data['address']))
            {
                $_data['address'] = $data['address'];
            }
            if(isset($data['gender'])) {
                $_data['gender'] = $data['gender'];
            }
            if(isset($data['email'])) {
                $_data['email_client'] = $data['email'];
            }
            if(isset($data['note'])) {
                $_data['note'] = $data['note'];
            }
            $_data['id_facebook'] = $data['id_facebook'];

            if(isset($data['company'])) {
                $_data['company'] = $data['company'];
            }
            if(isset($data['fullname'])) {
                $_data['fullname'] = $data['fullname'];
            }
            if(isset($data['birtday'])) {
                $_data['birtday'] = to_sql_date($data['birtday']);
            }

            if(empty($data['userid']))
            {
                $_data['datecreated'] = date('Y-m-d H:i:s');
                $_data['addedfrom'] = get_staff_user_id();
                $_data['prefix_client'] = date('ymd');
                if(!empty($data['zcode']))
                {
                    $_data['code_type'] = 'TN';
                }
                else
                {
                    $_data['code_type'] = "NEW";
                }
                $this->db->insert(db_prefix().'clients', $_data);
                if($this->db->insert_id())
                {
                    $userid = $this->db->insert_id();
                    $paste_client = FCPATH . 'uploads/clients' . '/'.$userid.'/';
                    _maybe_create_upload_path($paste_client);
                    $code_client   =   sprintf("%06s", $userid);
                    $array_code = ['code_client' => $code_client];
                    if(!empty($_data['id_facebook']))
                    {
                        $image_small = 'https://graph.facebook.com/'.$_data['id_facebook'].'/picture?height=24&width=32&access_token='.$_COOKIE['access_token_page_active'];
                        $image_thumb = 'https://graph.facebook.com/'.$_data['id_facebook'].'/picture?height=100&width=100&access_token='.$_COOKIE['access_token_page_active'];
                        $time = time();
                        @copy($image_small, $paste_client.'small_'.$time.'.jpg');
                        @copy($image_thumb, $paste_client.'thumb_'.$time.'.jpg');
                        $array_code['client_image'] = $time.'.jpg';
                    }

                    $this->db->where('name', 'Facebook');
                    $leads_sources = $this->db->get(db_prefix().'leads_sources')->row();
                    if(!empty($leads_sources))
                    {
                        $source = $leads_sources->id;
                    }
                    else
                    {
                        $this->db->insert(db_prefix().'leads_sources', ['name' => 'Facebook']);
                        $source = $this->db->insert_id();
                    }

                    $array_code['sources'] = $source;

                    $this->db->where('userid', $userid);
                    $this->db->update(db_prefix().'clients', $array_code);

                    $this->updateDataTag($userid, $data['tag'], 'client');
                    echo json_encode([
                        'message' => _l('cong_add_true'),
                        'alert_type' => 'success',
                        'success' => true
                    ]);die();
                }
            }
            else
            {
                if(isset($data['zcode']))
                {
                    $_data['zcode'] = $data['zcode'];
                }
                $this->db->where('userid', $data['userid']);
                $get_client = $this->db->get(db_prefix().'clients')->row();
                if(!empty($get_client))
                {
                    if($get_client->code_type == "NEW" || $get_client->code_type == "NOT NEW")
                    {
                        if(!empty($data['zcode']))
                        {
                            $_data['code_type'] = 'TN';
                        }
                    }
                    $this->db->where('userid', $data['userid']);
                    if($this->db->update(db_prefix().'clients', $_data))
                    {
                        echo json_encode([
                            'message' => _l('cong_update_true'),
                            'alert_type' => 'success',
                            'success' => true
                        ]);die();
                    }
                }
            }
        }
        echo json_encode([
            'message' => _l('cong_add_false'),
            'alert_type' => 'danger',
            'success' => false
        ]);die();
    }

    public function load_new_client()
    {
        if($this->input->post())
        {
            $data = $this->input->post();
            if(!empty($data['id_facebook']))
            {
                if($data['type'] == 'lead')
                {
                    echo json_encode([
                        'type_data' => 'KHTN',
                        'data' => $this->load->view('messagers/manage/right/tab_content_lead', ['data' => [], 'id_facebook' => $data['id_facebook']], true),
                        'advisory' => ""
                    ]);die();
                }
                else if($data['type'] == 'client')
                {
                    echo json_encode([
                        'type_data' => 'KH',
                        'data' => $this->load->view('messagers/manage/right/tab_content_customer', ['data' => [], 'id_facebook' => $data['id_facebook']], true),
                        'advisory' => ""
                    ]);die();
                }
            }
        }
    }

    // cập nhật tag cho khách hàng tiềm năng - khách hàng - listfb
    public function updateDataTag($id = "", $tag = "" , $rel_type = 'lead')
    {
        if(!empty($id))
        {
            //Xóa tag
            if($this->input->post('rel_type'))
            {
                $rel_type = $this->input->post('rel_type');
            }
            else
            {
                return false;
            }

            $this->db->where('rel_id', $id)->where('rel_type', $rel_type)->delete('tbltaggablesfb');

            if($this->input->post('tag'))
            {
                $tag = $this->input->post('tag');
            }
            $tag = explode(',', $tag);
            foreach($tag as $key => $value)
            {
                if(!empty(trim($value)))
                {
                    $this->db->where('name', trim($value));
                    $Gettag = $this->db->get('tbltagsfb')->row();
                    if(!empty($Gettag))
                    {
                        $this->db->insert('tbltaggablesfb', [
                            'rel_type' => $rel_type,
                            'rel_id' => $id,
                            'tag_id' => $Gettag->id,
                            'tag_order' => ($key+1)
                        ]);
                    }
                    else
                    {
                        $this->db->insert('tbltagsfb', ['name' => trim($value)]);
                        $idtag = $this->db->insert_id();
                        if(!empty($idtag))
                        {
                            $this->db->insert('tbltaggablesfb', [
                                'rel_type' => $rel_type,
                                'rel_id' => $id,
                                'tag_id' => $idtag,
                                'tag_order' => ($key+1)
                            ]);
                        }
                    }
                }
            }
        }
    }


    public function staff_assigned_client()
    {
        if($this->input->post())
        {
            $data = $this->input->post();
            if(!empty($data['userid']))
            {
                $_data = [];
                $_data['customer_admins'] = $data['id_staff'];
                if($this->clients_model->assign_admins($_data, $data['userid']))
                {
                    echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => _l('cong_event_assigned_true') ]);die();
                }
            }
        }
        echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => _l('cong_event_assigned_false') ]);die();
    }

    public function staff_assigned_lead()
    {
        if($this->input->post())
        {
            $data = $this->input->post();
            $lead = $data['id'];
            if(!empty($lead)) {
                $assInsert = 0;
                $array_id = [];
                foreach ($data['id_staff'] as $key => $value) {
                    $this->db->where('id_lead', $lead);
                    $this->db->where('staff', $value);
                    $kt_assigned = $this->db->get('lead_assigned')->row();
                    if (empty($kt_assigned)) {
                        $data_insert = [
                            'id_lead' => $lead,
                            'staff' => $value,
                            'date_create' => !empty($create_date[$value]) ? $create_date[$value] : date('Y-m-d H:i:s'),
                            'created_by' => !empty($create_by[$value]) ? $create_by[$value] : get_staff_user_id(),
                        ];
                    } else {
                        $array_id[] = $kt_assigned->id;
                        ++$assInsert;
                        continue;
                    }
                    $this->db->insert(db_prefix() . 'lead_assigned', $data_insert);
                    if ($this->db->insert_id()) {
                        $array_id[] = $this->db->insert_id();
                        add_notification([
                            'description' => 'cong_assigned_lead',
                            'touserid' => $value,
                            'link' => 'leads/index=' . $lead
                        ]);
                        ++$assInsert;
                    }
                }

                if (!empty($array_id)) {
                    $this->db->where_not_in('id', $array_id);
                }
                $this->db->where('id_lead', $lead);
                $assigned_delete = $this->db->get(db_prefix() . 'lead_assigned')->result_array();
                foreach ($assigned_delete as $key => $value) {
                    add_notification([
                        'description' => 'cong_none_assigned_lead',
                        'touserid' => $value['staff'],
                        'link' => 'leads/index=' . $value['id_lead']
                    ]);
                }

                if (!empty($array_id)) {
                    $this->db->where_not_in('id', $array_id);
                }
                $this->db->where('id_lead', $lead);
                $this->db->delete(db_prefix() . 'lead_assigned');

                if ($assInsert > 0) {
                    echo json_encode([
                        'success' => true,
                        'alert_type' => 'success',
                        'message' => _l('cong_add_true')
                    ]);
                    die();
                }
            }
        }
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => _l('cong_add_false')
        ]);die();
    }

    public function staff_assigned_listfb()
    {
        if($this->input->post())
        {
            $data = $this->input->post();
            $idList = $data['id'];
            if(!empty($idList)) {
                $assInsert = 0;
                $array_id = [];
                foreach ($data['id_staff'] as $key => $value) {
                    $this->db->where('id_listfb', $idList);
                    $this->db->where('staff', $value);
                    $kt_assigned = $this->db->get('tbllistfb_assigned')->row();
                    if (empty($kt_assigned)) {
                        $data_insert = [
                            'id_listfb' => $idList,
                            'staff' => $value,
                            'date_create' => !empty($create_date[$value]) ? $create_date[$value] : date('Y-m-d H:i:s'),
                            'created_by' => !empty($create_by[$value]) ? $create_by[$value] : get_staff_user_id(),
                        ];
                    } else {
                        $array_id[] = $kt_assigned->id;
                        ++$assInsert;
                        continue;
                    }
                    $this->db->insert('tbllistfb_assigned', $data_insert);
                    if ($this->db->insert_id()) {
                        $array_id[] = $this->db->insert_id();
                        ++$assInsert;
                    }
                }

                if (!empty($array_id)) {
                    $this->db->where_not_in('id', $array_id);
                }
                $this->db->where('id_listfb', $idList);
                $assigned_delete = $this->db->get('tbllistfb_assigned')->result_array();
                foreach ($assigned_delete as $key => $value) {
                    add_notification([
                        'description' => 'cong_none_assigned_lead',
                        'touserid' => $value['staff'],
                        'link' => 'leads/index=' . $value['id_lead']
                    ]);
                }

                if (!empty($array_id)) {
                    $this->db->where_not_in('id', $array_id);
                }
                $this->db->where('id_listfb', $idList);
                $this->db->delete('tbllistfb_assigned');

                if ($assInsert > 0) {
                    echo json_encode([
                        'success' => true,
                        'alert_type' => 'success',
                        'message' => _l('cong_add_true')
                    ]);
                    die();
                }
            }
        }
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => _l('cong_add_false')
        ]);die();
    }
    //Hàm chuyển đổi khách hàng tiềm năng của facebook
    public function get_convert_dataFB($id)
    {
        $this->load->model('leads_model');
        if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($id)) {
            ajax_access_denied();
        }
        if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1') {
            $this->load->model('gdpr_model');
            $data['purposes'] = $this->gdpr_model->get_consent_purposes($id, 'lead');
        }
        $data['lead'] = $this->leads_model->get($id);

        //Công bổ sung
        $customer_default_country = get_option('customer_default_country');  // quốc gia mặc định
        $data['city'] = get_table_where(db_prefix().'province', [
            'countries' => (!empty($data['lead']->country) ? $data['lead']->country : $customer_default_country)
        ]);

        $data['district'] = get_table_where(db_prefix().'district', [
            'provinceid' => $data['lead']->city
        ]);

        $data['ward'] = get_table_where(db_prefix().'ward', ['districtid' => $data['lead']->district]);
        $data['dt'] = get_table_where(db_prefix().'combobox_client', ['type' => 'dt']);
        $data['kt'] = get_table_where(db_prefix().'combobox_client', ['type' => 'kt']);
        $data['marriage'] = get_table_where(db_prefix().'combobox_client', ['type' => 'marriage']);
        $data['religion'] = get_table_where(db_prefix().'combobox_client', ['type' => 'religion']);
        $data['info_group'] = $this->leads_model->getInfoGroupLead($id);

        $data['type_client'] = get_table_where(db_prefix().'type_client');
        $data['sources']  = $this->leads_model->get_source();

        //end
        $this->load->view('messagers/manage/modal/convert_to_customerFB', $data);
    }


    //Chuyển thành khách hàng từ listFB
    public function WarClient()
    {
        $id = $this->input->post('id');
        if(!empty($id))
        {
            $this->db->where('id', $id);
            $listFB = $this->db->get('tbllist_fb')->row();
            if(!empty($listFB))
            {
                $this->db->where('id_facebook', $listFB->id_facebook);
                $Numclients = $this->db->get('tblclients')->num_rows();
                if($Numclients == 0)
                {
                    $this->db->insert('tblclients', [
                        'id_facebook' => $listFB->id_facebook,
                        'prefix_client' => date('ymd'),
                        'code_type' => 'NEW',
                        'fullname' => $listFB->name,
                        'company' => $listFB->company,
                        'email_client' => $listFB->email,
                        'address' => $listFB->address,
                        'birtday' => $listFB->birtday,
                        'gender' => $listFB->gender,
                        'addedfrom' => get_staff_user_id(),
                        'datecreated' => date('Y-m-d H:i:s')
                    ]);
                    $idClient = $this->db->insert_id();
                    if(!empty($idClient))
                    {
                        $paste_img = FCPATH . 'uploads/avatarFB' . '/' . $listFB->id . '/';
                        $paste_imgClient = get_upload_path_by_type('customer') . $idClient . '/';
                        _maybe_create_upload_path($paste_imgClient);

                        $image_small = $paste_img.'small_'.$listFB->avatar;
                        $image_thumb = $paste_img.'thumb_'.$listFB->avatar;
                        $time = time();
                        @copy($image_small, $paste_imgClient . 'small_' . $time . '.jpg');
                        @copy($image_thumb, $paste_imgClient . 'thumb_' . $time . '.jpg');
                        $avatar = $time . '.jpg';

                        $code_client   =   sprintf("%06s", $idClient);

                        $this->db->where('userid', $idClient);
                        $this->db->update(db_prefix().'clients', [
                            'code_client' => $code_client,
                            'client_image' => $avatar
                        ]);
                        echo json_encode([
                            'alert_type' => 'success',
                            'success' => true,
                            'id_facebook' => $listFB->id_facebook,
                            'message' => _l('cong_war_client_true')
                        ]);die();
                    }
                }
                echo json_encode([
                    'alert_type' => 'danger',
                    'success' => true,
                    'id_facebook' => $listFB->id_facebook,
                    'message' => _l('cong_war_isset_client')
                ]);die();
            }
        }
        echo json_encode([
            'alert_type' => 'danger',
            'success' => false,
            'message' => _l('cong_war_client_false')
        ]);die();
    }

    //Chuyển thành khách hàng tiềm năng từ listFB
    public function WarLead()
    {
        $id = $this->input->post('id');
        if(!empty($id))
        {
            $this->db->where('id', $id);
            $listFB = $this->db->get('tbllist_fb')->row();
            if(!empty($listFB))
            {
                $this->db->where('id_facebook', $listFB->id_facebook);
                $Numclients = $this->db->get('tblclients')->num_rows();
                if($Numclients == 0)
                {
                    $this->db->where('id_facebook', $listFB->id_facebook);
                    $NumLead = $this->db->get('tblleads')->num_rows();
                    if($NumLead == 0)
                    {
                        $this->db->insert('tblleads', [
                            'id_facebook' => $listFB->id_facebook,
                            'prefix_lead' => date('ymd'),
                            'code_type' => 'NEW',
                            'name' => $listFB->name,
                            'company' => $listFB->company,
                            'email' => $listFB->email,
                            'address' => $listFB->address,
                            'birtday' => $listFB->birtday,
                            'gender' => $listFB->gender,
                            'addedfrom' => get_staff_user_id(),
                            'dateadded' => date('Y-m-d H:i:s')
                        ]);
                        $idLead = $this->db->insert_id();
                        if(!empty($idLead))
                    {
                        $paste_img = FCPATH . 'uploads/avatarFB' . '/' . $listFB->id . '/';
                        $paste_imgLead = get_upload_path_by_type('lead') . $idLead . '/';
                        _maybe_create_upload_path($paste_imgLead);

                        $image_small = $paste_img.'small_'.$listFB->avatar;
                        $image_thumb = $paste_img.'thumb_'.$listFB->avatar;
                        $time = time();
                        @copy($image_small, $paste_imgLead . 'small_' . $time . '.jpg');
                        @copy($image_thumb, $paste_imgLead . 'thumb_' . $time . '.jpg');
                        $avatar = $time . '.jpg';

                        $code_client   =   sprintf("%06s", $idLead);

                        $this->db->where('id', $idLead);
                        $this->db->update(db_prefix().'leads', [
                            'code_lead' => $code_client,
                            'lead_image' => $avatar
                        ]);
                        echo json_encode([
                            'alert_type' => 'success',
                            'success' => true,
                            'id_facebook' => $listFB->id_facebook,
                            'message' => _l('cong_war_lead_true')
                        ]);die();
                    }
                    }
                    echo json_encode([
                        'alert_type' => 'danger',
                        'success' => true,
                        'id_facebook' => $listFB->id_facebook,
                        'message' => _l('cong_war_isset_lead_not_add_lead')
                    ]);die();
                }
                echo json_encode([
                    'alert_type' => 'danger',
                    'success' => true,
                    'id_facebook' => $listFB->id_facebook,
                    'message' => _l('cong_war_isset_client_not_add_lead')
                ]);die();
            }
        }
        echo json_encode([
            'alert_type' => 'danger',
            'success' => false,
            'message' => _l('cong_war_lead_false')
        ]);die();
    }

    public function GetAdvisoryClient($client = "")
    {
        if(!empty($client))
        {
            $this->db->where('type', 'client');
            $procedure = $this->db->get(db_prefix().'procedure_client')->row();
            if(!empty($procedure))
            {
                //Lấy chăm sóc
                $this->db->where('client', $client);
                $this->db->order_by('date', 'desc');
                $care_of_client = $this->db->get('tblcare_of_clients')->result();
                foreach($care_of_client as $kCareOf => $vCareOf)
                {
                    $this->db->select('tblprocedure_client_detail.*, tblprocedure_care_of.active, .tblprocedure_care_of.date_create');
                    $this->db->where('id_detail', $procedure->id)->order_by('orders', 'asc');
                    $this->db->join('tblprocedure_care_of', 'tblprocedure_care_of.status_procedure = tblprocedure_client_detail.id and id_care_of = '.$vCareOf->id, 'left');
                    $care_of_client[$kCareOf]->detail  = $this->db->get('tblprocedure_client_detail')->result();
                }
                return $care_of_client;
            }
        }
        return false;
    }

    public function GetAdvisoryLead($lead = "")
    {
        $this->db->where('type', 'lead');
        $procedure = $this->db->get(db_prefix().'procedure_client')->row();
        if(!empty($procedure))
        {
            $this->db->where('lead', $lead);
            $this->db->order_by('date', 'desc');
            $advisory_lead = $this->db->get('tbladvisory_lead')->result();
            foreach($advisory_lead as $kAdvisory => $vAdvisoty)
            {
                $this->db->select('tblprocedure_client_detail.*, tblprocedure_advisory_lead.active, .tblprocedure_advisory_lead.date_create');
                $this->db->where('id_detail', $procedure->id);
                $this->db->order_by('orders', 'asc');
                $this->db->join('tblprocedure_advisory_lead', 'tblprocedure_advisory_lead.status_procedure = tblprocedure_client_detail.id and id_advisory = '.$vAdvisoty->id, 'left');
                $advisory_lead[$kAdvisory]->detail  = $this->db->get('tblprocedure_client_detail')->result();
            }
            return $advisory_lead;
        }
        return false;
    }

    //modal tạo đơn hàng
    public function ViewCreateOrder(){

        $id = $this->input->post('id');
        $type = $this->input->post('type');
        if($type == 'lead')
        {
            $data['type'] = $type;
            $data['id'] = $id;
            $lead = get_table_where('tblleads', ['id' => $id], '', 'row');
            if(!empty($lead))
            {
                $message_err = "";
                if(empty($lead->company)) {
                    $message_err .= ','._l('cong_t_company');
                }
                if(empty($lead->phonenumber)) {
                    $message_err .= ','._l('cong_t_phone');
                }
                if(empty($lead->address)) {
                    $message_err .= ','._l('cong_t_address');
                }
                if(empty($lead->zcode)) {
                    $message_err .= ','._l('cong_t_zcode');
                }
                if(!empty($message_err))
                {
                    echo json_encode([
                            'success' => false,
                            'alert_type' => 'danger',
                            'message' => (_l('cong_pls_input').' '.trim($message_err, ','))
                        ]);die();
                }
                else
                {
                    echo json_encode([
                        'data' => $this->load->view('messagers/manage/modal/orders', $data, true),
                        'success' => true
                    ]);die();
                }
            }
        }
        else if($type == 'listfb')
        {
            $data['type'] = $type;
            $data['id'] = $id;
            $list_fb = get_table_where('tbllist_fb', ['id' => $id], '', 'row');
            if(!empty($list_fb))
            {
                $message_err = "";
                if(empty($list_fb->company)) {
                    $message_err .= ','._l('cong_t_company');
                }
                if(empty($list_fb->phonenumber)) {
                    $message_err .= ','._l('cong_t_phone');
                }
                if(empty($list_fb->address)) {
                    $message_err .= ','._l('cong_t_address');
                }
                if(empty($list_fb->zcode)) {
                    $message_err .= ','._l('cong_t_zcode');
                }
                if(!empty($message_err))
                {
                    echo json_encode([
                            'success' => false,
                            'alert_type' => 'danger',
                            'message' => (_l('cong_pls_input').' '.trim($message_err, ','))
                        ]);die();
                }
                else
                {
                    echo json_encode([
                        'data' => $this->load->view('messagers/manage/modal/orders', $data, true),
                        'success' => true
                    ]);die();
                }
            }
        }
        else if($type == 'client')
        {
            $data['type'] = $type;
            $data['id'] = $id;
            $client = get_table_where('tblclients', ['userid' => $id], '', 'row');
            if(!empty($client))
            {
                $message_err = "";
                if(empty($client->company)) {
                    $message_err .= ','._l('cong_t_company');
                }
                if(empty($client->phonenumber)) {
                    $message_err .= ','._l('cong_t_phone');
                }
                if(empty($client->address)) {
                    $message_err .= ','._l('cong_t_address');
                }
                if(empty($client->zcode)) {
                    $message_err .= ','._l('cong_t_zcode');
                }
                if(!empty($message_err))
                {
                    echo json_encode([
                            'success' => false,
                            'alert_type' => 'danger',
                            'message' => (_l('cong_pls_input').' '.trim($message_err, ','))
                        ]);die();
                }
                else
                {
                    $data['shipping'] = get_table_where('tblshipping_client', ['client' => $data['id']]);
                    echo json_encode([
                        'data' => $this->load->view('messagers/manage/modal/orders', $data, true),
                        'success' => true
                    ]);die();
                }
            }
        }
    }

    public function create_orders()
    {
        if($this->input->post())
        {
            $data = $this->input->post();

            $id = $data['id'];
            unset($data['id']);
            $type = $data['type'];
            unset($data['type']);
            if($type == 'lead')
            {
                $shipping = $data['shipping'];
                unset($data['shipping']);
                $this->db->where('id', $id);
                $lead = $this->db->get('tblleads')->row();
                if(!empty($lead)) {
                    $this->db->where('leadid', $lead->id);
                    $ktClient = $this->db->get('tblclients')->row();
                    if(empty($ktClient))
                    {
                        $arrayAdd = [
                            'prefix_client' => date('ymd'),
                            'email_client' => $lead->email,
                            'birtday' => $lead->birtday,
                            'note' => $lead->description,
                            'company' => $lead->company,
                            'fullname' => $lead->name,
                            'phonenumber' => $lead->phonenumber,
                            'id_facebook' => $lead->id_facebook,
                            'leadid' => $lead->id,
                            'zcode' => $lead->zcode,
                            'datecreated' => date('Y-m-d H:i:s'),
                            'addedfrom' => get_staff_user_id()
                        ];
                        //Mã loại hệ thống
                        if (!empty($arrayAdd['zcode'])) {
                            $arrayAdd['code_type'] = 'TN';
                        } else {
                            $arrayAdd['code_type'] = "NEW";
                        }
                        $this->db->insert('tblclients', $arrayAdd);
                        $idClient = $this->db->insert_id();
                    }
                    else
                    {
                        $idClient = $ktClient->userid;
                    }
                    if (!empty($idClient)) {

                        $img_lead =  get_upload_path_by_type('lead') . $lead->id . '/';
                        $img_client = get_upload_path_by_type('customer') . $idClient . '/';
                        _maybe_create_upload_path($img_client);
                        @copy($img_lead.'small_'.$lead->lead_image, $img_client.'small_'.$lead->lead_image);
                        @copy($img_lead.'thumb_'.$lead->lead_image, $img_client.'thumb_'.$lead->lead_image);

                        $code_client = sprintf("%06s", $idClient);
                        $arrayUpdateClient = [
                            'client_image' => $lead->lead_image
                        ];
                        $arrayUpdateClient['code_client'] = $code_client;

                        $this->db->where('userid', $idClient);
                        $this->db->update('tblclients', $arrayUpdateClient);
                        $this->db->insert('tblshipping_client', [
                            'client' => $idClient,
                            'name' => $shipping['name'],
                            'phone' => $shipping['phone'],
                            'address' => $shipping['address'],
                            'date_create' => date('Y-m-d H:i:s'),
                            'create_by' => get_staff_user_id(),
                            'address_primary' => 1
                        ]);
                        $idShipping = $this->db->insert_id();
                        $data['client'] = $idClient;
                        $data['shipping'] = $idShipping;
                        $data['address_shipping'] = $shipping['address'];
                        $this->load->model('orders_model');
                        $success = $this->orders_model->add($data);
                        if(!empty($success))
                        {
                            $this->db->where('id', $lead->id);
                            $this->db->update('tblleads', ['status' => '1']);
                            echo json_encode([
                                'success' => true,
                                'alert_type' => 'success',
                                'id_facebook' => $lead->id_facebook,
                                'message' => _l('cong_add_true')
                            ]);die();
                        }
                        else
                        {
                            //Nếu thêm đơn hàng không thành công => xóa khách hàng
                            if(empty($ktClient))
                            {
                                $this->db->where('userid', $idClient);
                                $this->db->delete('tblclients');
                                $this->db->where('client', $idClient);
                                $this->db->delete('tblshipping_client');
                            }
                            echo json_encode([
                                'success' => false,
                                'alert_type' => 'danger',
                                'message' => _l('cong_add_false')
                            ]);
                        }
                    }
                }
            }
            if($type == 'listfb')
            {
                $shipping = $data['shipping'];
                unset($data['shipping']);
                $this->db->where('id', $id);
                $list_fb = $this->db->get('tbllist_fb')->row();
                if(!empty($list_fb)) {
                    $this->db->where('id_facebook', $list_fb->id_facebook);
                    $ktClient = $this->db->get('tblclients')->row();
                    if(empty($ktClient))
                    {
                        $arrayAdd = [
                            'prefix_client' => date('ymd'),
                            'email_client' => $list_fb->email,
                            'birtday' => $list_fb->birtday,
                            'note' => $list_fb->note,
                            'company' => $list_fb->company,
                            'fullname' => $list_fb->name,
                            'phonenumber' => $list_fb->phonenumber,
                            'id_facebook' => $list_fb->id_facebook,
                            'zcode' => $list_fb->zcode,
                            'datecreated' => date('Y-m-d H:i:s'),
                            'addedfrom' => get_staff_user_id()
                        ];
                        //Mã loại hệ thống
                        if (!empty($arrayAdd['zcode'])) {
                            $arrayAdd['code_type'] = 'TN';
                        } else {
                            $arrayAdd['code_type'] = "NEW";
                        }
                        $this->db->insert('tblclients', $arrayAdd);
                        $idClient = $this->db->insert_id();
                    }
                    else
                    {
                        $idClient = $ktClient->userid;
                    }
                    if (!empty($idClient)) {

                        $paste_img = FCPATH . 'uploads/avatarFB' . '/' . $list_fb->id . '/';
                        $img_client = get_upload_path_by_type('customer') . $idClient . '/';
                        @copy($paste_img.'small_'.$list_fb->avatar, $img_client.'small_'.$list_fb->avatar);
                        @copy($paste_img.'thumb_'.$list_fb->avatar, $img_client.'thumb_'.$list_fb->avatar);
                        _maybe_create_upload_path($img_client);
                        $code_client = sprintf("%06s", $idClient);
                        $arrayUpdateClient = [
                            'client_image' => $list_fb->avatar
                        ];
                        $arrayUpdateClient['code_client'] = $code_client;

                        $this->db->where('userid', $idClient);
                        $this->db->update('tblclients', $arrayUpdateClient);
                        $this->db->insert('tblshipping_client', [
                            'client' => $idClient,
                            'name' => $shipping['name'],
                            'phone' => $shipping['phone'],
                            'address' => $shipping['address'],
                            'date_create' => date('Y-m-d H:i:s'),
                            'create_by' => get_staff_user_id(),
                            'address_primary' => 1
                        ]);
                        $idShipping = $this->db->insert_id();
                        $data['client'] = $idClient;
                        $data['shipping'] = $idShipping;
                        $data['address_shipping'] = $shipping['address'];
                        $this->load->model('orders_model');
                        $success = $this->orders_model->add($data);
                        if(!empty($success))
                        {
                            echo json_encode([
                                'success' => true,
                                'alert_type' => 'success',
                                'id_facebook' => $list_fb->id_facebook,
                                'message' => _l('cong_add_true')
                            ]);die();
                        }
                        else
                        {
                            //Nếu thêm đơn hàng không thành công => xóa khách hàng
                            if(empty($ktClient))
                            {
                                $this->db->where('userid', $idClient);
                                $this->db->delete('tblclients');
                                $this->db->where('client', $idClient);
                                $this->db->delete('tblshipping_client');
                            }
                            echo json_encode([
                                'success' => false,
                                'alert_type' => 'danger',
                                'message' => _l('cong_add_false')
                            ]);
                        }
                    }
                }
            }
            if($type == 'client')
            {
                $this->db->where('userid', $id);
                $ktClient = $this->db->get('tblclients')->row();
                if(!empty($ktClient))
                {
                    $idClient = $ktClient->userid;
                }
                if (!empty($idClient)) {
                    $data['client'] = $idClient;
                    $this->load->model('orders_model');
                    $success = $this->orders_model->add($data);
                    if(!empty($success))
                    {
                        echo json_encode([
                            'success' => true,
                            'alert_type' => 'success',
                            'id_facebook' => $ktClient->id_facebook,
                            'message' => _l('cong_add_true')
                        ]);die();
                    }
                    else
                    {
                        //Nếu thêm đơn hàng không thành công => xóa khách hàng
                        echo json_encode([
                            'success' => false,
                            'alert_type' => 'danger',
                            'message' => _l('cong_add_false')
                        ]);
                    }
                }
            }
        }
    }







    //hau
    public function find_uid_facebook($id_facebook = NULL)
    {
        $facebook = $this->messager_model->find_user_facebook($id_facebook);
        $ckeck_sales = false;
        if($facebook)
        {
            $ckeck_sales = $this->messager_model->get_sale_client($facebook->userid);
            // $ckeck_sales = $this->messager_model->find_user_sales($facebook->userid);
        }
        $data['facebook']=$facebook;
        $data['ckeck_sales']=$ckeck_sales;
        echo json_encode($data);
    }
    public function find_name_phone()
    {
        $data = $this->input->post();
        echo json_encode($this->messager_model->find_name_phone($data));
    }
    public function add_profile()
    {
        $data = $this->input->post();
        $ckeck = $this->messager_model->find_user_facebook($data['id_facebook']);
        if(!empty($ckeck))
        {
            $this->messager_model->update_profile($data, $ckeck->userid);
            echo json_encode(array(
                                    'message'       => 'Cập nhật khách hàng thành công',
                                    'alert_type'    =>  'success'
                            ));die;
        }
        else
        {   
            if(!empty($data['client_id']))
            {
                $this->messager_model->update_profile($data, $data['client_id']);
                echo json_encode(array(
                            'message' => 'Cập nhật khách hàng thành công',
                            'alert_type'=>'success'
                        ));die;
            }
            else
            {
                $id = $this->messager_model->add_profile($data);
                if(!empty($id))
                {
                    echo json_encode(array(
                                            'message'       => 'Thêm khách hàng thành công',
                                            'alert_type'    =>  'success'
                                     ));die;
                }
            }
        }
    }
    public function get_sale_client($userid = '')
    {
        echo json_encode($this->messager_model->get_sale_client($userid));die();
    }
}

