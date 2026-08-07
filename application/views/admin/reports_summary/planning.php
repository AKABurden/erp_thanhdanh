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
                        <div class="">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <?= lang('tnh_reference_productions_orders', 'productions_orders_search') ?>
                                    <input type="text" name="productions_orders_search" data-placeholder="<?= lang('tnh_reference_productions_orders') ?>" id="productions_orders_search" class="productions_orders_search" style="width: 100%;" value="">
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
                                    <a href="javascript:void(0)" onclick="filterPlanning()" class="btn btn-primary" style="margin-top: 25px;"><?= lang('search') ?></a>
                                    <a href="javascript:void(0)" onclick="exportExcelPlanning()" class="btn btn-success" style="margin-top: 25px;"><?php echo lang('Xuất excel'); ?></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-planning" class="table table-planning dataTable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('STT') ?></th>
                                            <th class="text-center"><?= lang('Ngày Kế Hoạch') ?></th>
                                            <th class="text-center"><?= lang('Lệnh Sản Xuất') ?></th>
                                            <th class="text-center"><?= lang('Qui Cách Vận Hành') ?></th>
                                            <th class="text-center"><?= lang('Tổng Số Lượng') ?></th>
                                            <th class="text-center"><?= lang('Số Con/Lần Vận Hành') ?></th>
                                            <th class="text-center"><?= lang('Tổng Số Lần Vận Hành') ?></th>
                                            <th class="text-center"><?= lang('Phiếu Xuất Mẫu Sản Xuất') ?></th>
                                            <th class="text-center"><?= lang('Phiếu Xuất Khuân Bế') ?></th>
                                            <th class="text-center"><?= lang('Phiếu Xuất NPL') ?></th>
                                            <th class="text-center"><?= lang('Phiếu Xuất Kẽm') ?></th>
                                            <th class="text-center"><?= lang('Ghép Size') ?></th>
                                            <th class="text-center"><?= lang('Dàn Trang') ?></th>
                                            <th class="text-center"><?= lang('NPL Canh Bài') ?></th>
                                            <th class="text-center"><?= lang('Ngày Về NPL') ?></th>
                                            <th class="text-center"><?= lang('Đã Có NPL') ?></th>
                                            <th class="text-center"><?= lang('Phiếu Cắt Giấy') ?></th>
                                            <th class="text-center"><?= lang('Mã BCKPH') ?></th>
                                            <th class="text-center"><?= lang('Ngày Cập Nhật Foso') ?></th>
                                            <th class="text-center"><?= lang('Xác Nhận Bàn Giao Cho GĐSX') ?></th>
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
        productions_orders_search: '#productions_orders_search',
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
    };

    oTable = tnhInitDataTable('#table-planning', '', {
        'order': [
            [0, 'desc']
        ],
        'fixedHeader': {
            header: true,
        },
        'searching': false,
        'ordering': false,
        "ajax": {
            "url": '<?= site_url('admin/reports_summary_h/getPlanning') ?>',
            "type": "POST",
            "data": function(d) {
                if (typeof(csrfData) !== 'undefined') {
                    d[csrfData['token_name']] = csrfData['hash'];
                }
                for (var key in fnserverparams) {
                    d[key] = $(fnserverparams[key]).val();
                }
                if ($('#table-planning').attr('data-last-order-identifier')) {
                    d['last_order_identifier'] = $('#table-planning').attr('data-last-order-identifier');
                }
            },
            "dataSrc": function(json) {
                return json.aaData;
            }
        },
        "createdRow": function(row, data, index) {},
        "columnDefs": [],
    });

    function filterPlanning() {
        oTable.draw();
    }

    $(document).ready(function () {
        ajaxSelectParams('#customers_search', 'admin/clients/searchCustomers', $('#customers_search').val(), false, true);
    });

    // $(document).on('change',
    //     '#end_date_search,#start_date_search',
    //     function(
    //         event) {
    //         event.preventDefault();
    //         oTable.draw();
    //     });

    function exportExcelPlanning() {
        var dataPOST = {};
        dataPOST[token] = hash;
        for (var key in fnserverparams) {
            dataPOST[key] = $(fnserverparams[key]).val();
        }

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/reports_summary_h/exportExcelPlanning',
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

    $(document).ready(function () {
        ajaxSelectParamsCallback('#productions_orders_search', 'admin/manufactures/searchProductionsOrders', 0, false, true);
    });
</script>