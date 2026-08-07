<?php if ($_value == 1): ?>
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
        <div class="col-md-12 text-center">
            <h2 class="uppercase text-primary"><?= lang('Báo Cáo Sản Xuất') ?></h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div id="chartTotalAnnualProductivity"></div>
        </div>
        <div class="col-md-6">
            <div id="chartRoundTotalAnnualProductivity"></div>
        </div>
        <div class="col-md-12">
            <div id="chartProductivityRate"></div>
        </div>
    </div>

    <script>
        function chartTotalAnnualProductivity() {
            var dataGET = {};
            dataGET[csrf_token_name] = hash;
            for (var key in fnserverparams) {
                dataGET[key] = $(fnserverparams[key]).val();
            }

            $.ajax({
                type: "GET",
                url: site.base_url + 'admin/report_dashboards/chartTotalAnnualProductivity',
                data: dataGET,
                dataType: "json",
                success: function(response) {
                    Highcharts.chart('chartTotalAnnualProductivity', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Tổng Năng Suất Năm',
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
                                text: 'Số lượng',
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
                            color: '#006400'
                        }, ],
                        credits: {
                            enabled: false
                        }
                    });
                }
            });
        }

        function chartRoundTotalAnnualProductivity() {
            var dataGET = {};
            dataGET[csrf_token_name] = hash;
            for (var key in fnserverparams) {
                dataGET[key] = $(fnserverparams[key]).val();
            }

            $.ajax({
                type: "GET",
                url: site.base_url + 'admin/report_dashboards/chartRoundTotalAnnualProductivity',
                data: dataGET,
                dataType: "json",
                success: function(response) {
                    Highcharts.chart('chartRoundTotalAnnualProductivity', {
                        chart: {
                            type: 'pie',
                        },
                        title: {
                            text: 'Tỷ Lệ Ngừng Máy Sản Xuất',
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
                                    format: '{point.name}: {point.y}',
                                    style: {
                                        fontWeight: 'bold',
                                        color: 'black'
                                    }
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

        function chartProductivityRate() {
            var dataGET = {};
            dataGET[csrf_token_name] = hash;
            for (var key in fnserverparams) {
                dataGET[key] = $(fnserverparams[key]).val();
            }

            $.ajax({
                type: "GET",
                url: site.base_url + 'admin/report_dashboards/chartProductivityRate',
                data: dataGET,
                dataType: "json",
                success: function(response) {
                    Highcharts.chart('chartProductivityRate', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Tỷ lệ nặng suất',
                            style: {
                                fontFamily: 'Arial, sans-serif',
                                fontSize: '20px',
                                color: '#333333'
                            }
                        },
                        xAxis: {
                            categories: response.categories,
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
                                text: 'Số lượng',
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

        function filterChart() {
            chartTotalAnnualProductivity();
            chartRoundTotalAnnualProductivity();
            chartProductivityRate();
        }

        $(document).ready(function() {
            chartTotalAnnualProductivity();
            chartRoundTotalAnnualProductivity();
            chartProductivityRate();
        });
    </script>
<?php elseif ($_value == 2): ?>
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
            <a class="btn btn-primary mtop1" onclick="filterChartTwo()" href="javascript:void(0)"><?= lang('filter') ?></a>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12 text-center">
            <h2 class="uppercase text-primary"><?= lang('Báo Cáo Tổng Quan Sản Xuất') ?></h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="tnh-card tnh-text-white tnh-bg-success o-hidden h-100" style="height: 80px;">
                <div class="tnh-card-body">
                    <img src="<?= base_url('assets/images/circle.svg') ?>" class="tnh-card-img-absolute" alt="circle-image">
                    <div class="tnh-h3">Số LSX theo đơn<br>
                        <span id="so-lsx-theo-don">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="tnh-card tnh-text-white tnh-bg-success o-hidden h-100" style="height: 80px;">
                <div class="tnh-card-body">
                    <img src="<?= base_url('assets/images/circle.svg') ?>" class="tnh-card-img-absolute" alt="circle-image">
                    <div class="tnh-h3">Số lượng LSX theo đơn<br>
                        <span id="so-luong-lsx-theo-don">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="tnh-card tnh-text-white tnh-bg-success o-hidden h-100" style="height: 80px;">
                <div class="tnh-card-body">
                    <img src="<?= base_url('assets/images/circle.svg') ?>" class="tnh-card-img-absolute" alt="circle-image">
                    <div class="tnh-h3">Số LSX dự phòng<br>
                        <span id="so-lsx-du-phong">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="tnh-card tnh-text-white tnh-bg-success o-hidden h-100" style="height: 80px;">
                <div class="tnh-card-body">
                    <img src="<?= base_url('assets/images/circle.svg') ?>" class="tnh-card-img-absolute" alt="circle-image">
                    <div class="tnh-h3">Số lượng LSX dự phòng<br>
                        <span id="so-luong-lsx-du-phong">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <div id="chartNumberProductionByTypeOrder"></div>
        </div>
        <div class="col-md-6">
            <div id="chartNumberGroupPO"></div>
        </div>
        <div class="col-md-12">
            <div id="chartQualityFinishedProduct"></div>
        </div>
        <div class="col-md-12">
            <div id="tableQualityFinishedProduct"></div>
        </div>
    </div>

    <script>
        var params = {
            start_date: "#start_date",
            end_date: "#end_date",
        };

        function countPOByOrder() {
            var dataGET = {};
            dataGET[csrf_token_name] = hash;
            for (var key in params) {
                dataGET[key] = $(params[key]).val();
            }

            $.ajax({
                type: "GET",
                url: site.base_url + 'admin/report_dashboards/countPOByOrder',
                data: dataGET,
                dataType: "json",
                success: function(response) {
                    $('#so-lsx-theo-don').html(response.count_po);
                    $('#so-luong-lsx-theo-don').html(response.total_quantity_po);

                    $('#so-lsx-du-phong').html(response.count_po_preventive);
                    $('#so-luong-lsx-du-phong').html(response.total_quantity_po_preventive);
                }
            });
        }

        function chartNumberProductionByTypeOrder() {
            var dataGET = {};
            dataGET[csrf_token_name] = hash;
            for (var key in params) {
                dataGET[key] = $(params[key]).val();
            }

            $.ajax({
                type: "GET",
                url: site.base_url + 'admin/report_dashboards/chartNumberProductionByTypeOrder',
                data: dataGET,
                dataType: "json",
                success: function(response) {
                    Highcharts.chart('chartNumberProductionByTypeOrder', {
                        chart: {
                            type: 'bar'
                        },
                        title: {
                            text: 'Số Lệnh Sản Xuất Theo Loại Đơn Hàng'
                        },
                        xAxis: {
                            categories: response.categories
                        },
                        yAxis: {
                            title: {
                                text: 'Số lệnh sản xuất'
                            }
                        },
                        series: [{
                            name: '',
                            showInLegend: false,
                            data: response.series
                        }],
                        credits: {
                            enabled: false
                        }
                    });
                }
            });
        }

        function chartNumberGroupPO() {
            var dataGET = {};
            dataGET[csrf_token_name] = hash;
            for (var key in params) {
                dataGET[key] = $(params[key]).val();
            }

            $.ajax({
                type: "GET",
                url: site.base_url + 'admin/report_dashboards/chartNumberGroupPO',
                data: dataGET,
                dataType: "json",
                success: function(response) {
                    Highcharts.chart('chartNumberGroupPO', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Báo Cáo Số Lượng Nhóm Công Đoạn In',
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
                                text: 'Số lượng',
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
                        series: response?.series,
                        credits: {
                            enabled: false
                        }
                    });
                }
            });
        }

        function chartQualityFinishedProduct() {
            var dataGET = {};
            dataGET[csrf_token_name] = hash;
            for (var key in params) {
                dataGET[key] = $(params[key]).val();
            }

            $.ajax({
                type: "GET",
                url: site.base_url + 'admin/report_dashboards/chartQualityFinishedProduct',
                data: dataGET,
                dataType: "json",
                success: function(response) {
                    Highcharts.chart('chartQualityFinishedProduct', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Chất lượng thành phẩm tuần',
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
                                text: 'Số lượng',
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
                        series: response?.series,
                        credits: {
                            enabled: false
                        }
                    });

                    //table
                    if (response?.list) {
                        var tHead = '';
                        var tBody = '';
                        var tdHead = '<th style="min-width: 80px;"></th>';
                        var tdOne = '';
                        var tdTwo = '';
                        var tdThree = '';
                        var tdFour = '';
                        $.each(response?.list, function(index, value) {
                            tdHead += `<th class="text-center">${value.name}</th>`;
                            tdOne += `<td class="text-center">${tnhFormatNumber(value?.data?._quantity)}</td>`;
                            tdTwo += `<td class="text-center">${tnhFormatNumber(value?.data?._quantityPurchase)}</td>`;
                            tdThree += `<td class="text-center">${tnhFormatNumber(value?.data?._quantityError)}</td>`;
                            tdFour += `<td class="text-center">${tnhFormatNumber(value?.data?.rate)}</td>`;
                        });
                        tHead += `<tr>${tdHead}</tr>`;
                        var trOne = `<tr>
                            <td style="color: green;">Tổng SX</td>
                            ${tdOne}
                        </tr>`;

                        var trTwo = `<tr>
                            <td style="color: blue;">Đạt</td>
                            ${tdTwo}
                        </tr>`;

                        var trThree = `<tr>
                            <td style="color: red;">Lỗi</td>
                            ${tdThree}
                        </tr>`;

                        var trFour = `<tr>
                            <td style="color: red;">% Lỗi</td>
                            ${tdFour}
                        </tr>`;

                        var tableQualityFinishedProduct = `
                        <div class="table-responsive">
                            <table class="table dataTable">
                                <thead>${tHead}</thead>
                                <tbody>
                                    ${trOne}
                                    ${trTwo}
                                    ${trThree}
                                    ${trFour}
                                </tbody>
                            </table>
                        </div>
                         `;
                        $('#tableQualityFinishedProduct').html(tableQualityFinishedProduct);
                    }
                }
            });
        }

        function filterChartTwo() {
            countPOByOrder();
            chartNumberProductionByTypeOrder();
            chartNumberGroupPO();
            chartQualityFinishedProduct();
        }

        $(document).ready(function() {
            countPOByOrder();
            chartNumberProductionByTypeOrder();
            chartNumberGroupPO();
            chartQualityFinishedProduct();
        });
    </script>

<?php elseif ($_value == 3): ?>
    <?php
    $year = date('Y');
    $startDate = new DateTime("first day of January 2022");
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
            <a class="btn btn-primary mtop1" onclick="filterChartThree()" href="javascript:void(0)"><?= lang('filter') ?></a>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12 text-center">
            <h2 class="uppercase text-primary"><?= lang('Báo Cáo Tổng Quan QC') ?></h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="tnh-card tnh-text-white tnh-bg-success o-hidden h-100" style="height: 80px;">
                <div class="tnh-card-body">
                    <img src="<?= base_url('assets/images/circle.svg') ?>" class="tnh-card-img-absolute" alt="circle-image">
                    <div class="tnh-h3">Sản Lượng QC<br>
                        <span id="san-luong-qc">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="tnh-card tnh-text-white tnh-bg-success o-hidden h-100" style="height: 80px;">
                <div class="tnh-card-body">
                    <img src="<?= base_url('assets/images/circle.svg') ?>" class="tnh-card-img-absolute" alt="circle-image">
                    <div class="tnh-h3">Sản Lượng Đạt<br>
                        <span id="san-luong-dat">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="tnh-card tnh-text-white tnh-bg-success o-hidden h-100" style="height: 80px;">
                <div class="tnh-card-body">
                    <img src="<?= base_url('assets/images/circle.svg') ?>" class="tnh-card-img-absolute" alt="circle-image">
                    <div class="tnh-h3">Sản Lượng Lỗi<br>
                        <span id="san-luong-loi">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div id="chartNumberErrorsByErrorName"></div>
        </div>
        <div class="col-md-12">
            <hr>
        </div>
        <div class="col-md-6">
            <div id="chartTopProductErrors"></div>
        </div>
        <div class="col-md-6">
            <div id="chartTopClientErrors"></div>
        </div>
    </div>

    <script>
        var params = {
            start_date: "#start_date",
            end_date: "#end_date",
        };

        function countQC() {
            var dataGET = {};
            dataGET[csrf_token_name] = hash;
            for (var key in params) {
                dataGET[key] = $(params[key]).val();
            }

            $.ajax({
                type: "GET",
                url: site.base_url + 'admin/report_dashboards/countQC',
                data: dataGET,
                dataType: "json",
                success: function(response) {
                    $('#san-luong-qc').html(response?.quantity_qc);
                    $('#san-luong-dat').html(response?.quantity_success);
                    $('#san-luong-loi').html(response?.quantity_recycling);
                }
            });
        }

        function chartNumberErrorsByErrorName() {
            var dataGET = {};
            dataGET[csrf_token_name] = hash;
            for (var key in params) {
                dataGET[key] = $(params[key]).val();
            }

            $.ajax({
                type: "GET",
                url: site.base_url + 'admin/report_dashboards/chartNumberErrorsByErrorName',
                data: dataGET,
                dataType: "json",
                success: function(response) {
                    Highcharts.chart('chartNumberErrorsByErrorName', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Báo Cáo Số Lượng Lỗi Theo Tên Lỗi',
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
                                text: 'Số lượng',
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
                            color: '#047edf'
                        }, ],
                        credits: {
                            enabled: false
                        }
                    });
                }
            });
        }

        function chartTopProductErrors() {
            var dataGET = {};
            dataGET[csrf_token_name] = hash;
            for (var key in params) {
                dataGET[key] = $(params[key]).val();
            }

            $.ajax({
                type: "GET",
                url: site.base_url + 'admin/report_dashboards/chartTopProductErrors',
                data: dataGET,
                dataType: "json",
                success: function(response) {
                    Highcharts.chart('chartTopProductErrors', {
                        chart: {
                            type: 'bar'
                        },
                        title: {
                            text: 'Top 10 Sản Phẩm Lỗi Nhiều Nhất',
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
                                text: 'Số lượng',
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
                            color: '#006400'
                        }, ],
                        credits: {
                            enabled: false
                        }
                    });
                }
            });
        }

        function chartTopClientErrors() {
            var dataGET = {};
            dataGET[csrf_token_name] = hash;
            for (var key in params) {
                dataGET[key] = $(params[key]).val();
            }

            $.ajax({
                type: "GET",
                url: site.base_url + 'admin/report_dashboards/chartTopClientErrors',
                data: dataGET,
                dataType: "json",
                success: function(response) {
                    Highcharts.chart('chartTopClientErrors', {
                        chart: {
                            type: 'pie',
                        },
                        title: {
                            text: 'Top 10 Hàng Lỗi Theo Khách Hàng',
                            style: {
                                fontFamily: 'Arial, sans-serif',
                                fontSize: '20px',
                                color: '#333333'
                            }
                        },
                        plotOptions: {
                            pie: {
                                innerSize: '50%',
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
                            '#FF5733', '#33FF57', '#3357FF', '#F0E68C',
                            '#FF33A1', '#A133FF', '#33FFF5', '#FF8C33',
                            '#FF3333', '#33FF8C', '#8C33FF', '#FF33D4',
                            '#33D4FF', '#D4FF33', '#FF33B8', '#B8FF33',
                            '#FF33A8', '#A8FF33', '#33FFB8', '#B8A8FF',
                            '#A8B8FF', '#B8A8D4', '#D4A8FF', '#FFD4A8'
                        ],
                        series: [{
                            name: 'Giá trị',
                            colorByPoint: true,
                            size: '100%',
                            data: response?.series
                        }],
                        legend: {
                            layout: 'vertical', // Đặt layout là dọc
                            align: 'right',
                            verticalAlign: 'middle', // Đặt vị trí theo chiều dọc
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

        function filterChartThree() {
            countQC();
            chartNumberErrorsByErrorName();
            chartTopProductErrors();
            chartTopClientErrors();
        }

        $(document).ready(function() {
            countQC();
            chartNumberErrorsByErrorName();
            chartTopProductErrors();
            chartTopClientErrors();
        });
    </script>
<?php endif; ?>

<script>
    $(document).ready(function() {
        init_selectpicker();
        init_datepicker();
    });
</script>