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
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-3">
                <select name="year" id="year" data-live-search="true" class="form-control selectpicker">
                    <?php $currentYear = date("Y"); ?>
                    <?php for ($i = 0; $i < 4; $i++): ?>
                        <?php
                        $year = $currentYear - $i;
                        ?>
                        <option value="<?= $year ?>"><?= $year ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-1">
                <a class="btn btn-primary mtop1" onclick="filterChart()" href="javascript:void(0)"><?= lang('filter') ?></a>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div id="chartTotalScrapValue"></div>
            </div>
            <div class="col-md-12">
                <hr>
            </div>
            <div class="col-md-12">
                <div id="chartScrapRate"></div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('hightcharts/highcharts.js') ?>"></script>
<script type="text/javascript" src="<?= js('hightcharts/exporting.js') ?>"></script>

<script>
    var fnserverparams = {
        year: "#year",
    };

    function chartTotalScrapValue() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in fnserverparams) {
            dataGET[key] = $(fnserverparams[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_dashboards/chartTotalScrapValue',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                Highcharts.chart('chartTotalScrapValue', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Quản Lý Chất Lượng',
                        style: {
                            fontFamily: 'Arial, sans-serif',
                            fontSize: '20px',
                            color: '#333333'
                        }
                    },
                    xAxis: {
                        categories: response?.categories,
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
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Giá Trị',
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
                        name: '',
                        data: response?.series,
                        showInLegend: false,
                        color: 'red'
                    }, ],
                    credits: {
                        enabled: false
                    }
                });
            }
        });
    }

    function chartScrapRate() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in fnserverparams) {
            dataGET[key] = $(fnserverparams[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_dashboards/chartScrapRate',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                Highcharts.chart('chartScrapRate', {
                    chart: {
                        type: 'pie'
                    },
                    title: {
                        text: 'Tỷ Lệ Phế',
                        style: {
                            fontFamily: 'Arial, sans-serif',
                        }
                    },
                    plotOptions: {
                        pie: {
                            innerSize: '50%', // Kích thước lỗ ở giữa
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '{point.name}: {point.percentage:.1f} %'
                            },
                            showInLegend: true
                        }
                    },
                    colors: [
                        '#FF5733', '#33FF57', '#3357FF', '#F0E68C',
                        '#FF33A1', '#A133FF', '#33FFF5', '#FF8C33',
                        '#FF3333', '#33FF8C', '#8C33FF', '#FF33D4',
                        '#33D4FF', '#D4FF33', '#FF33B8', '#B8FF33',
                        '#FF33A8', '#A8FF33', '#33FFB8', '#B8A8FF',
                        '#A8B8FF', '#B8A8D4', '#D4A8FF', '#FFD4A8'
                    ],
                    series: [{
                        name: 'Phần trăm',
                        colorByPoint: true,
                        size: '100%',
                        data: response?.series_data
                    }],
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom',
                        itemMarginTop: 5,
                        itemMarginBottom: 5,
                        itemStyle: {
                            fontFamily: 'Arial, sans-serif', // Font chữ
                        }
                    },
                    credits: {
                        enabled: false
                    }
                });
            }
        });
    }

    function filterChart() {
        chartTotalScrapValue();
        chartScrapRate();
    }

    $(document).ready(function() {
        chartTotalScrapValue();
        chartScrapRate();
    });
</script>