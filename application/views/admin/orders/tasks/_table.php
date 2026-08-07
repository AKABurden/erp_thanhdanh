<?php

defined('BASEPATH') or exit('No direct script access allowed');

$table_data = [
    _l('the_number_sign'),
	_l('Liên quan đến'),
    _l('Mã công việc'),
    _l('tasks_dt_name'),
    _l('tasks_dt_datestart'),
    [
        'name'     => _l('Hạn chót'),
        'th_attrs' => ['class' => 'duedate'],
    ],
	_l('Người giao việc'),
	_l('Người được phân công'),
	_l('task_status'),
    _l('Kết quả'),
    _l('tasks_list_priority'),
    _l('Báo cáo sự cố'),
];

//array_unshift($table_data, [
//    'name'     => '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="tasks"><label></label></div>',
//    'th_attrs' => ['class' => (isset($bulk_actions) ? '' : 'not_visible')],
//]);
array_unshift($table_data, [
    'name'     => '<div class="text-center" style="width: 30px;"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child-all fa fa-caret-right"></a></div>',
    'th_attrs' => ['class' => (isset($bulk_actions) ? '' : 'not_visible')],
]);

$custom_fields = get_custom_fields('tasks', [
    'show_on_table' => 1,
]);

foreach ($custom_fields as $field) {
    array_push($table_data, $field['name']);
}

$table_data = hooks()->apply_filters('tasks_table_columns', $table_data);

$orders_by = htmlentities('[[1, "desc"]]');
render_datatable($table_data, 'tasks', [], [
        'data-last-order-identifier' => 'tasks',
//        'data-default-order'         => get_table_last_order('tasks'),
        'data-default-order'         => $orders_by,
]);
//print_arrays(get_table_last_order('tasks'));