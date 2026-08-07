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
        <?php
            $year = date('Y');
            $startDate = new DateTime("first day of January $year");
            $endDate = new DateTime("last day of December $year");
        
            $start_date = $startDate->format('d/m/Y');
            $end_date = $endDate->format('d/m/Y');
        ?>
        <div class="row">
            <div class="col-md-2">
                <input type="text" name="start_date" id="start_date" autocomplete="off" class="form-control datepicker" placeholder="<?= lang('start_date') ?>" value="<?= $start_date ?>">
            </div>
            <div class="col-md-2">
                <input type="text" name="end_date" id="end_date" autocomplete="off" class="form-control datepicker" placeholder="<?= lang('end_date') ?>" value="<?= $end_date ?>">
            </div>
            <div class="col-md-1">
                <a class="btn btn-primary mtop1" onclick="filterQuotes()" href="javascript:void(0)"><?= lang('filter') ?></a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                <h2 class="uppercase text-primary"><?= lang('Báo Cáo Báo Giá') ?></h2>
            </div>
        </div>
        <div class="row">
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
            <div class="col-md-3">
                <div class="tnh-card tnh-text-white tnh-bg-success o-hidden h-100" style="height: 80px;">
                    <div class="tnh-card-body">
                        <img src="<?= base_url('assets/images/circle.svg') ?>" class="tnh-card-img-absolute" alt="circle-image">
                        <div class="tnh-h3">Tổng Báo Giá Phát Triển Mẫu<br>
                            <span id="tong-bao-gia-phat-trien-mau">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row col-md-12">
            <hr>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div id="chartDetailQuotes"></div>
            </div>
            <div class="col-md-4">
                <div id="chartQuotesPassFail"></div>
            </div>
        </div>
        <div class="row col-md-12">
            <hr>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="tnh-card tnh-text-white tnh-bg-primary o-hidden h-100" style="height: 80px;">
                    <div class="tnh-card-body">
                        <img src="<?= base_url('assets/images/circle.svg') ?>" class="tnh-card-img-absolute" alt="circle-image">
                        <div class="tnh-h3">Tổng Số Yêu Cầu Phát Triển Mẫu<br>
                            <span id="tong-so-yeu-cau-phat-trien-mau">0</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div id="chartDetailRequestTemplate"></div>
            </div>
        </div>
        <div class="row col-md-12">
            <hr>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="tnh-card tnh-text-white tnh-bg-warning o-hidden h-100" style="height: 80px;">
                    <div class="tnh-card-body">
                        <img src="<?= base_url('assets/images/circle.svg') ?>" class="tnh-card-img-absolute" alt="circle-image">
                        <div class="tnh-h3">Tổng Số Mẫu Đạt<br>
                            <span id="tong-so-mau-dat">0</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="tnh-card tnh-text-white tnh-bg-warning o-hidden h-100" style="height: 80px;">
                    <div class="tnh-card-body">
                        <img src="<?= base_url('assets/images/circle.svg') ?>" class="tnh-card-img-absolute" alt="circle-image">
                        <div class="tnh-h3">Tổng Số Mẫu K Đạt<br>
                            <span id="tong-so-mau-khong-dat">0</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row col-md-12">
                <hr>
            </div>
            <div class="col-md-12">
                <div id="chartProductsSampleMoreTwo"></div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('hightcharts/highcharts.js') ?>"></script>
<script type="text/javascript" src="<?= js('hightcharts/exporting.js') ?>"></script>

<script>
    var params = {
        start_date: "#start_date",
        end_date: "#end_date",
    };

    Highcharts.setOptions({
        chart: {
            style: {
                fontFamily: 'Arial, sans-serif'
            }
        }
    });

    function countQuotes() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in params) {
            dataGET[key] = $(params[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_quotes/countQuotes',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                $('#tong-so-bao-gia').html(response?.count_quotes);
                $('#tong-bao-gia-phat-trien-mau').html(response?.count_quotes_sample);
                $('#tong-so-yeu-cau-phat-trien-mau').html(response?.count_request_template);

                $('#tong-so-mau-dat').html(response?.count_quotes_sample_pass);
                $('#tong-so-mau-khong-dat').html(response?.count_quotes_sample_fail);
            }
        });
    }

    function chartDetailQuotes() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in params) {
            dataGET[key] = $(params[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_quotes/chartDetailQuotes',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                Highcharts.chart('chartDetailQuotes', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Chi Tiết Báo Giá',
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
                            text: 'Số báo giá',
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
                        name: 'Khách hàng',
                        data: response?.series,
                        showInLegend: true,
                        color: '#047edf',
                        dataLabels: {
                            enabled: true, // Bật hiển thị số
                            format: '{point.y}' // Hiển thị giá trị
                        }
                    }, ],
                    credits: {
                        enabled: false
                    }
                });
            }
        });
    }

    function chartQuotesPassFail() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in params) {
            dataGET[key] = $(params[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_quotes/chartQuotesPassFail',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                Highcharts.chart('chartQuotesPassFail', {
                    chart: {
                        type: 'pie',
                    },
                    title: {
                        text: 'Báo Giá Đạt, Không Đạt',
                        style: {
                            fontFamily: 'Arial, sans-serif',
                            fontSize: '20px',
                            color: '#333333'
                        }
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '{point.name}: {point.y} ({point.percentage:.1f}%)',
                                style: {
                                    fontWeight: 'bold',
                                    color: 'black'
                                }
                            },
                            showInLegend: true
                        }
                    },
                    colors: [
                         '#047edf', '#FF5733',
                    ],
                    series: [{
                        name: 'Giá trị',
                        colorByPoint: true,
                        size: '100%',
                        data: response.seriesData
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

    function chartDetailRequestTemplate() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in params) {
            dataGET[key] = $(params[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_quotes/chartDetailRequestTemplate',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                Highcharts.chart('chartDetailRequestTemplate', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Chi Tiết Phát Triển Mẫu',
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
                            text: 'Số YC phát triển mẫu',
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
                        name: 'Khách hàng',
                        data: response?.series,
                        showInLegend: true,
                        color: '#07cdae',
                        dataLabels: {
                            enabled: true, // Bật hiển thị số
                            format: '{point.y}' // Hiển thị giá trị
                        }
                    }, ],
                    credits: {
                        enabled: false
                    }
                });
            }
        });
    }

    function chartProductsSampleMoreTwo() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in params) {
            dataGET[key] = $(params[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_quotes/chartProductsSampleMoreTwo',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                Highcharts.chart('chartProductsSampleMoreTwo', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Tổng Số Mẫu Đánh Lần 2 3',
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
                            text: 'Số báo giá',
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
                        name: 'Sản phẩm',
                        data: response?.series,
                        showInLegend: true,
                        color: '#ffbf96',
                        dataLabels: {
                            enabled: true, // Bật hiển thị số
                            format: '{point.y}' // Hiển thị giá trị
                        }
                    }, ],
                    credits: {
                        enabled: false
                    }
                });
            }
        });
    }

    function filterQuotes() {
        countQuotes();
        chartDetailQuotes();
        chartQuotesPassFail();
        chartDetailRequestTemplate();
        chartProductsSampleMoreTwo();
    }

    $(document).ready(function() {
        countQuotes();
        chartDetailQuotes();
        chartQuotesPassFail();
        chartDetailRequestTemplate();
        chartProductsSampleMoreTwo();
    });
</script>