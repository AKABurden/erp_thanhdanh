<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .card {
        background: #fff;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,.08);
        margin-top: 10px;
        border:1px solid #eee;
    }

    .kpi-title {
        color: #777;
        font-size: 13px;
    }

    .kpi-value {
        font-size: 26px;
        font-weight: bold;
    }

    .kpi-note {
        font-size: 12px;
        margin-top: 5px;
    }

    .red { color: #e74c3c; }
    .green { color: #27ae60; }
    .blue { color: #3498db; }
    .yellow { color: #f1c40f; }

    /* Chart */
    .bar-row {
        margin-bottom: 12px;
    }

    .bar-label {
        font-size: 13px;
        margin-bottom: 3px;
    }

    .bar-bg {
        background: #eaeaea;
        height: 16px;
        border-radius: 4px;
    }

    .bar-fill {
        height: 16px;
        border-radius: 4px;
    }

    .bar-green { background: #2ecc71; }
    .bar-yellow { background: #f1c40f; }
    .bar-red { background: #e74c3c; }

    /* Alert list */
    .alert-box {
        border-left: 5px solid;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 4px;
    }

    .alert-green {
        background: #ecf9f1;
        border-color: #2ecc71;
    }

    .alert-yellow {
        background: #fff8e1;
        border-color: #f1c40f;
    }

    .alert-title {
        font-weight: bold;
    }

    .score {
        font-size: 18px;
        font-weight: bold;
        float: right;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right hide" href="javascript:void(0)">Xuất Excel</a>
                <a href="<?= base_url('admin/personnel_assessment/importExcel') ?>" class="tnh-modal hide pull-right mright5 btn btn-info H_action_button">
                    <?php echo _l('Import Excel'); ?>
                </a>
                <?php if ($this->preAddPersonnelAssessment): ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/personnel_assessment/detail?type='.$type.'') ?>" class="btn btn-info H_action_button">
                            <?php echo _l('add'); ?>
                        </a>
                    </div>
                <?php endif ?>
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
                            </div>
                            <div class="clearfix"></div>
                            <div class="horizontal-scrollable-tabs">
                                <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                                <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                                <div class="horizontal-tabs">
                                    <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                        <li role="presentation" class="active">
                                            <a href="#dashboard" aria-controls="all" role="dashboard" value="dashboard"
                                               data-toggle="tab"><?= lang('Dashboard') ?></a>
                                        </li>
                                        <li role="presentation" class="">
                                            <a href="#all" aria-controls="all" role="tab" value="all"
                                               data-toggle="tab"><?= lang('all') ?></a>
                                        </li>
                                        <?php foreach ($dtRoom as $key => $value): ?>
                                        <li role="presentation">
                                            <a href="#<?= $value['id'] ?>" aria-controls="<?= $value['id'] ?>" role="tab"
                                               value="<?= $value['id'] ?>"
                                               data-toggle="tab"><?= $value['name'] ?></a>
                                        </li>
                                        <?php endforeach ?>
                                    </ul>
                                </div>
                                <input type="hidden" name="status_table" id="status_table"
                                       class="form-control status_table" value="all">
                            </div>
                            <div class="view_list hide">
                                <table id="table-personnel-assessment" class="table dt-tnh table-personnel-assessment" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Mã phiếu') ?></th>
                                        <th class="text-center"><?= lang('Nhân viên/Ứng viên') ?></th>
                                        <th class="text-center"><?= lang('Mã vị trí') ?></th>
                                        <th class="text-center"><?= lang('Phòng ban') ?></th>
                                        <th class="text-center"><?= lang('Cấp độ vai trò') ?></th>
                                        <th class="text-center"><?= lang('Loại') ?></th>
                                        <th class="text-center"><?= lang('Tổng điểm') ?></th>
                                        <th class="text-center"><?= lang('Xếp loại') ?></th>
                                        <th class="text-center"><?= lang('Cảnh báo/Hướng xử lý') ?></th>
                                        <th class="text-center"><?= lang('Ghi chú') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="view_dashboard ">
                                <div class="row">
                                    <div class="col-md-12 result_box">
                                    </div>

                                    <!-- Main -->
                                    <div class="col-md-12">

                                        <!-- Chart -->
                                        <div class="col-md-7">
                                            <div class="card">
                                                <div id="container_room" style="height: 550px"></div>
                                            </div>
                                        </div>
                                        <!-- Alerts -->
                                        <div class="col-sm-5">
                                            <div class="card">
                                                <div id="container_rating"></div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
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
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script>
    var oTable = '';

    var fnserverparams = {
        status_table: '#status_table',
    };
    function initList(){
        oTable = tnhInitDataTable('#table-personnel-assessment',
            '<?= site_url('admin/personnel_assessment/getEvaluationEmployee') ?>', {
                'order': [
                    [0, 'desc']
                ],
                "ajax": {
                    "url": '<?= site_url('admin/personnel_assessment/getEvaluationEmployee?type='.$type.'') ?>',
                    "type": "POST",
                    "data": function(d) {
                        if (typeof(csrfData) !== 'undefined') {
                            d[csrfData['token_name']] = csrfData['hash'];
                        }
                        for (var key in fnserverparams) {
                            d[key] = $(fnserverparams[key]).val();
                        }
                        if (table.attr('data-last-order-identifier')) {
                            d['last_order_identifier'] = table.attr('data-last-order-identifier');
                        }
                    },
                    "dataSrc": function(json) {
                        return json.aaData;
                    }
                },
                "createdRow": function(row, data, index) {
                },
                "columnDefs": [
                ],
            });
    }

    $(document).on('change',
        '#room_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });
    $(function() {
        initList();
        initDashboard();
    });
    $(document).on('click', '.status-table li a', function(event) {
        status_table = $(this).attr('value');
        $('#status_table').val(status_table);
        if (status_table == 'dashboard'){
            $(".view_dashboard").removeClass('hide');
            $(".view_list").addClass('hide');
            initDashboard();
        } else {
            $(".view_list").removeClass('hide');
            $(".view_dashboard").addClass('hide');
            if (typeof oTable != 'undefined' && oTable != '') {
                oTable.draw();
            } else {
                initList();
            }
        }
    });
    function initDashboard(){
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/personnel_assessment/initDashboard?type=<?= $type ?>',
            data: {
                csrf_token_name: hash,
                type:<?= $type ?>
            },
            dataType: "json",
            success: function(response) {
                htmlBox = '';
                $.each(response.dtRatingList,function (k,v){
                    htmlBox += `<div class="col-sm-3">
                                    <div class="card">
                                        <div class="kpi-title">${v.rating}</div>
                                        <div class="kpi-value">${v.count}</div>
                                        <div class="kpi-note blue">${v.warning}</div>
                                    </div>
                                </div>`;
                })
                $(".result_box").html(htmlBox);
                loadCharDashboard(response.categories,response.series);
                loadChartRating(response.categoriesRating,response.seriesRating);
            }
        });
    }

    function loadCharDashboard(categories,series){
        Highcharts.chart('container_room', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Năng lực theo phòng ban',
                style: {
                    fontSize: '20px' // Tăng font size cho title
                }
            },
            xAxis: {
                categories: categories,
                title: {
                    text: null
                },
                gridLineWidth: 1,
                lineWidth: 0,
                labels: {
                    style: {
                        fontSize: '14px' // Tăng kích thước font các nhãn trục X
                    }
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: '',
                    align: 'high',
                    style: {
                        fontSize: '14px' // Tăng kích thước font tiêu đề yAxis
                    }
                },
                labels: {
                    overflow: 'justify',
                    style: {
                        fontSize: '14px' // Tăng kích thước font nhãn trục Y
                    },
                    formatter: function () {
                        return Math.round(this.value); // Làm tròn giá trị yAxis thành số nguyên
                    }
                },
                gridLineWidth: 0,
                tickInterval: 1,
            },
            tooltip: {
                valueSuffix: '',
                style: {
                    fontSize: '14px' // Tăng kích thước font tooltip
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: '50%',
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '13px'
                        }
                    },
                    groupPadding: 0.1,
                    pointWidth: 5
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -10,
                y: 80,
                floating: true,
                borderWidth: 1,
                backgroundColor: 'var(--highcharts-background-color, #ffffff)',
                shadow: true,
                itemStyle: {
                    fontSize: '12px' // Tăng kích thước font trong legend
                }
            },
            credits: {
                enabled: false
            },
            series: series
        });
    }

    function loadChartRating(categories,series){
        Highcharts.chart('container_rating', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Phân bố xếp loại (toàn bộ phòng ban)',
                style: {
                    fontSize: '20px' // Tăng font size cho title
                }
            },
            xAxis: {
                categories: categories,
                crosshair: true,
                labels: {
                    style: {
                        fontSize: '16px'
                    }
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: '',
                    style: {
                        fontSize: '16px'
                    }
                },
                labels: {
                    style: {
                        fontSize: '16px'
                    }
                }
            },
            tooltip: {
                valueSuffix: '',
                style: {
                    fontSize: '14px' // Tăng font size cho tooltip
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: series
        });
    }

    function exportExcel() {
        room_search = $('#room_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/category_regulations/exportExcelQuestionBank',
            data: {
                csrf_token_name: hash,
                room_search: room_search,
                export_excel: 1,
            },
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }
</script>