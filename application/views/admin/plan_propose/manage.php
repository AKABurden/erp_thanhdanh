<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" href="<?= base_url('assets/treegrid/') ?>css/jquery.treegrid.css">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">

<style>
    .nav-tabs>li.active>a,
    .nav-tabs>li.active>a:focus,
    .nav-tabs>li.active>a:hover,
    .nav-tabs>li>a:focus,
    .nav-tabs>li>a:hover {
        border: unset;
        border-radius: 0;
        border-bottom: 0px solid #02a9f4;
        background: 0 0;
        color: #008ece;
        margin-bottom: 0px;
    }

    #data-table img {
        height: 20px;
        width: 20px;
    }

    .btn-dt-reload {
        min-height: 32px;
    }

    .max-400 table {
        width: 100% !important;
    }

    li.active .btn-search {
        outline: 0;
        outline-offset: 0;
        -webkit-box-shadow: none;
        box-shadow: none;
        background: red;
    }

    .nav-tabs>li.active>a,
    .nav-tabs>li.active>a:focus,
    .nav-tabs>li.active>a:hover,
    .navbar-pills.nav-tabs>li>a:focus,
    .navbar-pills.nav-tabs>li>a:hover {
        border-bottom: unset;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
                <div class="line-sp"></div>
                <a class="btn btn-info mright5 test pull-right H_action_button c_modal" href="<?= admin_url('plan_propose/detail' . (!empty($type) ? ('?type=' . $type) : '')) ?>">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('create_add_new'); ?>
                </a>
                <div class="line-sp"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- data table -->
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <?php echo render_select('staff_search', (!empty($staff) ? $staff : []), ['staffid', ['firstname', 'lastname']], 'Nhân viên tạo kế hoạch') ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_select('category_tasks_search', (!empty($category_tasks) ? $category_tasks : []), ['id', 'code', 'content'], 'Mã công việc') ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_date_input('date_start', 'Từ ngày') ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_date_input('date_end', 'Đến ngày', date('d/m/Y')) ?>
                            </div>
                        </div>

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
                                        <li>
                                            <a class="H_filter" data-id="1">
                                                Chưa hoàn thành <b class="filter_0"></b>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="H_filter" data-id="2">
                                                Đã hoàn thành <b class="filter_1"></b>
                                            </a>
                                        </li>
                                        <!-- <li>
                                            <a class="H_filter" data-id="3">
                                                Không Duyệt <b class="filter_2"></b>
                                            </a>
                                        </li> -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-left: -15px">
                            <!--                            <input type="hidden" id="filterStatus" value=""/>-->
                            <input type="hidden" name="groups_search" id="groups_search" value="<?= !empty($group) ? $group : '' ?>" />

                        </div>
                        <input type="hidden" name="filterStatus" id="filterStatus" value="">
                        <input type="hidden" name="type_propose" id="type_propose" value="<?= !empty($type) ? $type : '' ?>">
                        <table id="data-table" class="table-plan_propose table dt-tnh table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center">
                                        <div class="text-center" style="width: 30px;"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child-all fa fa-caret-right"></a></div>
                                    </th>
                                    <th class="text-center"><?= lang('Ngày kế hoạch') ?></th>
                                    <th class="text-center"><?= lang('Mã kế hoạch') ?></th>
                                    <th class="text-center"><?= lang('Khách hàng/NCC') ?></th>
                                    <th class="text-center"><?= lang('Phiếu ĐXNB') ?></th>
                                    <th class="text-center"><?= lang('Phiếu PO') ?></th>

                                    <th class="text-center"><?= lang('Loại kế hoạch') ?></th>
                                    <th class="text-center"><?= lang('Phiếu yêu cầu') ?></th>
                                    <th class="text-center"><?= lang('Chi nhánh') ?></th>
                                    <th class="text-center"><?= lang('Người tạo kế hoạch') ?></th>
                                    <th class="text-center"><?= lang('Mã công việc') ?></th>
                                    <th class="text-center"><?= lang('Người duyệt') ?></th>
                                    <th class="text-center"><?= lang('Ngân sách') ?></th>
                                    <th class="text-center"><?= lang('tnh_ht') ?></th>
                                    <th class="text-center"><?= lang('intpro_content') ?></th>
                                    <th class="text-center"><?= lang('Công việc') ?></th>
                                    <th class="text-center"><?= lang('action') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="99"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal"></div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script>
    var costs = <?= json_encode($costs) ?>;
    var units = <?= json_encode($units) ?>;
    var units_cost = <?= json_encode($units_cost) ?>;

    init_editor('textarea[name="content"]');

    $('body').on('click', '.H_filter', function(e) {
        $('.H_filter').parent('li').removeClass('active');
        $(this).parent('li').addClass('active');
        $('input[name="filterStatus"]').val($(this).attr('data-id')).trigger('change');
        // tAPI.draw('page');
    });
    $('body').on('click', '.btn-search', function(e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-value');
        console.log(value)
        $('input[name="groups_search"]').val(value);
        $('input[name="groups_search"]').change();
    });
    var oTable;
    $(function() {
        var fnserverparams = {
            'filterStatus': '[name="filterStatus"]',
            'staff_search': '[name="staff_search"]',
            'category_tasks': '[name="category_tasks_search"]',
            'date_start': '[name="date_start"]',
            'date_end': '[name="date_end"]',
            'groups_search': '[name="groups_search"]',
        };


        oTable = initDataTable('.table-plan_propose', admin_url + 'plan_propose/table', [0], [0], fnserverparams, []);

        $('body').on('change', 'input[name="filterStatus"],input[name="groups_search"], select[name="staff_search"], select[name="category_tasks_search"], input[name="date_start"], input[name="date_end"]', function() {
            if (oTable) {
                oTable.draw('page');
                $('.rows-child-all').trigger('click');
            }
        })
    });


    $('.table-plan_propose tbody').on('click', 'td .rows-child', function() {
        var tr = $(this).closest('tr');
        var row = oTable.row(tr);
        if (row.child.isShown()) {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            $(this).removeClass('fa-caret-right');
            $(this).addClass('fa-caret-down');
            row.child(loadItemsplan_propose(row.data())).show();
            tr.addClass('shown');
        }
    });
    $('.table-plan_propose thead').on('click', '.rows-child-all', function() {
        if ($(this).hasClass('fa-caret-right')) {
            $(this).addClass('fa-caret-down');
            $(this).removeClass('fa-caret-right');
            var rows = $('td .rows-child');
            $.each(rows, function(index, value) {
                var tr = $(value).parents('tr');
                var row = oTable.row(tr);
                $(value).removeClass('fa-caret-right');
                $(value).addClass('fa-caret-down');
                row.child(loadItemsplan_propose(row.data())).show();
                tr.addClass('shown');
            })
        } else {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            var rows = $('td .rows-child');
            $.each(rows, function(index, value) {
                var tr = $(value).parents('tr');
                var row = oTable.row(tr);
                $(value).removeClass('fa-caret-down');
                $(value).addClass('fa-caret-right');
                row.child.hide();
                tr.removeClass('shown');
            })
        }

    });

    function loadItemsplan_propose(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        cHtml = cData[13];
        return `<div>${cHtml}</div>`;
    }
    $('.table-plan_propose').on('draw.dt', function() {
        var expenseReportsTable = $(this).DataTable();
        var total = expenseReportsTable.ajax.json().total;
        var numTotal = 0;
        $.each(total, function(i, v) {
            $('.filter_' + i).html('(' + tnhFormatNumber(v) + ')');
            numTotal += (v * 1)
        })
        $('.filter_all').html('(' + tnhFormatNumber(numTotal) + ')');
        $('.rows-child-all').trigger('click');
    });




    function deleting(id) {
        var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
        if (r == false) {
            return false;
        } else {
            $.get(admin_url + 'plan_propose/delete/' + id, function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    oTable.draw();
                } else {
                    alert_float('danger', response.message);
                }
            }, 'json');
        }
        return false;
    };

    $('body').on('click', '#agree', function() {
        var id = $(this).data('id');
        $.get(admin_url + 'plan_propose/approve/' + id, function(response) {
            $('.popover').closest('div.popover').popover('hide');
            if (response.success) {
                alert_float('success', response.message);
                if (response.id_task) {
                    init_task_modal(response.id_task);
                }
                oTable.draw();
            } else {
                alert_float('danger', response.message);
            }
        }, 'json');
        return false;
    })

    $('body').on('click', '#not_agree', function() {
        var id = $(this).data('id');
        var status = $(this).data('status');
        var reason = $(this).parents('.not_agree').find('.reason').val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        data['status'] = status;
        data['reason'] = reason;

        $.post(admin_url + 'plan_propose/not_approve', data, function(response) {
            $('.popover').closest('div.popover').popover('hide');
            if (response.success) {
                alert_float('success', response.message);
                if (response.id_task) {
                    init_task_modal(response.id_task);
                }
                oTable.draw();
            } else {
                alert_float('danger', response.message);
            }
        }, 'json');
        return false;
    })
    $(document).on('click', '.po-close', function() {
        $('.popover').closest('div.popover').popover('hide');
        return false;
    });

    $('body').on('click', '.c_modal_tasks', function() {
        var url = $(this).attr('href');
        $.get(url, function(result) {
            $('.modal-backdrop.in').remove();
            $("#_task").html(result);
            $("body").find("#_task_modal").modal({
                show: !0,
                backdrop: "static"
            })

        }).error(function(response) {
            alert_float('danger', response.responseText);
        });
        return false;
    })
    $('#task-modal').on('hidden.bs.modal', function() {
        oTable.draw();
    });
    $(document).on('change', '#type_plan_propose', function(event) {
        event.preventDefault();
        type_plan_propose = $('#type_plan_propose').val();
        id_plan_propose = $('#id_plan_propose').val();
        type_plan_propose_old = type_plan_propose;

        $('.loai').addClass('hide');

        $('.infodetail_time').addClass('hide');
        $('.infodetail_items').addClass('hide');
        if (type_plan_propose) {
            if (type_plan_propose == 'machining') {
                return false;
            }
            if (type_plan_propose == 'system') {
                return false;
            }
            if (type_plan_propose == 'train') {
                $('.type_plan_propose_train').removeClass('hide');
                $('.infodetail_time').removeClass('hide');
                $('.infodetail_items').removeClass('hide');
            }
            if (type_plan_propose == 'repair') {
                $('.type_plan_propose_repair').removeClass('hide');
                $('.infodetail_time').removeClass('hide');
                $('.infodetail_items').removeClass('hide');
            }
            if (type_plan_propose == 'quality' || type_plan_propose == 'calibration' || type_plan_propose == 'replace' || type_plan_propose == 'check') {
                type_plan_propose = 'repair';
                $('.type_plan_propose_repair').removeClass('hide');
                $('.infodetail_time').removeClass('hide');
                $('.infodetail_items').removeClass('hide');
            }
            if (type_plan_propose == 'npl' || type_plan_propose == 'tools' || type_plan_propose == 'sanxuat') {
                type_plan_propose = 'items';
                $('.infodetail_items').removeClass('hide');
                $('.type_plan_propose_items').removeClass('hide');
            }
            if (type_plan_propose == 'pay_slip' || type_plan_propose == 'vouchers_coupon') {
                type_plan_propose = 'payment';
                $('.infodetail_items').removeClass('hide');
            }
            if (type_plan_propose == 'recruit') {
                $('.infodetail_items').removeClass('hide');
                $('.type_plan_propose_recruit').removeClass('hide');
            }

            $.ajax({
                    url: site.base_url + 'admin/plan_propose/get_plan_propose',
                    type: 'GET',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        type_plan_propose: type_plan_propose,
                        type_plan_propose_old: type_plan_propose_old,
                        id: id_plan_propose
                    },
                })
                .done(function(data) {
                    $('#detail_items').html(data);
                    init_selectpicker();
                    init_datepicker();
                })
                .fail(function(data) {
                    alert_float('danger', 'errors');
                })
            $.ajax({
                    url: site.base_url + 'admin/plan_propose/get_plan_propose_time',
                    type: 'GET',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        type_plan_propose: type_plan_propose,
                        type_plan_propose_old: type_plan_propose_old,
                        id: id_plan_propose
                    },
                })
                .done(function(data) {
                    $('#detail_time').html(data);
                    init_selectpicker();
                    init_datepicker();
                })
                .fail(function(data) {
                    alert_float('danger', 'errors');
                })

        }
    });
</script>