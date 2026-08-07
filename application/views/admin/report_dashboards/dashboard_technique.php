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
        <div role="tabpanel">
            <div class="tab-content">
                <div id="content-report">
                    <div class="row">
                        <div class="col-md-3">
                            <?php echo render_date_input('start_date', 'Ngày bắt đầu', _d($date_start))?>
                        </div>
                        <div class="col-md-3">
                            <?php echo render_date_input('end_date', 'Ngày kết thúc', _d($date_end))?>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="type_time" class="control-label">Theo</label>
                                <select id="type_time" name="type_time" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                    <option value="day">Ngày</option>
                                    <option value="month" selected>Tháng</option>
                                    <option value="year">Năm</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <a class="btn btn-info mtop25" onclick="chartTechnique()">Lọc</a>
                        </div>
                        <div class="clearfix"></div>
                        <div id="chartTechnique"></div>
                    </div>
                    <hr/>
                    <div class="row mtop40">

                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-3">
                                    <?php echo render_date_input('start_date_detail', 'Ngày bắt đầu', _d($date_start))?>
                                </div>
                                <div class="col-md-3">
                                    <?php echo render_date_input('end_date_detail', 'Ngày kết thúc', _d($date_end))?>
                                </div>
                                <div class="col-md-3">
                                    <a class="btn btn-info mtop25" onclick="chartTechniqueDetail()">Lọc</a>
                                </div>
                                <div class="clearfix"></div>
                                <div id="chartTechniqueDetail"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-5">
                                    <?php echo render_date_input('start_date_desc', 'Ngày bắt đầu', _d($date_start))?>
                                </div>
                                <div class="col-md-5">
                                    <?php echo render_date_input('end_date_desc', 'Ngày kết thúc', _d($date_end))?>
                                </div>
                                <div class="col-md-2">
                                    <a class="btn btn-info mtop25" onclick="chartTechniqueDesc()">Lọc</a>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div id="chartTechniqueDesc"></div>
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
<!--<script src="https://code.highcharts.com/highcharts.js"></script>-->
<!--<script src="https://code.highcharts.com/highcharts-3d.js"></script>-->
<!--<script type="text/javascript" src="--><?php //= js('hightcharts/exporting.js') ?><!--"></script>-->
<!--<script src="https://code.highcharts.com/modules/exporting.js"></script>-->
<!--<script src="https://code.highcharts.com/modules/export-data.js"></script>-->
<!--<script src="https://code.highcharts.com/modules/accessibility.js"></script>-->
<script>
    var fnserverparams = {
        start_date: "#start_date",
        end_date: "#end_date",
        type_time: "#type_time",
    };
    var fnserverparamsDetail = {
        start_date: "#start_date_detail",
        end_date: "#end_date_detail",
    };

    var fnserverparamsDesc = {
        start_date: "#start_date_desc",
        end_date: "#end_date_desc",
    };

    Highcharts.setOptions({
        chart: {
            style: {
                fontFamily: 'Arial, sans-serif' // Thiết lập font chữ cho tất cả biểu đồ
            }
        }
    });


    function chartTechnique() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in fnserverparams) {
            dataGET[key] = $(fnserverparams[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_dashboards/chartTechnique',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                Highcharts.chart('chartTechnique', {
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
                        // labels: {
                        //     style: {
                        //         fontFamily: 'Arial, sans-serif',
                        //         color: '#333333'
                        //     }
                        // }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Chi Phí',
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
                        // colors: [
                        //     '#002060', '#002060', '#002060', '#002060', '#002060', '#002060',
                        //     '#002060', '#002060', '#002060', '#002060', '#002060', '#002060'
                        // ],
                        colorByPoint: true,
                        groupPadding: 0,
                        data: response.series.data ? response.series.data : {},
                        dataLabels: {
                            enabled: true,
                            rotation: -90,
                            color: '#FFFFFF',
                            inside: true,
                            verticalAlign: 'middle',
                            format: '{point.y:.1f}', // one decimal
                            y: 10, // 10 pixels down from the top
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif'
                            }
                        }
                    }],
                    credits: {
                        enabled: false
                    }
                });
            }
        });
    }

    function chartTechniqueDetail() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in fnserverparamsDetail) {
            dataGET[key] = $(fnserverparamsDetail[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_dashboards/chartTechniqueDetail',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                Highcharts.chart('chartTechniqueDetail', {
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
                            text: 'Chi Phí',
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
                        dataLabels: {
                            enabled: true,
                            rotation: 0,
                            color: '#FFFFFF',
                            inside: true,
                            verticalAlign: 'top',
                            format: '{point.y:.1f}', // one decimal
                            y: 1, // 10 pixels down from the top
                            style: {
                                fontSize: '9px',
                                fontFamily: 'Verdana, sans-serif'
                            }
                        }
                    }],
                    credits: {
                        enabled: false
                    }
                });
            }
        });
    }

    function chartTechniqueDesc() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in fnserverparamsDesc) {
            dataGET[key] = $(fnserverparamsDesc[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_dashboards/chartTechniqueDesc',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                console.log(response.series.data);
                Highcharts.chart('chartTechniqueDesc', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45,
                            beta: 0
                        }
                    },
                    title: {
                        text: 'Những Loại Danh Mục Thiết Bị Được Bảo Trì Nhiều Nhất',
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
                                format: '{point.name}'
                            }
                        }
                    },
                    series: [{
                        type: 'pie',
                        name: 'Phần Trăm',
                        data : response.series.data,
                    }]
                });
            }
        });
    }
    $(document).ready(function() {
        chartTechnique();
        chartTechniqueDetail();
        chartTechniqueDesc();
    });
</script>