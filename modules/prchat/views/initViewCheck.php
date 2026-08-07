<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (get_option('pusher_chat_enabled') == '1') {

    if (get_option('pusher_realtime_notifications')  ==  0) {
        echo '<script src="https://js.pusher.com/4.3/pusher.min.js"></script>';
    }

    $this->load->view('perfex_chat_view');
}
