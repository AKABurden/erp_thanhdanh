<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <?= lang('customers', 'customers_search') ?>
                                <input type="text" name="customers_search" id="customers_search" style="width: 100%;" data-placeholder="<?= lang('customers') ?>" value="">
                            </div>
                        </div>
                        <?php 
                            $start_date = date('01/m/Y');
                            $end_date = date('t/m/Y');
                        ?>
                        <div class="col-md-2">
                            <div class="form-group">
                                <?= lang('start_date', 'start_date_search') ?>
                                <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>"
                                    id="start_date_search" class="start_date_search datepicker form-control"
                                    style="width: 100%;" value="<?= $start_date ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <?= lang('end_date', 'end_date_search') ?>
                                <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>"
                                    id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;"
                                    value="<?= $end_date ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <a href="javascript:void(0)" onclick="filterQuotes()" class="btn btn-primary" style="margin-top: 25px;"><?= lang('search') ?></a>
                                <a href="javascript:void(0)" onclick="exportExcelQuotes()" class="btn btn-success" style="margin-top: 25px;"><?php echo lang('Xuất excel'); ?></a>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-quotes" class="table table-quotes dataTable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('STT') ?></th>
                                            <th class="text-center"><?= lang('Mã khách hàng') ?></th>
                                            <th class="text-center"><?= lang('Tên khách hàng') ?></th>
                                            <th class="text-center"><?= lang('Mã Brand') ?></th>
                                            <th class="text-center"><?= lang('Mã phiếu yêu cầu báo giá') ?></th>
                                            <th class="text-center"><?= lang('Bảng báo giá') ?></th>
                                            <th class="text-center"><?= lang('Ngày báo giá') ?></th>
                                            <th class="text-center"><?= lang('Ngày hoàn thành báo giá') ?></th>
                                            <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                                            <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                                            <th class="text-center"><?= lang('Theo lô') ?></th>
                                            <th class="text-center"><?= lang('Báo giá theo con') ?></th>
                                            <th class="text-center"><?= lang('Chiết khấu') ?></th>
                                            <th class="text-center"><?= lang('Ngày duyệt báo giá') ?></th>
                                            <th class="text-center"><?= lang('Ngày cập nhật Foso') ?></th>
                                            <th class="text-center"><?= lang('Có đơn hàng') ?></th>
                                            <th class="text-center"><?= lang('Không có đơn hàng') ?></th>
                                            <th class="text-center"><?= lang('Báo giá lại') ?></th>
                                            <th class="text-center"><?= lang('Mã BCKPH') ?></th>
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
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    
    var fnserverparams = {
        customers_search: '#customers_search',
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
    };

    oTable = tnhInitDataTable('#table-quotes', '', {
        'order': [
            [0, 'desc']
        ],
        'fixedHeader': {
            header: true,
        },
        'searching': false,
        'ordering': false,
        "ajax": {
            "url": '<?= site_url('admin/reports_summary_h/getSummaryQuotes') ?>',
            "type": "POST",
            "data": function(d) {
                if (typeof(csrfData) !== 'undefined') {
                    d[csrfData['token_name']] = csrfData['hash'];
                }
                for (var key in fnserverparams) {
                    d[key] = $(fnserverparams[key]).val();
                }
                if ($('#table-quotes').attr('data-last-order-identifier')) {
                    d['last_order_identifier'] = $('#table-quotes').attr('data-last-order-identifier');
                }
            },
            "dataSrc": function(json) {
                return json.aaData;
            }
        },
        "createdRow": function(row, data, index) {},
        "columnDefs": [],
    });

    function filterQuotes() {
        oTable.draw();
    }

    $(document).ready(function () {
        ajaxSelectParams('#customers_search', 'admin/clients/searchCustomers', $('#customers_search').val(), false, true);
    });

    $(document).on('change',
        '#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function exportExcelQuotes() {
        var dataPOST = {};
        dataPOST[token] = hash;
        for (var key in fnserverparams) {
            dataPOST[key] = $(fnserverparams[key]).val();
        }

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/reports_summary_h/exportExcelQuotes',
            data: dataPOST,
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