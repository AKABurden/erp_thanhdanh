<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
    table tr td {
        vertical-align: middle !important;
    }

    .tnh-status-sm {
        height: 70px !important;
    }

    #table-productions-orders td:nth-child(7) img {
        width: 50px;
        height: 50px;
    }

    .option_main {
        font-weight: 500;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=3.1') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
        </div>
    </div>
    <div class="content">
        <div class="row ">
            <div class="col-md-12">
                <div class="col-md-2">
                    <div class="form-group">
                        <?= lang('tnh_reference_productions_orders', 'productions_orders_search') ?>
                        <input type="text" name="productions_orders_search" data-placeholder="<?= lang('tnh_reference_productions_orders') ?>" id="productions_orders_search" class="productions_orders_search" style="width: 100%;" value="">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <?= lang('tnh_items', 'items_search') ?>
                        <input type="text" name="items_search" data-placeholder="<?= lang('tnh_items') ?>" id="items_search" class="items_search" style="width: 100%;" value="">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <?= lang('tnh_orders_and_business_plan', 'orders_and_business_plan') ?>
                        <input type="text" name="orders_and_business_plan" id="orders_and_business_plan" style="width: 100%;" data-placeholder="<?= lang('ĐHB/Kế hoạch BTP') ?>" value="">
                    </div>
                </div>
                <div class="col-md-2">
                    <?= lang('customers', 'customers') ?>
                    <input type="text" name="customer_search" data-placeholder="<?= lang('customers') ?>" id="customer_search" class="customer_search" style="width: 100%;" value="">
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <?= lang('tnh_type_print', 'type_print_search') ?>
                        <select name="type_print_search" id="type_print_search" data-placeholder="<?= lang('tnh_type_print') ?>" class="modal-select2" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($CategoryStages)) : ?>
                                <?php foreach ($CategoryStages as $key => $value) : ?>
                                    <option class="<?= ($value['main'] == '1' ? 'option_main' : '') ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <?= lang('Công đoạn sản phẩm', 'type_stage') ?>
                        <select name="type_stage[]" multiple="1" id="type_stage" data-placeholder="<?= lang('Công đoạn sản phẩm') ?>" data-live-search="true" data-actions-box="1" class="selectpicker no-margin" style="width: 100%;">
                            <?php if (!empty($stage)) : ?>
                                <?php foreach ($stage as $key => $value) : ?>
                                    <option  value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-2">
                    <div class="form-group">
                        <?= lang('tnh_period_time', 'period_time') ?>
                        <input type="text" name="period_time" autocomplete="off" placeholder="<?= lang('tnh_period_time') ?>" id="period_time" class="period_time form-control dateranger-custom" style="width: 100%;" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body p-top-0">
                        <?php echo $this->load->view('admin/alert') ?>
                        <table id="table-synthetic-stage" class="table table-hover table-condensed table-synthetic-stage dont-responsive-table dataTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                    <th class="text-center"><?= lang('Ngày lập lệnh') ?></th>
                                    <th class="text-center"><?= lang('tnh_reference_productions_orders') ?></th>
                                    <th class="text-center"><?= lang('dt_product_code') ?></th>
                                    <th class="text-center"><?= lang('dt_product_name') ?></th>
                                    <th class="text-center"><?= lang('Ngày DK giao hàng') ?></th>
                                    <th class="text-center"><?= lang('Ngày xuất đủ NPL') ?></th>
                                    <th class="text-center"><?= lang('Các NPL đã xuất') ?></th>
                                    <th class="text-center"><?= lang('Khổ in ngang x dọc = cm') ?></th>
                                    <th class="text-center"><?= lang('Số con/tờ') ?></th>
                                    <th class="text-center"><?= lang('Số con/khuôn bể') ?></th>
                                    <th class="text-center"><?= lang('Số lượng đặt') ?></th>
                                    <th class="text-center"><?= lang('Số lượng sx') ?></th>
                                    <th class="text-center"><?= lang('Số lượng giữ hàng') ?></th>
                                    <th class="text-center"><?= lang('Số lượng tờ in') ?></th>
                                    <th class="text-center"><?= lang('số lượng bù hao (tờ in)') ?></th>
                                    <th class="text-center"><?= lang('Số lượng hoàn thành') ?></th>
                                    <th class="text-center"><?= lang('tnh_quantity_errors') ?></th>
                                    <th class="text-center"><?= lang('tnh_status') ?></th>
                                    <th class="text-center"><?= lang('Loại hình in') ?></th>
                                    <th class="text-center"><?= lang('Nhóm công đoạn') ?></th>
                                    <th class="text-center"><?= lang('Công đoạn sản phẩm') ?></th>
                                    <th class="text-center"><?= lang('Chi nhánh') ?></th>
                                    <th class="text-center"><?= lang('note') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="99"></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="bold">
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
<?php echo form_close(); ?>
<?php init_tail(); ?>
<a href="" class="tnh-modal hide" id="clickFini"></a>
<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/colReorderWithResize.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    // var site = <?= json_encode(array('base_url' => base_url())) ?>;
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        status_table: '#status_table',
        productions_orders_search: '#productions_orders_search',
        items_search: '#items_search',
        period_time: '#period_time',
        type_stage: '#type_stage',
        type_print_search: '#type_print_search',
        orders_and_business_plan: '#orders_and_business_plan',
        customer_search: '#customer_search',
    };
    var oTable = '';
    var oTableNew = '';
    var trItem = '';
    var trIndex = '';
