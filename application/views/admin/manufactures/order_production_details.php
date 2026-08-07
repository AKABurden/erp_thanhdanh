<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">

<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse in" aria-expanded="true">
                    <div class="col-md-3">
                        <?= lang('tnh_reference_productions_orders', 'productions_orders_search') ?>
                        <input type="text" name="productions_orders_search" data-placeholder="<?= lang('tnh_reference_productions_orders') ?>" id="productions_orders_search" class="productions_orders_search" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('tnh_items', 'items_search') ?>
                        <input type="text" name="items_search" data-placeholder="<?= lang('tnh_items') ?>" id="items_search" class="items_search" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('start_date', 'start_date_search') ?>
                        <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('end_date', 'end_date_search') ?>
                        <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div role="tabpanel">
                            <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="">
                        </div>
                        <div class="">
                            <table id="table-productions-orders-details" class="table table-hover table-condensed table-productions-orders-details" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('id') ?></th>
                                        <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                        <th class="text-center"><?= lang('tnh_reference_productions_orders') ?></th>
                                        <th class="text-center"><?= lang('tnh_reference_productions_orders_details') ?></th>
                                        <th class="text-center"><?= lang('tnh_product_code') ?></th>
                                        <th class="text-center"><?= lang('tnh_quantity_nk') ?></th>
                                        <th class="text-center"><?= lang('tnh_status') ?></th>
                                        <th class="text-center"><?= lang('tnh_total_cost') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>
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
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/colReorderWithResize.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    // var site = <?= json_encode(array('base_url' => base_url())) ?>;
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        status_table: '#status_table',
        productions_orders_search: '#productions_orders_search',
        items_search: '#items_search',
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
        departments_search: '#departments_search'
    };
    var oTable = '';
    var arr = [];
