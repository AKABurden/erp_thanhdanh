<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comment extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data['title'] = 'Quản lý bình luận';

        $VersionAppFB = get_option('VersionAppFB');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://graph.facebook.com/".$VersionAppFB."/".$_COOKIE['page_active']."/posts?access_token=".$_COOKIE['access_token_page_active'].'&fields=post_id,updated_time,comments.limit(1)',
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
//
//        echo "<pre>";
//        var_dump($kt_data);die();
        if(empty($kt_data->error->code))
        {
            $data['list_comment'] = $response;
            $this->load->view('comment/manage', $data);
        }
        else
        {
            redirect('admin/messager/login');
        }
//        redirect('admin/messager/login');
    }
}