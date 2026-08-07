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
                            <a class="btn btn-info mtop25" onclick="chartInternalRoom()">Lọc</a>
                        </div>
                        <div class="clearfix"></div>
                        <div id="chartInternalRoom"></div>
                    </div>
                    <div class="clearfix"></div>
                    <hr/>
                    <div class="row mtop40">
                        <div class="col-md-3">
                            <?php echo render_date_input('date_start', 'Ngày bắt đầu', _d($date_start))?>
                        </div>
                        <div class="col-md-3">
                            <?php echo render_date_input('date_end', 'Ngày kết thúc', _d($date_end))?>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="time_type" class="control-label">Theo</label>
                                <select id="time_type" name="time_type" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                    <option value="day">Ngày</option>
                                    <option value="month" selected>Tháng</option>
                                    <option value="year">Năm</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <a class="btn btn-info mtop25" onclick="chartInternalControl()">Lọc</a>
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-md-4">
                            <div id="chartInternalControl_1"></div>
                        </div>
                        <div class="col-md-4">
                            <div id="chartInternalControl_2"></div>
                        </div>
                        <div class="col-md-4">
                            <div id="chartInternalControl_3"></div>
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
    init_selectpicker();
    var fnserverparams = {
        start_date: "#start_date",
        end_date: "#end_date",
        type_time: "#type_time",
    };

    var fnserverparamsControl = {
        start_date: 'input[name="date_start"]',
        end_date: 'input[name="date_end"]',
        type_time: 'select[name="time_type"]'
    };

    Highcharts.setOptions({
        chart: {
            style: {
                fontFamily: 'Arial, sans-serif' // Thiết lập font chữ cho tất cả biểu đồ
            }
        }
    });


    function chartInternalRoom() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in fnserverparams) {
            dataGET[key] = $(fnserverparams[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_dashboards/chartInternalRoom',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                Highcharts.chart('chartInternalRoom', {
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

    function chartInternalControl() {
        var dataGET = {};
        dataGET[csrf_token_name] = hash;
        for (var key in fnserverparamsControl) {
            dataGET[key] = $(fnserverparamsControl[key]).val();
        }

        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/report_dashboards/chartInternalControl',
            data: dataGET,
            dataType: "json",
            success: function(response) {
                list_title = response.list_title
                $.each(response.series, function(type, value) {
                    console.log(value);
                        Highcharts.chart(`chartInternalControl_${type}`, {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: list_title[type],
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
                        series: value,
                        credits: {
                            enabled: false
                        }
                    });
                })
            }
        });
    }

    $(document).ready(function() {
        chartInternalRoom();
        chartInternalControl();
        // chartInternalControl(2);
        // chartInternalControl(3);
    });
</script>