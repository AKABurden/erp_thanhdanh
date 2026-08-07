<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
/* Dashboard Contract Styles - Bootstrap 3 */
.content-bashboard {
    padding: 15px !important;
    background-color: #ecf0f5;
}

.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 25px;
    margin-bottom: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    color: #fff;
}

.dashboard-header h4 {
    margin: 0 0 8px 0;
    font-size: 24px;
    font-weight: 700;
    color: #fff;
}

.dashboard-header .text-muted {
    font-size: 14px;
    color: rgba(255,255,255,0.9);
    font-weight: 400;
}

/* Info Box - Enhanced Style */
.info-box {
    display: block;
    min-height: 100px;
    background: #fff;
    width: 100%;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    border-radius: 5px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.info-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
}

.info-box-icon {
    border-top-left-radius: 5px;
    border-bottom-left-radius: 5px;
    display: block;
    float: left;
    height: 100px;
    width: 100px;
    text-align: center;
    font-size: 48px;
    line-height: 100px;
    background: rgba(0,0,0,0.1);
}

.info-box-icon > i {
    color: #fff;
}

.info-box-content {
    padding: 10px 15px;
    margin-left: 100px;
}

.info-box-text {
    text-transform: uppercase;
    font-weight: 600;
    font-size: 13px;
    color: #666;
    margin-bottom: 5px;
}

.info-box-number {
    display: block;
    font-weight: 700;
    font-size: 24px;
    color: #333;
}

