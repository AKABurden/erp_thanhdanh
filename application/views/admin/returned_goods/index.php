<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
    table tr td {
        vertical-align: middle !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="pull-right mright5 H_border">
                <a class="btn btn-info test H_action_button btn-search-tnh" href="<?= base_url('admin/returned_goods/add') ?>"><?= lang('add') ?></a>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse in" aria-expanded="true" style="">
                    <div class="col-md-3">
                        <?= lang('tnh_reference_orders', 'orders_search') ?>
                        <input type="text" name="orders_search" id="orders_search" style="width: 100%;" data-placeholder="<?= lang('tnh_reference_orders') ?>" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('customers', 'customers') ?>
                        <input type="text" name="customer_search" data-placeholder="<?= lang('customers') ?>" id="customer_search" class="customer_search" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('start_date', 'start_date_search') ?>
                        <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('end_date', 'end_date_search') ?>
                        <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="">
                            <table id="table-returned-goods" class="table dt-tnh table-hover table-condensed table-returned-goods">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="checkbox mass_select_all_wrap text-center"><input type="checkbox" id="mass_select_all" data-to-table="returned-goods"><label for="mass_select_all"></label></div>
                                        </th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('tnh_reference_no_returned_goods') ?></th>
                                        <th><?= lang('customers') ?></th>
                                        <th><?= lang('tnh_reference_orders') ?></th>
                                        <th><?= lang('tnh_employees') ?></th>
                                        <th><?= lang('tnh_handling_solution') ?></th>
                                        <th><?= lang('tnh_grand_total') ?></th>
                                        <th><?= lang('tnh_created_by') ?></th>
                                        <th><?= lang('tnh_status') ?></th>
                                        <th><?= lang('tnh_user_agree') ?></th>
                                        <th><?= lang('tnh_note') ?></th>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('Duyệt kho trả hàng bán'); ?></span>
                </h4>
            </div>
            <?php echo form_open(admin_url('returned_goods/confirm_warehous/'), array('id' => 'warehouse-form')); ?>
            <div class="modal-body">
                <input type="text" class="hide" name="id_return" id="id_return">
                <div id="table_html"></div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-info" id="submit" autocomplete="off"><?= _l('submit') ?></button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?= _l('close') ?></button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        customer_search: "#customer_search",
        status_table: '#status_table',
        orders_search: '#orders_search',
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search'
    };
    var oTable = '';

    $(document).ready(function() {
        ajaxSelectParams('#orders_search', 'admin/orders/searchOrders', 0, true, true);
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);

        oTable = tnhDatatable(
            '#table-returned-goods', {
                'order': [
                    [1, 'desc'],
                    [2, 'desc']
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
                'sAjaxSource': '<?= site_url('admin/returned_goods/getReturnedGoods') ?>',
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
                "rowCallback": function(row, data) {},
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "columnDefs": [{
                        "render": function(data, type, row) {
                            return '<div class="checkbox"><input type="checkbox" name="returned_goods_id[]" id="check-item' + data + '" value="' + data + '"><label for="check-item' + data + '"></label></div>';
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
                            <a data-tnh="modal" class="tnh-modal" href="' + site.base_url + 'admin/returned_goods/view_returned_goods/' + row[0] + '" data-toggle="modal" data-target="#myModal">' + data + '</a>\
                            </div><div class="td-reference-no"></div>';
                        },
                        "targets": 2,
                        "name": 'reference_no',
                        'width': '150px'
                    },
                    {
                        "targets": 3,
                        "name": 'customer_name',
                        'width': '150px',
                    },
                    {
                        "targets": 4,
                        "name": 'reference_order',
                        'width': '120px'
                    },
                    {
                        "targets": 5,
                        "name": 'employee',
                        'width': '120px'
                    },
                    {
                        "render": function(data, type, row) {
                            var str = '';
                            if (data == "debt_reduction") {
                                str = '<span class="label btn-primary"><?= lang('tnh_debt_reduction') ?></span>';
                            } else if (data == "pay_down") {
                                str = '<span class="label btn-danger"><?= lang('tnh_pay_down') ?></span>';
                            }
                            return str;
                        },
                        "targets": 6,
                        "name": 'handling_solution',
                        'width': '120px'
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
                        "targets": 8,
                        "name": 'created_by',
                        'width': '100px'
                    },
                    {
                        "render": function(data, type, row) {
                            if (data == 0) {
                                return '<a href="javascript:void(0)" onclick="confirm_warehous(' + row[0] + ')" class=" btn btn-info btn-icon " data-toggle="tooltip" data-loading-text="Xin vui lòng đợi..." data-original-title="Thủ kho duyệt"><i class="fa  fa-square-o"></i> Chưa duyệt kho</a>';
                            }
                            var str = '';
                            if (row[10]) {
                                str = '<div class="mtop5">' + row[10] + '</div>';
                            }
                            if (row[13] > 0) {
                                return '<a href="javascript:void(0)" class=" btn btn-warning btn-icon " data-toggle="tooltip"  data-original-title="Thủ kho duyệt"> Đã có xuất kho</a>' + str;
                            }
                            return '<a href="javascript:void(0)" onclick="can_confirm_warehous(' + row[0] + ', ' + data + ')" class=" btn btn-danger btn-icon " data-toggle="tooltip" data-loading-text="Xin vui lòng đợi..." data-original-title="Thủ kho duyệt"><i class="fa  fa-check-square-o"></i> Đã duyệt kho</a>' + str;
                            // return data;
                        },
                        "targets": 9,
                        "name": 'status',
                        'width': '100px'
                    },
                    {
                        "targets": 10,
                        "name": 'user_status',
                        'visible': false
                    },
                    {
                        "targets": 11,
                        "name": 'note',
                        'width': '100px'
                    },
                    {
                        "targets": 12,
                        "name": 'actions',
                        'sortable': false,
                        'searchable': false,
                        'width': '150px'
                    },
                ],
                "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                    // var grand_total = 0;
                    // for (var i = 0; i < aaData.length; i++) {
                    //     grand_total+= intVal(aaData[i][5]);
                    // }
                    // var nCells = nRow.getElementsByTagName('th');
                    // nCells[5].innerHTML = '<div class="text-right bold">'+tnhFormatMoney(grand_total)+'</div>';
                }
            }
        );

        $(document).on('click', '.btn-dt-reload', function(event) {
            oTable.draw('page');
        });

        $(document).on('change', '#customer_search, #orders_search, #start_date_search, #end_date_search', function(event) {
            event.preventDefault();
            oTable.draw();
        });

        $(document).on('click', '#agree', function(event) {
            event.preventDefault();
            index = this;
            returned_goods_id = $(this).attr('returned_goods_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (returned_goods_id) {
                $.ajax({
                        url: site.base_url + 'admin/returned_goods/agree',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                            returned_goods_id: returned_goods_id,
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
    });

    function validate_form() {
        _validate_form($('#warehouse-form'), {}, confirm_warehous_s);
    }

    function confirm_warehous_s(form) {
        var data = $(form).serialize(),
            action = form.action;
        return $.post(action, data).done(function(form) {
            form = JSON.parse(form),
                alert_float(form.alert_type, form.message);
            oTable.draw('page');
            $('#confirm_warehous').modal('hide');

        }), !1
    }

    function can_confirm_warehous(id, warehouseman_id) {
        dataString = {
            id: id,
            warehouseman_id: warehouseman_id,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>returned_goods/can_confirm_warehous",
            data: dataString,
            cache: false,
            success: function(response) {
                response = JSON.parse(response);
                alert_float(response.alert_type, response.message);
                oTable.draw('page');
            }
        });
        return false;
    }
    $('body').on('hidden.bs.modal', '#confirm_warehous', function() {
        oTable.draw('page');
    });
    function formatNumber_new(nStr, decSeperate=".", groupSeperate=",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        x2=x2.substr(0,2);
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    };
    function confirm_warehous(id) {
        dataString = {
            id: id,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>returned_goods/get_items_returned_goods",
            data: dataString,
            cache: false,
            success: function(response) {
                response = JSON.parse(response);
                $('#id_return').val(id);
                var html = '<table id="view-enquiry_ch" class="table dt-tnh table-hover table-bordered table-condensed table-export-warehouses-new" style="width:100%">\
                            <thead>\
                                <tr>\
                                    <th style="width:40%" class="text-center"><?= lang('tnh_items') ?></th>\
                                    <th style="width:10%" class="text-center"><?= lang('invoice_table_quantity_heading') ?></th>\
                                    <th style="width:10%" class="text-center"><?= lang('tnh_quantity_loss') ?></th>\
                                    <th style="width:10%" class="text-center"><?= lang('tnh_sample_quantity') ?></th>\
                                    <th style="width:25%" class="text-center"><?= lang('tblwarehouse') ?></th>\
                                    <th style="width:35%" class="text-center"><?= lang('tbllocaltion_warehouses') ?></th>\
                                </tr>\
                            </thead>\
                            <tbody>';
                $.each(response.returned_goods, function(key, value) {
                    html += '<tr>\
                                        <td style="width:28%"><input class="hide" type="text" name="items[' + key + '][id]" value="' + value.id + '">' + value.item_name + '(' + value.item_code + ')</td>\
                                        <td style="width:10%" class="text-center">' + formatNumber_new(value.quantity) + '</td>\
                                        <td style="width:10%" class="text-center">' + formatNumber_new(value.quantity_loss) + '</td>\
                                        <td style="width:10%" class="text-center">' + formatNumber_new(value.quantity_sample) + '</td>\
                                        <td style="width:17%" class="warehouse"></td>\
                                        <td style="width:25%"  class="text-center"><div class="form-group " style="width: 100%">\
                                             <select  required="true" name="items[' + key + '][localtion_to]" data-placeholder="<?= _l('choose') ?>" id="warehouse_to_' + key + '" class="warehouse_to_to" style="width: 100%;">' + value.local + '</select>\
                                        </div></td>\
                                    </tr>';

                });

                html += '</tbody>\
                        </table>';
                $('#confirm_warehous').modal('show');
                $('#table_html').html(html);
                $.each(response.returned_goods, function(key, value) {
                    $('#warehouse_to_' + key).select2();
                    $('#location_in_stock_' + key).select2({
                        width: 'resolve'
                    });

                });
                validate_form();
                dtItems = $('#view-enquiry_ch').DataTable({

                    "language": app.lang.datatables,
                    "pageLength": app.options.tables_pagination_limit,
                    "lengthMenu": [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "<?= lang('all') ?>"]
                    ],
                    scrollY: '200px',
                    scrollX: true,
                    // fixedColumns:   {
                    //     leftColumns: 4,
                    //     rightColumns: 0
                    // },
                    'searching': false,
                    'ordering': false,
                    'paging': false,
                    pageLength: 100,
                    // "info": false,
                    'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
                    "initComplete": function(settings, json) {
                        var t = this;
                        t.parents('.table-loading').removeClass('table-loading');
                        t.removeClass('dt-table-loading');
                    },
                    "footerCallback": function(row, data, start, end, display) {

                    }
                });
                setTimeout(function() {
                    dtItems.draw('page');
                }, 150);
            }
        });
        return false;
    }
    $(document).on('change', '.warehouse_to_to', (e) => {
        currentQuantityInput = $(e.currentTarget);
        currentQuantityInput.parents('tr').find('td.warehouse').html(currentQuantityInput.find("option:selected").attr("data-text"));
        dataString = {
            id: currentQuantityInput.val(),
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>returned_goods/get_location_in_stock_returned_goods",
            data: dataString,
            cache: false,
            success: function(response) {
                response = JSON.parse(response);
                currentQuantityInput.parents('tr').find('select.location_in_stock').html(response.location_in_stock);

            }
        });

    });
</script>