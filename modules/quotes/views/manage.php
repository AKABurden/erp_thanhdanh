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
            <?php if ($this->perAddQuotes): ?>
                <div class="pull-right mright5 H_border">
                    <a href="<?= base_url('admin/quotes/add') ?>" class="btn btn-info H_action_button">
                        <?php echo _l('add'); ?>
                    </a>
                </div>
            <?php endif ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse in" aria-expanded="true">
                    <div class="col-md-2">
                        <?= lang('tnh_reference_no_quote', 'quotes_search') ?>
                        <input type="text" name="quotes_search" id="quotes_search" style="width: 100%;" data-placeholder="<?= lang('tnh_reference_no_quote') ?>" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('customers', 'customers_search') ?>
                        <input type="text" name="customers_search" id="customers_search" style="width: 100%;" data-placeholder="<?= lang('customers') ?>" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('products', 'products_search') ?>
                        <input type="text" name="products_search" id="products_search" data-placeholder="<?= lang('products') ?>" class="products_search" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('start_date', 'start_date_search') ?>
                        <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-2">
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
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                    <li role="presentation">
                                        <a href="#un_approved" aria-controls="un_approved" role="tab" value="un_approved" data-toggle="tab"><?= lang('tnh_un_approved') ?>(<span class="count-un_approved"><?= $un_approved ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#approved" aria-controls="approved" role="tab" value="approved" data-toggle="tab"><?= lang('tnh_approved') ?>(<span class="count-approved"><?= $approved ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#approved" aria-controls="cancel" role="tab" value="cancel" data-toggle="tab"><?= lang('Không đạt') ?>(<span class="count-approved"><?= $cancel ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#approved" aria-controls="approved" role="tab" value="un_created_an_order" data-toggle="tab"><?= lang('tnh_un_created_an_order') ?>(<span class="count-un_created_an_order"><?= $un_created_an_order ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#approved" aria-controls="approved" role="tab" value="created_an_order" data-toggle="tab"><?= lang('tnh_created_an_order') ?>(<span class="count-created_an_order"><?= $created_an_order ?></span>)</a>
                                    </li>
                                    <li role="presentation" class="active">
                                        <a href="#all" aria-controls="all" role="tab" value="all" data-toggle="tab"><?= lang('all') ?>(<span class="count-all"><?= $all ?></span>)</a>
                                    </li>
                                </ul>
                                <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="all">
                            </div>
                        </div>
                        <div class="">
                            <table id="table-quotes" class="table dt-tnh table-hover table-condensed table-quotes-new" style="width: 100%:">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="checkbox mass_select_all_wrap text-center">
                                                <input type="checkbox" id="mass_select_all" data-to-table="quotes-new"><label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('tnh_reference_no_quote') ?></th>
                                        <th><?= lang('customers') ?></th>
                                        <th><?= lang('tnh_grand_total') ?></th>
                                        <th><?= lang('tnh_note_order') ?></th>
                                        <th><?= lang('tnh_created_by') ?></th>
                                        <th><?= lang('tnh_status') ?></th>
                                        <th><?= lang('tnh_user_agree') ?></th>
                                        <th><?= lang('tnh_status_order') ?></th>
                                        <th><?= lang('id_branch') ?></th>
                                        <th><?= lang('Báo cáo sự cố') ?></th>
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
<?php echo form_close(); ?>
<?php init_tail(); ?>

<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    // var site = <?= json_encode(array('base_url' => base_url())) ?>;
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        'start_date_search': '#start_date_search',
        'end_date_search': '#end_date_search',
        'quotes_search': '#quotes_search',
        'customers_search': '#customers_search',
        'status_table': '#status_table',
        'products_search': '#products_search',
    };
    var oTable = '';
    $(document).ready(function() {
        ajaxSelectParams('#quotes_search', 'admin/quotes/searchPreReferenceNoQuotes', $('#quotes_search').val(), false, true);
        ajaxSelectParams('#customers_search', 'admin/clients/searchCustomers', $('#customers_search').val(), false, true);
        ajaxSelectParamsCallback('#products_search', 'admin/products/searchProductsSelect2', $('#products_search').val(), false, true);

        oTable = tnhDatatable(
            '#table-quotes', {
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
                // fixedColumns:   {
                //     leftColumns: 3,
                //     rightColumns: 1
                // },
                // scrollY: height_body,
                // scrollX: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/quotes/getQuotes') ?>',
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
                    if (data[9] || data[7] == 'un_approved') {
                        $(row).find('.cv').addClass('tnh-disabled');
                    }
                    if (data[10] || data[7] == 'un_approved') {
                        $(row).find('.cvc').addClass('tnh-disabled');
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
                            return '<div class="checkbox"><input type="checkbox" name="quote_id[]" id="check-item' + data + '" value="' + data + '"><label for="check-item' + data + '"></label></div>';
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
                            return '<div style="min-width: 150px;">\
                            <a data-tnh="modal" class="tnh-modal" href="' + site.base_url + 'admin/quotes/view_quotes/' + row[0] + '" data-toggle="modal" data-target="#myModal">' + data + '</a>\
                            </div><div><i style="font-size: 11px;">' + (row[10] ? ('Chi nhánh: ' + row[10]) : "") + '</i></div>';
                        },
                        "targets": 2,
                        "name": 'reference_no',
                        'width': '150px'
                    },
                    {
                        "targets": 3,
                        "name": 'customer',
                        'width': '150px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                        },
                        "targets": 4,
                        "name": 'grand_total',
                        'width': '100px'
                    },
                    {
                        "targets": 5,
                        "name": 'note',
                        'width': '100px'
                    },
                    {
                        "targets": 6,
                        "name": 'created_by',
                        'width': '100px'
                    },
                    {
                        "render": function(data, type, row) {
                            str = '';
                            quote_id = row[0];
                            if (data == "approved" || data == "cancel") {
                                user_status = '<div class="mtop10"><?= lang('tnh_user_agree') ?>: ' + row[8] + '</div>';
                            } else {
                                user_status = '';
                            }
                            if (data == "un_approved") {
                                str = '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_status') ?>" data-content="<p><a id=\'agree\' quote_id=\'' + quote_id + '\' value=\'approved\' class=\'btn btn-success\'><?= lang('tnh_agree') ?></a><a id=\'agree\' quote_id=\'' + quote_id + '\' value=\'cancel\' class=\'btn btn-warning\'><?= lang('Không đạt') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-danger po"><?= lang('tnh_un_approved') ?></span></div>' + user_status;
                            } else if (data == "approved") {
                                str = '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_status') ?>\" data-content="<p><a id=\'agree\' quote_id=\'' + quote_id + '\' value=\'un_approved\' class=\'btn btn-danger\'><?= lang('tnh_un_agree') ?></a><a id=\'agree\' quote_id=\'' + quote_id + '\' value=\'cancel\' class=\'btn btn-warning\'><?= lang('Không đạt') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-success po"><?= lang('tnh_approved') ?></span></div>' + user_status;
                            } else if (data == "cancel") {
                                str = '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_status') ?>\" data-content="<p><a id=\'agree\' quote_id=\'' + quote_id + '\' value=\'un_approved\' class=\'btn btn-danger\'><?= lang('tnh_un_agree') ?></a><a id=\'agree\' quote_id=\'' + quote_id + '\' value=\'approved\' class=\'btn btn-success\'><?= lang('tnh_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-warning po"><?= lang('Không đạt') ?></span></div>' + user_status;
                            }

                            return str;
                        },
                        "targets": 7,
                        "name": 'status',
                        'width': '100px'
                    },
                    {
                        "targets": 8,
                        "name": 'user_status',
                        'visible': false
                    },
                    {
                        "render": function(data, type, row) {
                            str = '';
                            if (data) {
                                str = '<div class="label label-success"><?= lang('tnh_created_an_order') ?></div><div class="mtop10">' + data + '</div>';
                            } else {
                                str = '<div class="label label-danger"><?= lang('tnh_un_created_an_order') ?></div>';
                            }
                            return str;
                        },
                        "targets": 9,
                        "name": 'status_order',
                        'width': '140px'
                    },
                    {
                        "targets": 10,
                        "name": 'name_branch',
                        'visible': false
                    },
                    {
                        "render": function(data, type, row) {
                            var str = `<a class="btn btn-info btn-icon mbot5" href="${site.base_url+'admin/production_report/detail?id_quotes='+row[0]}">Tạo phiếu báo cáo</a>`;
                            if (data?.length > 0) {
                                $.each(data, function (index, value) { 
                                    str+= `<div><a class="c_modal" href="${site.base_url+'admin/production_report/modal/'+value?.id_production_report}">${value?.reference_no}</a></div>`;
                                });
                            }
                            return str;
                        },
                        "targets": 11,
                        "name": 'production_report',
                        'width': '100px',
                        'searchable': false,
                        'orderable': false
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
                    var grand_total = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        grand_total += intVal(aaData[i][4]);
                    }
                    var nCells = nRow.getElementsByTagName('td');
                    nCells[4].innerHTML = '<div class="text-right bold">' + tnhFormatMoney(grand_total) + '</div>';
                }
            }
        );
        $('#table-tools_supplies').on('draw.dt', function() {})
        $(document).on('click', '.btn-dt-reload', function(event) {
            // oTable.draw();
        });
        $(document).on('click', '.status-table li a', function(event) {
            status_table = $(this).attr('value');
            $('#status_table').val(status_table);
            oTable.draw();
        });
        $(document).on('click', '#agree', function(event) {
            event.preventDefault();
            index = this;
            quote_id = $(this).attr('quote_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (quote_id) {
                $.ajax({
                        url: site.base_url + 'admin/quotes/agree',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                            quote_id: quote_id,
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
        $(document).on('click', '.remove-sub', function(event) {
            event.preventDefault();
            $(this).closest('.sb').remove();
            totalOrder();
        });
        $(document).on('change', '#start_date_search, #end_date_search, #quotes_search, #customers_search, #products_search', function(event) {
            oTable.draw();
        });
        $(document).on('change', '.quantity_sub, .discount, .c_type_discount, .shipping', function(event) {
            totalOrder();
        });
        $(document).on('click', '.status-table li a', function(event) {
            status_table = $(this).attr('value');
            $('#status_table').val(status_table);
            oTable.draw();
        });
    });
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>
<script>
    $(document).ready(function() {
        $(document).on('change', '.quantity_put, .quantity_loss, .sample_quantity_item', function(event) {
            totalOrder();
        });
    });
</script>