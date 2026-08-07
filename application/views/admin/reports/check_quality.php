<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="bold uppercase fsize18 H_title"><?= _l('Báo cáo QC thành phẩm') ?></div>
        </div>
    </div>
    <div class="content">
        <div class="col-md-12 view-check-quality">
            <h3 style="color: #2b6fa2;font-weight: bold;" class="no-mtop text-center title_ch">
                <?php echo mb_strtoupper(_l('Báo Cáo QC thành phẩm')); ?></h3>
        </div>
        <hr class="hr-panel-heading">
        <div class="row">
            <div class="col-md-2">
                <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
                <div class="input-group date">
                    <input type="text" class="form-control datepicker" id="report-from" name="report-from"
                        value="<?= _d(date('Y-m-01')) ?>">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar calendar-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
                <div class="input-group date">
                    <input type="text" class="form-control datepicker" id="report-to" name="report-to"
                        value="<?= _d(date('Y-m-d')) ?>">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar calendar-icon"></i>
                    </div>
                </div>
            </div>
            <div style="margin-top: 28px;">
                <input type="submit" name="btnSave" id="btnSave" onclick="change_event()" value="Lọc"
                    class="btn btn-success first last">
            </div>
            <div class="clearfix"></div>
            <br>
            <div class="">
                <div class="col-lg-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <canvas id="mycotChart" width="auto" height="200"></canvas>
                        </div>
                        <div class="card-footer small Updated text-muted">Updated <?=_dt(date('Y-m-d H:i:s'))?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <table class="table table table-striped table-reason">
                        <thead>
                            <tr>
                                <th class="text-center"><?php echo ucwords(_l('Tên nguyên nhân')); ?></th>
                                <th class="text-center"><?php echo ucwords(_l('Số lỗi')); ?></th>
                                <th class="text-center"><?php echo ucwords(_l('Tỷ lệ')); ?></th>
                            </tr>
                        </thead>
                        <tbody id="reason"></tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-lg-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <canvas id="mycotChart_product" width="auto" height="200"></canvas>
                        </div>
                        <div class="card-footer small Updated text-muted">Updated <?=_dt(date('Y-m-d H:i:s'))?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <table class="table table table-striped table-product">
                        <thead>
                            <tr>
                                <th class="text-center"><?php echo ucwords(_l('Tên thành phẩm')); ?></th>
                                <th class="text-center"><?php echo ucwords(_l('Số lỗi')); ?></th>
                                <th class="text-center"><?php echo ucwords(_l('Tỉ lệ')); ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="tb-order-check-quality" class="table table-hover table-bordered table-condensed"
                        style="width: 100%;">
                        <thead>
                            <tr>
                                <th><?= lang('tnh_numbers') ?></th>
                                <th><?= lang('Lệnh sản xuất') ?></th>
                                <th><?= lang('Đơn hàng') ?></th>
                                <th><?= lang('Tên lỗi') ?></th>
                                <th><?= lang('Số lượng lỗi') ?></th>
                                <th><?= lang('type') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="tb-production-order-qc" class="table table-hover table-bordered table-condensed"
                           style="width: 100%;">
                        <thead>
                        <tr>
                            <th><?= lang('tnh_numbers') ?></th>
                            <th><?= lang('Lệnh sản xuất') ?></th>
                            <th><?= lang('Số lượng QC') ?></th>
                            <th><?= lang('Số lượng lỗi') ?></th>
                            <th><?= lang('Số lượng đạt') ?></th>
                            <th><?= lang('Tỷ lệ lỗi') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
var myPieChart_items;
var myPieChart;
var reason;
var product_top;
var client_top;

var oTable;
var oTableNew;


