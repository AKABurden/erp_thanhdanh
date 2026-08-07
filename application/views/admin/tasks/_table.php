<?php

defined('BASEPATH') or exit('No direct script access allowed');

$table_data = [
    _l('the_number_sign'),
	_l('Liên quan đến'),
    _l('Mã công việc'),
    _l('tasks_dt_name'),
    _l('Loại phiếu'),
    _l('Mã phiếu'),
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
    _l('Số thứ tự ưu tiên'),
    _l('Báo cáo sự cố'),
];


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

$orders_by = htmlentities('[[14, "desc"]]');
render_datatable($table_data, 'tasks sortable', [], [
        'data-last-order-identifier' => 'tasks',
        'data-default-order'         => $orders_by,
] , 'ui-sortable');