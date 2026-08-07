<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
table tr td {
    vertical-align: middle !important;
}

.progressbar li:not(.initli) {
    width: 100px !important;
}
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?php if($this->perAddOutsource){ ?>
            <div class="pull-right  H_border">
                <a class="add_contact_person btn btn-info H_action_button option_barcode">
                    <?php echo _l('ch_pay_slip_total'); ?>
                </a>
            </div>
            <a href="<?= base_url('admin/outsource/add') ?>" class="btn btn-info pull-right H_action_button mright5">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('add'); ?>
            </a>
            <?php } ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    <?= lang('Số đơn hàng / Ref Id & Platfox Order Id', 'orders_search') ?>
                    <input type="text" name="orders_search" id="orders_search" style="width: 100%;"
                        data-placeholder="<?= lang('tnh_reference_orders') ?> - <?= lang('Ref Id & Platfox Order Id') ?>"
                        value="">
                </div>
                <div class="col-md-3">
                    <?= lang('Nhà cung cấp', 'supplier_search') ?>
                    <input type="text" name="supplier_search" data-placeholder="<?= lang('Nhà cung cấp') ?>"
                        id="supplier_search" class="supplier_search" style="width: 100%;" value="">
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
                        <input type="hidden" name="" id="suppliers_id" class="form-control" value="">
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                    <li role="presentation">
                                        <a href="#un_approved" aria-controls="un_approved" role="tab"
                                            value="un_approved" data-toggle="tab"><?= lang('tnh_un_approved') ?>(<span
                                                class="count-un_approved"><?= $un_approved ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#approved" aria-controls="approved" role="tab" value="approved"
                                            data-toggle="tab"><?= lang('tnh_approved') ?>(<span
                                                class="count-approved"><?= $approved ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#approved" aria-controls="approved" role="tab"
                                            value="invoice_status_unpaid"
                                            data-toggle="tab"><?= lang('invoice_status_unpaid') ?>(<span
                                                class="count-invoice_status_unpaid"><?= $invoice_status_unpaid ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#approved" aria-controls="approved" role="tab"
                                            value="invoice_status_not_paid_completely"
                                            data-toggle="tab"><?= lang('invoice_status_not_paid_completely') ?>(<span
                                                class="count-invoice_status_not_paid_completely"><?= $invoice_status_not_paid_completely ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#approved" aria-controls="approved" role="tab"
                                            value="invoice_status_paid"
                                            data-toggle="tab"><?= lang('invoice_status_paid') ?>(<span
                                                class="count-invoice_status_paid"><?= $invoice_status_paid ?></span>)</a>
                                    </li>
                                    <li role="presentation" class="active">
                                        <a href="#all" aria-controls="all" role="tab" value="all"
                                            data-toggle="tab"><?= lang('all') ?>(<span
                                                class="count-all"><?= $all ?></span>)</a>
                                    </li>
                                </ul>
                                <input type="hidden" name="status_table" id="status_table"
                                    class="form-control status_table" value="all">
                            </div>
                        </div>
                        <div class="">
                            <table id="table-outsource"
                                class="table dt-tnh table-hover table-condensed table-outsource-new">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="checkbox mass_select_all_wrap text-center"><input
                                                    type="checkbox" id="mass_select_all"
                                                    data-to-table="outsource-new"><label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('Số phiếu') ?></th>
                                        <th><?= lang('tnh_supplies') ?></th>
                                        <th><?= lang('tnh_warehouses') ?></th>
                                        <th><?= lang('tnh_grand_total') ?></th>
                                        <th><?= lang('invoice_status_paid') ?></th>
                                        <th><?= lang('tnh_rest') ?></th>
                                        <th><?= lang('invoice_received_payments') ?></th>
                                        <th><?= lang('tnh_created_by') ?></th>
                                        <th><?= lang('tnh_status') ?></th>
                                        <th><?= lang('tnh_workflow_outsource') ?></th>
                                        <th><?= lang('tnh_reference_export_outsource') ?></th>
                                        <th><?= lang('tnh_reference_import_outsource') ?></th>
                                        <th><?= lang('Số xuất kho khác') ?></th>
                                        <th><?= lang('note') ?></th>
                                        <th><?= lang('tnh_user_agree') ?></th>
                                        <th class="hide"><?= lang('po_id') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
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
<div id="payment_data"></div>
<?php $this->load->view('loader')?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
var fnserverparamsIndex = {
    status_table: '#status_table',
    'suppliers_id': '#suppliers_id',
    'orders_search': '#orders_search',
    'supplier_search': '#supplier_search',
    'start_date_search': '#start_date_search',
    'end_date_search': '#end_date_search',
};
var oTable = '';

function exportOutsource(el) {
    outsource_id = $(el).attr('value');
    bootbox.confirm({
        message: '<?= lang('tnh_you_want_export_outsource') ?>',
        buttons: {
            confirm: {
                label: lang_core['yes'],
                className: 'btn-success'
            },
            cancel: {
                label: lang_core['no'],
                className: 'btn-danger'
            }
        },
        callback: function(result) {
            if (result) {
                if (outsource_id) {
                    $.ajax({
                            url: site.base_url + 'admin/outsource/exportOutsource/' + outsource_id,
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                outsource_id: outsource_id,
                                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                            },
                        })
                        .done(function(data) {
                            if (data.result) {
                                alert_float('success', data.message);
                                oTable.draw('false');
                            } else {
                                alert_float('danger', data.message);
                                oTable.draw('false');
                            }
                        })
                        .fail(function() {
                            console.log("error");
                        });
                }
            }
        }
    });
}

$(document).ready(function() {
    ajaxSelectParams('#orders_search', 'admin/orders/searchOrders', 0, true, true);
    ajaxSelectParams('#supplier_search', 'admin/outsource/searchSupplier', 0, true, true);
    oTable = tnhDatatable(
        '#table-outsource', {
            'order': [
                [1, 'desc']
            ],
            'orderCellsTop': true,
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            // fixedColumns: {
            //     leftColumns: 0,
            //     rightColumns: 0
            // },
            scrollY: height_body,
            // scrollX: true,
            "responsive": true,
            "serverSide": true,
            'sAjaxSource': '<?= site_url('admin/outsource/getOutsource') ?>',
            'fnServerData': function(sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                for (var key in fnserverparamsIndex) {
                    aoData.push({
                        "name": key,
                        "value": $(fnserverparamsIndex[key]).val()
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
            "rowCallback": function(row, data) {
                st = data[10];
                workflow = data[11];
                // if (st != "approved" || workflow > 0) {
                if (st != "approved") {
                    $(row).find('.export-outsource').addClass('tnh-disabled');
                }
                if (workflow < 1) {
                    $(row).find('.tnh-import-outsource').addClass('tnh-disabled');
                }
                if (data[8] == 2 || st != "approved") {
                    $(row).find('.tnh-payment-processing').addClass('tnh-disabled');
                }
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                mainWrapperHeightFix();
            },
            "columnDefs": [{
                    "render": function(data, type, row) {
                        return '<div class="checkbox"><input type="checkbox" name="outsource_id[]" id="check-item' +
                            data + '" value="' + data + '"><label for="check-item' + data +
                            '"></label></div>';
                    },
                    "targets": 0,
                    "name": 'id',
                    'orderable': false,
                    'width': '30px'
                },
                {
                    "render": function(data, type, row) {
                        actions = row[18];
                        return fld(data) + '<br>' + actions;
                    },
                    "targets": 1,
                    "name": 'date',
                    'width': '110px',
                    'searchable': false
                },
                {
                    "render": function(data, type, row) {
                        if (!data) return '';
                        data = data.split('||');
                        branch = data[1];

                        return '<div style="min-width: 80px;" class="">\
                            <a data-tnh="modal" class="tnh-modal" href="' + site.base_url +
                            'admin/outsource/view_outsource/' + row[0] +
                            '" data-toggle="modal" data-target="#myModal">' + data[0] + '</a>\
                            <div style="font-style: italic;">' + branch + '</div>\
                            </div><div class="td-reference-no"></div>';
                    },
                    "targets": 2,
                    "name": 'reference_no',
                    'width': '80px'
                },
                {
                    "render": function(data, type, row) {
                        po = row[17];
                        return data + '<div style="font-style: italic;color:red">' + po + '</div>';
                    },
                    "targets": 3,
                    "name": 'supplier_company',
                    'width': '150px'
                },
                {
                    "targets": 4,
                    "name": 'warehouses',
                    'width': '150px',
                    'visible': false
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                    },
                    "targets": 5,
                    "name": 'grand_total',
                    'width': '80px'
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                    },
                    "targets": 6,
                    "name": 'amount_paid',
                    'width': '100px',
                    'visible': false
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                    },
                    "targets": 7,
                    "name": 'rest',
                    'width': '100px',
                    'visible': false
                },
                {
                    "render": function(data, type, row) {
                        var str = '';
                        rest = row[7];
                        amount_paid = row[6];
                        if (data == 1) {
                            str =
                                '<span class="label btn-warning"><?= lang('Thanh toán một phần') ?> (' +
                                tnhFormatMoney(amount_paid) + ')</span>';
                        } else if (data == 2) {
                            str =
                                '<span class="label btn-primary"><?= lang('invoice_status_paid') ?>(' +
                                tnhFormatMoney(amount_paid) + ')</span>';
                        } else {
                            str =
                                '<span class="label btn-danger"><?= lang('invoice_status_unpaid') ?></span>';
                        }
                        return str + '<br>' +
                            '<div style="margin-top:8px;text-transform: uppercase;font-weight: bold;">Còn lại: <span>' +
                            tnhFormatMoney(rest) +
                            '</span></div>';
                    },
                    "targets": 8,
                    "name": 'status_pay',
                    'width': '100px'
                },
                {
                    "targets": 9,
                    "name": 'created_by',
                    'width': '100px',
                    'visible': false
                },
                {
                    "render": function(data, type, row) {
                        str1 = '';
                        outsource_id = row[0];
                        if (data == "approved") {
                            user_status = '<div class="mtop10"><?= lang('tnh_user_agree') ?>: ' +
                                row[16] + '</div>';
                        } else {
                            user_status = '';
                        }
                        if (data == "un_approved") {
                            str1 =
                                '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_agree') ?>" data-content="<p><a id=\'agree\' outsource_id=\'' +
                                outsource_id +
                                '\' value=\'approved\' class=\'btn btn-success\'><?= lang('tnh_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-danger po"><?= lang('tnh_un_approved') ?></span></div>' +
                                user_status;
                        } else if (data == "approved") {
                            str1 =
                                '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_agree') ?>\" data-content="<p><a id=\'agree\' outsource_id=\'' +
                                outsource_id +
                                '\' value=\'un_approved\' class=\'btn btn-danger\'><?= lang('tnh_un_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-success po"><?= lang('tnh_approved') ?></span></div>' +
                                user_status;
                        }

                        return str1;
                    },
                    "targets": 10,
                    "name": 'status',
                    'width': '100px',
                    'visible': false
                },
                {
                    "render": function(data, type, row) {
                        arrExport = row[14];
                        arrImport = row[13];
                        str_status_index = '';
                        outsource_id = row[0];
                        status = row[10];
                        if (status == "approved") {
                            user_status = '<div class="mtop10"><?= lang('tnh_user_agree') ?>: ' +
                                row[16] + '</div>';
                        } else {
                            user_status = '';
                        }
                        if (status == "un_approved") {
                            str_status_index =
                                '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_agree') ?>" data-content="<p><a id=\'agree\' outsource_id=\'' +
                                outsource_id +
                                '\' value=\'approved\' class=\'btn btn-success\'><?= lang('tnh_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-danger po"><?= lang('tnh_un_approved') ?></span></div>' +
                                user_status;
                        } else if (status == "approved") {
                            str_status_index =
                                '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_agree') ?>\" data-content="<p><a id=\'agree\' outsource_id=\'' +
                                outsource_id +
                                '\' value=\'un_approved\' class=\'btn btn-danger\'><?= lang('tnh_un_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-success po"><?= lang('tnh_approved') ?></span></div>' +
                                user_status;
                        }

                        conditionExportOutsource = data > 0 ? 'active' : '';
                        conditionImportOutsource = data > 1 ? 'active' : '';
                        conditionStatus = status == 'approved' ? 'active' : '';

                        str = `
                        <ul class="progressbar" style="display: flex;justify-content: center;">
                            <li class="${conditionStatus}">
                                <div>${str_status_index}</div>
                            </li>
                            <li class="${conditionExportOutsource}">
                                <div><?= lang('tnh_export_outsource') ?></div>
                                <div style="margin-top:10px;">${arrExport}</div>
                            </li>
                            <li class="${conditionImportOutsource}">
                                <div><?= lang('tnh_import_outsource') ?> </div>
                                <div style="margin-top:10px;">${arrImport}</div>
                            </li>
                        </ul>`;
                        return str;
                    },
                    "targets": 11,
                    "name": 'workflow',
                    'width': '200px'
                },
                {
                    "targets": 12,
                    "name": 'export_outsource',
                    'width': '100px',
                    'visible': false
                },
                {
                    "targets": 13,
                    "name": 'import_outsource',
                    'width': '130px',
                    'visible': false
                },
                {
                    "targets": 14,
                    "name": 'export_different',
                    'width': '100px',
                    'visible': false
                },
                {
                    "targets": 15,
                    "name": 'note',
                    'width': '120px'
                },
                {
                    "targets": 16,
                    "name": 'user_status',
                    'visible': false
                },
                {
                    "targets": 17,
                    "name": 'po_id',
                    'visible': false
                },
                {
                    "targets": 18,
                    "name": 'actions',
                    'sortable': false,
                    'searchable': false,
                    'width': '130px',
                    'visible': false
                },
            ],
            "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                // var grand_total = 0;
                // for (var i = 0; i < aaData.length; i++) {
                //     grand_total+= intVal(aaData[i][6]);
                // }
                // var nCells = nRow.getElementsByTagName('th');
                // nCells[6].innerHTML = '<div class="text-right bold">'+tnhFormatMoney(grand_total)+'</div>';
            }
        }
    );

    $('#table-tools_supplies').on('draw.dt', function() {})

    $(document).on('click', '.status-table li a', function(event) {
        return;
        status_table = $(this).attr('value');
        $('#status_table').val(status_table);
        oTable.draw();
    });

    $(document).on('click', '#agree', function(event) {
        event.preventDefault();
        index = this;
        outsource_id = $(this).attr('outsource_id');
        status = $(this).attr('value');
        $(index).attr('disabled', 'disabled');
        $('.po').popover('hide');
        if (outsource_id) {
            $.ajax({
                    url: site.base_url + 'admin/outsource/agree',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        outsource_id: outsource_id,
                        status: status
                    },
                })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        oTable.draw('false');
                    } else {
                        alert_float('danger', data.message);
                        oTable.draw('false');
                    }
                })
                .fail(function(data) {
                    alert_float('danger', 'errors');
                    $(index).removeAttr('disabled');
                })
        }
    });

    $(document).on('click', '.status-table li a', function(event) {
        status_table = $(this).attr('value');
        $('#status_table').val(status_table);
        oTable.draw();
    });
    $(document).on('change', '#supplier_search, #orders_search, #start_date_search, #end_date_search', function(
        event) {
        event.preventDefault();
        oTable.draw();
    });
});
</script>
<script type="text/javascript">
function payment_all() {
    if (empty($('#suppliers_id').val())) {
        alert("<?= _l('not_chose_ncc') ?>");
        return;
    }
    var ids = '';
    var rows = $('.DTFC_LeftBodyWrapper .table-outsource-new').find('tbody tr');
    $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input');
        if (checkbox.prop('checked') == true) {
            ids += checkbox.val() + ',';
        }
    });
    if (empty(ids)) {
        alert("<?= _l('ch_pay_total_all') ?>");
        return;
    } else {
        $('#payment_data').html('');
        dataString = {
            ids: ids,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?=admin_url()?>purchase_order/payment_all_outsource",
            data: dataString,
            cache: false,
            success: function(response) {
                $('#payment_data').html(response);
                $('#payment').modal({
                    show: true,
                    backdrop: 'static'
                });

                init_selectpicker();
                init_datepicker();
                $('#id_supplierss').val($('#suppliers_id').val());
            }
        });
        return false;
    }
}
$(document).ready(function() {
    var inner_popover_template =
        '<div class="popover" style="width:400px;"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';
    $(document).on('click', '.add_contact_person', function(e) {
        $('#suppliers_id').val('');
        $('#suppliers_id').change();
        $('.add_contact_person_invoice').popover('hide');
        var id = $(this).attr('data-id');
        var dropdown_menu = '\
            <?php
                echo render_select('id_suppliers', $suppliers, array('id','company'), 'ch_chose_suppliers');
                ?>\
            <button type="button" onclick="payment_all();return false;" class="btn btn-info btn-block mtop15"><?php echo _l('ch_submit_import'); ?></button>\
            </div>'
        $('select[name="id_suppliers"]').selectpicker('refresh');
        $(this).popover({
            html: true,
            container: 'body',
            placement: "bottom",
            trigger: 'click focus',
            // trigger: 'focus',
            title: '<?= _l('ch_pay_slip_total') ?><button type="button" class="close close_pay">&times;</button>',
            content: function() {
                return dropdown_menu;
            },
            template: inner_popover_template
        });
        $('#suppliers_id').selectpicker('refresh');
    });

    $(document).on('click', '.close_pay', function(e) {
        $('.add_contact_person').popover('hide');
        $('#suppliers_id').val('');
        $('#suppliers_id').change();
    });
});

