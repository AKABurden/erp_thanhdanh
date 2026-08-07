<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?= $this->load->view('admin/breadcrumb') ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse in" aria-expanded="true">
                    <div class="col-md-2">
                        <?= lang('customers', 'customers') ?>
                        <input type="text" name="customer_search" data-placeholder="<?= lang('customers') ?>" id="customer_search" class="customer_search" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('start_date', 'start_date_search') ?>
                        <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" autocomplete="off" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('end_date', 'end_date_search') ?>
                        <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" autocomplete="off" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-2">
                        <button type="button" onclick="excelStatisticalPlanning()" style="margin-top: 25px;" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> <?= lang('tnh_excel_statistical_planning') ?></button>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="">
                            <table id="table-statistical-planning" class="table dt-tnh table-statistical-planning-new" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <?= lang('tnh_numbers') ?>
                                        </th>
                                        <th class="text-center"><?= lang('Mã KH') ?></th>
                                        <th class="text-center"><?= lang('Mã ĐĐH') ?></th>
                                        <th class="text-center"><?= lang('LSX tổng') ?></th>
                                        <th class="text-center"><?= lang('Ngày sản xuất') ?></th>
                                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                                        <th class="text-center"><?= lang('SL sản xuất') ?></th>
                                        <th class="text-center"><?= lang('Chủng loại') ?></th>
                                        <th class="text-center"><?= lang('Loại hình in') ?></th>
                                        <th class="text-center"><?= lang('SL tồn kho TP') ?></th>
                                        <th class="text-center"><?= lang('Hình sản phẩm') ?></th>
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
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        customer_search: "#customer_search",
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
    };
    var oTable = '';

    function excelStatisticalPlanning() {
        customer_search = $('#customer_search').val();
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        var url_print = site.base_url+'admin/manufactures/excel_statistical_planning?customer_search='+customer_search+'&start_date_search='+start_date_search+'&end_date_search='+end_date_search;
        window.open(url_print, "_blank");
    }

    $(document).ready(function() {
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);
        $('#type_orders_search').select2({allowClear: true});
        $('#status_orders_search').select2({allowClear: true});

        oTable = tnhInitDataTable('#table-statistical-planning', '', {
            // 'order': [
            //     [2, 'desc']
            // ],
            'ordering': false,
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/manufactures/getStatisticalPlanning') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                },
                "dataSrc": function(json) {
                    $('#table-statistical-planning tfoot tr td:nth-child(7)').html('<div class="text-center">' + tnhFormatMoney(json.quantity) + '</div>');
                    $('#table-statistical-planning tfoot tr td:nth-child(10)').html('<div class="text-center">' + tnhFormatMoney(json.quantity_warehouses) + '</div>');
                    return json.aaData;
                }
            },
            "columnDefs": [],
            "createdRow": function(row, data, index) {
                if (data[1] === 'group') {
                    $('td:eq(0)', row).attr('colspan', 10);
                    $('td:eq(1)', row).css('display', 'none');
                    $('td:eq(2)', row).css('display', 'none');
                    $('td:eq(3)', row).css('display', 'none');
                    $('td:eq(4)', row).css('display', 'none');
                    $('td:eq(5)', row).css('display', 'none');
                    $('td:eq(6)', row).css('display', 'none');
                    $('td:eq(7)', row).css('display', 'none');
                    $('td:eq(8)', row).css('display', 'none');
                    $('td:eq(9)', row).css('display', 'none');
                    $('td:eq(10)', row).css('display', 'none');
                    this.api().cell($('td:eq(0)', row)).data(data[2]);
                    $(row).addClass('bg-group bold');
                }
                $(row).addClass('shown');
            },
        });

        $(document).on('change', '#customer_search, #start_date_search, #end_date_search, #type_orders_search, #status_orders_search', function(
            event) {
            event.preventDefault();
            oTable.draw();
        });
    });
</script>