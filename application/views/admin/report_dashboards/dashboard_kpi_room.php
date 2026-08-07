<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(false); ?>
<style>
    #wrapper {
        margin: 0;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
        </div>
    </div>
    <div class="content">
        <div role="tabpanel">
            <div class="tab-content">
                <div id="content-report">
                    <div class="row">
                        <div class="col-md-6">
                            <div id="chartViolate"></div>
                        </div>
                        <div class="col-md-6">
                            <div id="chartRadioViolate"></div>
                        </div>
                    </div>
                    <div class="row mtop30">
                        <div class="col-md-6">
                            <div id="chartReportKPIRoom"></div>
                        </div>
                        <div class="col-md-6">
                            <div id="chartPointRoom"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('hightcharts/highcharts.js') ?>"></script>
<script type="text/javascript" src="<?= js('hightcharts/exporting.js') ?>"></script>
<script>
    var fnserverparams = {
        year: "#year",
    };

    Highcharts.setOptions({
        chart: {
            style: {
                fontFamily: 'Arial, sans-serif' // Thiết lập font chữ cho tất cả biểu đồ
            }
        }
    });


    function chartViolate() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in fnserverparams) {
            dataGET[key] = $(fnserverparams[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_dashboards/chartViolate',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                Highcharts.chart('chartViolate', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: response.title
                    },
                    xAxis: {
                        categories: response.categories,
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Tổng số'
                        }
                    },
                    legend: {
                        align: 'left',
                        verticalAlign: 'left',
                        x: 70,
                        y: 20,
                        // floating: true,
                        backgroundColor:
                            Highcharts.defaultOptions.legend.backgroundColor || 'white',
                        borderColor: '#CCC',
                        borderWidth: 1,
                        shadow: false
                    },
                    tooltip: {
                        headerFormat: '<b>{point.x}</b><br/>',
                        pointFormat: '{series.name}: {point.y}<br/>Tổng: {point.stackTotal}'
                    },
                    plotOptions: {
                        column: {
                            stacking: 'normal'
                        }
                    },
                    series: response.series,
                    credits: {
                        enabled: false // Tắt tín dụng Highcharts
                    },
                });
            }
        });
    }

    function chartRadioViolate() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in fnserverparams) {
            dataGET[key] = $(fnserverparams[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_dashboards/chartRadioViolate',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                Highcharts.chart('chartRadioViolate', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45,
                            beta: 0
                        }
                    },
                    title: {
                        text: response.title,
                        align: 'left'
                    },
                    accessibility: {
                        point: {
                            valueSuffix: '%'
                        }
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            depth: 35,
                            dataLabels: {
                                enabled: true,
                                format: '{point.name}: <b>{point.percentage:.1f}%</b>'
                            }
                        }
                    },
                    series: [{
                        type: 'pie',
                        name: 'Phần Trăm',
                        data : response.series.data,
                    }],
                    credits: {
                        enabled: false
                    }
                });
            }
        });
    }

    function chartReportKPIRoom() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in fnserverparams) {
            dataGET[key] = $(fnserverparams[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_dashboards/chartReportKPIRoom',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                Highcharts.chart('chartReportKPIRoom', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: response.title,
                        style: {
                            fontFamily: 'Arial, sans-serif',
                            fontSize: '20px',
                            color: '#333333'
                        }
                    },
                    xAxis: {
                        categories: response?.categories,
                        labels: {
                            useHTML: true,
                            formatter: function() {
                                return this.value;
                            }
                        },
                        title: {
                            text: '',
                            style: {
                                fontFamily: 'Arial, sans-serif',
                                color: '#333333'
                            }
                        },
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: '',
                            style: {
                                fontFamily: 'Arial, sans-serif',
                                color: '#333333'
                            }
                        },
                        labels: {
                            style: {
                                fontFamily: 'Arial, sans-serif',
                                color: '#333333'
                            }
                        }
                    },
                    series: response.series,
                    credits: {
                        enabled: false
                    }
                });
            }
        });
    }

    function chartPointRoom() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in fnserverparams) {
            dataGET[key] = $(fnserverparams[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_dashboards/chartPointRoom',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                Highcharts.chart('chartPointRoom', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: response.title,
                        style: {
                            fontFamily: 'Arial, sans-serif',
                            fontSize: '20px',
                            color: '#333333'
                        }
                    },
                    xAxis: {
                        categories: response?.categories,
                        labels: {
                            useHTML: true,
                            formatter: function() {
                                return this.value; // Thay 'Dòng 2' và 'Dòng 3' bằng nội dung bạn muốn
                            }
                        },
                        title: {
                            text: '',
                            style: {
                                fontFamily: 'Arial, sans-serif',
                                color: '#333333'
                            }
                        },
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: '',
                            style: {
                                fontFamily: 'Arial, sans-serif',
                                color: '#333333'
                            }
                        },
                        labels: {
                            style: {
                                fontFamily: 'Arial, sans-serif',
                                color: '#333333'
                            }
                        }
                    },
                    series: [{
                        name: response.series.name,
                        showInLegend: false,
                        colors: response.series.colors,
                        colorByPoint: true,
                        groupPadding: 0,
                        data: response.series.data ? response.series.data : {},
                    }],
                    credits: {
                        enabled: false
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        chartViolate();
        chartRadioViolate();
        chartReportKPIRoom();
        chartPointRoom();
    });
</script>