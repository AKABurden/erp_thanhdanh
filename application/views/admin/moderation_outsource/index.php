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
                                <table id="table-moderation-outsource" class="table dt-tnh table-moderation-outsource-new" style="width:2200px;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Mã Phiếu') ?></th>
                                        <th class="text-center"><?= lang('Ngày Lập') ?></th>
                                        <th class="text-center"><?= lang('Mã ĐĐH (TD)') ?></th>
                                        <th class="text-center"><?= lang('Mã Lệnh Sản Xuất') ?></th>
                                        <th class="text-center"><?= lang('Công Đoạn') ?></th>
                                        <th class="text-center"><?= lang('Mã SP') ?></th>
                                        <th class="text-center"><?= lang('Tên SP') ?></th>
                                        <th class="text-center"><?= lang('Tổng SL') ?></th>
                                        <th class="text-center"><?= lang('Thời Gian Dự Kiến') ?></th>
                                        <th class="text-center"><?= lang('Thời Gian Bắt Đầu') ?></th>
                                        <th class="text-center"><?= lang('Thời Gian Kết Thúc') ?></th>
                                        <th class="text-center"><?= lang('Nhóm Gia Công') ?></th>
                                        <th class="text-center"><?= lang('Đơn Vị Gia Công (NCC)') ?></th>
                                        <th class="text-center"><?= lang('Đơn Giá Gia Công') ?></th>
                                        <th class="text-center"><?= lang('Thuế Vat') ?></th>
                                        <th class="text-center"><?= lang('Thành Tiền') ?></th>
                                        <th class="text-center"><?= lang('Đơn Vị Vận Chuyển Gia Công') ?></th>
                                        <th class="text-center"><?= lang('Phương Tiện Vận Chuyển Gia Công') ?></th>
                                        <th class="text-center"><?= lang('Đơn Giá Vận Chuyển') ?></th>
                                        <th class="text-center"><?= lang('Thành Tiền') ?></th>
                                        <th class="text-center"><?= lang('Kết Quả') ?></th>
                                        <th class="text-center"><?= lang('Phiếu Báo Cáo Không Phù Hợp') ?></th>
                                        <th class="text-center"><?= lang('Người Lập Kế Hoạch') ?></th>
                                        <th class="text-center"><?= lang('Nhân Viên Điều Độ Gia Công') ?></th>
                                        <th class="text-center"><?= lang('Định Mức Thời Gian') ?></th>
                                        <th class="text-center"><?= lang('Thời Gian Dự Kiến') ?></th>
                                        <th class="text-center"><?= lang('Thời Gian Bắt Đầu') ?></th>
                                        <th class="text-center"><?= lang('Thời Gian Kết Thúc') ?></th>
                                        <th class="text-center"><?= lang('Tiêu Chuẩn/ Quy Định') ?></th>
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
    oTable = tnhInitDataTable('#table-moderation-outsource',
        '<?= site_url('admin/moderation_outsource/getModerationOutsources') ?>', {
            'order': [
                [0, 'desc']
            ],
            scrollX:true,
            "ajax": {
                "url": '<?= site_url('admin/moderation_outsource/getModerationOutsources') ?>',
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

    $('#table-moderation-outsource').on('draw.dt', function(event) {
        init_datepicker();
    });

    $(document).on('change',
        '#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function updateModeration(_this,suggest_outsource_id,suggest_outsource_item_id,name){
        var dataPOST = {};
        dataPOST[csrf_token_name] = hash;
        dataPOST['suggest_outsource_id'] = suggest_outsource_id;
        dataPOST['suggest_outsource_item_id'] = suggest_outsource_item_id;
        dataPOST['value'] = $(_this).val();
        dataPOST['name'] = name;

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/moderation_outsource/updateModeration',
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
            url: site.base_url + 'admin/moderation_outsource/exportExcel',
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