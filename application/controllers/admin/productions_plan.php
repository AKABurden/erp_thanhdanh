<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
table tr td {
    vertical-align: middle !important;
}
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=2.5') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <?php if ($this->perAddProductionsPlan): ?>
            <a href="<?= base_url('admin/manufactures/add_productions_plan') ?>"
                class="btn btn-info mright5 pull-right H_action_button">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('add'); ?>
            </a>
            <?php endif ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse in" aria-expanded="true">
                    <div class="col-md-2">
                        <?= lang('tnh_reference_orders', 'orders_search') ?>
                        <input type="text" name="orders_search" id="orders_search" style="width: 100%;"
                            data-placeholder="<?= lang('tnh_reference_orders') ?>" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('tnh_business_plan', 'business_plan_search') ?>
                        <input type="text" name="business_plan_search"
                            data-placeholder="<?= lang('tnh_business_plan') ?>" id="business_plan_search"
                            class="business_plan_search" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('productions_orders', 'productions_orders_search') ?>
                        <input type="text" name="productions_orders_search" id="productions_orders_search" class="productions_orders_search" value="" style="width: 100%;" data-placeholder="<?= lang('productions_orders') ?>">
                    </div>
                    <div class="col-md-2">
                    <?= lang('products', 'products_search') ?>
                        <input type="text" name="products_search" id="products_search" class="" style="width: 100%;" value="" data-placeholder="<?= lang('products') ?>">
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
                        <div role="tabpanel">
                            <ul class="nav nav-tabs status-table" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#all" aria-controls="all" role="tab" value=""
                                        data-toggle="tab"><?= lang('all') ?><span class="count_all"></span></a>
                                </li>
                                <?php foreach (status_productions_plan() as $key => $value): ?>
                                <li role="presentation">
                                    <a href="#<?= $key ?>" aria-controls="plan" role="<?= $key ?>" value="<?= $key ?>"
                                        data-toggle="tab"><?= $value ?><span class="count_<?= $key ?>"></span></a>
                                </li>
                                <?php endforeach ?>
                            </ul>
                            <input type="hidden" name="status_table" id="status_table" class="form-control status_table"
                                value="">
                        </div>
                        <div class="">
                            <table id="table-productions-plan"
                                class="table dt-tnh table-hover table-condensed table-productions-plan"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th><?= lang('id') ?></th>
                                        <th><?= lang('tnh_numbers') ?></th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('tnh_reference_productions_plan') ?></th>
                                        <th><?= lang('productions_orders') ?></th>
                                        <th><?= lang('tnh_planning_cycle') ?></th>
                                        <th><?= lang('tnh_orders') ?>/<?= lang('tnh_business_plan') ?></th>
                                        <th><?= lang('tnh_lt') ?></th>
                                        <th><?= lang('tnh_status') ?></th>
                                        <th><?= lang('note') ?></th>
                                        <th><?= lang('tnh_items') ?></th>
                                        <th><?= lang('tnh_status_manufactures') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>
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
<?php echo form_close(); ?>
<?php init_tail(); ?>

<?php $this->load->view('loader')?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
var fnserverparams = {
    status_table: '#status_table',
    productions_orders_search: '#productions_orders_search',
    orders_search: '#orders_search',
    business_plan_search: '#business_plan_search',
    start_date: '#start_date',
    end_date: '#end_date',
    products_search: '#products_search'
};
var oTable = '';
</script>
<script type="text/javascript">

function loadItemsProductionsPlan(cData) {
    return cData[10];
}

