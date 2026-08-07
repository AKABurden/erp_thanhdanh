<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
.fixedHeader-floating {
    position: fixed !important;
}
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <!-- <div> -->
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <?php if (has_permission('payroll_payment', '', 'create')) { ?>
            <a href="<?= base_url('admin/payroll/add_payroll_payment') ?>"
                class="btn btn-info pull-right H_action_button tnh-modal" data-tnh="modal" data-toggle="modal"
                data-target="#myModal">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo lang('add'); ?>
            </a>
            <?php } ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    <?= lang('Nhân viên', 'staff') ?>
                    <input name="staff" id="staff" class="" data-placeholder="<?= lang('Nhân viên') ?>"
                        style="width: 100%;" />
                </div>
                <div class="col-md-3">
                    <?= lang('start_date', 'start_date_search') ?>
                    <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>"
                        id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;"
                        value="">
                </div>
                <div class="col-md-3">
                    <?= lang('end_date', 'end_date_search') ?>
                    <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>" id="end_date_search"
                        class="end_date_search datepicker form-control" style="width: 100%;" value="">
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#all" aria-controls="all" role="tab" value="all"
                                            data-toggle="tab"><?= lang('all') ?>(<span class="all"><?= 0 ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#approved" aria-controls="approved" role="tab" value="approved"
                                            data-toggle="tab"><?= lang('Đã cấn trừ') ?>(<span
                                                class="approved"><?= 0 ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#un_approved" aria-controls="un_approved" role="tab"
                                            value="un_approved" data-toggle="tab"><?= lang('Chưa cấn trừ') ?>(<span
                                                class="un_approved"><?= 0 ?></span>)</a>
                                    </li>
                                </ul>
                                <input type="hidden" name="status_table" id="status_table"
                                    class="form-control status_table" value="all">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="table-payroll-payment"
                                class="table table-hover table-bordered table-condensed dataTable table-payroll-payment"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="checkbox mass_select_all_wrap text-center"><input
                                                    type="checkbox" id="mass_select_all"
                                                    data-to-table="payroll-payment"><label
                                                    for="mass_select_all"></label></div>
                                        </th>
                                        <th><?= lang('Mã phiếu') ?></th>
                                        <th><?= lang('Ngày tạm ứng') ?></th>
                                        <th><?= lang('Nhân viên') ?></th>
                                        <th><?= lang('Số tiền') ?></th>
                                        <th><?= lang('Bảng lương cấn trừ') ?></th>
                                        <th><?= lang('Ghi chú') ?></th>
                                        <th><?= lang('actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                </tbody>
                                <tfoot class="bold">
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<?php $this->load->view('loader')?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript">
var site = <?= json_encode(array('base_url' => base_url())) ?>;
var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
var fnserverparams = {
    staff: "#staff",
    start_date_search: "#start_date_search",
    end_date_search: "#end_date_search",
    status_table: "#status_table",
};
var oTable = '';
var arr = [];

$(document).ready(function() {
    ajaxSelectParams('#staff', 'admin/payroll/searchStaffPayment', 0, true, true);
    oTable = tnhDatatable(
        '#table-payroll-payment', {
            'order': [
                [1, 'asc']
            ],
            'orderCellsTop': true,
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            scrollY: height_body,
            scrollX: true,
            "serverSide": true,
            'sAjaxSource': '<?= site_url('admin/payroll/getPayrollPayment') ?>',
            'fnServerData': function(sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                for (var key in fnserverparams) {
                    aoData.push({
                        "name": key,
                        "value": $(fnserverparams[key]).val()
                    });
                }
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "columnDefs": [{
                    "render": function(data, type, row) {
                        return '<div class="checkbox"><input type="checkbox" class="category_id" name="payroll_payment_id[]" id="check-item' +
                            data + '" value="' + data + '"><label for="check-item' + data +
                            '"></label></div>';
                    },
                    "targets": 0,
                    "name": 'id',
                    'orderable': false,
                    'width': '50px'
                },
                {
                    "render": function(data, type, row) {
                        return '<div>' + data + '</div>';
                    },
                    "targets": 1,
                    "name": 'code',
                    'width': '100px',
                },
                {
                    "render": function(data, type, row) {
                        return data;
                    },
                    "targets": 2,
                    "name": 'date',
                    'width': '100px',
                    'searchable': false
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-left">' + data + '</div>';
                    },
                    "targets": 3,
                    "name": 'staff_name',
                    'width': '150px'
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                    },
                    "targets": 4,
                    "name": 'amount',
                    'width': '150px'
                },
                {
                    "render": function(data, type, row) {
                        code_payroll_item = '';
                        if (data != null) {
                            data = data.split('||');
                            $.each(data, function(k, v) {
                                v = v.split('__');
                                code_payroll_item +=
                                    `<div>${v[0]} (${tnhFormatMoney(v[1])})</div>`;
                            });
                        } else {
                            code_payroll_item = '';
                        }
                        return '<div class="text-left">' + code_payroll_item + '</div>';
                    },
                    "targets": 5,
                    "name": 'code_payroll_item',
                    'width': '150px'
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-left">' + data + '</div>';
                    },
                    "targets": 6,
                    "name": 'note',
                    'width': '150px'
                },
                {
                    "targets": 7,
                    "name": 'actions',
                    'orderable': false,
                    'searchable': false,
                    'width': '100px'
                }
            ],
            "footerCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                var total_amount = 0;
                var api = this.api(),
                    data;
                for (var i = 0; i < aaData.length; i++) {
                    total_amount += intVal(aaData[i][4]);
                }
                $(api.column(4).footer()).html('<div class="text-right">' + tnhFormatNumber(
                    total_amount) + '</div>');
            }
        }
    );

    $(document).on('click', '.btn-dt-reload', function(event) {
        oTable.draw();
        total_limit();
    });
    $(document).on('change', '#staff, #start_date_search, #end_date_search', function(
        event) {
        event.preventDefault();
        oTable.draw();
        total_limit();
    });
    $(document).on('click', '.status-table li a', function(event) {
        status_table = $(this).attr('value');
        $('#status_table').val(status_table);
        oTable.draw();
    });
});
total_limit();

function total_limit() {
    dataString = {
        [csrfData['token_name']]: csrfData['hash'],
        staff: $("#staff").val(),
        start_date_search: $("#start_date_search").val(),
        end_date_search: $("#end_date_search").val(),
    };
    jQuery.ajax({
        type: "post",
        url: "<?=admin_url()?>payroll/count_all_payroll_payment/",
        data: dataString,
        cache: false,
        success: function(data) {
            data = JSON.parse(data);
            $('.all').html(data.all);
            $('.approved').html(data.approved);
            $('.un_approved').html(data.un_approved);
        }
    });
}
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>