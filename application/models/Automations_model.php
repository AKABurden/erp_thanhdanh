<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Automations_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tasks_model');
    }

    function add($data = array())
    {
        if (!empty($data)) {
            $CountInsert = 0;

            $status = 0;
            if (!empty($data['status'])) {
                $status = $data['status'];
            }
            $this->db->insert(db_prefix() . 'automations', array(
                'name' => $data['name'],
                'note' => $data['note'],
                'status' => $status,
                'created_by' => get_staff_user_id(),
                'date_create' => date('Y-m-d H:i:s'),
                'action' => $data['action']
            ));
            $id = $this->db->insert_id();
            if (!empty($id)) {
                ++$CountInsert;
                /*
                 * Điều kiện
                 */

                //Lọc theo ngày
                if (!empty($data['day'])) {
                    foreach ($data['day'] as $key => $value) {
                        $array_proviso = array(
                            'id_auto' => $id,
                            'type' => 1,
                            'day' => $value
                        );
                        if ($this->db->insert(db_prefix() . 'automation_proviso', $array_proviso)) {
                            ++$CountInsert;
                        }
                    }
                }
                //Lọc theo tuần
                if (!empty($data['week'])) {
                    foreach ($data['week'] as $key => $value) {
                        $array_proviso = array(
                            'id_auto' => $id,
                            'type' => 2,
                            'week' => $value
                        );
                        if ($this->db->insert(db_prefix() . 'automation_proviso', $array_proviso)) {
                            ++$CountInsert;
                        }
                    }
                }

                //Lọc theo thời gian
                if (!empty($data['time'])) {
                    $array_proviso = array(
                        'id_auto' => $id,
                        'type' => 3,
                        'time' => to_sql_date($data['time'], true)
                    );
                    if ($this->db->insert(db_prefix() . 'automation_proviso', $array_proviso)) {
                        ++$CountInsert;
                    }
                }
                /*
                 * END Điều kiện
                 */

                /*
                 * Nội dung gửi
                 */

                if (!empty($data['detail'])) {
                    foreach ($data['detail'] as $key => $value) {
                        $this->db->insert(db_prefix() . 'automation_detail', array(
                            'id_auto' => $id,
                            'title' => ($value['title'] ? $value['title'] : ''),
                            'content' => $value['content'],
                            'send' => $value['send'],
                            'type' => $value['type'],
                            'staff_follow' => (!empty($value['staff_follow']) ? $value['staff_follow'] : NULL),
                            'public' => (!empty($value['public']) ? $value['public'] : 0)
                        ));
                        $id_detail = $this->db->insert_id();

                        if (!empty($value['receive'])) {
                            foreach ($value['receive'] as $k => $v) {
                                $this->db->insert(db_prefix() . 'automations_receive', array(
                                    'id_detail' => $id_detail,
                                    'receive' => $v
                                ));
                                if (!empty($this->db->insert_id())) {
                                    ++$CountInsert;
                                }
                            }
                        }

                    }
                }


                if (!empty($data['proviso'])) {
                    foreach ($data['proviso'] as $key => $value) {
                        if ($value['where_colum'] == 'datecreated') {
                            $sub_where = $value['where_colum'] . ' ' . $value['then_colum'] . ' CURDATE() - INTERVAL ' . $value['then_data'] . ' DAY ';
                        } else if ($value['where_colum'] == 'dateadded') {
                            $sub_where = 'DATE_FORMAT(dateadded,"%Y-%m-%d")' . ' ' . $value['then_colum'] . ' CURDATE() - INTERVAL ' . $value['then_data'] . ' DAY ';
                        } else if (is_array($value['then_data'])) {
                            $sub_where = $value['where_colum'] . ' ' . $value['then_colum'] . ' (' . implode(',', $value['then_data']) . ') ';
                        } else {
                            $sub_where = $value['where_colum'] . ' ' . $value['then_colum'] . ' ' . $value['then_data'] . ' ';
                        }

                        $array_where = [
                            'id_auto' => $id,
                            'where_colum' => $value['where_colum'],
                            'then_colum' => $value['then_colum'],
                            'then_data' => is_array($value['then_data']) ? implode(',', $value['then_data']) : $value['then_data'],
                            'sub_where' => $sub_where
                        ];
                        if ($this->db->insert(db_prefix() . 'automation_where', $array_where)) {
                            ++$CountInsert;
                        }
                    }
                }
            }
            if ($CountInsert > 0) {
                return $id;
            }
            return false;
        }
        return false;
    }

    function update($id, $data)
    {
        if (!empty($id)) {
            $status = 0;
            if (!empty($data['status'])) {
                $status = $data['status'];
            }

            $this->db->where('id', $id);
            if ($this->db->update(db_prefix() . 'automations', ['name' => $data['name'], 'note' => $data['note'], 'status' => $status, 'action' => $data['action']])) {
                /*
                 * Điều kiện
                 */
                $list_not_delete_proviso = array();
                //Lọc theo ngày
                if (!empty($data['day'])) {
                    foreach ($data['day'] as $key => $value) {
                        $array_proviso = array(
                            'id_auto' => $id,
                            'type' => 1,
                            'day' => $value
                        );
                        $this->db->where($array_proviso);
                        $proviso = $this->db->get(db_prefix() . 'automation_proviso')->row();
                        if (empty($proviso)) {
                            $this->db->insert(db_prefix() . 'automation_proviso', $array_proviso);
                            $list_not_delete_proviso[] = $this->db->insert_id();
                        } else {
                            $list_not_delete_proviso[] = $proviso->id;
                        }
                    }
                }
                //Lọc theo tuần
                if (!empty($data['week'])) {
                    foreach ($data['week'] as $key => $value) {
                        $array_proviso = array(
                            'id_auto' => $id,
                            'type' => 2,
                            'week' => $value
                        );
                        $this->db->where($array_proviso);
                        $proviso = $this->db->get(db_prefix() . 'automation_proviso')->row();
                        if (empty($proviso)) {
                            $this->db->insert(db_prefix() . 'automation_proviso', $array_proviso);
                            $list_not_delete_proviso[] = $this->db->insert_id();
                        } else {
                            $list_not_delete_proviso[] = $proviso->id;
                        }
                    }
                }

                //Lọc theo thời gian
                if (!empty($data['time'])) {
                    $array_proviso = array(
                        'id_auto' => $id,
                        'type' => 3,
                        'time' => to_sql_date($data['time'], true)
                    );
                    $this->db->where($array_proviso);
                    $proviso = $this->db->get(db_prefix() . 'automation_proviso')->row();
                    if (empty($proviso)) {
                        $this->db->insert(db_prefix() . 'automation_proviso', $array_proviso);
                        $list_not_delete_proviso[] = $this->db->insert_id();
                    } else {
                        $list_not_delete_proviso[] = $proviso->id;
                    }
                }
                /*
                 * END Điều kiện
                 */

                $list_not_delete_detail = array();
                if (!empty($data['detail'])) {
                    foreach ($data['detail'] as $key => $value) {
                        if (empty($value['id'])) {

                            $this->db->insert(db_prefix() . 'automation_detail', array(
                                'id_auto' => $id,
                                'title' => ($value['title'] ? $value['title'] : ''),
                                'content' => $value['content'],
                                'send' => $value['send'],
                                'type' => $value['type'],
                                'staff_follow' => (!empty($value['staff_follow']) ? $value['staff_follow'] : NULL),
                                'public' => (!empty($value['public']) ? $value['public'] : 0)
                            ));
                            $id_detail = $this->db->insert_id();
                            $list_not_delete_detail[] = $id_detail;
                            if (!empty($value['receive'])) {
                                foreach ($value['receive'] as $k => $v) {
                                    $this->db->insert(db_prefix() . 'automations_receive', array(
                                        'id_detail' => $id_detail,
                                        'receive' => $v
                                    ));
                                }
                            }
                        } else {
                            $this->db->where('id', $value['id']);
                            $auto_detail = $this->db->get(db_prefix() . 'automation_detail')->row();
                            if (!empty($auto_detail)) {
                                $list_not_delete_detail[] = $auto_detail->id;
                                $array_detail = array(
                                    'id_auto' => $id,
                                    'title' => (!empty($value['title']) ? $value['title'] : ''),
                                    'content' => $value['content'],
                                    'send' => $value['send'],
                                    'type' => $value['type'],
                                    'staff_follow' => (!empty($value['staff_follow']) ? $value['staff_follow'] : NULL),
                                    'public' => (!empty($value['public']) ? $value['public'] : 0)
                                );
                                $this->db->where('id', $auto_detail->id);
                                $this->db->update(db_prefix() . 'automation_detail', $array_detail);
                                $list_receive = array();
                                foreach ($value['receive'] as $k => $v) {
                                    $array_receive = array(
                                        'id_detail' => $auto_detail->id,
                                        'receive' => $v
                                    );
                                    $this->db->where($array_receive);
                                    $row_receive = $this->db->get(db_prefix() . 'automations_receive')->row();
                                    if (!empty($row_receive)) {
                                        $list_receive[] = $row_receive->id;
                                    } else {
                                        $this->db->insert(db_prefix() . 'automations_receive', $array_receive);
                                        $id_receive = $this->db->insert_id();
                                        $list_receive[] = $id_receive;
                                    }
                                }

                                if (!empty($list_receive)) {
                                    $this->db->where_not_in('id', $list_receive);
                                }
                                $this->db->where('id_detail', $auto_detail->id);
                                $this->db->delete(db_prefix() . 'automations_receive');

                            }
                        }

                    }
                }


                $list_not_delete_where = array();
                if (!empty($data['proviso'])) {
                    foreach ($data['proviso'] as $key => $value) {
                        if ($value['where_colum'] == 'datecreated') {
                            $sub_where = 'DATE_FORMAT(datecreated,"%Y-%m-%d")' . ' ' . $value['then_colum'] . ' CURDATE() - INTERVAL ' . $value['then_data'] . ' DAY ';
                        } else if ($value['where_colum'] == 'dateadded') {
                            $sub_where = 'DATE_FORMAT(dateadded,"%Y-%m-%d")' . ' ' . $value['then_colum'] . ' CURDATE() - INTERVAL ' . $value['then_data'] . ' DAY ';
                        } else if (is_array($value['then_data'])) {
                            $sub_where = $value['where_colum'] . ' ' . $value['then_colum'] . ' (' . implode(',', $value['then_data']) . ') ';
                        } else {
                            $sub_where = $value['where_colum'] . ' ' . $value['then_colum'] . ' ' . $value['then_data'] . ' ';
                        }
                        $array_where = [
                            'id_auto' => $id,
                            'where_colum' => $value['where_colum'],
                            'then_colum' => $value['then_colum']
                        ];
                        $this->db->where($array_where);
                        $automation_where = $this->db->get(db_prefix() . 'automation_where')->row();
                        if (!empty($automation_where)) {
                            $list_not_delete_where[] = $automation_where->id;
                            $array_where['then_data'] = is_array($value['then_data']) ? implode(',', $value['then_data']) : $value['then_data'];
                            $array_where['sub_where'] = $sub_where;
                            $this->db->where('id', $automation_where->id);
                            $this->db->update(db_prefix() . 'automation_where', $array_where);
                        } else {
                            $array_where['then_data'] = is_array($value['then_data']) ? implode(',', $value['then_data']) : $value['then_data'];
                            $array_where['sub_where'] = $sub_where;
                            if ($this->db->insert(db_prefix() . 'automation_where', $array_where)) {
                                $list_not_delete_where[] = $this->db->insert_id();
                            }
                        }


                    }
                }


                if (!empty($list_not_delete_detail)) {
                    $this->db->where_not_in('id', $list_not_delete_detail);
                }
                $this->db->where('id_auto', $id);
                $this->db->delete(db_prefix() . 'automation_detail');

                if (!empty($list_not_delete_proviso)) {
                    $this->db->where_not_in('id', $list_not_delete_proviso);
                }
                $this->db->where('id_auto', $id);
                $this->db->delete(db_prefix() . 'automation_proviso');

                if (!empty($list_not_delete_where)) {
                    $this->db->where_not_in('id', $list_not_delete_where);
                }
                $this->db->where('id_auto', $id);
                $this->db->delete(db_prefix() . 'automation_where');

                return true;


            }
            return false;
        }
        return false;
    }

    function get($id = "")
    {
        if (!empty($id)) {
            $this->db->where('id', $id);
            $automations = $this->db->get(db_prefix() . 'automations')->row();
            if (!empty($automations)) {
                $this->db->where('id_auto', $id);
                $automations->proviso = $this->db->get(db_prefix() . 'automation_proviso')->result_array();

                $this->db->select(db_prefix() . 'automation_detail.*,GROUP_CONCAT(' . db_prefix() . 'automations_receive.receive,"") as receive');
                $this->db->where('id_auto', $id);
                $this->db->group_by(db_prefix() . 'automation_detail.id');
                $this->db->join(db_prefix() . 'automations_receive', db_prefix() . 'automations_receive.id_detail = ' . db_prefix() . 'automation_detail.id', 'left');
                $automations->detail = $this->db->get(db_prefix() . 'automation_detail')->result_array();

                $this->db->where('id_auto', $id);
                $automations->where = $this->db->get(db_prefix() . 'automation_where')->result_array();
                return $automations;
            }
        }
        return false;
    }

    function active_automations()
    {
        $list_week_array = ['monday' => 2, 'tuesday' => 3, 'wednesday' => 4, 'thursday' => 5, 'friday' => 6, 'saturday' => 7, 'sunday' => 1];
        $weekday = date("l");
        $weekday = strtolower($weekday);

        $week = $list_week_array[$weekday];
        $day = (int)date('d');
        $where = " (type = 1 and day =" . $day . ") or (type = 2 and week = " . $week . ") ";

        $this->db->select(db_prefix() . 'automation_proviso.*, ' . db_prefix() . 'automations.action');
        $this->db->where($where);
        $this->db->join(db_prefix() . 'automations', db_prefix() . 'automations.id = ' . db_prefix() . 'automation_proviso.id_auto');
        $this->db->where('status', 2);
        $automation_proviso = $this->db->get(db_prefix() . 'automation_proviso')->result_array();
        $Eaction = 0; // biến kiểm tra xem có thay đổi
        $EactionSendEmail = 0; // biến kiểm tra xem có thay đổi
        if (!empty($automation_proviso)) {
            foreach ($automation_proviso as $key => $value) {
                //Lấy thông tin nội dung và người gửi  + người nhận thông báo
                $this->db->select(db_prefix() . 'automation_detail.*,GROUP_CONCAT(' . db_prefix() . 'automations_receive.receive,"") as receive');
                $this->db->group_by(db_prefix() . 'automation_detail.id', db_prefix() . 'automation_detail.id');
                $this->db->where('id_auto', $value['id_auto']);
                $this->db->join(db_prefix() . 'automations_receive', db_prefix() . 'automations_receive.id_detail = ' . db_prefix() . 'automation_detail.id', 'left');
                $automation_detail = $this->db->get(db_prefix() . 'automation_detail')->result_array();

                // Lấy điều kiện khách hàng
                $this->db->where('id_auto', $value['id_auto']);
                $automation_where = $this->db->get(db_prefix() . 'automation_where')->result_array();
                if ($value['action'] == 1) {
                    $this->db->select(db_prefix() . 'clients.*, GROUP_CONCAT(' . db_prefix() . 'customer_admins.staff_id,"") as staff_id');
                    foreach ($automation_where as $keyWhere => $valueWhere) {
                        $this->db->where($valueWhere['sub_where']);
                    }
                    $this->db->join(db_prefix() . 'customer_admins', db_prefix() . 'customer_admins.customer_id = ' . db_prefix() . 'clients.userid', 'left');
                    $this->db->group_by('userid');
                    $clients = $this->db->get(db_prefix() . 'clients')->result_array(); // khách hàng thỏa điều kiện
                    if (!empty($clients)) {
                        foreach ($clients as $keyClient => $valueClient) {
                            foreach ($automation_detail as $keyDetail => $valueDetail) {
                                if ($valueDetail['type'] == 1) //gửi thông báo nhân viên
                                {
                                    $staff_isset = array();
                                    $receive = explode(',', $valueDetail['receive']);
                                    foreach ($receive as $keyReceive => $valueReceive) {
                                        if ($valueReceive == '-1') {
                                            if (empty($staff_isset[$valueClient['addedfrom']])) {
                                                $staff_isset[$valueClient['addedfrom']] = true;
                                                add_notification([
                                                    'touserid' => $valueClient['addedfrom'],
                                                    'fromuserid' => $valueDetail['send'],
                                                    'description' => $valueDetail['content'],
                                                    'link' => 'clients/client/' . $valueClient['userid']
                                                ]);
                                            }
                                            continue;
                                        } else if ($valueReceive == '-2') {
                                            if (!empty($valueClient['staff_id'])) {
                                                $valueClient['staff_id'] = explode(',', $valueClient['staff_id']);
                                                foreach ($valueClient['staff_id'] as $ik => $iv) {
                                                    if (empty($staff_isset[$iv])) {
                                                        $staff_isset[$iv] = true;
                                                        add_notification([
                                                            'touserid' => $iv,
                                                            'fromuserid' => $valueDetail['send'],
                                                            'description' => $valueDetail['content'],
                                                            'link' => 'clients/client/' . $valueClient['userid']
                                                        ]);
                                                    }
                                                }
                                                continue;
                                            }
                                        } else {
                                            if (empty($staff_isset[$valueReceive])) {
                                                $staff_isset[$valueReceive] = true;
                                                add_notification([
                                                    'touserid' => $valueReceive,
                                                    'fromuserid' => $valueDetail['send'],
                                                    'description' => $valueDetail['content'],
                                                    'link' => 'clients/client/' . $valueClient['userid']
                                                ]);
                                            }
                                            continue;
                                        }
                                    }
                                } else if ($valueDetail['type'] == 2)//gửi email khách hàng
                                {
                                    $this->load->config('email');
                                    $this->email->initialize();
                                    if (get_option('mail_engine') == 'phpmailer') {
                                        $this->email->set_debug_output(function ($err) {
                                            if (!isset($GLOBALS['debug'])) {
                                                $GLOBALS['debug'] = '';
                                            }
                                            $GLOBALS['debug'] .= $err . '<br />';
                                            return false;
                                        });
                                        $this->email->set_smtp_debug(3);
                                    }
                                    $company = get_option('companyname');
                                    $this->email->set_newline(config_item('newline'));
                                    $this->email->set_crlf(config_item('crlf'));
                                    $this->email->from(get_option('smtp_email'), $company);
                                    $this->email->to($valueClient['email_client']);
                                    $systemBCC = get_option('bcc_emails');
                                    if ($systemBCC != '') {
                                        $this->email->bcc($systemBCC);
                                    }
                                    $this->email->subject($valueDetail['title']);
                                    $this->email->message($valueDetail['content']);
                                    if ($this->email->send(true)) {
                                        ++$EactionSendEmail;
                                    }
                                } else if ($valueDetail['type'] == 3)//Tạo công việc cho nhân viên liên quan đến khách hàng
                                {
                                    $staff_isset = array();
                                    $receive = explode(',', $valueDetail['receive']);
                                    foreach ($receive as $keyReceive => $valueReceive) {
                                        if ($valueReceive == '-1') {
                                            if (empty($staff_isset[$valueClient['addedfrom']])) {
                                                $staff_isset[$valueClient['addedfrom']] = true;
                                                $array_tasks = [
                                                    'name' => $valueDetail['title'],
                                                    'startdate' => date('Y-m-d'),
                                                    'dateadded' => date('Y-m-d'),
                                                    'addedfrom' => $valueDetail['send'],
                                                    'is_public' => $valueDetail['public'],
                                                    'description' => $valueDetail['content'],
                                                    'rel_type' => 'customer',
                                                    'rel_id' => $valueClient['userid']
                                                ];
                                                $this->db->insert(db_prefix() . 'tasks', $array_tasks);
                                                $id_task = $this->db->insert_id();
                                                if (!empty($id_task)) {
                                                    $this->tasks_model->add_task_assignees([
                                                        'assignee' => $valueClient['addedfrom'],
                                                        'taskid' => $id_task,
                                                        'assigned_from' => $valueDetail['send']
                                                    ]);

                                                    if (!empty($valueDetail['staff_follow'])) {
                                                        $this->tasks_model->add_task_followers([
                                                            'taskid' => $valueDetail['staff_follow'],
                                                            'follower' => $id_task
                                                        ]);
                                                    }
                                                }
                                            }
                                            continue;
                                        } else if ($valueReceive == '-2') {
                                            if (!empty($valueClient['staff_id'])) {
                                                $valueClient['staff_id'] = explode(',', $valueClient['staff_id']);
                                                $array_tasks = [
                                                    'name' => $valueDetail['title'],
                                                    'startdate' => date('Y-m-d'),
                                                    'dateadded' => date('Y-m-d'),
                                                    'addedfrom' => $valueDetail['send'],
                                                    'is_public' => $valueDetail['public'],
                                                    'description' => $valueDetail['content'],
                                                    'status' => 1,
                                                    'rel_type' => 'customer',
                                                    'rel_id' => $valueClient['userid']
                                                ];
                                                $this->db->insert(db_prefix() . 'tasks', $array_tasks);
                                                $id_task = $this->db->insert_id();

                                                if (!empty($id_task)) {
                                                    foreach ($valueClient['staff_id'] as $ik => $iv) {
                                                        if (empty($staff_isset[$iv])) {
                                                            $staff_isset[$iv] = true;
                                                            $this->db->insert(db_prefix() . 'task_assigned', [
                                                                'staffid' => $iv,
                                                                'taskid' => $id_task,
                                                                'assigned_from' => $valueDetail['send'],
                                                            ]);
                                                        }
                                                    }
                                                    if (!empty($valueDetail['staff_follow'])) {
                                                        $this->db->insert(db_prefix() . 'task_followers', [
                                                            'staffid' => $valueDetail['staff_follow'],
                                                            'taskid' => $id_task
                                                        ]);
                                                    }
                                                }
                                                continue;
                                            }
                                        } else {
                                            if (empty($staff_isset[$valueReceive])) {
                                                $staff_isset[$valueReceive] = true;
                                                $array_tasks = [
                                                    'name' => $valueDetail['title'],
                                                    'startdate' => date('Y-m-d'),
                                                    'dateadded' => date('Y-m-d'),
                                                    'addedfrom' => $valueDetail['send'],
                                                    'is_public' => $valueDetail['public'],
                                                    'description' => $valueDetail['content'],
                                                    'status' => 1,
                                                    'rel_type' => 'customer',
                                                    'rel_id' => $valueClient['userid']
                                                ];
                                                $this->db->insert(db_prefix() . 'tasks', $array_tasks);
                                                $id_task = $this->db->insert_id();

                                                if (!empty($id_task)) {

                                                    $this->tasks_model->add_task_assignees([
                                                        'assignee' => $valueReceive,
                                                        'taskid' => $id_task,
                                                        'assigned_from' => $valueDetail['send']
                                                    ]);

                                                    if (!empty($valueDetail['staff_follow'])) {
                                                        $this->tasks_model->add_task_followers([
                                                            'taskid' => $valueDetail['staff_follow'],
                                                            'follower' => $id_task
                                                        ]);
                                                    }
                                                }
                                            }
                                            continue;
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else if ($value['action'] == 2) {
                    foreach ($automation_where as $keyWhere => $valueWhere) {
                        $this->db->where($valueWhere['sub_where']);
                    }
                    $leads = $this->db->get('tblleads')->result_array(); //khách hàng tiềm năng thỏa điều kiện
                    foreach ($leads as $keyLead => $valueLead) {
                        foreach ($automation_detail as $keyDetail => $valueDetail) {
                            if ($valueDetail['type'] == 1) //gửi thông báo nhân viên
                            {
                                $receive = explode(',', $valueDetail['receive']);
                                $staff_isset = array();
                                foreach ($receive as $keyReceive => $valueReceive) {
                                    if ($valueReceive == '-1') {
                                        if (empty($staff_isset[$valueLead['addedfrom']])) {
                                            $staff_isset[$valueLead['addedfrom']] = true;
                                            add_notification([
                                                'touserid' => $valueLead['addedfrom'],
                                                'fromuserid' => $valueDetail['send'],
                                                'description' => $valueDetail['content'],
                                                'link' => 'leads/index/' . $valueLead['id']
                                            ]);
                                        }
//                                        continue;
                                    } else {
                                        if (empty($staff_isset[$valueReceive])) {
                                            $staff_isset[$valueReceive] = true;
                                            add_notification([
                                                'touserid' => $valueReceive,
                                                'fromuserid' => $valueDetail['send'],
                                                'description' => $valueDetail['content'],
                                                'link' => 'leads/index/' . $valueLead['id']
                                            ]);
                                        }
                                    }
                                }
                            } else if ($valueDetail['type'] == 2) //gửi email khách hàng
                            {

                                $this->load->config('email');
                                $this->email->initialize();
                                if (get_option('mail_engine') == 'phpmailer') {
                                    $this->email->set_debug_output(function ($err) {
                                        if (!isset($GLOBALS['debug'])) {
                                            $GLOBALS['debug'] = '';
                                        }
                                        $GLOBALS['debug'] .= $err . '<br />';
                                        return false;
                                    });
                                    $this->email->set_smtp_debug(3);
                                }
                                $company = get_option('companyname');
                                $this->email->set_newline(config_item('newline'));
                                $this->email->set_crlf(config_item('crlf'));
                                $this->email->from(get_option('smtp_email'), $company);
                                $this->email->to($valueLead['email']);
                                $systemBCC = get_option('bcc_emails');
                                if ($systemBCC != '') {
                                    $this->email->bcc($systemBCC);
                                }
                                $this->email->subject($valueDetail['title']);
                                $this->email->message($valueDetail['content']);
                                if ($this->email->send(true)) {
                                    ++$EactionSendEmail;
                                }
                            }
                            if ($valueDetail['type'] == 3) //gửi thông báo nhân viên
                            {
                                $receive = explode(',', $valueDetail['receive']);
                                $staff_isset = array();
                                foreach ($receive as $keyReceive => $valueReceive) {
                                    if ($valueReceive == '-1') {
                                        if (empty($staff_isset[$valueLead['addedfrom']])) {
                                            $staff_isset[$valueLead['addedfrom']] = true;
                                            $array_tasks = [
                                                'name' => $valueDetail['title'],
                                                'startdate' => date('Y-m-d'),
                                                'dateadded' => date('Y-m-d H:i:s'),
                                                'addedfrom' => $valueDetail['send'],
                                                'is_public' => $valueDetail['public'],
                                                'description' => $valueDetail['content'],
                                                'status' => 1,
                                                'rel_type' => 'lead',
                                                'rel_id' => $valueLead['id']
                                            ];
                                            $this->db->insert(db_prefix() . 'tasks', $array_tasks);
                                            $id_task = $this->db->insert_id();
                                            if (!empty($id_task)) {

                                                $this->tasks_model->add_task_assignees([
                                                    'assignee' => $valueLead['addedfrom'],
                                                    'taskid' => $id_task,
                                                    'assigned_from' => $valueDetail['send']
                                                ]);

                                                if (!empty($valueDetail['staff_follow'])) {
                                                    $this->tasks_model->add_task_followers([
                                                        'taskid' => $valueDetail['staff_follow'],
                                                        'follower' => $id_task
                                                    ]);
                                                }

                                            }
                                        }
                                    } else {
                                        if (empty($staff_isset[$valueReceive])) {
                                            $staff_isset[$valueReceive] = true;
                                            $array_tasks = [
                                                'name' => $valueDetail['title'],
                                                'startdate' => date('Y-m-d'),
                                                'dateadded' => date('Y-m-d H:i:s'),
                                                'addedfrom' => $valueDetail['send'],
                                                'is_public' => $valueDetail['public'],
                                                'description' => $valueDetail['content'],
                                                'status' => 1,
                                                'rel_type' => 'lead',
                                                'rel_id' => $valueLead['id']
                                            ];
                                            $this->db->insert(db_prefix() . 'tasks', $array_tasks);
                                            $id_task = $this->db->insert_id();
                                            if (!empty($id_task)) {
                                                $this->tasks_model->add_task_assignees([
                                                    'assignee' => $valueReceive,
                                                    'taskid' => $id_task,
                                                    'assigned_from' => $valueDetail['send']
                                                ]);

                                                if (!empty($valueDetail['staff_follow'])) {
                                                    $this->tasks_model->add_task_followers([
                                                        'taskid' => $valueDetail['staff_follow'],
                                                        'follower' => $id_task
                                                    ]);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $this->db->where('id', $value['id']);
                if ($this->db->update(db_prefix() . 'automation_proviso', ['number_run' => ($value['number_run'] + 1)])) {
                    ++$Eaction;
                }
            }
            if ($Eaction > 0 || $EactionSendEmail > 0) {
                return true;
            }
        }
        return false;
    }

    // chạy không cần kiểm tra điều kiện
    function RunNow($id = "")
    {
        if (!empty($id)) {
            $this->db->where(db_prefix() . 'automations.id', $id);
            $this->db->where('status', 2);
            $automation_proviso = $this->db->get(db_prefix() . 'automations')->row();

            $Eaction = 0; // biến kiểm tra xem có thay đổi
            $EactionSendEmail = 0; // biến kiểm tra xem có thay đổi
            if (!empty($automation_proviso)) {
                //Lấy thông tin nội dung và người gửi  + người nhận thông báo
                $this->db->select(db_prefix() . 'automation_detail.*,GROUP_CONCAT(' . db_prefix() . 'automations_receive.receive,"") as receive');
                $this->db->group_by(db_prefix() . 'automation_detail.id', db_prefix() . 'automation_detail.id');
                $this->db->where('id_auto', $automation_proviso->id);
                $this->db->join(db_prefix() . 'automations_receive', db_prefix() . 'automations_receive.id_detail = ' . db_prefix() . 'automation_detail.id', 'left');
                $automation_detail = $this->db->get(db_prefix() . 'automation_detail')->result_array();

                // Lấy điều kiện khách hàng
                $this->db->where('id_auto', $automation_proviso->id);
                $automation_where = $this->db->get(db_prefix() . 'automation_where')->result_array();

                if ($automation_proviso->action == 1) {
                    $this->db->select(db_prefix() . 'clients.*, GROUP_CONCAT(' . db_prefix() . 'customer_admins.staff_id,"") as staff_id');
                    foreach ($automation_where as $keyWhere => $valueWhere) {
                        $this->db->where($valueWhere['sub_where']);
                    }
                    $this->db->join(db_prefix() . 'customer_admins', db_prefix() . 'customer_admins.customer_id = ' . db_prefix() . 'clients.userid', 'left');
                    $this->db->group_by('userid');
                    $clients = $this->db->get(db_prefix() . 'clients')->result_array(); // khách hàng thỏa điều kiện
                    if (!empty($clients)) {
                        foreach ($clients as $keyClient => $valueClient) {
                            foreach ($automation_detail as $keyDetail => $valueDetail) {
                                if ($valueDetail['type'] == 1) //gửi thông báo nhân viên
                                {
                                    $staff_isset = array();
                                    $receive = explode(',', $valueDetail['receive']);
                                    foreach ($receive as $keyReceive => $valueReceive) {
                                        if ($valueReceive == '-1') {
                                            if (empty($staff_isset[$valueClient['addedfrom']])) {
                                                $staff_isset[$valueClient['addedfrom']] = true;
                                                add_notification([
                                                    'touserid' => $valueClient['addedfrom'],
                                                    'fromuserid' => $valueDetail['send'],
                                                    'description' => ('Auto:('._l('cong_clients_auto').':'.$valueClient['company'].') - ').$valueDetail['content'],
                                                    'link' => 'clients/client/' . $valueClient['userid']
                                                ]);
                                            }
                                            continue;
                                        }
                                        else if ($valueReceive == '-2') {
                                            if (!empty($valueClient['staff_id'])) {
                                                $valueClient['staff_id'] = explode(',', $valueClient['staff_id']);
                                                foreach ($valueClient['staff_id'] as $ik => $iv) {
                                                    if (empty($staff_isset[$iv])) {
                                                        $staff_isset[$iv] = true;
                                                        add_notification([
                                                            'touserid' => $iv,
                                                            'fromuserid' => $valueDetail['send'],
                                                            'description' => ('Auto:('._l('cong_clients_auto').':'.$valueClient['company'].') - ').$valueDetail['content'],
                                                            'link' => 'clients/client/' . $valueClient['userid']
                                                        ]);
                                                    }
                                                }
                                                continue;
                                            }
                                        }
                                        else {
                                            if (empty($staff_isset[$valueReceive])) {
                                                $staff_isset[$valueReceive] = true;
                                                add_notification([
                                                    'touserid' => $valueReceive,
                                                    'fromuserid' => $valueDetail['send'],
                                                    'description' => ('Auto:('._l('cong_clients_auto').':'.$valueClient['company'].') - ').$valueDetail['content'],
                                                    'link' => 'clients/client/' . $valueClient['userid']
                                                ]);
                                            }
                                            continue;
                                        }
                                    }
                                }
                                else if ($valueDetail['type'] == 2)//gửi email khách hàng
                                {
                                    $this->load->config('email');
                                    $this->email->initialize();
                                    if (get_option('mail_engine') == 'phpmailer') {
                                        $this->email->set_debug_output(function ($err) {
                                            if (!isset($GLOBALS['debug'])) {
                                                $GLOBALS['debug'] = '';
                                            }
                                            $GLOBALS['debug'] .= $err . '<br />';
                                            return false;
                                        });
                                        $this->email->set_smtp_debug(3);
                                    }
                                    $company = get_option('companyname');
                                    $this->email->set_newline(config_item('newline'));
                                    $this->email->set_crlf(config_item('crlf'));
                                    $this->email->from(get_option('smtp_email'), $company);
                                    $this->email->to($valueClient['email_client']);
                                    $systemBCC = get_option('bcc_emails');
                                    if ($systemBCC != '') {
                                        $this->email->bcc($systemBCC);
                                    }
                                    $this->email->subject($valueDetail['title']);
                                    $this->email->message($valueDetail['content']);
                                    if ($this->email->send(true)) {
                                        ++$EactionSendEmail;
                                    }
                                }
                                else if ($valueDetail['type'] == 3)//Tạo công việc cho nhân viên liên quan đến khách hàng
                                {
                                    $staff_isset = array();
                                    $receive = explode(',', $valueDetail['receive']);
                                    foreach ($receive as $keyReceive => $valueReceive) {
                                        if ($valueReceive == '-1') {
                                            if (empty($staff_isset[$valueClient['addedfrom']])) {
                                                $staff_isset[$valueClient['addedfrom']] = true;
                                                $array_tasks = [
                                                    'name' => $valueDetail['title'],
                                                    'startdate' => date('Y-m-d'),
                                                    'dateadded' => date('Y-m-d'),
                                                    'addedfrom' => $valueDetail['send'],
                                                    'is_public' => $valueDetail['public'],
                                                    'description' => $valueDetail['content'],
                                                    'rel_type' => 'customer',
                                                    'rel_id' => $valueClient['userid']
                                                ];
                                                $this->db->insert(db_prefix() . 'tasks', $array_tasks);
                                                $id_task = $this->db->insert_id();
                                                if (!empty($id_task)) {
                                                    $this->tasks_model->add_task_assignees([
                                                        'assignee' => $valueClient['addedfrom'],
                                                        'taskid' => $id_task,
                                                        'assigned_from' => $valueDetail['send']
                                                    ]);

                                                    if (!empty($valueDetail['staff_follow'])) {
                                                        $this->tasks_model->add_task_followers([
                                                            'taskid' => $valueDetail['staff_follow'],
                                                            'follower' => $id_task
                                                        ]);
                                                    }
                                                }
                                            }
                                            continue;
                                        } else if ($valueReceive == '-2') {
                                            if (!empty($valueClient['staff_id'])) {
                                                $valueClient['staff_id'] = explode(',', $valueClient['staff_id']);
                                                $array_tasks = [
                                                    'name' => $valueDetail['title'],
                                                    'startdate' => date('Y-m-d'),
                                                    'dateadded' => date('Y-m-d'),
                                                    'addedfrom' => $valueDetail['send'],
                                                    'is_public' => $valueDetail['public'],
                                                    'description' => $valueDetail['content'],
                                                    'status' => 1,
                                                    'rel_type' => 'customer',
                                                    'rel_id' => $valueClient['userid']
                                                ];
                                                $this->db->insert(db_prefix() . 'tasks', $array_tasks);
                                                $id_task = $this->db->insert_id();

                                                if (!empty($id_task)) {
                                                    foreach ($valueClient['staff_id'] as $ik => $iv) {
                                                        if (empty($staff_isset[$iv])) {
                                                            $staff_isset[$iv] = true;
                                                            $this->db->insert(db_prefix() . 'task_assigned', [
                                                                'staffid' => $iv,
                                                                'taskid' => $id_task,
                                                                'assigned_from' => $valueDetail['send'],
                                                            ]);
                                                        }
                                                    }
                                                    if (!empty($valueDetail['staff_follow'])) {
                                                        $this->db->insert(db_prefix() . 'task_followers', [
                                                            'staffid' => $valueDetail['staff_follow'],
                                                            'taskid' => $id_task
                                                        ]);
                                                    }
                                                }
                                                continue;
                                            }
                                        } else {
                                            if (empty($staff_isset[$valueReceive])) {
                                                $staff_isset[$valueReceive] = true;
                                                $array_tasks = [
                                                    'name' => $valueDetail['title'],
                                                    'startdate' => date('Y-m-d'),
                                                    'dateadded' => date('Y-m-d'),
                                                    'addedfrom' => $valueDetail['send'],
                                                    'is_public' => $valueDetail['public'],
                                                    'description' => $valueDetail['content'],
                                                    'status' => 1,
                                                    'rel_type' => 'customer',
                                                    'rel_id' => $valueClient['userid']
                                                ];
                                                $this->db->insert(db_prefix() . 'tasks', $array_tasks);
                                                $id_task = $this->db->insert_id();

                                                if (!empty($id_task)) {

                                                    $this->tasks_model->add_task_assignees([
                                                        'assignee' => $valueReceive,
                                                        'taskid' => $id_task,
                                                        'assigned_from' => $valueDetail['send']
                                                    ]);

                                                    if (!empty($valueDetail['staff_follow'])) {
                                                        $this->tasks_model->add_task_followers([
                                                            'taskid' => $valueDetail['staff_follow'],
                                                            'follower' => $id_task
                                                        ]);
                                                    }
                                                }
                                            }
                                            continue;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                else if ($automation_proviso->action == 2) {
                    foreach ($automation_where as $keyWhere => $valueWhere) {
                        $this->db->where($valueWhere['sub_where']);
                    }
                    $leads = $this->db->get('tblleads')->result_array(); //khách hàng tiềm năng thỏa điều kiện
                    foreach ($leads as $keyLead => $valueLead) {
                        foreach ($automation_detail as $keyDetail => $valueDetail) {
                            if ($valueDetail['type'] == 1) //gửi thông báo nhân viên
                            {
                                $receive = explode(',', $valueDetail['receive']);
                                $staff_isset = array();
                                foreach ($receive as $keyReceive => $valueReceive) {
                                    if ($valueReceive == '-1') {
                                        if (empty($staff_isset[$valueLead['addedfrom']])) {
                                            $staff_isset[$valueLead['addedfrom']] = true;
                                            add_notification([
                                                'touserid' => $valueLead['addedfrom'],
                                                'fromuserid' => $valueDetail['send'],
                                                'description' => ('Auto:('._l('cong_lead_auto').': '.$valueLead['name'].') - ').$valueDetail['content'],
                                                'link' => 'leads/index/' . $valueLead['id']
                                            ]);
                                        }
                                        //                                        continue;
                                    } else {
                                        if (empty($staff_isset[$valueReceive])) {
                                            $staff_isset[$valueReceive] = true;
                                            add_notification([
                                                'touserid' => $valueReceive,
                                                'fromuserid' => $valueDetail['send'],
                                                'description' => ('Auto:('._l('cong_lead_auto').': '.$valueLead['name'].') - ').$valueDetail['content'],
                                                'link' => 'leads/index/' . $valueLead['id']
                                            ]);
                                        }
                                    }
                                }
                            }
                            else if ($valueDetail['type'] == 2) //gửi email khách hàng
                            {

                                $this->load->config('email');
                                $this->email->initialize();
                                if (get_option('mail_engine') == 'phpmailer') {
                                    $this->email->set_debug_output(function ($err) {
                                        if (!isset($GLOBALS['debug'])) {
                                            $GLOBALS['debug'] = '';
                                        }
                                        $GLOBALS['debug'] .= $err . '<br />';
                                        return false;
                                    });
                                    $this->email->set_smtp_debug(3);
                                }
                                $company = get_option('companyname');
                                $this->email->set_newline(config_item('newline'));
                                $this->email->set_crlf(config_item('crlf'));
                                $this->email->from(get_option('smtp_email'), $company);
                                $this->email->to($valueLead['email']);
                                $systemBCC = get_option('bcc_emails');
                                if ($systemBCC != '') {
                                    $this->email->bcc($systemBCC);
                                }
                                $this->email->subject($valueDetail['title']);
                                $this->email->message($valueDetail['content']);
                                if ($this->email->send(true)) {
                                    ++$EactionSendEmail;
                                }
                            }
                            else if ($valueDetail['type'] == 3) //gửi công việc cho nhân viên
                            {
                                $receive = explode(',', $valueDetail['receive']);
                                $staff_isset = array();
                                foreach ($receive as $keyReceive => $valueReceive) {
                                    if ($valueReceive == '-1') {
                                        if (empty($staff_isset[$valueLead['addedfrom']])) {
                                            $staff_isset[$valueLead['addedfrom']] = true;
                                            $array_tasks = [
                                                'name' => $valueDetail['title'],
                                                'startdate' => date('Y-m-d'),
                                                'dateadded' => date('Y-m-d H:i:s'),
                                                'addedfrom' => $valueDetail['send'],
                                                'is_public' => $valueDetail['public'],
                                                'description' => $valueDetail['content'],
                                                'status' => 1,
                                                'rel_type' => 'lead',
                                                'rel_id' => $valueLead['id']
                                            ];
                                            $this->db->insert(db_prefix() . 'tasks', $array_tasks);
                                            $id_task = $this->db->insert_id();
                                            if (!empty($id_task)) {

                                                $this->tasks_model->add_task_assignees([
                                                    'assignee' => $valueLead['addedfrom'],
                                                    'taskid' => $id_task,
                                                    'assigned_from' => $valueDetail['send']
                                                ]);

                                                if (!empty($valueDetail['staff_follow'])) {
                                                    $this->tasks_model->add_task_followers([
                                                        'taskid' => $valueDetail['staff_follow'],
                                                        'follower' => $id_task
                                                    ]);
                                                }

                                            }
                                        }
                                    } else {
                                        if (empty($staff_isset[$valueReceive])) {
                                            $staff_isset[$valueReceive] = true;
                                            $array_tasks = [
                                                'name' => $valueDetail['title'],
                                                'startdate' => date('Y-m-d'),
                                                'dateadded' => date('Y-m-d H:i:s'),
                                                'addedfrom' => $valueDetail['send'],
                                                'is_public' => $valueDetail['public'],
                                                'description' => $valueDetail['content'],
                                                'status' => 1,
                                                'rel_type' => 'lead',
                                                'rel_id' => $valueLead['id']
                                            ];
                                            $this->db->insert(db_prefix() . 'tasks', $array_tasks);
                                            $id_task = $this->db->insert_id();
                                            if (!empty($id_task)) {
                                                $this->tasks_model->add_task_assignees([
                                                    'assignee' => $valueReceive,
                                                    'taskid' => $id_task,
                                                    'assigned_from' => $valueDetail['send']
                                                ]);

                                                if (!empty($valueDetail['staff_follow'])) {
                                                    $this->tasks_model->add_task_followers([
                                                        'taskid' => $valueDetail['staff_follow'],
                                                        'follower' => $id_task
                                                    ]);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                return true;
            }
        }
        return false;
    }
}