var paramsOrderCheckQuality = {
    "report-from": "#report-from",
    "report-to": "#report-to"
};
$(document).ready(function() {

    oTable = tnhDatatable(
        '#tb-order-check-quality', {
            'order': [
                [2, 'asc']
            ],
            'orderCellsTop': true,
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            "processing": true,
            'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
            buttons: [{
                text: 'Excel',
                title: '<?= lang('report_of_material_norms') ?>',
                // extend: 'excelHtml5',
                // autoFilter: true,
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                },
                // customize: function ( xlsx ){
                //     var sheet = xlsx.xl.worksheets['sheet1.xml'];
                //     $('row c', sheet).attr( 's', '25' );
                // }
            }, ],

            "serverSide": true,
            'sAjaxSource': '<?= site_url('admin/reports/getOrderCheckQuality') ?>',
            'fnServerData': function(sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                for (var key in paramsOrderCheckQuality) {
                    aoData.push({
                        "name": key,
                        "value": $(paramsOrderCheckQuality[key]).val()
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
            "drawCallback": function(aoData, settings) {},
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {
                typeCustom = aData[5];
                if (typeCustom == "orders") {
                    nRow.className = "bold";
                    nRow.style = "background-color: #daeaf9;";
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
                    'width': '50px',
                    'class': 'text-center',
                    'sortable': false
                },
                {
                    "targets": 1,
                    "name": 'reference_no_pod'
                },
                {
                    "targets": 2,
                    "name": 'reference_no'
                },
                {
                    "render": function(data, type, row) {
                        return data;
                    },
                    "targets": 3,
                    "name": 'id_check_quality_item',
                    'class': 'text-left'
                },
                {
                    "render": function(data, type, row) {
                        if (data == '') {
                            return '';
                        }
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 4,
                    "name": 'quantity'
                },
                {
                    "targets": 5,
                    "name": 'type_hide',
                    'visible': false

                },
            ]
        }
    );

    $('.btn-dt-reload').click(function(event) {
        oTable.draw();
    });
});

function loadSalePerformance() {
    month = $('#month').val();
    precious = $('#precious').val();
    year = $('#year').val();
    branch = $('#branch').val();

    $.ajax({
        type: "POST",
        url: site.base_url + 'admin/reports/loadSalePerformance',
        data: {
            month: month,
            precious: precious,
            year: year,
            branch: branch,
            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
        },
        dataType: "html",
        success: function(response) {
            $('.view-check-quality').html(response);
        }
    });
}

function change_event() {
    reason.clear().draw();
    product_top.clear().draw();


    get_reason();
    get_product();

    init_dashboard_report_cot();
    init_dashboard_report_cot_product();

    oTable.draw();
    oTableNew.draw();
}

$(document).ready(function() {
    loadProductionQc();
    reason = $('.table-reason').DataTable({
        "language": app.lang.datatables,
        "pageLength": -1,
        "bLengthMenu": false,
        "ordering": false,
        "bLengthChange": false,
        scrollY: '200px',
        scrollX: false,
        'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
        "initComplete": function(settings, json) {
            var t = this;
            t.parents('.table-loading').removeClass('table-loading');
            t.removeClass('dt-table-loading');
        },
    });
    product_top = $('.table-product').DataTable({
        "language": app.lang.datatables,
        "pageLength": -1,
        "bLengthMenu": false,
        "ordering": false,
        "bInfo": false,
        "bLengthChange": false,
        scrollY: '200px',
        scrollX: false,
        'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
        "initComplete": function(settings, json) {
            var t = this;
            t.parents('.table-loading').removeClass('table-loading');
            t.removeClass('dt-table-loading');
        },
    });


    $(document).on('change', '#month, #precious, #year, #branch', function(event) {
        loadSalePerformance();
    });

    $(document).on('click', '#tb-cs_wrapper .btn-dt-reload', function(event) {
        oTableModal.draw();
    });

    $(document).on('click', '#tb-cs-detail_wrapper .btn-dt-reload', function(event) {
        oTableModalDetail.draw();
    });
});

function change_expense_report_year(year) {
    window.location.href = admin_url + 'reports/expenses_vs_income/' + year;
}

get_reason();
get_product();


function get_reason() {
    dataString = {
        report_from: $('#report-from').val(),
        report_to: $('#report-to').val(),
        [csrfData['token_name']]: csrfData['hash']
    };
    jQuery.ajax({
        type: "post",
        url: "<?= admin_url() ?>Reports/get_reason/",
        data: dataString,
        cache: false,
        success: function(data) {
            var data = JSON.parse(data);
            $.each(data, function(i, v) {
                reason.row.add([
                    '<div class="text-left">' + v.name_reason + '</div>',
                    '<div class="text-center">' + tnhFormatNumber(v.quantity_reason) +
                    '</div>',
                    '<div class="text-center">' + v.tyle + '%</div>',
                ]).draw(false);
            });

        }
    });
}

function get_product() {
    dataString = {
        report_from: $('#report-from').val(),
        report_to: $('#report-to').val(),
        [csrfData['token_name']]: csrfData['hash']
    };
    jQuery.ajax({
        type: "post",
        url: "<?= admin_url() ?>Reports/get_product/",
        data: dataString,
        cache: false,
        success: function(data) {
            var data = JSON.parse(data);
            $.each(data, function(i, v) {
                product_top.row.add([
                    '<div class="text-left">' + v.name + '</div>',
                    '<div class="text-center">' + tnhFormatNumber(v.quantity_reason) +
                    '</div>',
                    '<div class="text-center">' + v.tyle + '%</div>',
                ]).draw(false);
            });

        }
    });
}




init_dashboard_report_cot();
init_dashboard_report_cot_product();




var myCotChart;
var mycotChart_product;

function init_dashboard_report_cot() {
    dataString = {
        report_from: $('#report-from').val(),
        report_to: $('#report-to').val(),
        [csrfData['token_name']]: csrfData['hash']
    };
    $.post(admin_url + 'reports/dashboard_report_pie_dt/', dataString, function(response) {
        var response = JSON.parse(response);
        if (typeof(myCotChart) !== 'undefined') {
            myCotChart.destroy();
        }
        console.log(response);
        data: {
            datasets: [{
                label: '',
                backgroundColor: response.color,
                borderColor: response.color,
                data: response.data,
            }]
        }
        var ctx = document.getElementById("mycotChart");
        myCotChart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: response.labels,
                datasets: [{
                    label: '',
                    backgroundColor: response.color,
                    borderColor: response.color,
                    data: response.data,
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: "Biểu đồ theo nguyên nhân lỗi"
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return Number(tooltipItem.yLabel).toFixed(0).replace(/./g, function(c,
                                i, a) {
                                return i > 0 && c !== "." && (a.length - i) % 3 === 0 ?
                                    "," + c : c;
                            });
                        }
                    }
                },
                scales: {
                    axisX: {
                        ticks: {
                            labelAngle: 100,
                            labelMaxWidth: 20,
                        }
                    },
                    xAxes: [{
                        time: {
                            unit: "month"
                        },
                        gridLines: {
                            display: !1
                        },
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value, index, values) {
                                return tnhFormatNumber(value);
                            },
                            min: 0,
                            // max: (Number(response.max) + 100000),
                            maxTicksLimit: 100
                        },
                        gridLines: {
                            display: !0
                        }
                    }]
                },
                legend: {
                    display: !0
                }
            }
        });
    });
}

