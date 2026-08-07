<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
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
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-infomation-machines" class="table dt-tnh table-infomation-machines-new" style="width:2200px;">
                                    <thead>
                                    <tr>
                                        <th class="text-center" rowspan="2"><?= lang('STT') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Mã Nhóm Thiết Bị') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Tên Nhóm Thiết Bị') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Mã Chủng Loại') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Tên Chủng Loại') ?></th>
                                        <th class="text-center" colspan="2"><?= lang('Kích Thước Máy') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Mã Thiết Bị') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Tên Thiết Bị') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Nhãn Hiệu/Xuất Xứ/Năm Sản Xuất') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Tiêu Chuẩn Hệ Thống/Thiết Bị') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Năng Suất Lần/Giờ') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Thời Gian Canh Bài') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('NPL Canh Bài') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('5S') ?></th>
                                        <th class="text-center" colspan="4"><?= lang('Danh Mục Bảo Dưỡng Hàng Ngày') ?></th>
                                        <th class="text-center" colspan="4"><?= lang('Danh Mục Vật Tư Thay Thế Định Kỳ') ?></th>
                                        <th class="text-center" colspan="4"><?= lang('Danh Mục Hiệu Chuẩn Định Kỳ') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Định Mức Thay Thế') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Nhà Cung Cấp') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Thời Gian Bảo Hành') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Khấu Hao Thiết Bị') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Thời Gian Khấu Hao') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Qui Trình Vận Hành') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Người Phụ Trách') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Số Lần Sự Cố') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('KPI') ?></th>
                                    </tr>
                                    <tr>
                                        <th class="text-center"><?= lang('Width( Ngang-Rộng)') ?></th>
                                        <th class="text-center"><?= lang('Heigh(Cao-Dài)') ?></th>
                                        <th class="text-center"><?= lang('Cơ') ?></th>
                                        <th class="text-center"><?= lang('Điện') ?></th>
                                        <th class="text-center"><?= lang('Hơi') ?></th>
                                        <th class="text-center"><?= lang('Khí Nén') ?></th>
                                        <th class="text-center"><?= lang('Cơ') ?></th>
                                        <th class="text-center"><?= lang('Điện') ?></th>
                                        <th class="text-center"><?= lang('Hơi') ?></th>
                                        <th class="text-center"><?= lang('Khí Nén') ?></th>
                                        <th class="text-center"><?= lang('Cơ') ?></th>
                                        <th class="text-center"><?= lang('Điện') ?></th>
                                        <th class="text-center"><?= lang('Hơi') ?></th>
                                        <th class="text-center"><?= lang('Khí Nén') ?></th>
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
    };
    oTable = tnhInitDataTable('#table-infomation-machines',
        '<?= site_url('admin/infomation_machines/getInfomationMachines') ?>', {
            'order': [
                [0, 'desc']
            ],
            scrollX:true,
            "ajax": {
                "url": '<?= site_url('admin/infomation_machines/getInfomationMachines') ?>',
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
                    init_datepicker();
                    return json.aaData;
                }
            },
            "createdRow": function(row, data, index) {
            },
            "columnDefs": [
            ],
        });

    $('#table-infomation-machines').on('draw.dt', function(event) {
        init_datepicker();
    });

    $(document).on('change',
        '#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function updateModeration(_this,suggest_overtime_id,suggest_overtime_item_id,name){
        var dataPOST = {};
        dataPOST[csrf_token_name] = hash;
        dataPOST['suggest_overtime_id'] = suggest_overtime_id;
        dataPOST['suggest_overtime_item_id'] = suggest_overtime_item_id;
        dataPOST['value'] = $(_this).val();
        dataPOST['name'] = name;

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/moderation_overtime/updateModeration',
            data: dataPOST,
            dataType: "json",
            success: function (response) {
                if (response.result) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }

                if (typeof oTable !== 'undefined') {
                    oTable.draw('page');
                }
            },
            error: function (xhr, status, error) {
                $(_this).removeAttr('disabled');
            },
        });
    }
    function exportExcel() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();
        type = $('#type').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/moderation_overtime/exportExcel',
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