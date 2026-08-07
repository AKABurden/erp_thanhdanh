<style>
    #tb-reports tr th {
        width: 100px !important;
    }
</style>
<?php
?>
<div class="text-center uppercase">
    <h2><?= lang('Tổng báo giá & Chi tiết báo giá') ?></h2>
</div>
<hr>
<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <?= lang('customers', 'customers') ?>
            <input type="text" name="customers" id="customers" style="width: 100%;" data-placeholder="<?= lang('customers') ?>" value="">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <?= lang('start_date', 'start_date') ?>
            <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <?= lang('end_date', 'end_date') ?>
            <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
        </div>
    </div>
    <div class="col-md-1">
        <a href="javascript:void(0)" class="btn btn-success" onclick="loadTableReport()" style="margin-top: 27px;"><?= lang('Tìm kiếm') ?></a>
    </div>
</div>
<div class="row mbot10">
    <div class="col-md-3">
        <div class="tnh-card tnh-text-white tnh-bg-success o-hidden h-100" style="height: 80px;">
            <div class="tnh-card-body">
                <img src="<?= base_url('assets/images/circle.svg') ?>" class="tnh-card-img-absolute" alt="circle-image">
                <div class="tnh-h3">Tổng Số Báo Giá<br>
                    <span id="tong-so-bao-gia">0</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table id="tb-reports" class="table dataTable" style="width: 100%;">
        <thead>
            <tr>
                <th class="text-center"><?= lang('Ngày') ?></th>
                <th class="text-center"><?= lang('Số báo giá') ?></th>
                <th class="text-center"><?= lang('Khách hàng') ?></th>
                <th class="text-center"><?= lang('Trạng thái') ?></th>
                <th class="text-center"><?= lang('Mã thành phẩm') ?></th>
                <th class="text-center"><?= lang('Tên thành phẩm') ?></th>
                <th class="text-center"><?= lang('Bảng giá công đoạn') ?></th>
                <th class="text-center"><?= lang('Diễn giải & thông số kỹ thuật') ?></th>
                <th class="text-center"><?= lang('Đơn vị') ?></th>
                <th class="text-center"><?= lang('MOQ') ?></th>
                <th class="text-center"><?= lang('Đơn giá') ?></th>
                <th class="text-center"><?= lang('Chiết khấu') ?></th>
                <th class="text-center"><?= lang('Leadtime') ?></th>
                <th class="text-center"><?= lang('Ghi chú') ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <!-- <tfoot>
            <tr class="bold uppercase">
                <td></td>
                <td><?//= lang('tnh_grand_total') ?></td>
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
        </tfoot> -->
    </table>
</div>
<script type="text/javascript">
    var fnserverparams = {
        'start_date_search': '#start_date_search',
        'end_date_search': '#end_date_search',
        'customers': '#customers'
    };
    var _oTableReport = '';

    function loadTableReport() {
        _oTableReport.draw();
        getTotalQuotes();
    };

    function getTotalQuotes() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in fnserverparams) {
            dataGET[key] = $(fnserverparams[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_quotes/getTotalQuotes',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                $('#tong-so-bao-gia').html(response?.count_quotes);
            }
        });
    }

    $(document).ready(function() {
        init_datepicker();
        getTotalQuotes();
        ajaxSelectParams('#customers', 'admin/clients/searchOnlyCustomers', $('#customers').val(), false, true);
        _oTableReport = tnhInitDataTable('#tb-reports', '', {
            'ordering': false,
            'searching': false,
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/report_quotes/getDetailQuotes') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }

                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();

                        if ($(fnserverparams[key]).data('select2') && $(fnserverparams[key]).val()) {
                            d[key+'_text'] = $(fnserverparams[key]).select2('data').text;
                        }
                    }

                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    var sums = json.sums;
                    // $('tfoot th').eq(1).html('<div class="text-center">' + sums.slsx + '</div>');
                    // $('tfoot th').eq(2).html('<div class="text-center">' + sums.sln + '</div>');
                    // $('tfoot th').eq(3).html('<div class="text-center">' + sums.slv + '</div>');
                    // $('tfoot th').eq(4).html('<div class="text-center">' + sums.sll + '</div>');
                    // $('tfoot th').eq(8).html('<div class="text-right">' + sums.tt + '</div>');

                    return json.aaData;
                }
            },
            "columnDefs": [
                // {
                //     "targets": 0,
                //     "name": 'id',
                //     'width': '45px',
                //     'className': 'text-center',
                // }, 
            ],
            "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                // var grand_total = 0;
                // for (var i = 0; i < aaData.length; i++) {
                //     grand_total += intVal(aaData[i][8]);
                // }
                // var nCells = nRow.getElementsByTagName('th');
                // nCells[2].innerHTML = '<div class="text-center bold">' + tnhFormatNumber(grand_total) + '</div>';
            },
            "btnButtons": 1
        });

        $('#tb-reports').on('draw.dt', function(e, settings) {
            $('#tb-reports').DataTable().columns.adjust();
        });
    });
</script>