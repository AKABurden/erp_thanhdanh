<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-tasks tbody tr:nth-child(1) td:nth-child(5) .dropdown-menu-right {
        transform: translate3d(-29px, 241px, 0px)!important;
    }
    .table-tasks tbody tr:nth-child(2) td:nth-child(5) .dropdown-menu-right {
        transform: translate3d(-29px, 241px, 0px)!important;
    }
    .table-tasks tbody tr:nth-child(3) td:nth-child(5) .dropdown-menu-right {
        transform: translate3d(-29px, 241px, 0px)!important;
    }
    /*.table-tasks tbody tr:nth-child(1) td:nth-child(9) .dropdown-menu-right {*/
    /*    transform: translate3d(-29px, 133px, 0px)!important;*/
    /*}*/
    /*.table-tasks tbody tr:nth-child(2) td:nth-child(9) .dropdown-menu-right {*/
    /*    transform: translate3d(-29px, 133px, 0px)!important;*/
    /*}*/
    /*.table-tasks tbody tr:nth-child(3) td:nth-child(9) .dropdown-menu-right {*/
    /*    transform: translate3d(-29px, 133px, 0px)!important;*/
    /*}*/

    .table-tasks tbody tr td:nth-child(9) {
        min-width: 150px;
    }
    .lableMinus {
        font-size: 11px;
        padding-top: 2px;
        padding-bottom: 2px;
        margin-top: 5px;
        display: inline-grid;
    }
</style>