</script>
<script type="text/javascript">

    function loadTypePrintAndStages() {
        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        for (var key in fnserverparams) {
            dataPOST[key] = $(fnserverparams[key]).val();
        }

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/synthetic_stage/loadTypePrintAndStages',
            data: dataPOST,
            dataType: "json",
            success: function (response) {
                $('#type_print_search').html(response.option_type_print);
                $('#type_stage').html(response.option_stages);
                $('#type_stage').selectpicker('refresh');
            }
        });
    }

    $(document).ready(function() {
        loadTable();
        loadTypePrintAndStages();
        $('#type_print_search').select2({
            'allowClear': true
        });
        init_datepicker();
        ajaxSelectParamsCallback('#productions_orders_search', 'admin/manufactures/searchProductionsOrders', 0, false, true);
        ajaxSelectParamsCallback('#items_search', 'admin/products/searchProductsSelect2', 0, false, true);
        ajaxSelectParams('#orders_and_business_plan', 'admin/manufactures/searchOrdersAndBusinessPlan', 0, true, true);
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);


        $(document).on('click', '.status-table li a', function(event) {
            status_table = $(this).attr('value');
            $('#status_table').val(status_table);
            if (status_table == 'all') {
                $(".table-detail").addClass('hide');
                $(".table-all").removeClass('hide');
                loadTable();
                if (oTableNew !== 'undefined' && oTableNew !== '') {
                    oTableNew.draw();
                } else {
                    loadTable();
                }
            } else {
                $(".table-detail").removeClass('hide');
                $(".table-all").addClass('hide');
                oTable.draw();
            }
        });

        $(document).on('change', '#type_stage,#productions_orders_search, #items_search, #start_date_search, #end_date_search, #orders_search, #business_plan_search, #period_time, #orders_and_business_plan, #type_print_search, #customer_search', function() {
            oTableNew.draw();
        });

        $(document).on('change', '#productions_orders_search, #items_search, #start_date_search, #end_date_search, #orders_search, #business_plan_search, #period_time, #orders_and_business_plan, #customer_search', function() {
            loadTypePrintAndStages();
        });
    });


    function loadTable() {
        oTableNew = tnhInitDataTable('#table-synthetic-stage', '<?= site_url('admin/synthetic_stage/getSyntheticstage') ?>', {
            'order': [
                [1, 'desc']
            ],
            // 'fixedHeader': {
            //     header: true,
            // },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/synthetic_stage/getSyntheticstage') ?>',
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
                    $('#table-synthetic-stage tfoot tr td:nth-child(12)').html('<div class="text-center">' + tnhFormatNumber(json.total_quantity) + '</div>');
                    $('#table-synthetic-stage tfoot tr td:nth-child(13)').html('<div class="text-center">' + tnhFormatNumber(json.total_quantity_sx) + '</div>');
                    $('#table-synthetic-stage tfoot tr td:nth-child(14)').html('<div class="text-center">' + tnhFormatNumber(json.total_quantity_hold) + '</div>');
                    $('#table-synthetic-stage tfoot tr td:nth-child(15').html('<div class="text-center">' + (json.total_quantity_new) + '</div>');
                    $('#table-synthetic-stage tfoot tr td:nth-child(16)').html('<div class="text-center">' + tnhFormatNumber(json._total_quantity_compensation) + '</div>');
                    $('#table-synthetic-stage tfoot tr td:nth-child(17)').html('<div class="text-center">' + tnhFormatNumber(json.total_quantity_finished) + '</div>');
                    $('#table-synthetic-stage tfoot tr td:nth-child(18)').html('<div class="text-center">' + tnhFormatNumber(json.total_quantity_errors) + '</div>');
                    return json.aaData;
                }
            },
            "columnDefs": [{
                    "targets": 0,
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + data + '</div>';
                    },
                    'width': '50px'
                },
                {
                    "targets": 1,
                    'width': '80px'
                },
                {
                    "targets": 2,
                    'width': '120px'
                },
                {
                    "targets": 3,
                    'width': '120px'
                },
                {
                    "targets": 4,
                    'width': '120px'
                },
                {
                    "targets": 5,
                    'width': '80px'
                },
                {
                    "targets": 6,
                    'width': '80px',
                    'searchable': false,
                    'sortable': false
                },
                {
                    "targets": 7,
                    'width': '120px',
                    'sortable': false,
                    'sortable': false
                },
                {
                    "targets": 8,
                    'width': '80px',
                    'sortable': false,
                    'sortable': false
                },
                {
                    "targets": 9,
                    'width': '80px'
                },
                {
                    "targets": 10,
                    'width': '80px'
                },
                {
                    "targets": 11,
                    'width': '120px'
                },
                {
                    "targets": 14,
                    'sortable': false,
                    'searchable': false
                },
                {
                    "targets": 15,
                    'sortable': false,
                    'searchable': false
                },
            ]
        });
    }
</script>

<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>