</script>
<script type="text/javascript">
    $(document).ready(function() {
        init_datepicker();
        ajaxSelectParamsCallback('#productions_orders_search', 'admin/manufactures/searchProductionsOrders', 0, false, true);
        ajaxSelectParamsCallback('#items_search', 'admin/products/searchProductsSelect2', 0, false, true);
        $('#departments_search').select2({
            'allowClear': true
        });

        oTable = tnhInitDataTable('#table-productions-orders-details', '<?= site_url('admin/manufactures/getProductionsOrdersDetailsNew') ?>', {
            'order': [
                [3, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/manufactures/getProductionsOrdersDetailsNew') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    // $('.table-productions-orders-details tfoot td:nth-child(5)').html(`<div class="text-center">${tnhFormatNumber(json.totalQuantity)}</div>`);
                    $('.table-productions-orders-details tfoot td:nth-child(5)').html(`<div class="text-center">${tnhFormatNumber(json.totalQuantityFinished)}</div>`);
                    $('.table-productions-orders-details tfoot td:nth-child(7)').html(`<div class="text-right">${tnhFormatNumber(json.totalCost)}</div>`);
                    return json.aaData;
                }
            },
            "columnDefs": [{
                    "targets": 0,
                    "name": 'id',
                    'width': '45px',
                    'className': 'text-center',
                    'visible': false
                },
                {
                    "targets": 1,
                    "name": 'number_records',
                    'width': '30px',
                    'className': 'text-center',
                    'sortable': false
                },
                {
                    "targets": 2,
                    "name": 'reference_no_order',
                    "width": "110px"
                },
                {
                    "render": function(data, type, row) {
                        return '<a class="" title="<?= lang('tnh_detail') ?>" target="_blank" href="<?= base_url('admin/manufactures/detail_productions/') ?>' + row[0] + '">' + data + '</a>';
                    },
                    "targets": 3,
                    "name": 'reference_no',
                    "width": "110px"
                },
                {
                    "render": function(data, type, row) {
                        images = site.base_url + 'assets/images/tnh/no_image.png';
                        if (data) {
                            data = data.split('___');
                            txtReferenceObject = data[1];
                            sl = data[2];
                            precent = data[3];
                            data = data[0].split('||');
                            if (data[0]) {
                                images = site.base_url + 'uploads/products/' + data[0];
                            }
                            str = '<table class="tnh-table" style="width: 100%;"><tbody>';
                            str += `<tr>
                                <td style="width: 5%; padding: 5px !important;">
                                    <div class="td-image">
                                        <div class="preview_image" style="width: auto;">
                                            <div class="display-block contract-attachment-wrapper img">
                                                <div style="width:35px;">
                                                    <a href="${images}" data-lightbox="customer-profile" class="display-block mbot5">
                                                        <div class="">
                                                            <img src="${images}" style="border-radius: 50%">
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="width: 95%; padding: 5px !important;">
                                    ${data[1]}
                                    <div><?= lang('quantity') ?>: ${tnhFormatNumber(sl)}</div>
                                    ${txtReferenceObject}
                                    <div class="progress" style="margin-bottom: 0;">
                                        <div class="progress-bar progress-bar-success-green progress-bar-cs" role="progressbar" aria-valuenow="${precent}"
                                        aria-valuemin="0" aria-valuemax="100" style="width:${formatDecimalToFixed(precent, 0)}%">
                                            ${formatDecimalToFixed(precent, 0)}%
                                        </div>
                                    </div>
                                </td>
                            </tr>`;
                            str += '</tbody></table>';
                            return str;
                        }
                        return data;
                    },
                    "targets": 4,
                    "name": 'item_name',
                    "width": "200px"
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 5,
                    "name": 'quantity_finished',
                    "width": "100px"
                },
                {
                    'className': 'text-left',
                    "targets": 6,
                    "name": 'status',
                    "width": "180px"
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-right">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 7,
                    "name": 'total_cost',
                    "width": "100px"
                },
                {
                    "targets": 8,
                    "name": 'actions',
                    'searchable': false,
                    'sortable': false,
                    "width": "80px"
                },
            ]
        });

        $(document).on('click', '#agree', function(event) {
            event.preventDefault();
            index = this;
            productions_orders_details_id = $(this).attr('productions_orders_details_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (productions_orders_details_id) {
                $.ajax({
                        url: site.base_url + 'admin/manufactures/updateStatusDetailProductions',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                            productions_orders_details_id: productions_orders_details_id,
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
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>
<script type="text/javascript">
    //purchase internal
    $(document).on('change', '.items_id', function(event) {
        event.preventDefault();
        row = $(this).closest('tr');
        data = event.added;
        sl = this;
        item_id = $(this).val();
        if (item_id) {
            console.log(data);
            tr = $(sl).closest('tr');
            name = data.name;
            unit_name = data.unit_name;
            unit_id = data.unit_id;
            price_import = data.price_import;

            tr.find('.price').val(price_import);
            tr.find('.td-total-internal').val(price_import);
            tr.find('.unit_id').val(unit_id);
            tr.find('.td-item-name').html(name);
            tr.find('.td-unit').html(unit_name);
            $(row).find('select.locations').html(locations);

            lastrow = $('#tb-items tbody tr')[$('#tb-items tbody tr').length - 1];
            if ($(lastrow).find('input.items_id').select2('val')) {
                $('.add-row').click();
            }
        } else {
            tr.find('.td-item-name').html('');
            tr.find('.td-image a').attr('href', site.base_url + 'assets/images/tnh/no_image.png');
            tr.find('.td-image img').attr('src', site.base_url + 'assets/images/tnh/no_image.png');
        }
        totalPurchaseInternal();
    });

    $(document).on('change', '.quantity-internal, .price-internal', function(event) {
        event.preventDefault();
        totalPurchaseInternal();
    });

    $(document).on('click', '.remove-row', function(event) {
        event.preventDefault();
        dt.row($(this).parents('tr')).remove().draw();
        totalPurchaseInternal();
    });

    $(document).on('change', '#productions_orders_search, #items_search, #start_date_search, #end_date_search, #departments_search', function() {
        oTable.draw();
    });
</script>