$(document).ready(function() {
    ajaxSelectParams('#orders_search', 'admin/orders/searchOrders', 0, true, true);
    ajaxSelectParams('#business_plan_search', 'admin/business_plan/searchBusinessPlan', 0, true, true);
    ajaxSelectParams('#products_search', 'admin/products/searchProductsSelect2', 0, true, true);
    ajaxSelectParamsCallback('#productions_orders_search', 'admin/manufactures/searchProductionsOrders', 0, false, true);

    oTable = tnhDatatable(
        '#table-productions-plan', {
            'order': [
                [2, 'desc']
            ],
            'orderCellsTop': true,
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            "processing": true,
            "responsive": true,
            'fixedHeader': {
                header: true,
            },
            autoWidth: true,
            "serverSide": true,
            'sAjaxSource': '<?= site_url('admin/manufactures/getProductionsPlan') ?>',
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
                    'success': function(response) {
                        $('.count_all').html('(' + tnhFormatNumber(response.count_all) +
                            ')');
                        $('.count_keep').html('(' + tnhFormatNumber(response.count_keep) +
                            ')');
                        $('.count_ycmh').html('(' + tnhFormatNumber(response.count_ycmh) +
                            ')');
                        fnCallback(response);
                    }
                });
            },
            "drawCallback": function(settings, nRow) {},
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {
                stProductionPlan = aData[9];
                if (stProductionPlan != 'approved') {
                    $(nRow).find('.tnh-created-productions-capacity').addClass('tnh-disabled');
                }
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                mainWrapperHeightFix();
            },
            "footerCallback": function(tfoot, data, start, end, display) {},
            "columnDefs": [{
                    "targets": 0,
                    "name": 'id',
                    'visible': false
                },
                {
                    "targets": 1,
                    "name": 'number_records',
                    'className': 'text-center',
                    'sortable': false,
                    'width': '50px'
                },
                {
                    "render": function(data, type, row) {
                        return fld(data);
                    },
                    "targets": 2,
                    "name": 'date',
                    'searchable': false,
                    'width': '80px'
                },
                {
                    "render": function(data, type, row) {
                        if (!data) return '';
                        data = data.split('___');
                        return '<a class="tnh-modal" data-tnh="modal" href="' + site.base_url +
                            'admin/manufactures/view_productions_plan/' + row[0] +
                            '" data-toggle="modal" data-target="#myModal">' + data[0] +
                            '</a><div class="italic">' + data[1] + '</div>';
                    },
                    "targets": 3,
                    "name": 'reference_no',
                    'width': '140px'
                },
                {
                    "targets": 4,
                    "name": 'productions_orders',
                    'width': '150px'
                },
                {
                    "targets": 5,
                    "name": 'planning_cycle',
                    'width': '150px'
                },
                {
                    "render": function(data, type, row) {
                        return data;
                    },
                    "targets": 6,
                    "name": 'pb',
                    'width': '250px',
                    'visible': false
                },
                {
                    "render": function(data, type, row) {
                        str = '';
                        if (data) {
                            data = data.split('-');
                            if (data[0] == 1) {
                                str +=
                                    '<span class="label label-primary"><?= lang('tnh_sales_orders') ?></span>';
                            }
                            if (data[0] == 1 && data[1] == 1) {
                                str += '</br></br>';
                            }
                            if (data[1] == 1) {
                                str +=
                                    '<span class="label label-warning"><?= lang('tnh_business_plan') ?></span>';
                            }
                        }

                        if (!str) {
                            str +=
                                '<span class="label label-danger"><?= lang('HĐSX bán thành phẩm') ?></span>';
                        }
                        if (row[6]) {
                            db = row[6].split('custom');
                            titletoolTip = row[6].replace(/custom/g, "</br>");
                            countData = db.length;
                            str += `<div class="mtop10">
                                    <span data-toggle="tooltip" style="cursor: pointer;" data-html="true" title="${titletoolTip}" class="label label-success">${countData} <?= lang('tnh_single') ?></span>
                                </div>`;
                        }


                        return str;
                    },
                    "targets": 7,
                    "name": 'options',
                    'width': '130px'
                },
                {
                    "render": function(data, type, row) {
                        data = data.split('__');
                        if (data[0] > 0) {
                            imagesKeepWarehouses =
                                `<a class="tnh-modal" style="position: relative;" data-tnh="modal" href="${site.base_url}admin/manufactures/view_keep_stock_material/${row[0]}" data-toggle="modal" data-target="#myModal"><img class="images-status" src="${site.base_url+'assets/images/pallet_1.png'}"><span class="label icon-total-cs bg-warning">${data[0]}</span><a>`;

                        } else {
                            imagesKeepWarehouses =
                                `<a href="javascript:void(0)"><img class="images-status" src="${site.base_url+'assets/images/pallet_4.png'}"></a>`;
                        }

                        if (data[1] > 0) {
                            imagesPurchases =
                                `<a class="tnh-modal" style="position: relative;" data-tnh="modal" href="${site.base_url}admin/manufactures/view_purchases/${row[0]}" data-toggle="modal" data-target="#myModal"><img class="images-status" src="${site.base_url+'assets/images/shopping-bag_1.png'}"><span class="label icon-total-cs bg-warning">${data[1]}</span><a>`;
                        } else {
                            imagesPurchases =
                                `<a href="javascript:void(0)"><img class="images-status" src="${site.base_url+'assets/images/shopping-bag_4.png'}"></a>`;

                        }

                        // <div data-toggle="tooltip" class="pull-left pointer status-icon-text" title="<?= ''//lang('tnh_ycmh') ?>">${imagesPurchases}<span><?= ''//lang('tnh_ycmh') ?></span></div>
                        htmlStatus = `<div data-toggle="tooltip" class="pull-left pointer status-icon-text mright25" title="<?= lang('tnh_keep_stock') ?>">${imagesKeepWarehouses}<span><?= lang('tnh_keep_stock') ?></span></div>
                            
                            `;
                        return row[11]+htmlStatus;
                    },
                    "targets": 8,
                    "name": 'status',
                    'width': '160px'
                },
                {
                    "targets": 9,
                    "name": 'note',
                    'width': '130px'
                },
                {
                    "targets": 10,
                    "name": 'items',
                    'searchable': false,
                    'sortable': false,
                    'visible': false,
                    'width': '130px'
                },
                {"targets": 11, "name": 'status_manufactures', 'visible': false, 'searchable': false, 'sortable': false},
                {
                    "targets": 12,
                    "name": 'actions',
                    'searchable': false,
                    'sortable': false,
                    'width': '80px'
                }
            ]
        }
    );

    $(document).on('change', '#orders_search, #business_plan_search, #start_date, #end_date, #products_search, #productions_orders_search', function(event) {
        oTable.draw();
    });

    $('#table-productions-plan').on('draw.dt', function(e, settings) {})

    $(document).on('click', '.export-excel', function(event) {
        event.preventDefault();
        productions_plan_id = $(this).attr('value');
        bootbox.confirm({
            message: '<?= lang('tnh_you_want_to_export_excel') ?>',
            buttons: {
                confirm: {
                    label: '<?= lang('yes') ?>',
                    className: 'btn-success'
                },
                cancel: {
                    label: '<?= lang('no') ?>',
                    className: 'btn-danger'
                }
            },
            callback: function(result) {
                if (result) {
                    if (productions_plan_id) {
                        $.ajax({
                                url: site.base_url +
                                    'admin/manufactures/export_excel_production_plan',
                                type: 'POST',
                                dataType: 'JSON',
                                data: {
                                    productions_plan_id: productions_plan_id,
                                    export_excel: 1,
                                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>"
                                },
                            })
                            .done(function(data) {
                                if (data.result) {
                                    alert_float('success', data.message);
                                    download(data.filename, data.file);
                                } else {
                                    alert_float('danger', data.message);
                                }
                            })
                            .fail(function() {
                                alert_float('danger', 'errors');
                            });
                    }
                }
            }
        });
    });

    $(document).on('click', '#agree', function(event) {
        event.preventDefault();
        index = this;
        productions_plan_id = $(this).attr('productions_plan_id');
        status = $(this).attr('value');
        $(index).attr('disabled', 'disabled');
        $('.po').popover('hide');
        if (productions_plan_id) {
            $.ajax({
                    url: site.base_url + 'admin/manufactures/agreeProductionsPlan',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        productions_plan_id: productions_plan_id,
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

    $(document).on('click', '#agree-procedure', function(event) {
        event.preventDefault();
        index = this;
        productions_plan_id = $(this).attr('productions_plan_id');
        status = $(this).attr('value');
        $(index).attr('disabled', 'disabled');
        $('.po').popover('hide');
        if (productions_plan_id) {
            $.ajax({
                    url: site.base_url + 'admin/manufactures/agreeProductionsPlanProcedure',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        productions_plan_id: productions_plan_id,
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

    $('#table-productions-plan').on('click', 'td .rows-child', function() {
        var tr = $(this).closest('tr');
        var row = oTable.row(tr);
        if (row.child.isShown()) {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            $(this).removeClass('fa-caret-right');
            $(this).addClass('fa-caret-down');
            row.child( loadItemsProductionsPlan(row.data()) ).show();
            tr.addClass('shown');
        }
    });

    $('#table-productions-plan').on('draw.dt', function() {
        $('.rows-child').click();
    });
});
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>
<script>
$(document).ready(function() {
    fixedModal('#table-view-semi-products', false, 200, 265);
});
</script>