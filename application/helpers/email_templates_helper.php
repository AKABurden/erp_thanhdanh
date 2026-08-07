<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Prepares email template preview $data for the view
 * @param  string $template    template class name
 * @param  mixed $customer_id_or_email customer ID to fetch the primary contact email or email
 * @return array
 */
function prepare_mail_preview_data($template, $customer_id_or_email, $mailClassParams = [])
{
    $CI = &get_instance();

    if (is_numeric($customer_id_or_email)) {
        $contact = $CI->clients_model->get_contact(get_primary_contact_user_id($customer_id_or_email));
        $email   = $contact ? $contact->email : '';
    } else {
        $email = $customer_id_or_email;
    }

    $CI->load->model('emails_model');

    $data['template'] = $CI->app_mail_template->prepare($email, $template);
    $slug             = $CI->app_mail_template->get_default_property_value('slug', $template, $mailClassParams);

    $data['template_name'] = $slug;

    $template_result = $CI->emails_model->get(['slug' => $slug, 'language' => 'english'], 'row');

    $data['template_system_name'] = $template_result->name;
    $data['template_id']          = $template_result->emailtemplateid;

    $data['template_disabled'] = $template_result->active == 0;

    return $data;
}
/**
 * Parse email template with the merge fields
 * @param  mixed $template     template
 * @param  array  $merge_fields
 * @return object
 */
function parse_email_template($template, $merge_fields = [])
{
    $CI = & get_instance();
    if (!is_object($template) || $CI->input->post('template_name')) {
        $original_template = $template;

        if (!class_exists('emails_model', false)) {
            $CI->load->model('emails_model');
        }

        if ($CI->input->post('template_name')) {
            $template = $CI->input->post('template_name');
        }

        $template = $CI->emails_model->get(['slug' => $template], 'row');

        if ($CI->input->post('email_template_custom')) {
            $template->message = $CI->input->post('email_template_custom', false);
            // Replace the subject too
            $template->subject = $original_template->subject;
        }
    }

    $template = parse_email_template_merge_fields($template, $merge_fields);

    // Used in hooks eq for emails tracking
    $template->tmp_id = app_generate_hash();

    return hooks()->apply_filters('email_template_parsed', $template);
}

/**
 * This function will parse email template merge fields and replace with the corresponding merge fields passed before sending email
 * @param  object $template     template from database
 * @param  array $merge_fields available merge fields
 * @return object
 */
function parse_email_template_merge_fields($template, $merge_fields)
{
    $CI = &get_instance();

    if (!class_exists('other_merge_fields', false)) {
        $CI->load->library('merge_fields/other_merge_fields');
    }

    $merge_fields = array_merge($merge_fields, $CI->other_merge_fields->format());

    foreach ($merge_fields as $key => $val) {
        foreach (['message', 'fromname', 'subject'] as $replacer) {
            $template->{$replacer} = stripos($template->{$replacer}, $key) !== false
            ? str_ireplace($key, $val, $template->{$replacer})
            : str_ireplace($key, '', $template->{$replacer});
        }
    }

    return $template;
}

/**
 * Send mail template
 * @since  2.3.0
 * @return mixed
 */
function send_mail_template()
{
    $params = func_get_args();
    return mail_template(...$params)->send();
}

/**
 * Prepare mail template class
 * @param  string $class mail template class name
 * @return mixed
 */
function mail_template($class)
{
    $CI = &get_instance();

    $params = func_get_args();

    // First params is the $class param
    unset($params[0]);

    $params = array_values($params);

    $path = get_mail_template_path($class, $params);

    if (!file_exists($path)) {
        if (!defined('CRON')) {
            show_error('Mail Class Does Not Exists [' . $path . ']');
        } else {
            return false;
        }
    }

    // Include the mailable class
    if (!class_exists($class, false)) {
        include_once($path);
    }

    // Initialize the class and pass the params
    $instance = new $class(...$params);

    // Call the send method
    return $instance;
}

