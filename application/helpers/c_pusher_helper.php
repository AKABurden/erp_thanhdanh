<?php
//Helpers của công
defined('BASEPATH') or exit('No direct script access allowed');
if(!function_exists('ConnectPusher')) {
	function ConnectPusher($data = '', $channels = 'EventChangeData', $events = 'ChangeData') {
		$options = array(
			'cluster' => get_option('pusher_cluster'),
			'useTLS' => true
		);
		$pusher = new Pusher\Pusher(
			get_option('pusher_app_key'),
			get_option('pusher_app_secret'),
			get_option('pusher_app_id'),
			$options
		);
		return $pusher->trigger($channels, $events, $data);
	}
}


