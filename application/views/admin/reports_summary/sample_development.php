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
                                <a href="javascript:void(0)" onclick="filterSampleDevelopment()" class="btn btn-primary" style="margin-top: 25px;"><?= lang('search') ?></a>
                                <a href="javascript:void(0)" onclick="exportExcelDevelopment()" class="btn btn-success" style="margin-top: 25px;"><?php echo lang('Xuất excel'); ?></a>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-sample-development" class="table table-sample-development dataTable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('STT') ?></th>
                                            <th class="text-center"><?= lang('Mã Khách Hàng') ?></th>
                                            <th class="text-center"><?= lang('Tên Khách Hàng') ?></th>
                                            <th class="text-center"><?= lang('Phiếu Yêu Cầu PTM') ?></th>
                                            <th class="text-center"><?= lang('Mã Brand') ?></th>
                                            <th class="text-center"><?= lang('Mã Sản Phẩm') ?></th>
                                            <th class="text-center"><?= lang('Tên Sản Phẩm') ?></th>
                                            <th class="text-center"><?= lang('Ngày Chạy Mẫu') ?></th>
                                            <th class="text-center"><?= lang('Ngày Hoàn Thành Mẫu') ?></th>
                                            <th class="text-center"><?= lang('Ngày Gửi Mẫu') ?></th>
                                            <th class="text-center"><?= lang('Ngày Duyệt Mẫu') ?></th>
                                            <th class="text-center"><?= lang('Chạy Hàng Lấy Mẫu') ?></th>
                                            <th class="text-center"><?= lang('Ngày Hoàn Thành Mẫu SX') ?></th>
                                            <th class="text-center"><?= lang('Ngày Duyệt Báo Giá') ?></th>
                                            <th class="text-center"><?= lang('Ngày Cập Nhật BOM Foso') ?></th>
                                            <th class="text-center"><?= lang('Có Đơn Hàng') ?></th>
                                            <th class="text-center"><?= lang('Không Có Đơn Hàng') ?></th>
                                            <th class="text-center"><?= lang('Chạy Mẫu Lại') ?></th>
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

    oTable = tnhInitDataTable('#table-sample-development', '', {
        'order': [
            [0, 'desc']
        ],
        'fixedHeader': {
            header: true,
        },
        'searching': false,
        'ordering': false,
        "ajax": {
            "url": '<?= site_url('admin/reports_summary_h/getSampleDevelopment') ?>',
            "type": "POST",
            "data": function(d) {
                if (typeof(csrfData) !== 'undefined') {
                    d[csrfData['token_name']] = csrfData['hash'];
                }
                for (var key in fnserverparams) {
                    d[key] = $(fnserverparams[key]).val();
                }
                if ($('#table-sample-development').attr('data-last-order-identifier')) {
                    d['last_order_identifier'] = $('#table-sample-development').attr('data-last-order-identifier');
                }
            },
            "dataSrc": function(json) {
                return json.aaData;
            }
        },
        "createdRow": function(row, data, index) {},
        "columnDefs": [],
    });

    function filterSampleDevelopment() {
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

    function exportExcelDevelopment() {
        var dataPOST = {};
        dataPOST[token] = hash;
        for (var key in fnserverparams) {
            dataPOST[key] = $(fnserverparams[key]).val();
        }

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/reports_summary_h/exportExcelDevelopment',
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