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
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-machine-information" class="table dt-tnh table-machine-information-new" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center" rowspan="2"><?= lang('STT') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Ngày') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Mã Nhóm Thiết Bị') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Tên Nhóm Thiết Bị') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Mã Thiết Bị Máy Móc') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Tên Thiết Bị Máy Móc') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Model') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Xuất Xứ') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Năm Sản Xuất') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Thông Số Kỹ Thuật Chung') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Hiệu Suất') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Kỹ Thuật Ghi Bản') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Đặc Điểm Vật Lý') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Lắp Đặt Bởi (Nhà Cung Cấp)') ?></th>
                                        <th class="text-center" colspan="4"><?= lang('Đặc Tính') ?></th>
                                    </tr>
                                    <tr>
                                        <th class="text-center"><?= lang('Khổ Giấy (Max)') ?></th>
                                        <th class="text-center"><?= lang('Khổ Giấy (Min)') ?></th>
                                        <th class="text-center"><?= lang('Điện Áp') ?></th>
                                        <th class="text-center"><?= lang('Tốc Độ') ?></th>
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
    oTable = tnhInitDataTable('#table-machine-information',
        '<?= site_url('admin/machine_information/getMachineInfomation') ?>', {
            'order': [
                [0, 'asc']
            ],
            'fixedHeader': {
                header: true,
            },
            scrollX:true,
            "ajax": {
                "url": '<?= site_url('admin/machine_information/getMachineInfomation') ?>',
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
        '#machines_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function exportExcel() {
        machines_search = $('#machines_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/machine_information/exportExcel',
            data: {
                csrf_token_name: hash,
                machines_search: machines_search,
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