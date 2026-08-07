<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return array
     * Used in home dashboard page
     * Return all upcoming events this week
     */
    public function get_upcoming_events()
    {
        $this->db->where('(start BETWEEN "' . date('Y-m-d', strtotime('monday this week')) . '" AND "' . date('Y-m-d', strtotime('sunday this week')) . '")');
        $this->db->where('(userid = ' . get_staff_user_id() . ' OR public = 1)');
        $this->db->order_by('start', 'desc');
        $this->db->limit(6);

        return $this->db->get(db_prefix() . 'events')->result_array();
    }

    /**
     * @param  integer (optional) Limit upcoming events
     * @return integer
     * Used in home dashboard page
     * Return total upcoming events next week
     */
    public function get_upcoming_events_next_week()
    {
        $monday_this_week = date('Y-m-d', strtotime('monday next week'));
        $sunday_this_week = date('Y-m-d', strtotime('sunday next week'));
        $this->db->where('(start BETWEEN "' . $monday_this_week . '" AND "' . $sunday_this_week . '")');
        $this->db->where('(userid = ' . get_staff_user_id() . ' OR public = 1)');

        return $this->db->count_all_results(db_prefix() . 'events');
    }

    /**
     * @param  mixed
     * @return array
     * Used in home dashboard page, currency passed from javascript (undefined or integer)
     * Displays weekly payment statistics (chart)
     */
    //public function get_weekly_payments_statistics($currency)
    // {
    //     $all_payments                 = [];
    //     $has_permission_payments_view = has_permission('payments', '', 'view');
    //     $this->db->select('amount,' . db_prefix() . 'invoicepaymentrecords.date');
    //     $this->db->from(db_prefix() . 'invoicepaymentrecords');
    //     $this->db->join(db_prefix() . 'invoices', '' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
    //     $this->db->where('CAST(' . db_prefix() . 'invoicepaymentrecords.date as DATE) >= "' . date('Y-m-d', strtotime('monday this week')) . '" AND CAST(' . db_prefix() . 'invoicepaymentrecords.date as DATE) <= "' . date('Y-m-d', strtotime('sunday this week')) . '"');
    //     $this->db->where('' . db_prefix() . 'invoices.status !=', 5);
    //     if ($currency != 'undefined') {
    //         $this->db->where('currency', $currency);
    //     }

    //     if (!$has_permission_payments_view) {
    //         $this->db->where('invoiceid IN (SELECT id FROM ' . db_prefix() . 'invoices WHERE addedfrom=' . get_staff_user_id() . ')');
    //     }

    //     // Current week
    //     $all_payments[] = $this->db->get()->result_array();
    //     $this->db->select('amount,' . db_prefix() . 'invoicepaymentrecords.date');
    //     $this->db->from(db_prefix() . 'invoicepaymentrecords');
    //     $this->db->join(db_prefix() . 'invoices', '' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
    //     $this->db->where('CAST(' . db_prefix() . 'invoicepaymentrecords.date as DATE) >= "' . date('Y-m-d', strtotime('monday last week', strtotime('last sunday'))) . '" AND CAST(' . db_prefix() . 'invoicepaymentrecords.date as DATE) <= "' . date('Y-m-d', strtotime('sunday last week', strtotime('last sunday'))) . '"');

    //     $this->db->where('' . db_prefix() . 'invoices.status !=', 5);
    //     if ($currency != 'undefined') {
    //         $this->db->where('currency', $currency);
    //     }
    //     // Last Week
    //     $all_payments[] = $this->db->get()->result_array();

    //     $chart = [
    //         'labels'   => get_weekdays(),
    //         'datasets' => [
    //             [
    //                 'label'           => _l('this_week_payments'),
    //                 'backgroundColor' => 'rgba(37,155,35,0.2)',
    //                 'borderColor'     => '#84c529',
    //                 'borderWidth'     => 1,
    //                 'tension'         => false,
    //                 'data'            => [
    //                     0,
    //                     0,
    //                     0,
    //                     0,
    //                     0,
    //                     0,
    //                     0,
    //                 ],
    //             ],
    //             [
    //                 'label'           => _l('last_week_payments'),
    //                 'backgroundColor' => 'rgba(197, 61, 169, 0.5)',
    //                 'borderColor'     => '#c53da9',
    //                 'borderWidth'     => 1,
    //                 'tension'         => false,
    //                 'data'            => [
    //                     0,
    //                     0,
    //                     0,
    //                     0,
    //                     0,
    //                     0,
    //                     0,
    //                 ],
    //             ],
    //         ],
    //     ];


    //     for ($i = 0; $i < count($all_payments); $i++) {
    //         foreach ($all_payments[$i] as $payment) {
    //             $payment_day = date('l', strtotime($payment['date']));
    //             $x           = 0;
    //             foreach (get_weekdays_original() as $day) {
    //                 if ($payment_day == $day) {
    //                     $chart['datasets'][$i]['data'][$x] += $payment['amount'];
    //                 }
    //                 $x++;
    //             }
    //         }
    //     }

    //     return $chart;
    // }
    public function get_weekly_payments_statistics($data)
    {
        if (empty($data['year'])) {
            $year = date('Y');
        } else {
            $year = $data['year'];
        }
        if (empty($data['month']) || $data['month'] == ' ') {
            $month = NULL;
        } else {
            $month = $data['month'];
        }
        if (empty($data['day']) || $data['day'] == ' ') {
            $day = NULL;
        } else {
            $day = $data['day'];
        }
        $_data                 = [];
        $warehouse = get_table_where('tblwarehouse', ['id' => 0]);
        foreach ($warehouse as $key => $value) {
            $_data[] = $value['code'];
        }
        $chart = [
            'labels'   => $_data,
            'datasets' => [
                [
                    'label'           => _l('Số mặt hàng'),
                    'backgroundColor' => 'rgba(37,155,35,0.2)',
                    'borderColor'     => '#84c529',
                    'borderWidth'     => 1,
                    'tension'         => false,
                    'data'            => [
                        0,
                        0,
                        0,
                    ],
                ],
                [
                    'label'           => _l('Tổng số lượng mặt hàng'),
                    'backgroundColor' => 'rgba(197, 61, 169, 0.5)',
                    'borderColor'     => '#c53da9',
                    'borderWidth'     => 1,
                    'tension'         => false,
                    'data'            => [
                        0,
                        0,
                        0,
                    ],
                ],
            ],
        ];


        $i = 0;
        $x = 0;
        foreach ($warehouse as $key => $value) {
            $this->db->select_sum("quantity_left");
            $this->db->where("tblwarehouse_product.warehouse_id", $value['id']);
            if (!empty($year)) {
                $this->db->where("YEAR(date_warehouse)", $year);
            }
            if (!empty($month)) {
                $this->db->where("MONTH(date_warehouse)", $month);
            }
            if (!empty($day)) {
                $this->db->where("DAY(date_warehouse)", $day);
            }
            $subtotal = $this->db->get("tblwarehouse_product")->row()->quantity_left;
            $this->db->select("COUNT(*) as count");
            $this->db->where("tblwarehouse_product.warehouse_id", $value['id']);
            if (!empty($year)) {
                $this->db->where("YEAR(date_warehouse)", $year);
            }
            if (!empty($month)) {
                $this->db->where("MONTH(date_warehouse)", $month);
            }
            if (!empty($day)) {
                $this->db->where("DAY(date_warehouse)", $day);
            }
            $this->db->where("quantity_left >", 0);
            $this->db->group_by("product_id,type_items");
            $count = $this->db->get("tblwarehouse_product")->result_array();
            if (empty($count)) {
                $count_s = 0;
            } else {
                $count_s = count($count);
            }
            if (empty($subtotal)) {
                $subtotal_s = 0;
            } else {
                $subtotal_s = $subtotal;
            }
            $chart['datasets'][0]['data'][$i] = $count_s;
            $chart['datasets'][1]['data'][$i] = $subtotal_s;
            $i++;
        }
        return $chart;
    }

    public function projects_status_stats()
    {
        $this->load->model('projects_model');
        $statuses = $this->projects_model->get_project_statuses();
        $colors   = get_system_favourite_colors();

        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];


        $has_permission = has_permission('projects', '', 'view');
        $sql            = '';
        foreach ($statuses as $status) {
            $sql .= ' SELECT COUNT(*) as total';
            $sql .= ' FROM ' . db_prefix() . 'projects';
            $sql .= ' WHERE status=' . $status['id'];
            if (!$has_permission) {
                $sql .= ' AND id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';
            }
            $sql .= ' UNION ALL ';
            $sql = trim($sql);
        }

        $result = [];
        if ($sql != '') {
            // Remove the last UNION ALL
            $sql    = substr($sql, 0, -10);
            $result = $this->db->query($sql)->result();
        }

        foreach ($statuses as $key => $status) {
            array_push($_data['statusLink'], admin_url('projects?status=' . $status['id']));
            array_push($chart['labels'], $status['name']);
            array_push($_data['backgroundColor'], $status['color']);
            array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['color'], -20));
            array_push($_data['data'], $result[$key]->total);
        }

        $chart['datasets'][]           = $_data;
        $chart['datasets'][0]['label'] = _l('home_stats_by_project_status');

        return $chart;
    }

    public function leads_status_stats()
    {
        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];

        $result = get_leads_summary();

        foreach ($result as $status) {
            if (!isset($status['junk']) && !isset($status['lost'])) {
                if ($status['color'] == '') {
                    $status['color'] = '#737373';
                }
                array_push($chart['labels'], $status['name']);
                array_push($_data['backgroundColor'], $status['color']);
                array_push($_data['statusLink'], admin_url('leads?status=' . $status['id']));
                array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['color'], -20));
                array_push($_data['data'], $status['total']);
            }
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }

    /**
     * Display total tickets awaiting reply by department (chart)
     * @return array
     */
    public function tickets_awaiting_reply_by_department()
    {
        $this->load->model('departments_model');
        $departments = $this->departments_model->get();
        $colors      = get_system_favourite_colors();
        $chart       = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];

        $i = 0;
        foreach ($departments as $department) {
            if (!is_admin()) {
                if (get_option('staff_access_only_assigned_departments') == 1) {
                    $staff_deparments_ids = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                    $departments_ids      = [];
                    if (count($staff_deparments_ids) == 0) {
                        $departments = $this->departments_model->get();
                        foreach ($departments as $department) {
                            array_push($departments_ids, $department['departmentid']);
                        }
                    } else {
                        $departments_ids = $staff_deparments_ids;
                    }
                    if (count($departments_ids) > 0) {
                        $this->db->where('department IN (SELECT departmentid FROM ' . db_prefix() . 'staff_departments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="' . get_staff_user_id() . '")');
                    }
                }
            }
            $this->db->where_in('status', [
                1,
                2,
                4,
            ]);

            $this->db->where('department', $department['departmentid']);
            $total = $this->db->count_all_results(db_prefix() . 'tickets');

            if ($total > 0) {
                $color = '#333';
                if (isset($colors[$i])) {
                    $color = $colors[$i];
                }
                array_push($chart['labels'], $department['name']);
                array_push($_data['backgroundColor'], $color);
                array_push($_data['hoverBackgroundColor'], adjust_color_brightness($color, -20));
                array_push($_data['data'], $total);
            }
            $i++;
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }

    /**
     * Display total tickets awaiting reply by status (chart)
     * @return array
     */
    public function tickets_awaiting_reply_by_status()
    {
        $this->load->model('tickets_model');
        $this->load->model('departments_model');
        $statuses             = $this->tickets_model->get_ticket_status();
        $_statuses_with_reply = [
            1,
            2,
            4,
        ];

        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];

        foreach ($statuses as $status) {
            if (in_array($status['ticketstatusid'], $_statuses_with_reply)) {
                if (!is_admin()) {
                    if (get_option('staff_access_only_assigned_departments') == 1) {
                        $staff_deparments_ids = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                        $departments_ids      = [];
                        if (count($staff_deparments_ids) == 0) {
                            $departments = $this->departments_model->get();
                            foreach ($departments as $department) {
                                array_push($departments_ids, $department['departmentid']);
                            }
                        } else {
                            $departments_ids = $staff_deparments_ids;
                        }
                        if (count($departments_ids) > 0) {
                            $this->db->where('department IN (SELECT departmentid FROM ' . db_prefix() . 'staff_departments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="' . get_staff_user_id() . '")');
                        }
                    }
                }

                $this->db->where('status', $status['ticketstatusid']);
                $total = $this->db->count_all_results(db_prefix() . 'tickets');
                if ($total > 0) {
                    array_push($chart['labels'], ticket_status_translate($status['ticketstatusid']));
                    array_push($_data['statusLink'], admin_url('tickets/index/' . $status['ticketstatusid']));
                    array_push($_data['backgroundColor'], $status['statuscolor']);
                    array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['statuscolor'], -20));
                    array_push($_data['data'], $total);
                }
            }
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }

    //Công bổ sung

    public function client_time_status()
    {

        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];


        $year = date('Y');
        $quarter = array(
            '1' => [$year.'-01',$year.'-02',$year.'-03'],
            '2' => [$year.'-04',$year.'-05',$year.'-06'],
            '3' => [$year.'-07',$year.'-08',$year.'-09'],
            '4' => [$year.'-10',$year.'-11',$year.'-12']
        );

        $key_quarer = array(
            '1' => 1,
            '2' => 1,
            '3' => 1,
            '4' => 2,
            '5' => 2,
            '6' => 2,
            '7' => 3,
            '8' => 3,
            '9' => 3,
            '10' => 4,
            '11' => 4,
            '12' => 4
        );

        $this->db->select('count(userid) as count_id');
        $this->db->where('DATE_FORMAT(datecreated,"%Y-%m")',date('Y-m'));
        $client_month = $this->db->get(db_prefix().'clients')->row();
        $statuses[] = [
            'labels' => _l('cong_client_create_month'),
            'backgroundColor' => '#84c529',
            'hoverBackgroundColor' => '#8de017',
            'count' => $client_month->count_id,
        ];

        $this->db->select('count(userid)  as count_id');
        $this->db->where_in('DATE_FORMAT(datecreated,"%Y-%m")',$quarter[$key_quarer[(int)date('m')]]);
        $client_quarter = $this->db->get(db_prefix().'clients')->row();
        $statuses[] = [
            'labels' => _l('cong_client_create_quarter'),
            'backgroundColor' => '#c8ae2e',
            'hoverBackgroundColor' => '#e4c00c',
            'count' => $client_quarter->count_id,
        ];

        $this->db->select('count(userid)  as count_id');
        $this->db->where('DATE_FORMAT(datecreated,"%Y")',date('Y'));
        $client_year = $this->db->get(db_prefix().'clients')->row();

        $statuses[] = [
            'labels' => _l('cong_client_create_year'),
            'backgroundColor' => '#9a2a2a',
            'hoverBackgroundColor' => '#d21d1d',
            'count' => $client_year->count_id,
        ];





        foreach ($statuses as $key => $status) {
            array_push($_data['statusLink'],'');
            array_push($chart['labels'], $status['labels']);
            array_push($_data['backgroundColor'], $status['backgroundColor']);
            array_push($_data['hoverBackgroundColor'], $status['hoverBackgroundColor'], -20);
            array_push($_data['data'], $status['count']);
        }

        $chart['datasets'][]           = $_data;
        $chart['datasets'][0]['label'] = _l('home_stats_by_project_status');

        return $chart;
    }
    public function lead_time_status()
    {

        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];


        $year = date('Y');
        $quarter = array(
            '1' => [$year.'-01',$year.'-02',$year.'-03'],
            '2' => [$year.'-04',$year.'-05',$year.'-06'],
            '3' => [$year.'-07',$year.'-08',$year.'-09'],
            '4' => [$year.'-10',$year.'-11',$year.'-12']
        );

        $key_quarer = array(
            '1' => 1,
            '2' => 1,
            '3' => 1,
            '4' => 2,
            '5' => 2,
            '6' => 2,
            '7' => 3,
            '8' => 3,
            '9' => 3,
            '10' => 4,
            '11' => 4,
            '12' => 4
        );

        $this->db->select('count(id) as count_id');
        $this->db->where('DATE_FORMAT(dateadded,"%Y-%m")',date('Y-m'));
        $leads_month = $this->db->get(db_prefix().'leads')->row();
        $statuses[] = [
            'labels' => _l('cong_lead_create_month'),
            'backgroundColor' => '#84c529',
            'hoverBackgroundColor' => '#8de017',
            'count' => $leads_month->count_id,
        ];

        $this->db->select('count(id)  as count_id');
        $this->db->where_in('DATE_FORMAT(dateadded,"%Y-%m")',$quarter[$key_quarer[(int)date('m')]]);
        $leads_quarter = $this->db->get(db_prefix().'leads')->row();
        $statuses[] = [
            'labels' => _l('cong_lead_create_quarter'),
            'backgroundColor' => '#c8ae2e',
            'hoverBackgroundColor' => '#e4c00c',
            'count' => $leads_quarter->count_id,
        ];

        $this->db->select('count(id)  as count_id');
        $this->db->where('DATE_FORMAT(dateadded,"%Y")',date('Y'));
        $leads_year = $this->db->get(db_prefix().'leads')->row();
        $statuses[] = [
            'labels' => _l('cong_lead_create_year'),
            'backgroundColor' => '#9a2a2a',
            'hoverBackgroundColor' => '#d21d1d',
            'count' => $leads_year->count_id,
        ];







        foreach ($statuses as $key => $status) {
            array_push($_data['statusLink'],'');
            array_push($chart['labels'], $status['labels']);
            array_push($_data['backgroundColor'], $status['backgroundColor']);
            array_push($_data['hoverBackgroundColor'], $status['hoverBackgroundColor'], -20);
            array_push($_data['data'], $status['count']);
        }

        $chart['datasets'][]           = $_data;
        $chart['datasets'][0]['label'] = _l('home_stats_by_project_status');

        return $chart;
    }

    //End công bổ sung
}
