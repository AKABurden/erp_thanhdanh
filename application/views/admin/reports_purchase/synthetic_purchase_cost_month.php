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
                                <table id="table-synthetic_purchase_cost_month" class="table dt-tnh table-synthetic_purchase_cost_month-new" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Mã NCC') ?></th>
                                        <th class="text-center"><?= lang('Tên NCC') ?></th>
                                        <th class="text-center"><?= lang('Phiếu Yêu Cầu Mua Hàng') ?></th>
                                        <th class="text-center"><?= lang('Mã NPL') ?></th>
                                        <th class="text-center"><?= lang('Tên NPL') ?></th>
                                        <th class="text-center"><?= lang('Phiếu Đề Xuất Nội Bộ') ?></th>
                                        <th class="text-center"><?= lang('BCKPH-Vi Phạm') ?></th>
                                        <th class="text-center"><?= lang('Xác Nhận  Gấp Khẩn') ?></th>
                                        <th class="text-center"><?= lang('PO-Đơn Đặt Hàng') ?></th>
                                        <th class="text-center"><?= lang('Ngày Về Hàng') ?></th>
                                        <th class="text-center"><?= lang('Phiếu Giao Hàng') ?></th>
                                        <th class="text-center"><?= lang('Phiếu Nhập Kho') ?></th>
                                        <th class="text-center"><?= lang('Hoá Đơn Mua') ?></th>
                                        <th class="text-center"><?= lang('VAT') ?></th>
                                        <th class="text-center"><?= lang('Thành Tiền') ?></th>
                                        <th class="text-center"><?= lang('Số Ngày Chi') ?></th>
                                        <th class="text-center"><?= lang('Ngày Chi Nợ') ?></th>
                                        <th class="text-center"><?= lang('Phiếu Đề Xuất Tài Chính') ?></th>
                                        <th class="text-center"><?= lang('Phiếu Kế Hoạch Chi') ?></th>
                                        <th class="text-center"><?= lang('Mã Phiếu Chi') ?></th>
                                        <th class="text-center"><?= lang('Số Tiền Chi') ?></th>
                                        <th class="text-center"><?= lang('Số Tiền Còn Lại') ?></th>
                                        <th class="text-center"><?= lang('Ngày Nhập Misa') ?></th>
                                        <th class="text-center"><?= lang('Ngày Cập Nhật Foso') ?></th>

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
    oTable = tnhInitDataTable('#table-synthetic_purchase_cost_month',
        '<?= site_url('admin/reports_purchase/getSyntheticPurchaseCostMonth') ?>', {
            'order': [
                [1, 'desc']
            ],
            scrollX: true,
            "ajax": {
                "url": '<?= site_url('admin/reports_purchase/getSyntheticPurchaseCostMonth') ?>',
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

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/reports_purchase/exportExcel',
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