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
                                        <input type="hidden" name="type" id="type" value="<?= $type ?>">
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
                            <div class="table-responsive">
                                <table id="table-moderation-maintenance" class="table dt-tnh table-moderation-maintenance-new" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Mã Số Phiếu') ?></th>
                                        <th class="text-center"><?= lang('Ngày Bảo Dưỡng') ?></th>
                                        <th class="text-center"><?= lang('Loại Bảo Dưỡng') ?></th>
                                        <th class="text-center"><?= lang('Nhóm Bảo Dưỡng') ?></th>
                                        <th class="text-center"><?= lang('Bộ Phận Thiết Bị') ?></th>
                                        <th class="text-center"><?= lang('Khu Vực Bảo Dưỡng') ?></th>
                                        <th class="text-center"><?= lang('Chi Tiết Bảo Dưỡng') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng') ?></th>
                                        <th class="text-center"><?= lang('Nhóm Thiết Bị') ?></th>
                                        <th class="text-center"><?= lang('Mã Thiết Bị') ?></th>
                                        <th class="text-center"><?= lang('Tên Thiết Bị') ?></th>
                                        <th class="text-center"><?= lang('Chi Nhánh') ?></th>
                                        <th class="text-center"><?= lang('Kết Quả') ?></th>
                                        <th class="text-center"><?= lang('Tiêu Chuẩn/ Quy Định') ?></th>
                                        <?php foreach (getListColumTable() as $key => $value){ ?>
                                            <th class="text-center" style="min-width: 100px"><?= lang($value['name']) ?></th>
                                        <?php } ?>
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
        type: '#type',
    };
    oTable = tnhInitDataTable('#table-moderation-maintenance',
        '<?= site_url('admin/moderation/getModerationMaintenance') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            scrollX:true,
            "ajax": {
                "url": '<?= site_url('admin/moderation/getModerationMaintenance') ?>',
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
        '#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });


    function exportExcel() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();
        type = $('#type').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/moderation/exportExcelModerationMaintenance',
            data: {
                csrf_token_name: hash,
                start_date_search: start_date_search,
                end_date_search: end_date_search,
                type: type,
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