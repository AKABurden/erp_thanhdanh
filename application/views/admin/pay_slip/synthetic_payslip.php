<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất
                    Excel</a>
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
                                    <div class="col-md-3">
                                        <?= lang('Phiếu chi', 'pay_slip_search') ?>
                                        <input type="text" name="pay_slip_search"
                                               data-placeholder="<?= lang('Phiếu chi') ?>"
                                               id="pay_slip_search" class="pay_slip_search" style="width: 100%;"
                                               value="">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('supplier', 'suppliers_id') ?>
                                        <input type="text" name="suppliers_id"
                                               data-placeholder="<?= lang('supplier') ?>"
                                               id="suppliers_id" class="business_plan_search" style="width: 100%;"
                                               value="">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('start_date', 'start_date_search') ?>
                                        <input type="text" name="start_date_search" autocomplete="off"
                                               placeholder="<?= lang('start_date') ?>"
                                               id="start_date_search" class="start_date_search datepicker form-control"
                                               style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('end_date', 'end_date_search') ?>
                                        <input type="text" name="end_date_search" autocomplete="off"
                                               placeholder="<?= lang('end_date') ?>"
                                               id="end_date_search" class="end_date_search datepicker form-control"
                                               style="width: 100%;"
                                               value="">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-synthetic-pay-slip" class="table dt-tnh table-synthetic-pay-slip-new"
                                       style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('Mã Phiếu Chi Mua Hàng') ?></th>
                                        <th class="text-center"><?= lang('Ngày Lập PCMH') ?></th>
                                        <th class="text-center"><?= lang('Mã Nhập Kho') ?></th>
                                        <th class="text-center"><?= lang('Mã Tham Chiếu') ?></th>
                                        <th class="text-center"><?= lang('Ngày Nhập Kho') ?></th>
                                        <th class="text-center"><?= lang('PO-Đơn Mua Hàng') ?></th>
                                        <th class="text-center"><?= lang('Ngày Lập PO') ?></th>
                                        <th class="text-center"><?= lang('Mã NCC') ?></th>
                                        <th class="text-center"><?= lang('Nhà Cung Cấp') ?></th>
                                        <th class="text-center"><?= lang('Thời Hạn Thanh Toán') ?></th>
                                        <th class="text-center"><?= lang('Phương Pháp Thanh Toán') ?></th>
                                        <th class="text-center"><?= lang('Mã YCMH') ?></th>
                                        <th class="text-center"><?= lang('Tên Mã YCMH') ?></th>
                                        <th class="text-center"><?= lang('Ngày Lập YCMH') ?></th>
                                        <th class="text-center"><?= lang('Ngày về NPL') ?></th>
                                        <th class="text-center"><?= lang('Loại Nhà Cung Cấp') ?></th>
                                        <th class="text-center"><?= lang('Nhóm NPL') ?></th>
                                        <th class="text-center"><?= lang('Chủng Loại NPL') ?></th>
                                        <th class="text-center"><?= lang('Mã NPL') ?></th>
                                        <th class="text-center"><?= lang('Tên NPL') ?></th>
                                        <th class="text-center"><?= lang('Quy Cách') ?></th>
                                        <th class="text-center"><?= lang('Đơn Vị Chuẩn') ?></th>
                                        <th class="text-center"><?= lang('Đơn Vị Vào Kho') ?></th>
                                        <th class="text-center"><?= lang('Đơn Vị Thanh Toán') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng') ?></th>
                                        <th class="text-center"><?= lang('Giá Nhập') ?></th>
                                        <th class="text-center"><?= lang('Tổng Tiền') ?></th>
                                        <th class="text-center"><?= lang('Tiêu Chuẩn Đóng Gói') ?></th>
                                        <th class="text-center"><?= lang('Thời Gian Lưu Kho') ?></th>
                                        <th class="text-center"><?= lang('Tồn Cho Phép') ?></th>
                                        <th class="text-center"><?= lang('% Thuế') ?></th>
                                        <th class="text-center"><?= lang('Tổng Tiền Thuế') ?></th>
                                        <th class="text-center"><?= lang('Thành Tiền') ?></th>
                                        <th class="text-center"><?= lang('QC') ?></th>
                                        <th class="text-center"><?= lang('Duyệt Kho') ?></th>
                                        <th class="text-center"><?= lang('Mã Phiếu Đề Xuất Tài Chính') ?></th>
                                        <th class="text-center"><?= lang('Người Đề Xuất') ?></th>
                                        <th class="text-center"><?= lang('Người Đề xuất Duyệt') ?></th>
                                        <th class="text-center"><?= lang('Trưởng Phòng Duyệt') ?></th>
                                        <th class="text-center"><?= lang('Thủ Qũy Hoàn Thành') ?></th>
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
        pay_slip_search: "#pay_slip_search",
        suppliers_id: '#suppliers_id',
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
    };
    oTable = tnhInitDataTable('#table-synthetic-pay-slip',
        '<?= site_url('admin/pay_slip/getSyntheticPayslip') ?>', {
            'order': false,
            'fixedHeader': {
                header: true,
            },
            scrollX: true,
            "ajax": {
                "url": '<?= site_url('admin/pay_slip/getSyntheticPayslip') ?>',
                "type": "POST",
                "data": function (d) {
                    if (typeof (csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function (json) {
                    $('.table-synthetic-pay-slip-new tfoot tr td:nth-child(32)').html('<div class="text-center">' + tnhFormatMoney(json.total_tax) + '</div>');
                    $('.table-synthetic-pay-slip-new tfoot tr td:nth-child(33)').html('<div class="text-right">' + tnhFormatMoney(json.grand_total) + '</div>');
                    return json.aaData;
                }
            },
            "createdRow": function (row, data, index) {
            },
            "columnDefs": [],
        });

    $(document).ready(function () {
        ajaxSelectParams('#suppliers_id', 'admin/suppliers/searchSuppliers', 0, true, true);
        ajaxSelectParams('#pay_slip_search', 'admin/pay_slip/searchPayslips', 0, true, true);
        ajaxSelectCallBack($('#custom_item_select'), "<?= admin_url('purchases/SearchItems') ?>", 0);
    });

    $(document).on('change', '#pay_slip_search, #suppliers_id, #start_date_search, #end_date_search', function (event) {
        event.preventDefault();
        oTable.draw();
    });

    function exportExcel() {
        pay_slip_search = $('#pay_slip_search').val();
        suppliers_id = $('#suppliers_id').val();
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/pay_slip/exportExcelSyntheticPaySlip',
            data: {
                csrf_token_name: hash,
                start_date_search: start_date_search,
                end_date_search: end_date_search,
                pay_slip_search: pay_slip_search,
                suppliers_id: suppliers_id,
                export_excel: 1,
            },
            dataType: "json",
            success: function (response) {
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }

    function ajaxSelectCallBack(element, url, id, types = '') {
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: url + '/' + id + '/' + types,
                        dataType: "json",
                        success: function (data) {
                            callback(data.results[0].children[0]);
                        }
                    });
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            type: -1,
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                formatResult: repoFormatSelection_ch,
                formatSelection: repoFormatSelection_ch,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) {
                    return m;
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                ajax: {
                    url: url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            type: -1,
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    code_client: '',
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                formatResult: repoFormatSelection_ch,
                formatSelection: repoFormatSelection_ch,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) {
                    return m;
                }
            });
        }
    }
</script>