$(document).on('change', '#id_suppliers', function() {
    $('#type_suppliert').val(1);
    $('#suppliers_id').val($('#id_suppliers').val());
    $('#suppliers_id').change();
});

$(document).on('change', '#suppliers_id', function() {
    oTable.draw();
});
$(document).on('change', '.items_material_id', function(event) {
    event.preventDefault();
    rowMaterial = $(this).closest('tr');
    counters = rowMaterial.find('#input').val();


    var currentQuantityInput = $(event.currentTarget);
    dataMaterial = currentQuantityInput.select2('data');
    console.log(dataMaterial);
    slMaterial = this;
    item_material_id = $(this).val();

    elTr = $('.check_item[value="' + item_material_id + '"]').closest('tr');
    if (elTr.length > 0) {
        alert_float('warning', 'Mặt hàng đã tồn tại !');
        rowMaterial.remove();
        $('.add-row').click();
        return;
    }

    td_detail = '';
    if (item_material_id) {


        if (dataMaterial.type == 'nvl') {
            td_detail = '<span class="label label-primary">Nguyên vật liệu</span><br>';
        } else if (dataMaterial.type == 'semi_products') {
            td_detail = '<span class="label label-warning">Bán thành phẩm</span><br>';
        }

        trMaterial = $(slMaterial).closest('tr');
        nameMaterial = dataMaterial.text;
        unitName = dataMaterial.unit_name;


        // trMaterial.find('.show_detail').html(td_detail);
        trMaterial.find('.td-item-name-material').html(nameMaterial);
        trMaterial.find('.td-unit-material').html(unitName);
        trMaterial.find('.td-type-material').html(td_detail);
        trMaterial.find('.quantity_material').val(tnhFormatNumber(0));
        rowMaterial.find('.check_item').val(item_material_id);
        rowMaterial.find('.locations').attr('required', true);

        getWarehousesLocation(counters, dataMaterial.id, dataMaterial.type);
        lastrow = $('#tb-tranfer-outsource tbody tr')[$('#tb-tranfer-outsource tbody tr').length - 1];
        if ($(lastrow).find('.items_material_id').select2('val')) {
            $('.add-row').click();
        }
    } else {
        trMaterial.find('.td-item-name-material').html('');
    }
    totalMaterial();
});
$(document).on('change', '.locations, .quantity_material', function(event) {
    totalMaterial();
});
</script>