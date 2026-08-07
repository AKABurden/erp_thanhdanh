<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    #table-suggest-task tr th:nth-child(6) {
        width: 50px;
    }

    #table-suggest-task tr th:nth-child(2) {
        width: 90px;
    }

    #table-suggest-task tr th:nth-child(3) {
        width: 60px;
    }

    #table-suggest-task tr th:nth-child(4) {
        width: 60px;
    }

    #table-suggest-task tr th:nth-child(10) {
        width: 90px;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>
                <?php if ($this->preAddSuggestTask): ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/suggest_task/detail') ?>" class=" tnh-modal btn btn-info H_action_button">
                            <?php echo _l('add'); ?>
                        </a>
                    </div>
                <?php endif ?>
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
                            <div class="row" style="margin-bottom:5px">
                                <div id="search-tnh" class="collapse in" aria-expanded="true">
                                    <div class="col-md-3">
                                        <?= lang('start_date', 'start_date_search') ?>
                                        <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>"
                                            id="start_date_search" class="start_date_search datepicker form-control"
                                            style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('end_date', 'end_date_search') ?>
                                        <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>"
                                            id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;"
                                            value="">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-suggest-task" class="table dt-tnh table-suggest-task-new" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('STT') ?></th>
                                            <th class="text-center"><?= lang('Số Phiếu Công Việc') ?></th>
                                            <th class="text-center"><?= lang('Ngày Tạo') ?></th>
                                            <th class="text-center"><?= lang('Mã công việc') ?></th>
                                            <th class="text-center"><?= lang('Mã Vị Trí') ?></th>
                                            <th class="text-center"><?= lang('Phòng ban') ?></th>
                                            <th class="text-center"><?= lang('Chi Tiết Công Việc') ?></th>
                                            <th class="text-center"><?= lang('Quy Trình') ?></th>
                                            <th class="text-center"><?= lang('Quy Định') ?></th>
                                            <th class="text-center"><?= lang('Ngày Bắt Đầu') ?></th>
                                            <th class="text-center"><?= lang('Ngày Hoàn Thành') ?></th>
                                            <th class="text-center"><?= lang('Hạn Chót') ?></th>
                                            <th class="text-center"><?= lang('Người Giao Việc') ?></th>
                                            <th class="text-center"><?= lang('Người Được Phân Công') ?></th>
                                            <th class="text-center"><?= lang('Mức Độ Ưu Tiên') ?></th>
                                            <th class="text-center"><?= lang('Trạng Thái') ?></th>
                                            <th class="text-center"><?= lang('Kết Quả') ?></th>
                                            <th class="text-center"><?= lang('Công việc') ?></th>
                                            <th class="text-center"><?= lang('Báo Cáo Sự Cố') ?></th>
                                            <th class="text-center"><?= lang('actions') ?></th>
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
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    var oTable = '';

    var fnserverparams = {
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
    };
    oTable = tnhInitDataTable('#table-suggest-task',
        '<?= site_url('admin/suggest_task/getSuggestTasks') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/suggest_task/getSuggestTasks') ?>',
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
            "columnDefs": [],
        });

    $('#table-suggest-task').on('draw.dt', function() {
        var total_tr = $('#table-suggest-task').find('tbody').find('tr');
        $.each(total_tr, function(i, v) {
            $("#priority_" + i).select2({});
            $("#status_" + i).select2({});
            $("#result_id_" + i).select2({});
        });
    });

    $(document).on('change', '.priority', function(e) {
        var id = $(this).attr('data-id');
        var priority = $(this).val();
        var athis = $(this);
        var data = {};
        data['id'] = id;
        data['priority'] = priority;
        data['type'] = 'priority';
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url + 'suggest_task/updateTicket', data).done(function(response) {
            response = JSON.parse(response);
            if (response.result == 0) {
                athis.select2('val', '');
                alert_float('danger', response.message);
            } else {
                alert_float('success', response.message);
            }
        });
    });
    $(document).on('change', '.status', function(e) {
        var id = $(this).attr('data-id');
        var status = $(this).val();
        var athis = $(this);
        var data = {};
        data['id'] = id;
        data['status'] = status;
        data['type'] = 'status';
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url + 'suggest_task/updateTicket', data).done(function(response) {
            response = JSON.parse(response);
            if (response.result == 0) {
                athis.select2('val', '');
                alert_float('danger', response.message);
            } else {
                alert_float('success', response.message);
            }
        });
    });
    $(document).on('change', '#staff_id', function(e) {
        var staff_id = $(this).find('option:selected').attr('data-role');
        ajaxSelectParams('#role_id', 'admin/suggest_task/searchRoles', staff_id, true, true);

    });
    $(document).on('change', '.result_id', function(e) {
        var id = $(this).attr('data-id');
        var result_id = $(this).val();
        var athis = $(this);
        var data = {};
        data['id'] = id;
        data['result_id'] = result_id;
        data['type'] = 'result';
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url + 'suggest_task/updateTicket', data).done(function(response) {
            response = JSON.parse(response);
            if (response.result == 0) {
                athis.select2('val', '');
                alert_float('danger', response.message);
            } else {
                alert_float('success', response.message);
            }
        });
    });
    $(document).on('change',
        '#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function agree(_this, suggest_id, status) {
        var dataPOST = {};
        dataPOST[csrf_token_name] = hash;
        dataPOST['suggest_id'] = suggest_id;
        dataPOST['status'] = status;

        $(_this).attr('disabled', 'disabled');
        $('.po').popover('hide');

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/suggest_task/agree',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }

                if (typeof oTable !== 'undefined') {
                    oTable.draw('page');
                }
            },
            error: function(xhr, status, error) {
                $(_this).removeAttr('disabled');
            },
        });

    }

    function exportExcel() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/suggest_task/exportExcel',
            data: {
                csrf_token_name: hash,
                start_date_search: start_date_search,
                end_date_search: end_date_search,
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
</script>