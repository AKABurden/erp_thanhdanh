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
                                        <?= lang('start_date', 'start_date_search') ?>
                                        <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('end_date', 'end_date_search') ?>
                                        <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-moderation_purchase_material" class="table dt-tnh table-moderation_purchase_material-new" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" rowspan="2"><?= lang('STT') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Ngày Lập Phiếu') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Mã Phiếu Yêu Cầu Mua NPL') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Mã Lệnh SX') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Mã Đơn Hàng') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Tên NCC') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Mã NPL') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Tên NPL') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Tên Nhóm NPL') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Tên Mã Chủng Loại SP') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Tổng Số Lượng SX') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Số Lượng Tồn Kho ') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Số Lượng Cần SX') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Số Lượng Tồn Cho Phép') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Số Lượng Cần Mua') ?></th>
                                            <th class="text-center" colspan="2"><?= lang('Kích Thước NPL') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Độ Dày NPL') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Tổng Chiều Cao') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Đơn Giá') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Thuế VAT') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Thành Tiền') ?></th>
                                            <th class="text-center" rowspan="2"><?= lang('Hình Ảnh SP') ?></th>
                                            <?php foreach (getListColumTable() as $key => $value){ ?>
                                                <th class="text-center" rowspan="2" style="min-width: 100px"><?= lang($value['name']) ?></th>
                                            <?php } ?>
                                        </tr>
                                        <tr>
                                            <th class="text-center"><?= lang('Height') ?></th>
                                            <th class="text-center"><?= lang('Width') ?></th>
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
    oTable = tnhInitDataTable('#table-moderation_purchase_material',
        '<?= site_url('admin/moderation_purchase_material/getModerationPurchaseMaterial') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/moderation_purchase_material/getModerationPurchaseMaterial') ?>',
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

    $('#table-moderation_purchase_material').on('draw.dt', function(event) {
        init_datepicker();
    });

    function updateDate(_this, id, name) {
        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['_value'] = $(_this).val();
        dataPOST['id'] = id;
        dataPOST['name'] = name;
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/moderation_purchase_material/updateDate',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                if (response.result == 1) {
                    alert_float('success', response.message);
                    oTable.draw('page');
                } else if (response.result == 0) {
                    alert_float('danger', response.message);
                    oTable.draw('page');
                }
            }
        });
    }
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
            url: site.base_url + 'admin/purchase_request_material/agree',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }
                $(".click1")[0].click();
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
            url: site.base_url + 'admin/moderation_purchase_material/exportExcel',
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