<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Prchat_controller extends AdminController
{
    /**
     * Stores the pusher options
     * @var array
     */
    protected $pusher_options = array();

    /**
     * Controler __construct function to initialize options
     */
    public function __construct()
    {

      parent::__construct();
      $this->load->model('prchat_model', 'chat_model');

      $this->pusher_options['app_key']    = get_option('pusher_app_key');
      $this->pusher_options['app_secret'] = get_option('pusher_app_secret');
      $this->pusher_options['app_id']     = get_option('pusher_app_id');

      if (!isset($this->pusher_options['cluster']) && get_option('pusher_cluster') != '') {
        $this->pusher_options['cluster'] = get_option('pusher_cluster');
    }
}

    /**
     *  Initialize Pusher
     */
    public function initiateChat()
    {
        if ($this->input->post()) {
            $pusher = new Pusher\Pusher(
                $this->pusher_options['app_key'],
                $this->pusher_options['app_secret'],
                $this->pusher_options['app_id'],
                array('cluster' => $this->pusher_options['cluster'])
            );

            if ($this->input->post('typing') == 'false') {
                $imageData['sender_image']   = $this->chat_model->getUserImage(get_staff_user_id());
                $imageData['receiver_image'] = $this->chat_model->getUserImage(str_replace('#', '', $this->input->post('to')));

                $from = $this->input->post('from');
                $receiver = str_replace('#', '', $this->input->post('to'));

                $message_data = array(
                    'sender_id'   => $this->input->post('from'),
                    'reciever_id' => str_replace('#', '', $this->input->post('to')),
                    'message'     => $this->input->post('msg'),
                    'viewed'      => 0,
                );

                $last_id = $this->chat_model->createMessage($message_data);

                $pusher->trigger('presence-mychanel', 'send-event', array(
                    'message'        => pr_chat_convertLinkImageToString($this->input->post('msg'), $from, $receiver),
                    'from'           => $from,
                    'to'             => $receiver,
                    'last_insert_id' => $last_id,
                    'sender_image'   => $imageData['sender_image'],
                    'receiver_image' => $imageData['receiver_image'],
                ));

                $pusher->trigger('presence-mychanel', 'notify-event', array(
                    'from' => $this->input->post('from'),
                    'to'   => str_replace('#', '', $this->input->post('to')),
                ));

            } elseif ($this->input->post('typing') == 'true') {
                $pusher->trigger('presence-mychanel', 'typing-event', array('message' => $this->input->post('typing'), 'from' => $this->input->post('from'), 'to' => str_replace('#', '', $this->input->post('to'))));
            } else {
                $pusher->trigger('presence-mychanel', 'typing-event', array('message' => 'null', 'from' => $this->input->post('from'), 'to' => str_replace('#', '', $this->input->post('to'))));
            }
        }
    }

    /**
     * Get staff members for chat
     */
    public function users()
    {
        $users = $this->chat_model->getUsers();
        if ($users) {
            echo json_encode($users, true);
        } else {
            die(_l('chat_error_table'));
        }
    }

    /**
     * Get pusher key
     */
    public function getKey()
    {
        if (isset($this->pusher_options['app_key']) && !empty($this->pusher_options['app_key'])) {
            echo json_encode($this->pusher_options['app_key']);
        } else {
            die(_l('chat_app_key_not_found'));
        }
    }

    /**
     * Get staff that will be used for the chat window
     */
    public function getStaffInfo()
    {
        if ($this->input->post('id')) {
            $id       = $this->input->post('id');
            $response = $this->chat_model->getStaffInfo($id);
            if ($response) {
                echo json_encode($response);
            }
        }
        return false;
    }

    /**
     * Get logged in user messages sent to other user
     */
    public function getMessages()
    {
        $from    = $this->input->get('from');
        $to      = $this->input->get('to');
        $limit   = 10;
        $offset  = 0;
        $message = '';
        if ($this->input->get('offset')) {
            $offset = $this->input->get('offset');
        }
        $response = $this->chat_model->getMessages($from, $to, $limit, $offset);
        
        if ($response) {
            echo json_encode($response);
        } else {
            $message = 'No more messages found in database';
            echo json_encode($message);
        }
    }

    /**
     * Get unread messages, used when somebody sent a message while the user is offline
     */
    public function getUnread($return = false)
    {
      $response = array();
      $result   = $this->chat_model->getUnread();
      if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode($response['success'] = false);
    }
    return false;
}

    /**
     * Updated unread messages to read
     */
    public function updateUnread()
    {
        if ($this->input->post('id')) {
            $id       = $this->input->post('id');
            $response = array();
            $result   = $this->chat_model->updateUnread($id);

            if ($result) {
                echo json_encode($result);
            } else {
                echo json_encode($response['success'] = false);
            }
        }
    }

    /**
     * Pusher authentication
     */
    public function pusher_auth()
    {
        if ($this->input->get()) {
            $name         = get_staff_full_name();
            $user_id      = get_staff_user_id();
            $channel_name = $this->input->get('channel_name');
            $socket_id    = $this->input->get('socket_id');
            if (!$channel_name) {
                exit('channel_name must be supplied');
            }
            if (!$socket_id) {
                exit('socket_id must be supplied');
            }
            if (!empty($this->pusher_options['app_key'])
                && !empty($this->pusher_options['app_secret'])
                && !empty($this->pusher_options['app_id'])
            ) {
                $pusher = new Pusher\Pusher(
                    $this->pusher_options['app_key'],
                    $this->pusher_options['app_secret'],
                    $this->pusher_options['app_id'],
                    array('cluster' => $this->pusher_options['cluster'])
                );

            $presence_data = array('name' => $name);
            $auth          = $pusher->presence_auth($channel_name, $socket_id, $user_id, $presence_data);
            $callback      = str_replace('\\', '', $this->input->get('callback'));
            header('Content-Type: application/javascript');
            echo($callback . '(' . $auth . ');');
        } else {
            exit('Appkey, secret or appid is missing');
        }
    }
}

    /**
     *  Uploads chat files
     */
    public function uploadMethod()
    {
        $allowedFiles = get_option('allowed_files');
        $allowedFiles = str_replace(',', '|', $allowedFiles);
        $allowedFiles = str_replace('.', '', $allowedFiles);

        $config = array(
            'upload_path'   => PR_CHAT_MODULE_UPLOAD_FOLDER,
            'allowed_types' => $allowedFiles,
            'max_size'      => '9048000',
            'max_height'    => '961',
            'max_width'     => '1281',
        );
        $this->load->library('upload', $config);
        if ($this->upload->do_upload()) {
            echo json_encode($data = array('upload_data' => $this->upload->data()));
        } else {
            echo json_encode($error = array('error' => $this->upload->display_errors()));
        }
    }

    /**
     * Handles chat color change request
     */
    public function colorchange()
    {
        $id                 = $this->input->post('id');
        $color              = $this->input->post('color');
        if ($this->input->post('get_chat_color')) {
            echo json_encode(pr_get_chat_color($id));
        }
        if ($this->input->post('color')) {
            echo json_encode($this->chat_model->setChatColor($id, $color));
        }
    }
    public function deleteMessage(){
        $id = $this->input->post('id');
        echo json_encode($this->chat_model->deleteMessage($id));
    }
}