/* Panel Box - Enhanced */
.box {
    position: relative;
    border-radius: 5px;
    background: #ffffff;
    border-top: 4px solid #d2d6de;
    margin-bottom: 25px;
    width: 100%;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.box.box-primary {
    border-top-color: #3498db;
}

.box.box-danger {
    border-top-color: #e74c3c;
}

.box.box-success {
    border-top-color: #00a65a;
}

.box-header {
    color: #333;
    display: block;
    padding: 15px 20px;
    position: relative;
    background: #f9fafb;
}

.box-header.with-border {
    border-bottom: 2px solid #e8e8e8;
}

.box-title {
    display: inline-block;
    font-size: 18px;
    margin: 0;
    line-height: 1.4;
    font-weight: 700;
    color: #2c3e50;
}

.box-title i {
    margin-right: 8px;
    color: #3498db;
}

.box-body {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    border-bottom-right-radius: 5px;
    border-bottom-left-radius: 5px;
    padding: 20px;
}

.box-body .table {
    margin-bottom: 0;
}

/* Table Styles - Enhanced */
.table > thead > tr > th {
    border-bottom: 2px solid #e8e8e8;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 11px;
    background: #34495e;
    color: #fff;
    padding: 12px 10px;
    letter-spacing: 0.5px;
}

.table > tbody > tr > td {
    border-top: 1px solid #e8e8e8;
    padding: 10px;
    font-size: 13px;
    vertical-align: middle;
}

.table-striped > tbody > tr:nth-of-type(odd) {
    background-color: #f8f9fa;
}

.table > tbody > tr:hover {
    background-color: #e9ecef;
    transition: background-color 0.2s ease;
}

.table .text-right {
    text-align: right;
}

.table .bold {
    font-weight: 700;
}

/* Chart Container */
.chart-container {
    position: relative;
    height: 350px;
    padding: 15px;
}

.chart-container canvas {
    max-height: 100% !important;
    max-width: 100% !important;
}

/* Colors - Modern Palette */
.bg-aqua { 
    background: linear-gradient(135deg, #00c0ef 0%, #0099cc 100%) !important; 
}
.bg-yellow { 
    background: linear-gradient(135deg, #f39c12 0%, #d68910 100%) !important; 
}
.bg-green { 
    background: linear-gradient(135deg, #00a65a 0%, #008d4c 100%) !important; 
}
.bg-red { 
    background: linear-gradient(135deg, #dd4b39 0%, #c9302c 100%) !important; 
}
.bg-purple { 
    background: linear-gradient(135deg, #605ca8 0%, #4f4a90 100%) !important; 
}
.bg-navy { 
    background: linear-gradient(135deg, #001f3f 0%, #001529 100%) !important; 
}
.bg-maroon { 
    background: linear-gradient(135deg, #d81b60 0%, #c11856 100%) !important; 
}

.info-box.bg-aqua { border-left-color: #00c0ef; }
.info-box.bg-yellow { border-left-color: #f39c12; }
.info-box.bg-green { border-left-color: #00a65a; }
.info-box.bg-red { border-left-color: #dd4b39; }
.info-box.bg-purple { border-left-color: #605ca8; }
.info-box.bg-navy { border-left-color: #001f3f; }
.info-box.bg-maroon { border-left-color: #d81b60; }

/* Utility Classes */
.text-success { color: #00a65a !important; font-weight: 600; }
.text-danger { color: #dd4b39 !important; font-weight: 600; }
.text-warning { color: #f39c12 !important; font-weight: 600; }

/* Responsive */
@media (max-width: 768px) {
    .content-bashboard {
        padding: 10px !important;
    }
    .info-box-icon {
        width: 80px;
        height: 80px;
        font-size: 38px;
        line-height: 80px;
    }
    .info-box-content {
        margin-left: 80px;
    }
    .info-box {
        min-height: 80px;
    }
    .dashboard-header h4 {
        font-size: 20px;
    }
    .chart-container {
        height: 250px;
        padding: 10px;
    }
    .box-body {
        padding: 10px;
    }
}
</style>
<div id="wrapper">
    <div class="content content-bashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="dashboard-header">
                    <h4><i class="fa fa-tachometer"></i> <?php echo $title; ?></h4>
                    <p class="text-muted"><i class="fa fa-calendar"></i> Cập nhật: <?php echo date('d/m/Y H:i'); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Statistics Overview -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-file-text-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tổng hợp đồng</span>
                        <span class="info-box-number" id="total_contracts">0</span>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Chưa duyệt</span>
                        <span class="info-box-number" id="pending_contracts">0</span>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Đã duyệt</span>
                        <span class="info-box-number" id="approved_contracts">0</span>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Sắp hết hạn (30 ngày)</span>
                        <span class="info-box-number" id="expiring_contracts">0</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-purple"><i class="fa fa-times"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Không duyệt</span>
                        <span class="info-box-number" id="rejected_contracts">0</span>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 hide">
                <div class="info-box">
                    <span class="info-box-icon bg-navy"><i class="fa fa-money"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Lương cơ bản TB</span>
                        <span class="info-box-number" id="avg_salary_basic">0</span>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 hide">
                <div class="info-box">
                    <span class="info-box-icon bg-maroon"><i class="fa fa-money"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Lương vị trí TB</span>
                        <span class="info-box-number" id="avg_salary_position">0</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts Section -->
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-pie-chart"></i> Hợp đồng theo trạng thái</h3>
                    </div>
                    <div class="box-body">
                        <div class="chart-container">
                            <canvas id="chartContractsByStatus"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-pie-chart"></i> Hợp đồng theo loại</h3>
                    </div>
                    <div class="box-body">
                        <div class="chart-container">
                            <canvas id="chartContractsByType"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-line-chart"></i> Hợp đồng theo tháng (12 tháng gần nhất)</h3>
                    </div>
                    <div class="box-body">
                        <div class="chart-container">
                            <canvas id="chartContractsByMonth"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-bar-chart"></i> Phân bổ lương cơ bản</h3>
                    </div>
                    <div class="box-body">
                        <div class="chart-container">
                            <canvas id="chartSalaryDistribution"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tables Section -->
        <div class="row">
            <div class="col-md-6">
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-exclamation-triangle"></i> Hợp đồng sắp hết hạn</h3>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Mã HĐ</th>
                                        <th>Nhân viên</th>
                                        <th>Loại HĐ</th>
                                        <th>Ngày hết hạn</th>
                                        <th>Còn lại</th>
                                    </tr>
                                </thead>
                                <tbody id="expiringContractsList">
                                    <tr>
                                        <td colspan="5" class="text-center">Đang tải...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-clock-o"></i> Hợp đồng cần duyệt</h3>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Mã HĐ</th>
                                        <th>Nhân viên</th>
                                        <th>Loại HĐ</th>
                                        <th>Lương CB</th>
                                        <th>Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody id="pendingContractsList">
                                    <tr>
                                        <td colspan="5" class="text-center">Đang tải...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row hide">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-trophy"></i> Top 10 nhân viên có lương cao nhất</h3>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Nhân viên</th>
                                        <th>Mã HĐ</th>
                                        <th class="text-right">Lương cơ bản</th>
                                        <th class="text-right">Lương vị trí</th>
                                        <th class="text-right">Tổng lương</th>
                                    </tr>
                                </thead>
                                <tbody id="topSalaryStaffList">
                                    <tr>
                                        <td colspan="6" class="text-center">Đang tải...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<script src="<?php echo base_url('assets/plugins/chart.js/Chart.min.js'); ?>"></script>
<script>
$(function() {
    'use strict';
    
    var chartContractsByStatus, chartContractsByType, chartContractsByMonth, chartSalaryDistribution;
    
    // Load overview statistics
    function loadOverviewStatistics() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract/get_overview_statistics"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#total_contracts').text(response.total_contracts);
                $('#pending_contracts').text(response.pending_contracts);
                $('#approved_contracts').text(response.approved_contracts);
                $('#rejected_contracts').text(response.rejected_contracts);
                $('#expiring_contracts').text(response.expiring_contracts);
                $('#avg_salary_basic').text(formatMoney(response.avg_salary_basic) + ' VND');
                $('#avg_salary_position').text(formatMoney(response.avg_salary_position) + ' VND');
            }
        });
    }
    
    // Load contracts by status chart
    function loadContractsByStatus() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract/get_contracts_by_status"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var ctx = document.getElementById('chartContractsByStatus').getContext('2d');
                chartContractsByStatus = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: response.labels,
                        datasets: [{
                            data: response.values,
                            backgroundColor: response.colors,
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            position: 'bottom',
                            labels: {
                                fontSize: 13,
                                padding: 12,
                                usePointStyle: true,
                                boxWidth: 15
                            }
                        },
                        tooltips: {
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    var label = data.labels[tooltipItem.index] || '';
                                    var value = data.datasets[0].data[tooltipItem.index];
                                    var total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    var percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            },
                            titleFontSize: 15,
                            bodyFontSize: 14,
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            cornerRadius: 4,
                            displayColors: true
                        }
                    }
                });
            }
        });
    }
    
    // Load contracts by type chart
    function loadContractsByType() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract/get_contracts_by_type"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var ctx = document.getElementById('chartContractsByType').getContext('2d');
                chartContractsByType = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: response.labels,
                        datasets: [{
                            data: response.values,
                            backgroundColor: [
                                '#3498db',
                                '#27ae60',
                                '#f39c12',
                                '#e74c3c',
                                '#9b59b6',
                                '#e67e22'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutoutPercentage: 60,
                        legend: {
                            position: 'bottom',
                            labels: {
                                fontSize: 13,
                                padding: 12,
                                usePointStyle: true,
                                boxWidth: 15
                            }
                        },
                        tooltips: {
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    var label = data.labels[tooltipItem.index] || '';
                                    var value = data.datasets[0].data[tooltipItem.index];
                                    var total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    var percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            },
                            titleFontSize: 15,
                            bodyFontSize: 14,
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            cornerRadius: 4,
                            displayColors: true
                        }
                    }
                });
            }
        });
    }
    
    // Load contracts by month chart
    function loadContractsByMonth() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract/get_contracts_by_month"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var ctx = document.getElementById('chartContractsByMonth').getContext('2d');
                chartContractsByMonth = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: response.labels,
                        datasets: [{
                            label: 'Số hợp đồng',
                            data: response.values,
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            xAxes: [{
                                ticks: {
                                    fontSize: 12,
                                    autoSkip: true,
                                    maxRotation: 45,
                                    minRotation: 0
                                }
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    stepSize: 1,
                                    fontSize: 12
                                }
                            }]
                        },
                        legend: {
                            display: false
                        },
                        tooltips: {
                            titleFontSize: 14,
                            bodyFontSize: 13,
                            backgroundColor: 'rgba(0,0,0,0.8)'
                        }
                    }
                });
            }
        });
    }
    
    // Load salary distribution chart
    function loadSalaryDistribution() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract/get_salary_distribution"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var ctx = document.getElementById('chartSalaryDistribution').getContext('2d');
                chartSalaryDistribution = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: response.labels,
                        datasets: [{
                            label: 'Số nhân viên',
                            data: response.values,
                            backgroundColor: '#00a65a',
                            borderColor: '#008d4c',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            xAxes: [{
                                ticks: {
                                    fontSize: 12,
                                    autoSkip: true,
                                    maxRotation: 45,
                                    minRotation: 0
                                }
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    stepSize: 1,
                                    fontSize: 12
                                }
                            }]
                        },
                        legend: {
                            display: false
                        },
                        tooltips: {
                            titleFontSize: 14,
                            bodyFontSize: 13,
                            backgroundColor: 'rgba(0,0,0,0.8)'
                        }
                    }
                });
            }
        });
    }
    
    // Load expiring contracts list
    function loadExpiringContracts() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract/get_expiring_contracts"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                if (response.length > 0) {
                    $.each(response, function(i, item) {
                        var daysClass = item.days_left <= 7 ? 'text-danger bold' : (item.days_left <= 15 ? 'text-warning bold' : '');
                        html += '<tr>';
                        html += '<td>' + item.code + '</td>';
                        html += '<td>' + item.staff_name + '</td>';
                        html += '<td>' + item.type_name + '</td>';
                        html += '<td>' + formatDate(item.date_end) + '</td>';
                        html += '<td class="' + daysClass + '">' + item.days_left + ' ngày</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center">Không có dữ liệu</td></tr>';
                }
                $('#expiringContractsList').html(html);
            }
        });
    }
    
    // Load pending contracts list
    function loadPendingContracts() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract/get_pending_contracts"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                if (response.length > 0) {
                    $.each(response, function(i, item) {
                        html += '<tr>';
                        html += '<td>' + item.code + '</td>';
                        html += '<td>' + item.staff_name + '</td>';
                        html += '<td>' + item.type_name + '</td>';
                        html += '<td class="text-right">' + formatMoney(item.salary_basic) + '</td>';
                        html += '<td>' + formatDateTime(item.date_created) + '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center">Không có dữ liệu</td></tr>';
                }
                $('#pendingContractsList').html(html);
            }
        });
    }
    
    // Load top salary staff list
    function loadTopSalaryStaff() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract/get_top_salary_staff"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                if (response.length > 0) {
                    $.each(response, function(i, item) {
                        html += '<tr>';
                        html += '<td>' + (i + 1) + '</td>';
                        html += '<td>' + item.staff_name + '</td>';
                        html += '<td>' + item.code + '</td>';
                        html += '<td class="text-right">' + formatMoney(item.salary_basic) + '</td>';
                        html += '<td class="text-right">' + formatMoney(item.salary_position) + '</td>';
                        html += '<td class="text-right bold">' + formatMoney(item.total_salary) + '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center">Không có dữ liệu</td></tr>';
                }
                $('#topSalaryStaffList').html(html);
            }
        });
    }
    
    // Helper functions
    function formatMoney(number) {
        return new Intl.NumberFormat('vi-VN').format(number);
    }
    
    function formatDate(dateString) {
        if (!dateString) return '';
        var parts = dateString.split('-');
        return parts[2] + '/' + parts[1] + '/' + parts[0];
    }
    
    function formatDateTime(dateTimeString) {
        if (!dateTimeString) return '';
        var parts = dateTimeString.split(' ');
        var dateParts = parts[0].split('-');
        return dateParts[2] + '/' + dateParts[1] + '/' + dateParts[0] + ' ' + parts[1];
    }
    
    // Initialize
    loadOverviewStatistics();
    loadContractsByStatus();
    loadContractsByType();
    loadContractsByMonth();
    loadSalaryDistribution();
    loadExpiringContracts();
    loadPendingContracts();
    // loadTopSalaryStaff();
});
</script>

<?php init_tail(); ?>
