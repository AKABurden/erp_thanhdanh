<style>
    #tb-reports tr th {
        width: 100px !important;
    }
</style>
<?php
?>
<div class="text-center uppercase">
    <h2><?= lang('Tổng số YC phát triển mẫu') ?></h2>
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
                <div class="tnh-h3">Tổng số YC phát triển mẫu<br>
                    <span id="tong-so-yc-phat-trien-mau">0</span>
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
                <th class="text-center"><?= lang('Số YC phát triển mẫu') ?></th>
                <th class="text-center"><?= lang('Số báo giá') ?></th>
                <th class="text-center"><?= lang('Khách hàng') ?></th>
                <th class="text-center"><?= lang('Tên nhóm SP') ?></th>
                <th class="text-center"><?= lang('Tên chủng loại') ?></th>
                <th class="text-center"><?= lang('DV tính SP') ?></th>
                <th class="text-center"><?= lang('Height') ?></th>
                <th class="text-center"><?= lang('Width') ?></th>
                <th class="text-center"><?= lang('DV Đo SP') ?></th>
                <th class="text-center"><?= lang('Mã thành phẩm') ?></th>
                <th class="text-center"><?= lang('Tên thành phẩm') ?></th>
                <th class="text-center"><?= lang('Brand') ?></th>
                <th class="text-center"><?= lang('SL tồn cho phép') ?></th>
                <th class="text-center"><?= lang('Thời gian tồn kho') ?></th>
                <th class="text-center"><?= lang('Định mức thời gian') ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
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
        getTotalRequestQuotesSample();
    };

    function getTotalRequestQuotesSample() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in fnserverparams) {
            dataGET[key] = $(fnserverparams[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_quotes/getTotalRequestQuotesSample',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                $('#tong-so-yc-phat-trien-mau').html(tnhFormatNumber(response?.count_request_template));
            }
        });
    }

    $(document).ready(function() {
        init_datepicker();
        ajaxSelectParams('#customers', 'admin/clients/searchOnlyCustomers', $('#customers').val(), false, true);

        getTotalRequestQuotesSample();
        _oTableReport = tnhInitDataTable('#tb-reports', '', {
            'ordering': false,
            'searching': false,
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/report_quotes/getTotalRequestSample') ?>',
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
                    return json.aaData;
                }
            },
            "columnDefs": [
            ],
            "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
            },
            "btnButtons": 1
        });

        $('#tb-reports').on('draw.dt', function(e, settings) {
            $('#tb-reports').DataTable().columns.adjust();
        });
    });
</script>