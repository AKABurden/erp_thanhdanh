<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="row" style="margin-bottom:5px">
                                <div id="search-tnh" class="collapse in" aria-expanded="true">
                                    <div class="col-md-2">
                                        <?= lang('tnh_reference_orders', 'orders_search') ?>
                                        <input type="text" name="orders_search" id="orders_search" style="width: 100%;"
                                               data-placeholder="<?= lang('tnh_reference_orders') ?>" value="">
                                    </div>
                                    <div class="col-md-2">
                                        <?= lang('Phiếu giao hàng', 'delivery_search') ?>
                                        <input type="text" name="delivery_search" data-placeholder="<?= lang('Phiếu giao hàng') ?>"
                                               id="delivery_search" class="delivery_search" style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-2">
                                        <?= lang('customers', 'customers') ?>
                                        <input type="text" name="customer_search" data-placeholder="<?= lang('customers') ?>"
                                               id="customer_search" class="customer_search" style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-2">
                                        <?= lang('Thành phẩm', 'items_search') ?>
                                        <input type="text" name="items_search" id="items_search" class="items_search"
                                               style="width: 100%;" data-placeholder="Thành phẩm" value="">
                                    </div>
                                    <div class="col-md-2">
                                        <?= lang('start_date', 'start_date_search') ?>
                                        <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>"
                                               id="start_date_search" class="start_date_search datepicker form-control"
                                               style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-2">
                                        <?= lang('end_date', 'end_date_search') ?>
                                        <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>"
                                               id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;"
                                               value="">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-synthetic-coupon-invoice" class="table dt-tnh table-synthetic-coupon-invoice-new" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('Số Hóa Đơn Bán Hàng') ?></th>
                                        <th class="text-center"><?= lang('Ngày Lập Hóa Đơn Bán Hàng') ?></th>
                                        <th class="text-center"><?= lang('Số PGH') ?></th>
                                        <th class="text-center"><?= lang('Ngày Lập PGH') ?></th>
                                        <th class="text-center"><?= lang('Mã ĐĐH (TD)') ?></th>
                                        <th class="text-center"><?= lang('Mã ĐĐH (KH)') ?></th>
                                        <th class="text-center"><?= lang('Loại ĐĐH') ?></th>
                                        <th class="text-center"><?= lang('Ngày Lập ĐĐH') ?></th>
                                        <th class="text-center"><?= lang('Ngày Giao') ?></th>
                                        <th class="text-center"><?= lang('Mã KH') ?></th>
                                        <th class="text-center"><?= lang('Khách Hàng') ?></th>
                                        <th class="text-center"><?= lang('Brand') ?></th>
                                        <th class="text-center"><?= lang('Chỉ Lệch') ?></th>
                                        <th class="text-center"><?= lang('Mã Thành Phẩm (TD)') ?></th>
                                        <th class="text-center"><?= lang('Tên Thành Phẩm (TD)') ?></th>
                                        <th class="text-center"><?= lang('Mã Thành Phẩm (KH)') ?></th>
                                        <th class="text-center"><?= lang('Quy Cách') ?></th>
                                        <th class="text-center"><?= lang('ĐVT') ?></th>
                                        <th class="text-center"><?= lang('Số Con/Kiện') ?></th>
                                        <th class="text-center"><?= lang('Tổng Số Kiện') ?></th>
                                        <th class="text-center"><?= lang('Tổng SL') ?></th>
                                        <th class="text-center"><?= lang('Đơn Giá Bán') ?></th>
                                        <th class="text-center"><?= lang('Loại Giá Áp Dụng') ?></th>
                                        <th class="text-center"><?= lang('Tổng Tiền') ?></th>
                                        <th class="text-center"><?= lang('% Thuế') ?></th>
                                        <th class="text-center"><?= lang('Tổng Tiền Thuế') ?></th>
                                        <th class="text-center"><?= lang('Chi Phí Giao Hàng') ?></th>
                                        <th class="text-center"><?= lang('Thành Tiền') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr class="bold">
                                        <td class="uppercase"><?= lang('Tổng cộng') ?></td>
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
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    var oTable = '';

    var fnserverparams = {
        customer_search: "#customer_search",
        orders_search: '#orders_search',
        delivery_search: '#delivery_search',
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
        items_search: '#items_search',
    };
    oTable = tnhInitDataTable('#table-synthetic-coupon-invoice',
        '<?= site_url('admin/coupon_invoice/getSyntheticCouponInvoices') ?>', {
            'order': false,
            'fixedHeader': {
                header: true,
            },
            scrollX:true,
            "ajax": {
                "url": '<?= site_url('admin/coupon_invoice/getSyntheticCouponInvoices') ?>',
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
                    $('.table-synthetic-coupon-invoice-new tfoot tr td:nth-child(21)').html('<div class="text-right">'+tnhFormatMoney(json.total_quantity)+'</div>');
                    $('.table-synthetic-coupon-invoice-new tfoot tr td:nth-child(24)').html('<div class="text-right">'+tnhFormatMoney(json.grand_total)+'</div>');
                    $('.table-synthetic-coupon-invoice-new tfoot tr td:nth-child(26)').html('<div class="text-right">'+tnhFormatMoney(json.grand_total_tax)+'</div>');
                    $('.table-synthetic-coupon-invoice-new tfoot tr td:nth-child(28)').html('<div class="text-right">'+tnhFormatMoney(json.grand_total_all)+'</div>');
                    return json.aaData;
                }
            },
            "createdRow": function(row, data, index) {
            },
            "columnDefs": [
            ],
        });

    $(document).ready(function() {
        ajaxSelectParams('#orders_search', 'admin/orders/searchOrders', 0, true, true);
        ajaxSelectParams('#delivery_search', 'admin/coupon_invoice/searchCouponInvoice', 0, true, true);
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);
        ajaxSelectParams($('#items_search'), 'admin/products/searchProductAndGoodsMaterials', 0, true, true);
    });

    $(document).on('change', '#customer_search, #orders_search, #delivery_search, #start_date_search, #end_date_search, #items_search', function (event) {
        event.preventDefault();
        oTable.draw();
    });

    function exportExcel() {
        customer_search = $('#customer_search').val();
        orders_search = $('#orders_search').val();
        delivery_search = $('#delivery_search').val();
        items_search = $('#items_search').val();
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/coupon_invoice/exportExcelSyntheticCouponInvoice',
            data: {
                csrf_token_name: hash,
                start_date_search: start_date_search,
                end_date_search: end_date_search,
                customer_search: customer_search,
                orders_search: orders_search,
                delivery_search: delivery_search,
                items_search: items_search,
                export_excel: 1,
            },
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }
</script>