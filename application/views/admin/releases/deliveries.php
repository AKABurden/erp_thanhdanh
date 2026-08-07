<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
    table tr td {
        vertical-align: middle !important;
    }

    <?php if (!$this->perPriceDeliveries): ?>.view-delivery tr th:nth-child(8),
    .view-delivery tr td:nth-child(8) {
        display: none;
    }

    <?php endif ?>
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="dropdown pull-right">
                <button class="btn btn-info pull-right H_action_button dropdown-toggle nav-link" type="button"
                    id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                    <?= lang('actions') ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 200px;">
                    <li>
                        <a class="test btn-search-tnh" data-toggle="collapse" data-target="#search-tnh"
                            aria-expanded="true"><i class="fa fa-filter"></i> <?= lang('tnh_seach_statistical') ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="content view-delivery">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse in" aria-expanded="true" style="">
                    <div class="col-md-2">
                        <?= lang('tnh_reference_orders', 'orders_search') ?>
                        <input type="text" name="orders_search" id="orders_search" style="width: 100%;"
                            data-placeholder="<?= lang('tnh_reference_orders') ?>" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('customers', 'customers') ?>
                        <input type="text" name="customer_search" data-placeholder="<?= lang('customers') ?>"
                            id="customer_search" class="customer_search" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('Thành phẩm', 'items_search') ?>
                        <input type="text" name="items_search" id="items_search" class="items_search"
                            style="width: 100%;" data-placeholder="Thành phẩm" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('start_date', 'start_date_search') ?>
                        <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>"
                            id="start_date_search" class="start_date_search datepicker form-control"
                            style="width: 100%;" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('end_date', 'end_date_search') ?>
                        <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>"
                            id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;"
                            value="">
                    </div>
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
                                    <li role="presentation">
                                        <a href="#un_approved" aria-controls="un_approved" role="tab"
                                            value="status_undelivery"
                                            data-toggle="tab"><?= lang('tnh_status_undelivery') ?>
                                            (<span><?= $status_undelivery ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#approved" aria-controls="approved" role="tab" value="status_delivery"
                                            data-toggle="tab"><?= lang('tnh_status_delivery') ?>
                                            (<span><?= $status_delivery ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#received_certificate" aria-controls="received_certificate" role="tab"
                                            value="received_certificate"
                                            data-toggle="tab"><?= lang('Đã nhận chứng từ') ?>
                                            (<span><?= $received_certificate ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#not_received_certificate" aria-controls="not_received_certificate"
                                            role="tab" value="not_received_certificate"
                                            data-toggle="tab"><?= lang('Chưa nhận chứng từ') ?>
                                            (<span><?= $not_received_certificate ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#invoice" aria-controls="invoice"
                                            role="tab" value="invoice"
                                            data-toggle="tab"><?= lang('Đã xuất hóa đơn') ?>
                                            (<span><?= $invoice ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#not_invoice" aria-controls="not_invoice"
                                            role="tab" value="not_invoice"
                                            data-toggle="tab"><?= lang('Chưa xuất hóa đơn') ?>
                                            (<span><?= $not_invoice ?></span>)</a>
                                    </li>
                                    <li role="presentation" class="active">
                                        <a href="#all" aria-controls="all" role="tab" value="all"
                                            data-toggle="tab"><?= lang('all') ?>(<span><?= $all ?></span>)</a>
                                    </li>
                                </ul>
                                <input type="hidden" name="status_table" id="status_table"
                                    class="form-control status_table" value="all">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="table-deliveries"
                                class="table dt-tnh table-hover table-condensed table-deliveries-new" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="checkbox mass_select_all_wrap text-center">
                                                <input type="checkbox" id="mass_select_all" data-to-table="deliveries-new"><label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('tnh_reference_deliveries') ?></th>
                                        <th><?= lang('customers') ?></th>
                                        <th><?= lang('tnh_address_delivery') ?></th>
                                        <th><?= lang('Hóa đơn') ?></th>
                                        <th><?= lang('tnh_reference_orders') ?></th>
                                        <th><?= lang('Tổng tiền') ?></th>
                                        <th><?= lang('Tổng tiền(VND)') ?></th>
                                        <th><?= lang('tnh_created_by') ?></th>
                                        <th><?= lang('tnh_status') ?></th>
                                        <th><?= lang('Đã nhận chứng từ') ?></th>
                                        <th><?= lang('note') ?></th>
                                        <th><?= lang('tnh_user_agree') ?></th>
                                        <th><?= lang('tnh_view') ?></th>
                                        <th><?= lang('id_branch') ?></th>
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
<div class="modal fade" id="confirm_warehous" role="dialog">
    <div class="modal-dialog modal-lm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('ch_export_quantity_missing'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div id="table_html"></div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?= _l('close') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    // var site = <?= json_encode(array('base_url' => base_url())) ?>;
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        customer_search: "#customer_search",
        status_table: '#status_table',
        orders_search: '#orders_search',
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
        items_search: '#items_search',
    };
    var oTable = '';

    $(document).ready(function() {
        ajaxSelectParams('#orders_search', 'admin/orders/searchOrders', 0, true, true);
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);
        ajaxSelectParams($('#items_search'), 'admin/products/searchProductAndGoodsMaterials', 0, true, true);
        oTable = tnhDatatable(
            '#table-deliveries', {
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
                // scrollY: "450px",
                // "dom": '<"wrapper"flipt>',
                fixedColumns: {
                    leftColumns: 3,
                    rightColumns: 1
                },
                scrollY: height_body,
                scrollX: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/releases/getDeliveries') ?>',
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
                "rowCallback": function(row, data) {

                    customer = data[3].split('__');
                    is_separate_guest = customer[1];

                    st = data[10];
                    if (st == "approved") {
                        $(row).find('.ews').addClass('tnh-disabled');
                    }
                    ckV = data[13];
                    if (ckV > 0) {
                        $(row).find('.td-reference-no').html('<span class="wap-new">new</span>');
                    }

                    if (is_separate_guest == 1) {
                        $(row).find('.print_size').removeClass('hide');
                    } else {
                        $(row).find('.print_size').addClass('hide');
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
                            return '<div class="checkbox"><input type="checkbox" name="order_id[]" id="check-item' + data + '" value="' + data + '"><label for="check-item' + data + '"></label></div>';
                        },
                        "targets": 0,
                        "name": 'id',
                        'orderable': false,
                        'width': '40px'
                    },
                    {
                        "render": function(data, type, row) {
                            return fld(data);
                        },
                        "targets": 1,
                        "name": 'date',
                        'width': '100px',
                        'searchable': false
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div style="min-width: 150px;" class="">\
                            <a data-tnh="modal" class="tnh-modal" href="' + site.base_url + 'admin/releases/view_delivery/' + row[0] + '" data-toggle="modal" data-target="#myModal">' + data + '</a>\
                            </div><div class="td-reference-no"></div>';
                        },
                        "targets": 2,
                        "name": 'reference_no',
                        'width': '120px'
                    },
                    {
                        "render": function(data, type, row) {
                            data = data.split('__');
                            return data[0];
                        },
                        "targets": 3,
                        "name": 'customer_name',
                        'width': '120px',
                        'sortable': false,
                        'searchable': false,
                    },
                    {
                        "targets": 4,
                        "name": 'address_delivery',
                        'width': '120px',
                        'sortable': false,
                        'searchable': false,
                    },
                    {
                        "targets": 5,
                        "name": 'reference_invoice',
                        'width': '100px',
                        'sortable': false,
                        'searchable': false,
                    },
                    {
                        "targets": 6,
                        "name": 'reference_orders',
                        'width': '120px',
                        'sortable': false,
                        'searchable': false,
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                        },
                        "targets": 7,
                        "name": 'grand_total',
                        'width': '100px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                        },
                        "targets": 8,
                        "name": 'grand_total',
                        'width': '100px'
                    },
                    {
                        "targets": 9,
                        "name": 'created_by',
                        'width': '120px',
                        'visible': false,
                        'searchable': false
                    },
                    {
                        "render": function(data, type, row) {
                            if (data == 0) {
                                return '<a href="javascript:void(0)" onclick="confirm_warehous(' + row[0] + ')" class=" btn btn-info btn-icon " data-toggle="tooltip" data-loading-text="Xin vui lòng đợi..." data-original-title="Thủ kho duyệt"><i class="fa  fa-square-o"></i> Chưa duyệt kho</a>';
                            }
                            return '<a href="javascript:void(0)" onclick="confirm_warehous(' + row[0] + ', ' + data + ')" class=" btn btn-danger btn-icon " data-toggle="tooltip" data-loading-text="Xin vui lòng đợi..." data-original-title="Thủ kho duyệt"><i class="fa  fa-check-square-o"></i> Đã duyệt kho</a>';
                            // str = '';
                            // order_id = row[0];
                            // if (data == "approved") {
                            //     user_status = '<div class="mtop10"><?= lang('tnh_user_agree') ?>: '+row[10]+'</div>';
                            // } else {
                            //     user_status = '';
                            // }
                            // if (data == "un_approved") {
                            //     str = '<span class="label label-danger"><?= lang('tnh_status_undelivery') ?></span>';
                            // } else if (data == "approved") {
                            //     str = '<span class="label label-success"><?= lang('tnh_status_delivery') ?></span>';
                            // }

                            // return str;
                        },
                        "targets": 10,
                        "name": 'status',
                        'width': '100px'
                    },
                    {
                        "render": function(data, type, row) {
                            // <i class="fa fa-check-square-o" aria-hidden="true"></i>
                            <?php if (has_permission('releases_deliveries', '', 'approve_accept')) { ?>
                                var check = row[11] == 1 ? 'checked' : '';
                                return `<div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" ${check} class="received_certificate" id="received_certificate_${row[0]}" data-id="${row[0]}" value="1">
                                                    <label for="received_certificate_${row[0]}"></label>
                                            </div>
                                        </div>`;
                            <?php } else { ?>
                                if (row[11] == 1) {
                                    return `<i class="fa fa-check-square-o" style="font-size: 20px" aria-hidden="true"></i>`;
                                } else {
                                    return `<i class="fa fa-square-o" style="font-size: 20px" aria-hidden="true"></i>`;
                                }
                            <?php } ?>
                        },
                        "targets": 11,
                        "name": 'status',
                        'width': '100px'
                    },
                    {
                        "targets": 12,
                        "name": 'note',
                        'width': '100px'
                    },
                    {
                        "targets": 13,
                        "name": 'user_status',
                        'visible': false,
                        'visible': false,
                        'searchable': false
                    },
                    {
                        "targets": 14,
                        "name": 'list_users',
                        'visible': false,
                        'searchable': false
                    },
                    {
                        "targets": 15,
                        "name": 'name_branch',
                        'searchable': false,
                        'width': '100px'
                    },
                    {
                        "targets": 16,
                        "name": 'actions',
                        'sortable': false,
                        'searchable': false,
                        'width': '295px'
                    },
                ],
                "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                    var grand_total = 0;
                    var grand_total_vnd = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        grand_total += intVal(aaData[i][7]);
                        grand_total_vnd += intVal(aaData[i][8]);
                    }
                    var nCells = nRow.getElementsByTagName('th');
                    nCells[7].innerHTML = '<div class="text-right bold">' + tnhFormatMoney(grand_total) + '</div>';
                    nCells[8].innerHTML = '<div class="text-right bold">' + tnhFormatMoney(grand_total_vnd) + '</div>';
                }
            }
        );

        $('#table-tools_supplies').on('draw.dt', function() {})

        $(document).on('change', '#customer_search, #orders_search, #start_date_search, #end_date_search, #items_search', function(event) {
            event.preventDefault();
            oTable.draw();
        });

        // $(document).on('click', '.btn-dt-reload', function (event) {
        //     oTable.draw();
        // });

        $(document).on('click', '.status-table li a', function(event) {
            return;
            status_table = $(this).attr('value');
            $('#status_table').val(status_table);
            oTable.draw();
        });

        $(document).on('change', '.received_certificate', function() {
            var kt_received_certificate = $(this).prop('checked');
            if (kt_received_certificate) {
                received_certificate = 1;
            } else {
                received_certificate = 0;
            }
            var id = $(this).data('id')
            $.get(admin_url + 'releases/update_received_certificate/' + id + '/' + received_certificate, function(result) {
                result = JSON.parse(result);
                alert_float(result.alert_type, result.message);
            }).fail(function(error) {
                var response = JSON.parse(error.responseText);
                alert_float('danger', response.message);
            })
        })

        $(document).on('click', '#agree', function(event) {
            return;
            event.preventDefault();
            index = this;
            order_id = $(this).attr('order_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (order_id) {
                $.ajax({
                        url: site.base_url + 'admin/deliveries/agree',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                            order_id: order_id,
                            status: status
                        },
                    })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            oTable.draw('page');
                        } else {
                            alert_float('danger', data.message);
                            oTable.draw('page');
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
    });

    function confirm_warehous(id, warehouseman_id) {
        dataString = {
            id: id,
            warehouseman_id: warehouseman_id,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>releases/confirm_warehous_deleveries",
            data: dataString,
            cache: false,
            success: function(response) {
                response = JSON.parse(response);
                oTable.draw('page');
                if (response.success == false) {
                    alert_float(response.message.alert_type, response.message.message);
                    var html = '<table class="table dt-tnh table-hover table-bordered table-condensed table-export-warehouses-new">\
                            <thead>\
                                <tr>\
                                    <th class="text-center"><?= lang('tnh_items') ?></th>\
                                    <th class="text-center"><?= lang('custom_field_add_edit_type') ?></th>\
                                    <th class="text-center"><?= lang('tblwarehouse') ?></th>\
                                    <th class="text-center"><?= lang('ch_quantity_missing') ?></th>\
                                </tr>\
                            </thead>\
                            <tbody>';
                    $.each(response.item, function(key, value) {
                        html += '<tr>\
                                    <th>' + value.item_name + '(' + value.item_code + ')</th>\
                                    <th class="text-center">' + value.type + '</th>\
                                    <th class="text-center">' + value.name_ware + '</th>\
                                    <th class="text-center">' + tnhFormatNumber(value.quantity_net) + '</th>\
                                </tr>';
                    });
                    html += '</tbody>\
                        </table>';
                    $('#confirm_warehous').modal('show');
                    $('#table_html').html(html);
                } else {
                    alert_float(response.alert_type, response.message);
                }
            }
        });
        return false;
    }
</script>