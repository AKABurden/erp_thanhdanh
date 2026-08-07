<style>
    #tb-po thead {
        display: none;
    }

    #tb-po tr td {
        border: none !important;
    }

    #tb-po tr th {
        border: none !important;
    }

    #tb-po tr td:nth-child(1) {
        width: 50px;
    }

    #tb-po tr td:nth-child(2) {
        width: 200px;
    }
</style>
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<div class="widget" id="widget-<?php echo basename(__FILE__, ".php"); ?>" data-name="<?php echo _l('dashboard'); ?>">
    <div class="clearfix"></div>
    <div class="panel_s">
        <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="dt-loader hide"></div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('tnh_period_time', 'period_time') ?>
                        <input type="text" name="period_time" autocomplete="off" placeholder="<?= lang('tnh_period_time') ?>" id="period_time" class="period_time form-control dateranger-custom" style="width: 100%;" value="">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading p-relative">
                            <h3 class="panel-title"><span class="fa fa-file-text-o"></span> <?= lang('tnh_statistical_qc_errores') ?></h3>
                            <a class="accordion-toggle p-collapse" data-toggle="collapse" href="#collapse-qc" role="button" aria-controls="collapse-qc" aria-expanded="false"></a>
                        </div>
                        <div class="panel-body">
                            <div id="collapse-qc" class="collapse in">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <canvas id="mycotChart" width="auto" height="200"></canvas>
                                            </div>
                                            <div class="card-footer small Updated text-muted"><?= lang('tnh_updated') ?> <?= _dt(date('Y-m-d H:i:s')) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <table id="tb-qc" class="table table table-striped table-reason m-top-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-center"><?php echo _l('Tên nguyên nhân'); ?></th>
                                                    <th class="text-center"><?php echo _l('Số lỗi'); ?></th>
                                                    <th class="text-center"><?php echo _l('Tỷ lệ'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody id="reason"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading p-relative">
                            <h3 class="panel-title"><span class="fa fa-cube"></span> <?= lang('tnh_statistical_quantity_productions') ?></h3>
                            <a class="accordion-toggle p-collapse" data-toggle="collapse" href="#collapse-po" role="button" aria-controls="collapse-po" aria-expanded="false"></a>
                        </div>
                        <div class="panel-body">
                            <div id="collapse-po" class="collapse in">
                                <table id="tb-po" class="table table-hover" style="margin-top: 0px;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 80px;"><?= lang('image') ?></th>
                                            <th class="text-center" style="width: 150px;"><?= lang('tnh_name_code_item') ?></th>
                                            <th class="text-center"><?= lang('quantity') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading p-relative">
                            <h3 class="panel-title"><span class="fa fa-bar-chart"></span> <?= lang('tnh_statistical_import_finished') ?></h3>
                            <a class="accordion-toggle p-collapse" data-toggle="collapse" href="#collapse-pp" role="button" aria-controls="collapse-pp" aria-expanded="false"></a>
                        </div>
                        <div class="panel-body">
                            <div id="collapse-pp" class="collapse in">
                                <div id="chart-pp"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <hr>
</div>
<script src="<?= js('hightcharts/highcharts.js') ?>"></script>
<script src="<?= js('hightcharts/exporting.js') ?>"></script>
<!-- <script src="<?= js('hightcharts/export-data.js') ?>"></script> -->
<script src="<?= js('hightcharts/accessibility.js') ?>"></script>
<script>
    var myCotChart = '';
    var myChartPP = '';
    var oTablePO = '';
    var fnserverparamsPO = {
        period_time: '#period_time'
    };

    var oTableQC = '';
    var fnserverparamsQC = {
        period_time: '#period_time'
    };

    var totalQuantity = 0;
    var totalQuantityQC = 0;
    $(document).ready(function() {
        oTablePO = tnhInitDataTable('#tb-po', '<?= site_url('admin/dashboard/getQuantityPO') ?>', {
            'order': [
                [2, 'desc']
            ],
            'searching': false,
            'paging': false,
            "info": false,
            "dom": '',
            // 'fixedHeader': {
            //     header: true,
            // },
            'responsive': false,
            "ajax": {
                "url": '<?= site_url('admin/dashboard/getQuantityPO') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparamsPO) {
                        d[key] = $(fnserverparamsPO[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    totalQuantity = json.totalQuantity;
                    return json.aaData;
                }
            },
            "columnDefs": [{
                    "targets": 0,
                    'width': '50px'
                },
                {
                    "targets": 1,
                    'width': '150px'
                },
                {
                    "render": function(data, type, row) {
                        precent = tnhFormatNumber(data / totalQuantity * 100);
                        return `<div class="flex-center">
                            <div style="width: 80%; margin-bottom:0px;" class="progress">
                                <div class="progress-bar progress-bar-success-green progress-bar-cs" role="progressbar" aria-valuenow="${precent}" aria-valuemin="0" aria-valuemax="100" style="width:${precent}%;"></div>
                            </div>
                            <div style="width: 20%;">
                                <div class="text-right">${tnhFormatNumber(data)}</div>
                            </div>
                        </div>`;
                    },
                    "targets": 2,
                }
            ]
        });

        oTableQC = tnhInitDataTable('#tb-qc', '<?= site_url('admin/dashboard/getQCDashboard') ?>', {
            'order': [
                [1, 'desc']
            ],
            scrollY: '250px',
            scrollX: true,
            // 'searching': false,
            // 'paging': false,
            "info": false,
            // "dom": '',
            'responsive': false,
            "ajax": {
                "url": '<?= site_url('admin/dashboard/getQCDashboard') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparamsPO) {
                        d[key] = $(fnserverparamsPO[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    totalQuantityQC = json.totalQuantityQC;
                    return json.aaData;
                }
            },
            "columnDefs": [{
                    "targets": 0,
                    'width': '250px'
                },
                {
                    "render": function(data, type, row) {
                        return `<div class="text-center">${tnhFormatNumber(data)}</div>`;
                    },
                    "targets": 1,
                    'width': '100px'
                },
                {
                    "render": function(data, type, row) {
                        precent = tnhFormatNumber(data / totalQuantityQC * 100);
                        return `<div class="text-center">${precent}</div>`;
                    },
                    "targets": 2,
                    'width': '100px',
                    'sortable': false
                }
            ]
        });

        $('#period_time').on('change', function() {
            oTablePO.draw();
            chartQC();
            oTableQC.draw();
        });

        $(document).ready(function() {
            chartQC();
            chartPP();
        });
    });
</script>
<script>
    function chartQC() {
        dataString = {
            period_time: $('#period_time').val(),
            [csrfData['token_name']]: csrfData['hash']
        };
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/dashboard/chartQC',
            data: dataString,
            dataType: "json",
            success: function(response) {
                // if (typeof(myCotChart) !== 'undefined') {
                //     myCotChart.destroy();
                // }
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
            }
        });
    }

    function chartPP() {
        dataString = {
            period_time: $('#period_time').val(),
            [csrfData['token_name']]: csrfData['hash']
        };

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/dashboard/chartPP',
            data: dataString,
            dataType: "json",
            success: function(response) {
                myChartPP = Highcharts.chart('chart-pp', {
                    chart: {
                        type: 'bar'
                    },
                    title: {
                        text: ''
                    },
                    subtitle: {
                        text: ''
                    },
                    xAxis: {
                        categories: response.categories,
                        title: {
                            text: null
                        },

                        labels: {
                            useHTML: true,
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: '<?= lang('quantity_import') ?>',
                            align: 'high'
                        },
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    // tooltip: {
                    //     valueSuffix: ''
                    // },
                    tooltip: {
                        headerFormat: '<b>{point.x}</b><br/>',
                        pointFormat: '{series.name}: {point.y}'
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'top',
                        x: -30,
                        y: 10,
                        floating: true,
                        borderWidth: 1,
                        backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
                        shadow: true
                    },
                    credits: {
                        enabled: false
                    },
                    exporting: {
                        showTable: false
                    },
                    series: [{
                        name: '<?= lang('quantity_import') ?>',
                        data: response.quanttiy,
                    }]
                });
            }
        });

    }

</script>