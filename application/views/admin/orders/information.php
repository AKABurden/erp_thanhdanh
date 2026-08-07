<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<style>
    #table-orders-infomation tr th:nth-child(1) {
        width: 50px !important;
        min-width: 50px !important;
        max-width: 50px !important;
    }

    #table-orders-infomation tr th:nth-child(2) {
        width: 80px !important;
        min-width: 80px !important;
        max-width: 80px !important;
    }

    #table-orders-infomation tr th:nth-child(3) {
        width: 80px !important;
        min-width: 80px !important;
        max-width: 80px !important;
    }

    #table-orders-infomation tr th:nth-child(4) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(5) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(6) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(7) {
        width: 180px !important;
        min-width: 180px !important;
        max-width: 180px !important;
    }

    #table-orders-infomation tr th:nth-child(8) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(9) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(10) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(11) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(12) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(13) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(14) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(15) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(16) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(17) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(18) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }
</style>
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
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
                        <?= lang('tnh_type_orders', 'type_orders_search') ?>
                        <select name="type_orders_search" id="type_orders_search" data-placeholder="<?= lang('tnh_type_orders') ?>" class="type_orders" style="width: 100%;">
                            <option value=""></option>
                            <?php foreach ($type_orders as $key => $value) : ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <?= lang('tnh_status_orders', 'status_orders_search') ?>
                        <select name="status_orders_search" id="status_orders_search" data-placeholder="<?= lang('tnh_status_orders') ?>" class="status_orders" style="width: 100%;">
                            <option value=""></option>
                            <?php foreach ($status_orders as $key => $value) : ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?>(<?= $value['time'] ?>)</option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <?= lang('start_date', 'start_date_search') ?>
                        <?//= date('d/m/Y', strtotime('-5 months')) ?>
                        <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="<?= date('d/m/Y') ?>">
                    </div>
                    <div class="col-md-2">
                        <?= lang('end_date', 'end_date_search') ?>
                        <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="<?= date('d/m/Y') ?>">
                    </div>
                    <div class="col-md-2">
						<?= lang('Sản phẩm', 'items_search') ?>
                        <input type="text" name="items_search" id="items_search" class="items_search" style="width: 100%;" data-placeholder="Thành phẩm" value="">
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-2 mtop10">
                        <?= lang('tnh_branch', 'branch_search') ?>
                        <select name="branch_search" id="branch_search" data-placeholder="<?= lang('tnh_branch') ?>" class="" style="width: 100%;">
                            <option value=""></option>
                            <?php if(!empty($branch)): ?>
                                <?php foreach($branch as $key => $value): ?>
                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" onclick="excelOrdersInformation()" style="margin-top: 35px;" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> <?= lang('tnh_excel_orders_information') ?></button>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="">
                            <table id="table-orders-infomation" class="table dt-tnh table-orders-infomation-new" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <div class="checkbox mass_select_all_wrap checkbox-info"><input type="checkbox" id="mass_select_all" data-to-table="orders-infomation-new">
                                                <label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th class="text-center"><?= lang('Nhóm Đơn Hàng') ?></th>
                                        <th class="text-center"><?= lang('Hình sản phẩm') ?></th>
                                        <th class="text-center"><?= lang('Mã TP') ?></th>
                                        <th class="text-center"><?= lang('Trạng Thái ĐH') ?></th>
                                        
                                        <th class="text-center"><?= lang('Ngày mở đơn') ?></th>
                                        <th class="text-center"><?= lang('Mã KH') ?></th>
                                        <th class="text-center"><?= lang('Brand') ?></th>
                                        <th class="text-center"><?= lang('Mã ĐĐH') ?></th>
                                        <th class="text-center"><?= lang('Ngày giao dự kiến') ?></th>
                                        <th class="text-center"><?= lang('Tên TP của khách') ?></th>
                                        <th class="text-center"><?= lang('SL chưa giao') ?></th>
                                        <th class="text-center"><?= lang('SL đơn hàng') ?></th>
                                        <th class="text-center"><?= lang('SL đã giao') ?></th>
                                        <th class="text-center"><?= lang('SL còn lại') ?></th>
                                        <th class="text-center"><?= lang('Số lượng tồn') ?></th>
                                        <th class="text-center"><?= lang('Đơn giá') ?></th>
                                        <th class="text-center"><?= lang('Ngày giao hàng - SL chi tiết') ?></th>
                                        <th class="text-center"><?= lang('Chủng loại') ?></th>
                                        <th class="text-center"><?= lang('Loại hình in') ?></th>
                                        <th class="text-center" style="width: 100px;"><?= lang('Ghi chú') ?></th>
                                        <th class="text-center"><?= lang('Loại đơn hàng') ?></th>
                                        <th class="text-center"><?= lang('LSX') ?></th>
                                        <th class="text-center"><?= lang('Người Lập Đơn') ?></th>
                                        <th class="text-center" style="width: 100px;"><?= lang('tnh_branch') ?></th>
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
        type_orders_search: '#type_orders_search',
        status_orders_search: '#status_orders_search',
        items_search: '#items_search',
        branch_search: '#branch_search'
    };
    var oTable = '';

    function excelOrdersInformation() {
        customer_search = $('#customer_search').val();
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();
        type_orders_search = $('#type_orders_search').val();
        status_orders_search = $('#status_orders_search').val();
        items_search = $('#items_search').val();
        branch_search = $('#branch_search').val();

        if (!start_date_search || !end_date_search) {
            alert_float('danger', 'Vui lòng chọn ngày bắt đầu và kết thúc');
            return;
        }

        var url_print = site.base_url+'admin/orders/excel_orders_information?customer_search='+customer_search+'&start_date_search='+start_date_search+'&end_date_search='+end_date_search+'&type_orders_search='+type_orders_search+'&status_orders_search='+status_orders_search+'&branch_search='+branch_search+'&items_search='+items_search;
        window.open(url_print, "_blank");
    }

    $(document).ready(function() {
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);
        $('#type_orders_search').select2({allowClear: true});
        $('#status_orders_search').select2({allowClear: true});

        oTable = tnhInitDataTable('#table-orders-infomation', '', {
            // 'order': [
            //     [2, 'desc']
            // ],
            'ordering': false,
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/orders/getOrdersInfomation') ?>',
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
                    $('#table-orders-infomation tfoot tr td:nth-child(12)').html('<div class="text-center">' + tnhFormatMoney(json.quantity_not_delivery) + '</div>');
                    $('#table-orders-infomation tfoot tr td:nth-child(13)').html('<div class="text-center">' + tnhFormatMoney(json.quantity_orders) + '</div>');
                    $('#table-orders-infomation tfoot tr td:nth-child(14)').html('<div class="text-center">' + tnhFormatMoney(json.quantity_delivery) + '</div>');
                    $('#table-orders-infomation tfoot tr td:nth-child(15)').html('<div class="text-center">' + tnhFormatMoney(json.quantity_rest) + '</div>');
                    $('#table-orders-infomation tfoot tr td:nth-child(16)').html('<div class="text-center">' + tnhFormatMoney(json.quantity_warehouse) + '</div>');

                    setTimeout(() => {
                        oTable.columns.adjust()
                    }, 2000);
                    return json.aaData;
                }
            },
            "columnDefs": [],
            "createdRow": function(row, data, index) {
                if (data[1] === 'group') {
                    $('td:eq(0)', row).attr('colspan', 27);
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
                    $('td:eq(11)', row).css('display', 'none');
                    $('td:eq(12)', row).css('display', 'none');
                    $('td:eq(13)', row).css('display', 'none');
                    $('td:eq(14)', row).css('display', 'none');
                    $('td:eq(15)', row).css('display', 'none');
                    $('td:eq(16)', row).css('display', 'none');
                    $('td:eq(17)', row).css('display', 'none');
                    $('td:eq(18)', row).css('display', 'none');
                    $('td:eq(19)', row).css('display', 'none');
                    $('td:eq(20)', row).css('display', 'none');
                    $('td:eq(21)', row).css('display', 'none');
                    $('td:eq(22)', row).css('display', 'none');
                    $('td:eq(23)', row).css('display', 'none');
                    $('td:eq(24)', row).css('display', 'none');
                    $('td:eq(25)', row).css('display', 'none');
                    $('td:eq(26)', row).css('display', 'none');
                    $('td:eq(27)', row).css('display', 'none');
                    $('td:eq(28)', row).css('display', 'none');
                    $('td:eq(29)', row).css('display', 'none');
                    // $('td:eq(30)', row).css('display', 'none');
                    // $('td:eq(31)', row).css('display', 'none');
                    this.api().cell($('td:eq(0)', row)).data(data[6]);
                    $(row).addClass('bg-group bold');
                }
                $(row).addClass('shown');
            },
        });

        $(document).on('change', '#customer_search, #start_date_search, #end_date_search, #type_orders_search, #status_orders_search, #items_search, #branch_search', function(
            event) {
            event.preventDefault();
            oTable.draw();
        });
    });


    ajaxSelectParams($('#items_search'), 'admin/products/searchProductAndGoodsMaterials', 0,true,true);
    $('#branch_search').select2({'allowClear': true});
</script>