function init_dashboard_report_cot_product() {
    dataString = {
        report_from: $('#report-from').val(),
        report_to: $('#report-to').val(),
        [csrfData['token_name']]: csrfData['hash']
    };
    $.post(admin_url + 'reports/dashboard_report_pie_gt/', dataString, function(response) {
        var response = JSON.parse(response);
        if (typeof(mycotChart_product) !== 'undefined') {
            mycotChart_product.destroy();
        }
        var ctx = document.getElementById("mycotChart_product");
        mycotChart_product = new Chart(ctx, {
            type: "bar",
            data: {
                labels: response.labels,
                datasets: [{
                    label: '',
                    backgroundColor: response.color,
                    borderColor: response.color,
                    data: response.data,
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: "Biểu đồ theo sản phẩm có lỗi nhiều nhất"
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return Number(tooltipItem.yLabel).toFixed(0).replace(/./g, function(c,
                                i, a) {
                                return i > 0 && c !== "." && (a.length - i) % 3 === 0 ?
                                    "," + c : c;
                            });
                        }
                    }
                },
                scales: {
                    axisX: {
                        ticks: {
                            labelAngle: 100,
                            labelMaxWidth: 20,
                        }
                    },
                    xAxes: [{
                        time: {
                            unit: "month"
                        },
                        gridLines: {
                            display: !1
                        },
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value, index, values) {
                                return tnhFormatNumber(value);
                            },
                            min: 0,
                            // max: (Number(response.max) + 100000),
                            maxTicksLimit: 100
                        },
                        gridLines: {
                            display: !0
                        }
                    }]
                },
                legend: {
                    display: !0
                }
            }
        });
    });
}

function loadProductionQc(){
    oTableNew = tnhInitDataTable('#tb-production-order-qc', '<?= site_url('admin/reports/getProductionOrderQc') ?>', {
        'order': [
            [1, 'desc']
        ],
        'responsive': true,
        "ajax": {
            "url": '<?= site_url('admin/reports/getProductionOrderQc') ?>',
            "type": "POST",
            "data": function(d) {
                if (typeof(csrfData) !== 'undefined') {
                    d[csrfData['token_name']] = csrfData['hash'];
                }
                for (var key in paramsOrderCheckQuality) {
                    d[key] = $(paramsOrderCheckQuality[key]).val();
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
            {
                "targets": 0,
                "name": 'id',
                'width': '50px'
            }
        ],
    });
}
</script>