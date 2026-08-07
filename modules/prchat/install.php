<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The file is responsible for handing the chat installation
 */
$CI = &get_instance();

if (!is_dir(PR_CHAT_MODULE_UPLOAD_FOLDER)) {
  mkdir(PR_CHAT_MODULE_UPLOAD_FOLDER, 0755);
  $fp = fopen(PR_CHAT_MODULE_UPLOAD_FOLDER.'/index.html', 'w');
  fclose($fp);
}

add_option('pusher_chat_enabled', 0);
add_option('chat_desktop_active', 0);
add_option('chat_staff_can_delete_messages', 1);

$CI->db->query("CREATE TABLE IF NOT EXISTS `".db_prefix()."chatmessages` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `reciever_id` int(11) NOT NULL,
  `message` longtext NOT NULL,
  `viewed` int(11) DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `time_sent` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");

$CI->db->query("CREATE TABLE IF NOT EXISTS `".db_prefix()."chatsettings` (
  id INT NOT NULL AUTO_INCREMENT, 
  user_id INT(11) NOT NULL,
  name VARCHAR(255) NOT NULL,
  value VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)  
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
