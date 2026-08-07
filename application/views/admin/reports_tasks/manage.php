<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">

</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-2 hide">
                                    <?php echo render_select('departments_task', !empty($departments) ? $departments : [], ['departmentid', 'name'], 'departments', '', ['multiple' => true]) ?>
                                </div>
                                <div class="col-md-2">
                                    <?php echo render_select('room_task', !empty($room) ? $room : [], ['id', 'name', 'code'], 'Phòng', '', ['multiple' => true]) ?>
                                </div>
                                <div class="col-md-2">
                                    <?php echo render_select('staff_task', !empty($staff) ? $staff : [], ['staffid', 'fullname'], 'Người được phân công', '', ['multiple' => true, 'data-actions-box' => true], [], '', '', false) ?>
                                </div>
                                <div class="col-md-2">
                                    <?php echo render_select('staff_follower', !empty($staff) ? $staff : [], ['staffid', 'fullname'], 'Người được phân công theo dõi', '', ['multiple' => true, 'data-actions-box' => true], [], '', '', false) ?>
                                </div>
                                <div class="col-md-2">
                                    <?php echo render_select('staff_task_create', !empty($staff) ? $staff : [], ['staffid', 'fullname'], 'Người giao việc', '', ['multiple' => true, 'data-actions-box' => true], [], '', '', false) ?>
                                </div>
                                <div class="col-md-2">
                                    <?php echo render_date_input('date_start', 'Ngày bắt đầu từ') ?>
                                </div>
                                <div class="col-md-2">
                                    <?php echo render_date_input('date_end', 'Ngày bắt đầu đến') ?>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-2">
                                    <?php echo render_date_input('date_start_end', 'Ngày hoàn thành từ') ?>
                                </div>
                                <div class="col-md-2">
                                    <?php echo render_date_input('date_end_end', 'Ngày hoàn thành đến') ?>
                                </div>
                                <div class="col-md-2">
                                    <?= lang('15. Loại phiếu yêu cầu', 'category_recommended_id') ?>
                                    <select name="category_recommended_id" data-none-selected-text="Loại phiếu yêu cầu" data-live-search="true" id="category_recommended_id" class="form-control selectpicker category_recommended_id">
                                        <option value=""></option>
                                        <?php if (!empty($categoryRecommended)) : ?>
                                            <?php foreach ($categoryRecommended as $key => $value) : ?>
                                                <option <?= !empty($object->category_recommended_id) && $object->category_recommended_id == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <?= lang('Phiếu yêu cầu', 'suggest_id') ?>
                                    <select class="form-control suggest_id selectpicker" name="suggest_id" id="suggest_id" data-live-search="true" data-none-selected-text="<?= _l('dropdown_non_selected_tex') ?>">
                                        <option></option>
                                        <?php if (!empty($dtSuggest)) { ?>
                                            <?php foreach ($dtSuggest as $key => $value) { ?>
                                                <option data-subtext="<?= $value['staff_suggest_name'] ?>" <?= ($value['id'] == $valueSelected) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['reference_no'] ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group" app-field-wrapper="category_tasks_search">
                                        <label for="category_tasks_search" class="control-label">Mã công việc</label>
                                        <select id="category_tasks_search" name="category_tasks_search" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                            <option></option>
                                            <?php if (!empty($category_tasks)) {
                                                foreach ($category_tasks as $key => $v) { ?>
                                                    <option value="<?= $v['id'] ?>" data-subtext="<?= $v['content'] ?>" data-departments="<?= $v['departments'] ?>"><?= $v['code'] ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group" app-field-wrapper="type_tasks_search">
                                        <label for="type_tasks_search" class="control-label">Loại công việc</label>
                                        <select id="type_tasks_search" name="type_tasks_search" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                            <option></option>
                                            <option value="1">Công việc đột xuất</option>
                                            <option value="2">Công việc thường xuyên</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <?php
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
                            ];
                            array_unshift($table_data, [
                                'name'     => '<div class="text-center" style="width: 30px;"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child-all fa fa-caret-right"></a></div>',
                                'th_attrs' => ['class' => (isset($bulk_actions) ? '' : 'not_visible')],
                            ]);
                            render_datatable($table_data, 'tasks_reports sortable', [], [
                                'data-last-order-identifier' => 'tasks_reports',
                            ], 'ui-sortable');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_hidden('list_departments'); ?>
<?php echo form_hidden('list_staff'); ?>
<?php echo form_hidden('list_staff_create'); ?>
<?php echo form_hidden('date_start_end_search'); ?>
<?php echo form_hidden('date_end_end_search'); ?>
<?php echo form_hidden('date_start_search'); ?>
<?php echo form_hidden('category_tasks_search_search'); ?>
<?php echo form_hidden('procedure_tasks_search'); ?>
<?php echo form_hidden('date_end_search'); ?>
<?php echo form_hidden('suggest_id_search'); ?>
<?php echo form_hidden('staff_follower_search'); ?>
<?php echo form_hidden('category_recommended_id_search'); ?>
<?php echo form_hidden('type_tasks_search_'); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    var fnserverparams = {
        'filterStatus': 'input[name="filterStatus"]',
        'list_staff': 'input[name="list_staff"]',
        'suggest_id': 'select[name="suggest_id"]',
        'staff_follower': 'select[name="staff_follower"]',
        'category_recommended_id': 'select[name="category_recommended_id"]',
        'date_start_end_search': 'input[name="date_start_end_search"]',
        'date_end_end_search': 'input[name="date_end_end_search"]',
        'date_start_search': 'input[name="date_start_search"]',
        'date_end_search': 'input[name="date_end_search"]',
        'procedure_tasks_search': 'input[name="procedure_tasks_search"]',
        'category_tasks_search_search': 'input[name="category_tasks_search_search"]',
        'type_tasks_search': 'select[name="type_tasks_search"]',
        'list_departments': 'input[name="list_departments"]',
        'room_task': 'input[name="room_task"]',
        'list_staff_create': 'input[name="list_staff_create"]'
    };

    var _table_api = '';


    _table_api = tnhInitDataTable('.table-tasks_reports',
        '<?= site_url('admin/reports_tasks/getSyntheticTasks') ?>', {
            'order': [
                [1, 'desc']
            ],
            scrollX: true,
            "ajax": {
                "url": '<?= site_url('admin/reports_tasks/getSyntheticTasks') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
            "createdRow": function(row, data, index) {},
            "columnDefs": [{
                "targets": 0,
                "name": 'id',
                'orderable': false,
                'width': '40px'
            }],
        });


    $(document).on('change',
        '#category_search,#products_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function exportExcel() {
        category_search = $('#category_search').val();
        products_search = $('#products_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/reports_warehouse/exportExcelWarehouseProduct',
            data: {
                csrf_token_name: hash,
                category_search: category_search,
                products_search: products_search,
                export_excel: 1,
            },
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }
    var id_background;
    $('.table-tasks_reports').on('draw.dt', function() {
        var expenseReportsTable = $(this).DataTable();
        var total = expenseReportsTable.ajax.json().total;
        $.each(total, function(i, v) {
            $('.filter_' + i).html('(' + tnhFormatNumber(v) + ')');
        })
        $('.rows-child-all').trigger('click');
        $('.rows-child-all.fa-caret-right').trigger('click');
        $('.rows-child.fa-caret-right').trigger('click');

        if (id_background) {
            var idShow = id_background;
            id_background = null;
            $('.tr_' + idShow).addClass('bg-danger');
            setTimeout(function() {
                $('.tr_' + idShow).removeClass('bg-danger');
            }, 2000)
        }
    });


    $('.table-tasks_reports tbody').on('click', 'td .rows-child', function() {
        var tr = $(this).closest('tr');
        var row = _table_api.row(tr);
        if (row.child.isShown()) {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            $(this).removeClass('fa-caret-right');
            $(this).addClass('fa-caret-down');
            row.child(loadItemsTasks(row.data())).show();
            tr.addClass('shown');
        }
    });

    $('.table-tasks_reports thead').on('click', '.rows-child-all', function() {
        if ($(this).hasClass('fa-caret-right')) {
            $(this).addClass('fa-caret-down');
            $(this).removeClass('fa-caret-right');
            var rows = $('td .rows-child');
            $.each(rows, function(index, value) {
                var tr = $(value).parents('tr');
                var row = _table_api.row(tr);
                $(value).removeClass('fa-caret-right');
                $(value).addClass('fa-caret-down');
                row.child(loadItemsTasks(row.data())).show();
                tr.addClass('shown');
            })
        } else {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            var rows = $('td .rows-child');
            $.each(rows, function(index, value) {
                var tr = $(value).parents('tr');
                var row = _table_api.row(tr);
                $(value).removeClass('fa-caret-down');
                $(value).addClass('fa-caret-right');
                row.child.hide();
                tr.removeClass('shown');
            })
        }

    });
    $('#category_recommended_id').change(function() {
        var category_recommended_id = $(this).val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['category_recommended_id'] = category_recommended_id;

        $.post(admin_url + 'internal_proposal/getSuggestByRecommendedSingle', data, function(data) {
            data = JSON.parse(data);
            $('#suggest_id').html(`<option></option>`);
            $.each(data, function(index, value) {
                $('#suggest_id').append(`<option data-subtext="${value.staff_suggest_name}" value="${value.id}">${value.reference_no}</option>`);
            })
            $('#suggest_id').selectpicker('refresh');
        });
    });

    function loadItemsTasks(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        cHtml = cData[14];
        return `<div>${cHtml}</div>`;
    }
    $('body').on('change', `input[name="filterStatus"],
        input[name="list_staff"],
        select[name="suggest_id"],
        select[name="staff_follower"],
        select[name="category_recommended_id"],
        input[name="date_start_end_search"],
        input[name="date_end_end_search"],
        input[name="date_start_search"],
        input[name="date_end_search"],
        input[name="procedure_tasks_search"],
        input[name="category_tasks_search_search"],
        select[name="type_tasks_search"],
        input[name="list_departments"],
        input[name="room_task"],
        input[name="list_staff_create"]`, function() {
        if (_table_api) {
            _table_api.draw('page');
        }
    })

    $('#staff_task').change(function() {
        var staff_task = $(this).val();
        staff_task = staff_task.toString();
        $('input[name="list_staff"]').val(staff_task).trigger('change');
    })

    $('#staff_task_create').change(function() {
        var staff_task_create = $(this).val();
        staff_task_create = staff_task_create.toString();
        $('input[name="list_staff_create"]').val(staff_task_create).trigger('change');
    })

    $('#staff_follower').change(function() {
        var staff_follower = $(this).val();
        staff_follower = staff_follower.toString();
        $('input[name="staff_follower_search"]').val(staff_follower).trigger('change');
    })
    $('#room_task').change(function() {
        var departments_task = $(this).val();
        departments_task = departments_task.toString();
        $('input[name="list_departments"]').val(departments_task).trigger('change');
    })

    $('#date_start').change(function() {
        $('input[name="date_start_search"]').val($(this).val()).trigger('change');
    })

    $('#date_end').change(function() {
        $('input[name="date_end_search"]').val($(this).val()).trigger('change');
    })
    $('#date_start_end').change(function() {
        $('input[name="date_start_end_search"]').val($(this).val()).trigger('change');
    })

    $('#date_end_end').change(function() {
        $('input[name="date_end_end_search"]').val($(this).val()).trigger('change');
    })
    $('#procedure_tasks').change(function() {
        $('input[name="procedure_tasks_search"]').val($(this).val()).trigger('change');
    })
    $('#room_task').change(function() {
        var departments_task = $(this).val();
        departments_task = departments_task.toString();
        $('input[name="list_departments"]').val(departments_task).trigger('change');
    })

    $('#date_start').change(function() {
        $('input[name="date_start_search"]').val($(this).val()).trigger('change');
    })

    $('#date_end').change(function() {
        $('input[name="date_end_search"]').val($(this).val()).trigger('change');
    })
    $('#date_start_end').change(function() {
        $('input[name="date_start_end_search"]').val($(this).val()).trigger('change');
    })

    $('#date_end_end').change(function() {
        $('input[name="date_end_end_search"]').val($(this).val()).trigger('change');
    })
    $('#procedure_tasks').change(function() {
        $('input[name="procedure_tasks_search"]').val($(this).val()).trigger('change');
    })
    $('#category_tasks_search').change(function() {
        $('input[name="category_tasks_search_search"]').val($(this).val()).trigger('change');
        var category_tasks_search = $(this).val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['category_tasks_search'] = category_tasks_search;

        $.post(admin_url + 'tasks/getProcedureTasks', data, function(data) {
            data = JSON.parse(data);
            $('#procedure_tasks').html(`<option></option>`);
            $.each(data, function(index, value) {
                $('#procedure_tasks').append(`<option value="${value.id}">${value.name}</option>`);
            })
            $('#procedure_tasks').selectpicker('refresh');
        });
        $('#procedure_tasks').trigger('change');
    })
</script>