function get_mail_template_path($class, &$params)
{
    $CI  = &get_instance();
    $dir = APPPATH . 'libraries/mails/';

    // Check if second parameter is module and is activated so we can get the class from the module path
    if (isset($params[0]) && is_string($params[0]) && is_dir(module_dir_path($params[0]))) {
        $module = $CI->app_modules->get($params[0]);
        if ($module['activated'] === 1) {
            $dir = module_libs_path($params[0]) . 'mails/';
        }

        unset($params[0]);
        $params = array_values($params);
    }

    return $dir . ucfirst($class) . '.php';
}

function send_mail_task_assignees($email, $taskid)
{
	$CI       = & get_instance();
	$message = '';
	if (!empty($taskid) && !empty($email)) {

		$CI->db->where('id', $taskid);
		$tasks = $CI->db->get('tbltasks')->row();

		$message = '<br /><span style="font-size: 12pt;">Bạn vừa được phân công cho công việc mới:</span>
									<br />
									<br /><span style="font-size: 12pt;"><strong>Tên:</strong> {task_name}<br /></span><strong>Ngày bắt đầu:</strong> {task_startdate}
									<br /><span style="font-size: 12pt;"><strong>Hạng chót:</strong> {task_duedate}</span>
									<br /><span style="font-size: 12pt;"><strong>Mức độ ưu tiên:</strong> {task_priority}<br /><br /></span>';

		$task_rel_data = get_relation_data($tasks->rel_type, $tasks->rel_id);
		$task_rel_value = get_relation_values($task_rel_data, $tasks->rel_type);
		$row_QL = '';
		if(!empty($task_rel_value['type'])) {
			$row_QL = _l('c_tasks_' . $task_rel_value['type']) . ' <a target="_blank href="' . $task_rel_value['link'] . '">' . $task_rel_value['name'] . '</a>';
		}
		if(!empty($row_QL)) {
			$message .= '<div><b style="text-transform: capitalize;font-size:12pt">Liên quan đến</b> <span style="font-size:12pt">'.$row_QL.'</span></div>';
		}
			$rowDepartments = '';
			$CI->db->select('tbldepartments.*');
			$CI->db->join('tbldepartments', 'tbldepartments.departmentid = tbltask_department.department_id');
			$departments = $CI->db->get_where('tbltask_department', ['task_id' => $tasks->id])->result_array();
			if(!empty($departments)) {
				$rowDepartments .= '<div style="margin-left: -5px;">';
				foreach($departments as $k => $v) {
					$rowDepartments .= '<span class="inline-block label mleft5 mtop5" style="font-size:12pt;color:'.(!empty($color_department[$k]) ? $color_department[$k] : '').';border:1px solid '.(!empty($color_department[$k]) ? $color_department[$k] : '').'">'.$v['name'].'</span>';
				}
				$rowDepartments .= '</div>';
			}
		if(!empty($rowDepartments)) {
			$message .= '<div><b style="text-transform: capitalize;font-size:12pt">Phòng ban</b> <span>'.$rowDepartments.'</span></div>';
		}
		$message .= '<span style="font-size: 12pt;"><span>Link theo dõi</span>: <a href="{task_link}">{task_name}</a></span>';


		$message = str_replace('{task_name}', $tasks->name, $message);
		$message = str_replace('{task_startdate}', _d($tasks->startdate), $message);
		$message = str_replace('{task_duedate}', _d($tasks->duedate), $message);
		$tasks->priority = task_priority($tasks->priority);
		$message = str_replace('{task_priority}', $tasks->priority, $message);
		$message = str_replace('{task_link}', admin_url('tasks/view/' . $tasks->id), $message);


		$CI->load->config('email');
		$template           = new StdClass();
		$template->message  = $message;
		$template->fromname = get_option('companyname');
		$template->subject  = 'Phụ trách công việc mới';

		$template = parse_email_template($template);
		$CI->email->initialize();
		$CI->email->set_newline(config_item('newline'));
		$CI->email->set_crlf(config_item('crlf'));

		$CI->email->from(get_option('smtp_email'), $template->fromname);
		$CI->email->to($email);
		$systemBCC = get_option('bcc_emails');
		if ($systemBCC != '') {
			$CI->email->bcc($systemBCC);
		}
		$CI->email->subject($template->subject);
		$CI->email->message($template->message);
		$CI->email->send(true);
	}
}
