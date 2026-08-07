<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    #table-moderation-task tr th:nth-child(6) {
        width: 50px;
    }
    #table-moderation-task tr th:nth-child(2) {
        width: 90px;
    }
    #table-moderation-task tr th:nth-child(3) {
        width: 60px;
    }
    #table-moderation-task tr th:nth-child(4) {
        width: 60px;
    }
    #table-moderation-task tr th:nth-child(10) {
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
                                        <?= lang('Thiết bị', 'machines_search') ?>
                                        <input type="text" name="machines_search" id="machines_search" class="machines_search"
                                               data-placeholder="<?= lang('Thiết bị') ?>" style="width: 100%;"
                                               value=""
                                               title="">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('start_date', 'start_date_search') ?>
                                        <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>"
                                               id="start_date_search" class="start_date_search datepicker form-control"
                                               style="width: 100%;" value="<?= date('d/m/Y') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('end_date', 'end_date_search') ?>
                                        <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>"
                                               id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;"
                                               value="<?= date('d/m/Y') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-machine-productivity" class="table dt-tnh table-machine-productivity-new" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Mã Thiết Bị Máy Móc') ?></th>
                                        <th class="text-center"><?= lang('Tên Thiết Bị Máy Móc') ?></th>
                                        <th class="text-center"><?= lang('Tổng Số Lệnh') ?></th>
                                        <th class="text-center"><?= lang('Định Mức Trên Máy') ?></th>
                                        <th class="text-center"><?= lang('Định Mức Thực Tế') ?></th>
                                        <th class="text-center"><?= lang('Kết Quả') ?></th>
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
    ajaxSelectParams('#machines_search', 'admin/suggest_repalce/searchMachines', $("#machines_search").val(), true, true);
    var oTable = '';

    var fnserverparams = {
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
        machines_search: '#machines_search',
    };
    oTable = tnhInitDataTable('#table-machine-productivity',
        '<?= site_url('admin/machine_productivity/getMachineProductivity') ?>', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            scrollX:true,
            "ajax": {
                "url": '<?= site_url('admin/machine_productivity/getMachineProductivity') ?>',
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
            "createdRow": function(row, data, index) {
            },
            "columnDefs": [
            ],
        });

    $(document).on('change',
        '#machines_search,#start_date_search,#end_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function exportExcel() {
        machines_search = $('#machines_search').val();
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/machine_productivity/exportExcel',
            data: {
                csrf_token_name: hash,
                machines_search: machines_search,
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