<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=3.3') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="pull-right mright5 H_border">
                <a href="<?php if (!$this->input->get('project_id')) {
					echo admin_url('tasks/switch_kanban/' . $switch_kanban);
				} else {
					echo admin_url('projects/view/' . $this->input->get('project_id') . '?group=project_tasks');
				}; ?>" class="btn btn-info pull-left H_action_button <?= $switch_kanban == 1 ? 'h_switch' : '' ?>">
					<?php if ($switch_kanban == 1) {
						echo _l('switch_to_list_view');
					} else {
						echo _l('leads_switch_to_kanban');
					}; ?>
                </a>
            </div>
			<?php if (has_permission('tasks', '', 'create')) { ?>
                <div class="pull-right mright5 H_border mleft5">
                    <a href="#" onclick="new_task(<?php if ($this->input->get('project_id')) {
						echo "'" . admin_url('tasks/task?rel_id=' . $this->input->get('project_id') . '&rel_type=project') . "'";
					} ?>); return false;" class="btn btn-info pull-left new H_action_button">
                        <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
						<?php echo _l('create_add_new'); ?>
                    </a>
                </div>
			<?php } ?>
			<?php if ($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
			<?php } else { ?>
                <div class="">
				    <?php $this->load->view('admin/tasks/tasks_filter_by', array('view_table_name' => '.table-tasks')); ?>
                    <?php if (is_admin()) { ?>
                        <a href="<?php echo admin_url('tasks/detailed_overview'); ?>" class="btn btn-success pull-right mright5"><?php echo _l('detailed_overview'); ?></a>
                    <?php } ?>
                </div>
			<?php } ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
			<?php if ($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
                <div class="col-md-3">
					<?php echo render_select('departments_task', !empty($departments) ? $departments : [], ['departmentid', 'name'], 'departments', '', ['multiple' => true, 'onchange' => 'tasks_kanban()', 'data-actions-box' => true],[], '', '', false)?>
                </div>
                <div class="col-md-3">
					<?php echo render_select('staff_task', !empty($staff) ? $staff : [], ['staffid', 'fullname'], 'Nhân viên', '', ['multiple' => true, 'onchange' => 'tasks_kanban()', 'data-actions-box' => true],[], '', '', false)?>
                </div>
                <div class="col-md-3">
                    <div class="mtop25" data-toggle="tooltip" data-placement="bottom" data-title="<?php echo _l('search_by_tags'); ?>">
                        <?php echo render_input('search', '', '', 'search', array('data-name' => 'search', 'onkeyup' => 'tasks_kanban();', 'placeholder' => _l('search_tasks')), array(), 'no-margin') ?>
                    </div>
                </div>
                <div class="clearfix"></div>
			<?php } ?>
            <div class="panel_s">
                <div class="panel-body">
					<?php
					if ($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
                        <div class="kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                            <div class="row">
                                <div id="kanban-params">
									<?php echo form_hidden('project_id', $this->input->get('project_id')); ?>
                                </div>
                                <div class="container-fluid">
                                    <div id="kan-ban"></div>
                                </div>
                            </div>
                        </div>
					<?php } else { ?>
<!--						--><?php //$this->load->view('admin/tasks/_summary', array('table' => '.table-tasks')); ?>
                        <div class="row">
                            <div class="col-md-2">
                                <?php echo render_select('departments_task', !empty($departments) ? $departments : [], ['departmentid', 'name'], 'departments', '', ['multiple' => true])?>
                            </div>
                            <div class="col-md-2">
								<?php echo render_select('staff_task_create', !empty($staff) ? $staff : [], ['staffid', 'fullname'], 'Người giao việc', '', ['multiple' => true, 'data-actions-box' => true],[],'','', false)?>
                            </div>
                            <div class="col-md-2">
                                <?php echo render_select('staff_task', !empty($staff) ? $staff : [], ['staffid', 'fullname'], 'Người được phân công', '', ['multiple' => true, 'data-actions-box' => true],[],'','', false)?>
                            </div>
                            <div class="col-md-2">
                                <?php echo render_date_input('date_start', 'Ngày bắt đầu')?>
                            </div>
                            <div class="col-md-2">
                                <?php echo render_date_input('date_end', 'Ngày kết thúc')?>
                            </div>
                        </div>
                        <a href="#" data-toggle="modal" data-target="#tasks_bulk_actions" class="hide bulk-actions-btn table-btn" data-table=".table-tasks"><?php echo _l('bulk_actions'); ?></a>
                        <div class="btn-group mbot10" style="width: 100%;">
                            <div class="horizontal-scrollable-tabs">
                                <div class="scroller scroller-left arrow-left disabled" style="display: block;">
                                    <i class="fa fa-angle-left"></i>
                                </div>
                                <div class="scroller scroller-right arrow-right" style="display: block;">
                                    <i class="fa fa-angle-right"></i>
                                </div>
                                <div class="horizontal-tabs">
                                    <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                        <li class="active">
                                            <a class="H_filter" data-id="">
												<?= _l('cong_all') ?> <b class="filter_all"></b>
                                            </a>
                                        </li>
										<?php if (!empty($task_statuses)) { ?>
											<?php foreach ($task_statuses as $key => $value) { ?>
                                                <li>
                                                    <a class="H_filter" data-id="<?= $value['id'] ?>">
														<?= $value['name'] ?> <b class="filter_<?= $value['id'] ?>"></b>
                                                    </a>
                                                </li>
											<?php } ?>
										<?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
						<?php $this->load->view('admin/tasks/_table', array('bulk_actions' => true)); ?>
						<?php $this->load->view('admin/tasks/_bulk_actions'); ?>
					<?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    taskid = '<?php echo $taskid; ?>';
    $(function () {
        tasks_kanban();
        if ($('.h_switch').length > 0) {
            $('.action-menu').trigger('click');
        }
    });
</script>
</body>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<!--<script type="text/javascript" src="--><?//= js('datatables/dataTables.fixedHeader.min.js') ?><!--"></script>-->
<script>
    $('body').on('click', '.H_filter', function (e) {
        $('.H_filter').parent('li').removeClass('active');
        $(this).parent('li').addClass('active');
        $('input[name="filterStatus"]').val($(this).attr('data-id')).trigger('change');
        // tAPI.draw('page');
    });
    $('body').on('change', 'input[name="filterStatus"], input[name="list_staff"], input[name="date_start_search"], input[name="date_end_search"], input[name="list_departments"], input[name="list_staff_create"]', function () {
        if (_table_api) {
            _table_api.draw('page');
        }
    })
    $('.table-tasks').on('draw.dt', function () {
        var expenseReportsTable = $(this).DataTable();
        var total = expenseReportsTable.ajax.json().total;
        $.each(total, function (i, v) {
            $('.filter_' + i).html('(' + tnhFormatNumber(v) + ')');
        })
        $('.rows-child-all').trigger('click');
    });


    $('.table-tasks tbody').on('click', 'td .rows-child', function() {
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
    $('.table-tasks thead').on('click', '.rows-child-all', function() {
        if($(this).hasClass('fa-caret-right')) {
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
        }
        else {
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
    function loadItemsTasks(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        cHtml = cData[13];
        return `<div>${cHtml}</div>`;
    }

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

    $('#departments_task').change(function() {
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
</script>
<script>
    // function status_checklist(id, status, _this) {
    //     $.get(admin_url + 'tasks/checkbox_action_list/' + id + '/' + status, function(result) {
    //         result = JSON.parse(result);
    //         if(result.success) {
    //             if(result.data.finished == 1) {
    //                 $(_this).attr('onclick', 'status_checklist('+id+', 0, this)');
    //                 $(_this).addClass('active');
    //                 $(_this).find('.active_poin').text("Được " + result.data.name_finished_from + ' hoàn thành');
    //             }
    //             else {
    //
    //                 $(_this).attr('onclick', 'status_checklist('+id+', 1, this)');
    //                 $(_this).removeClass('active');
    //                 $(_this).find('.active_poin').text('');
    //             }
    //         }
    //     })
    // }
</script>
</html>
