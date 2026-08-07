<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?php if ($this->perAddCouponInvoice) : ?>
                <div class="pull-right mright5 H_border">
                    <a href="<?= base_url('admin/coupon_invoice/add') ?>"
                       class="btn btn-info H_action_button tnh-modal">
                        <?php echo _l('add'); ?>
                    </a>
                </div>
            <?php endif ?>
            <a class="btn btn-info pull-right mright5 H_action_button" onclick="excel(); return false;">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('Xuất hóa đơn'); ?>
            </a>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="searchStatistics" class="collapse in" aria-expanded="true">
                    <div class="col-md-3">
                        <?= lang('client_lowercase', 'business_plan_search') ?>
                        <input type="text" name="clients_id" data-placeholder="<?= lang('client_lowercase') ?>"
                               id="clients_id" class="business_plan_search" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('start_date', 'start_date_search') ?>
                        <input type="text" name="start_date_search" autocomplete="off"
                               placeholder="<?= lang('start_date') ?>" id="start_date_search"
                               class="start_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('end_date', 'end_date_search') ?>
                        <input type="text" name="end_date_search" autocomplete="off"
                               placeholder="<?= lang('end_date') ?>" id="end_date_search"
                               class="end_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="">
                            <table id="table-coupon-invoices"
                                   class="table dt-tnh table-hover table-condensed table-coupon-invoices">
                                <thead>
                                <tr>
                                    <th class="text-center">
                                        <div class="checkbox mass_select_all_wrap text-center"><input type="checkbox"
                                                                                                      id="mass_select_all"
                                                                                                      data-to-table="coupon-invoices"><label
                                                    for="mass_select_all"></label></div>
                                    </th>
                                    <th class="text-center"><?= lang('date') ?></th>
                                    <th class="text-center"><?= lang('tnh_reference_bill') ?></th>
                                    <th class="text-center"><?= lang('deliveries') ?></th>
                                    <th class="text-center"><?= lang('customers') ?></th>
                                    <th class="text-center"><?= lang('tnh_grand_total') ?></th>
                                    <th class="text-center"><?= lang('tnh_money_tax') ?></th>
                                    <th class="text-center"><?= lang('tnh_cost_delivery') ?></th>
                                    <th class="text-center"><?= lang('Chi phí cộng thêm') ?></th>
                                    <th class="text-center"><?= lang('invoice_total') ?></th>
                                    <th class="text-center"><?= lang('Trạng thái') ?></th>
                                    <th class="text-center"><?= lang('tnh_created_by') ?></th>
                                    <th class="text-center"><?= lang('note') ?></th>
                                    <th class="text-center"><?= lang('actions') ?></th>
                                    <th class="text-center"><?= lang('payment') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="99"></td>
                                </tr>
                                </tbody>
                                <tfoot class="bold">
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td class="text-center"><?= lang('tnh_grand_total') ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
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
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>

<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        status_table: '#status_table',
        clients_id: '#clients_id',
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search'
    };

    var oTable = '';
    $(document).ready(function () {
        ajaxSelectParams('#clients_id', 'admin/clients/searchCustomers', 0, true, true);

        oTable = tnhInitDataTable('#table-coupon-invoices', '', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/coupon_invoice/getInvoices') ?>',
                "type": "POST",
                "data": function (d) {
                    if (typeof (csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function (json) {
                    $('tfoot tr td:nth-child(9)').html(`<div class="text-right">${tnhFormatMoney(json.grandTotal)}</div>`);
                    $('tfoot tr td:nth-child(10)').html(`<div class="text-right">${tnhFormatMoney(json.TotalPayment)}</div>`);
                    return json.aaData;
                }
            },
            "columnDefs": [{
                "render": function (data, type, row) {
                    return '<div class="checkbox"><input type="checkbox" name="invoice_id[]" id="check-item' + data + '" value="' + data + '"><label for="check-item' + data + '"></label></div>';
                },
                "targets": 0,
                "name": 'id',
                'orderable': false,
                'width': '40px'
            },
                {
                    "render": function (data, type, row) {
                        return '<div>' + fld(data) + '</div>';
                    },
                    "targets": 1,
                    "name": 'date',
                    'width': '80px',
                    'searchable': false
                },
                {
                    "targets": 2,
                    "name": 'reference_no',
                    'width': '90px'
                },
                {
                    "targets": 3,
                    "name": 'reference_orders',
                    'width': '120px'
                },
                {
                    "targets": 4,
                    "name": 'customer_name',
                    'width': '150px'
                },
                {
                    "render": function (data, type, row) {
                        return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                    },
                    "targets": 5,
                    "name": 'total',
                    'width': '80px'
                },
                {
                    "render": function (data, type, row) {
                        return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                    },
                    "targets": 6,
                    "name": 'total_tax',
                    'width': '80px'
                },
                {
                    "render": function (data, type, row) {
                        return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                    },
                    "targets": 7,
                    "name": 'cost_delivery',
                    'width': '120px',
                    'visible': false
                },
                {
                    "render": function (data, type, row) {
                        return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                    },
                    "targets": 8,
                    "name": 'additional_costs',
                    'width': '100px'
                },
                {
                    "render": function (data, type, row) {
                        return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                    },
                    "targets": 9,
                    "name": 'grand_total',
                    'width': '100px'
                },
                {
                    "render": function (data, type, row) {
                        payment = row[14];
                        var str = '';
                        if (data == 1) {
                            str =
                                '<span class="label btn-warning"><?= lang('invoice_status_not_paid_completely') ?></span><br><div class="text-right">' + tnhFormatMoney(payment) + '</div>';
                        } else if (data == 2) {
                            str =
                                '<span class="label btn-primary"><?= lang('invoice_status_paid') ?></span><br><div class="text-right">' + tnhFormatMoney(payment) + '</div>';
                        } else {
                            str =
                                '<span class="label btn-danger"><?= lang('invoice_status_unpaid') ?></span>';
                        }
                        return str;
                    },
                    "targets": 10,
                    "name": 'status_payment',
                    'width': '100px'
                },
                {
                    "targets": 11,
                    "name": 'created_by',
                    'width': '100px'
                },
                {
                    "targets": 12,
                    "name": 'note',
                    'width': '100px'
                },
                {
                    "targets": 13,
                    "name": 'actions',
                    'width': '100px',
                    'sortable': false,
                    'searchable': false
                },
                {
                    "targets": 14,
                    "name": 'payment',
                    'visible': false
                },
            ],
        });

        $(document).on('change', '#clients_id, #start_date_search, #end_date_search', function (event) {
            event.preventDefault();
            oTable.draw();
        });
    });

    function excel() {
        var ids = '';
        var rows = $('#table-coupon-invoices').find('tbody tr');
        $.each(rows, function () {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids += checkbox.val() + ',';
            }
        });
        ids = ids.slice(0, -1);

        if (!ids) {
            bootbox.alert('Vui lòng chọn hóa đơn cần xuất');
            return;
        }

        $.ajax({
            url: site.base_url +'admin/coupon_invoice/excel',
            type: 'POST',
            dataType: 'JSON',
            data: {
                ids:ids,
                csrf_token_name: hash,
            },
        })
            .done(function (data) {
                if (data.result) {
                    alert_float('success', data.message);
                    download(data.filename, data.file);
                    $('.add').removeAttr('disabled', 'disabled');
                } else {
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function () {
                alert_float('danger', 'errors');
                $('.add').removeAttr('disabled', 'disabled');
            });
    }
</script>