<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
/* Dashboard Appendix Styles - Simple & Clean */
.dashboard-appendix {
    background: #f4f6f9;
    min-height: 100vh;
    padding: 20px 0;
}

.dashboard-title {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
}

.dashboard-subtitle {
    color: #7f8c8d;
    font-size: 13px;
    margin-bottom: 20px;
}

/* Info Box */
.dashboard-appendix .info-box {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-radius: 6px;
    border: none;
    margin-bottom: 20px;
    background: #fff;
}

.dashboard-appendix .info-box-icon {
    border-radius: 6px 0 0 6px;
    font-size: 40px;
}

.dashboard-appendix .info-box-content {
    padding: 15px;
}

.dashboard-appendix .info-box-text {
    font-size: 12px;
    font-weight: 500;
    color: #6c757d;
    text-transform: uppercase;
}

.dashboard-appendix .info-box-number {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
    display: block;
    margin-top: 5px;
}

/* Panel */
.dashboard-appendix .panel_s {
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    border-radius: 6px;
    border: 1px solid #e3e6f0;
    margin-bottom: 20px;
    background: #fff;
}

.dashboard-appendix .panel-body {
    padding: 20px;
}

.dashboard-appendix .panel_s h4.bold {
    color: #2c3e50;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #e74c3c;
    display: inline-block;
}

/* Table */
.dashboard-appendix .table thead th {
    background: #e74c3c;
    color: #fff;
    font-weight: 500;
    text-transform: uppercase;
    font-size: 11px;
    border: none;
    padding: 10px 8px;
}

.dashboard-appendix .table tbody tr:hover {
    background-color: #f8f9fa;
}

.dashboard-appendix .table tbody td {
    vertical-align: middle;
    padding: 10px 8px;
    border-bottom: 1px solid #e3e6f0;
    font-size: 13px;
}

.dashboard-appendix .table-responsive {
    border-radius: 4px;
}

/* Chart Container */
.dashboard-appendix canvas {
    max-height: 300px !important;
}

.chart-pie-container {
    max-width: 400px;
    margin: 0 auto;
}

/* Scrollbar */
.dashboard-appendix .table-responsive::-webkit-scrollbar {
    height: 6px;
    width: 6px;
}

.dashboard-appendix .table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.dashboard-appendix .table-responsive::-webkit-scrollbar-thumb {
    background: #888;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-appendix .info-box-number {
        font-size: 20px;
    }
    .dashboard-title {
        font-size: 20px;
    }
}

/* Simple solid colors */
.dashboard-appendix .bg-aqua { background: #3498db !important; }
.dashboard-appendix .bg-yellow { background: #f39c12 !important; }
.dashboard-appendix .bg-green { background: #27ae60 !important; }
.dashboard-appendix .bg-red { background: #e74c3c !important; }
.dashboard-appendix .bg-purple { background: #9b59b6 !important; }
.dashboard-appendix .bg-navy { background: #34495e !important; }
.dashboard-appendix .bg-maroon { background: #c0392b !important; }
</style>
<div id="wrapper">
    <div class="content dashboard-appendix">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="dashboard-title"><i class="fa fa-files-o"></i> <?php echo $title; ?></h4>
                        <p class="dashboard-subtitle"><i class="fa fa-calendar"></i> Cập nhật: <?php echo date('d/m/Y H:i'); ?></p>
                        
                        <!-- Thống kê tổng quan -->
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-aqua"><i class="fa fa-file-text"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Tổng phụ lục</span>
                                        <span class="info-box-number" id="total_appendix">0</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Chưa duyệt</span>
                                        <span class="info-box-number" id="pending_appendix">0</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Đã duyệt</span>
                                        <span class="info-box-number" id="approved_appendix">0</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-red"><i class="fa fa-times"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Không duyệt</span>
                                        <span class="info-box-number" id="rejected_appendix">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-purple"><i class="fa fa-calendar"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Phụ lục tháng này</span>
                                        <span class="info-box-number" id="appendix_this_month">0</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-navy"><i class="fa fa-arrow-up"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Tổng tăng lương CB</span>
                                        <span class="info-box-number" id="total_salary_increase">0</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-maroon"><i class="fa fa-arrow-up"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Tổng tăng lương VT</span>
                                        <span class="info-box-number" id="total_position_increase">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Biểu đồ -->
                        <div class="row mtop15">
                            <div class="col-md-6">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4 class="bold">Phụ lục theo trạng thái</h4>
                                        <div class="chart-pie-container">
                                            <canvas id="chartAppendixByStatus" height="300"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4 class="bold">Phân bổ theo loại thay đổi</h4>
                                        <div class="chart-pie-container">
                                            <canvas id="chartChangeDistribution" height="300"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mtop15">
                            <div class="col-md-12">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4 class="bold">Phụ lục theo tháng (12 tháng gần nhất)</h4>
                                        <canvas id="chartAppendixByMonth" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mtop15">
                            <div class="col-md-12">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4 class="bold">Biến động lương theo tháng (12 tháng gần nhất)</h4>
                                        <canvas id="chartSalaryChanges" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Danh sách -->
                        <div class="row mtop15">
                            <div class="col-md-6">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4 class="bold">Phụ lục cần duyệt</h4>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Mã phụ lục</th>
                                                        <th>Mã HĐ</th>
                                                        <th>Nhân viên</th>
                                                        <th>Lương CB</th>
                                                        <th>Lương VT</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="pendingAppendixList">
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
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4 class="bold">Phụ lục đã duyệt gần đây</h4>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Mã phụ lục</th>
                                                        <th>Mã HĐ</th>
                                                        <th>Nhân viên</th>
                                                        <th>Lương CB</th>
                                                        <th>Ngày duyệt</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="recentApprovedList">
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
                        
                        <div class="row mtop15">
                            <div class="col-md-6">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4 class="bold">Top 10 hợp đồng có nhiều phụ lục nhất</h4>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>STT</th>
                                                        <th>Mã HĐ</th>
                                                        <th>Nhân viên</th>
                                                        <th class="text-center">Số phụ lục</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="topContractsList">
                                                    <tr>
                                                        <td colspan="4" class="text-center">Đang tải...</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4 class="bold">Top 10 nhân viên tăng lương cao nhất</h4>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>STT</th>
                                                        <th>Nhân viên</th>
                                                        <th class="text-center">Số lần</th>
                                                        <th class="text-right">Tổng tăng</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="topSalaryIncreaseList">
                                                    <tr>
                                                        <td colspan="4" class="text-center">Đang tải...</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mtop15">
                            <div class="col-md-12">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4 class="bold">Tỷ lệ duyệt theo người duyệt</h4>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Người duyệt</th>
                                                        <th class="text-center">Tổng</th>
                                                        <th class="text-center">Đã duyệt</th>
                                                        <th class="text-center">Không duyệt</th>
                                                        <th class="text-center">Tỷ lệ duyệt</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="approvalRateList">
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
    
    var chartAppendixByStatus, chartChangeDistribution, chartAppendixByMonth, chartSalaryChanges;
    
    // Load overview statistics
    function loadOverviewStatistics() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract_appendix/get_overview_statistics"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#total_appendix').text(response.total_appendix);
                $('#pending_appendix').text(response.pending_appendix);
                $('#approved_appendix').text(response.approved_appendix);
                $('#rejected_appendix').text(response.rejected_appendix);
                $('#appendix_this_month').text(response.appendix_this_month);
                $('#total_salary_increase').text(formatMoney(response.total_salary_increase) + ' VND');
                $('#total_position_increase').text(formatMoney(response.total_position_increase) + ' VND');
            }
        });
    }
    
    // Load appendix by status chart
    function loadAppendixByStatus() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract_appendix/get_appendix_by_status"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var ctx = document.getElementById('chartAppendixByStatus').getContext('2d');
                chartAppendixByStatus = new Chart(ctx, {
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
                        maintainAspectRatio: true,
                        legend: {
                            position: 'bottom',
                            labels: {
                                fontSize: 14,
                                padding: 15,
                                usePointStyle: true
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
                            titleFontSize: 14,
                            bodyFontSize: 13
                        }
                    }
                });
            }
        });
    }
    
    // Load change distribution chart
    function loadChangeDistribution() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract_appendix/get_appendix_change_distribution"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var ctx = document.getElementById('chartChangeDistribution').getContext('2d');
                chartChangeDistribution = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: response.labels,
                        datasets: [{
                            data: response.values,
                            backgroundColor: [
                                '#3498db',
                                '#27ae60',
                                '#f39c12',
                                '#e74c3c'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutoutPercentage: 60,
                        legend: {
                            position: 'bottom',
                            labels: {
                                fontSize: 14,
                                padding: 15,
                                usePointStyle: true
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
                            titleFontSize: 14,
                            bodyFontSize: 13
                        }
                    }
                });
            }
        });
    }
    
    // Load appendix by month chart
    function loadAppendixByMonth() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract_appendix/get_appendix_by_month"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var ctx = document.getElementById('chartAppendixByMonth').getContext('2d');
                chartAppendixByMonth = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: response.labels,
                        datasets: [{
                            label: 'Phụ lục tạo mới',
                            data: response.created,
                            backgroundColor: 'rgba(60, 141, 188, 0.8)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Phụ lục đã duyệt',
                            data: response.approved,
                            backgroundColor: 'rgba(0, 166, 90, 0.8)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    stepSize: 1
                                }
                            }]
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                });
            }
        });
    }
    
    // Load salary changes chart
    function loadSalaryChanges() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract_appendix/get_salary_changes_by_month"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var ctx = document.getElementById('chartSalaryChanges').getContext('2d');
                chartSalaryChanges = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: response.labels,
                        datasets: [{
                            label: 'Lương cơ bản',
                            data: response.salary_basic,
                            backgroundColor: 'rgba(0, 49, 92, 0.2)',
                            borderColor: 'rgba(0, 49, 92, 1)',
                            borderWidth: 2,
                            fill: true
                        }, {
                            label: 'Lương vị trí',
                            data: response.salary_position,
                            backgroundColor: 'rgba(114, 20, 34, 0.2)',
                            borderColor: 'rgba(114, 20, 34, 1)',
                            borderWidth: 2,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    callback: function(value) {
                                        return formatMoney(value);
                                    }
                                }
                            }]
                        },
                        legend: {
                            position: 'bottom'
                        },
                        tooltips: {
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    var label = data.datasets[tooltipItem.datasetIndex].label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += formatMoney(tooltipItem.yLabel);
                                    return label;
                                }
                            }
                        }
                    }
                });
            }
        });
    }
    
    // Load pending appendix list
    function loadPendingAppendix() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract_appendix/get_pending_appendix"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                if (response.length > 0) {
                    $.each(response, function(i, item) {
                        html += '<tr>';
                        html += '<td>' + item.code + '</td>';
                        html += '<td>' + item.contract_code + '</td>';
                        html += '<td>' + item.staff_name + '</td>';
                        html += '<td class="text-right">' + formatMoney(item.salary) + '</td>';
                        html += '<td class="text-right">' + formatMoney(item.salary_position) + '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center">Không có dữ liệu</td></tr>';
                }
                $('#pendingAppendixList').html(html);
            }
        });
    }
    
    // Load recent approved appendix
    function loadRecentApproved() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract_appendix/get_recent_approved_appendix"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                if (response.length > 0) {
                    $.each(response, function(i, item) {
                        html += '<tr>';
                        html += '<td>' + item.code + '</td>';
                        html += '<td>' + item.contract_code + '</td>';
                        html += '<td>' + item.staff_name + '</td>';
                        html += '<td class="text-right">' + formatMoney(item.salary) + '</td>';
                        html += '<td>' + formatDateTime(item.date_status) + '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center">Không có dữ liệu</td></tr>';
                }
                $('#recentApprovedList').html(html);
            }
        });
    }
    
    // Load top contracts with appendix
    function loadTopContracts() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract_appendix/get_top_contracts_with_appendix"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                if (response.length > 0) {
                    $.each(response, function(i, item) {
                        html += '<tr>';
                        html += '<td>' + (i + 1) + '</td>';
                        html += '<td>' + item.contract_code + '</td>';
                        html += '<td>' + item.staff_name + '</td>';
                        html += '<td class="text-center bold">' + item.appendix_count + '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="4" class="text-center">Không có dữ liệu</td></tr>';
                }
                $('#topContractsList').html(html);
            }
        });
    }
    
    // Load top salary increase
    function loadTopSalaryIncrease() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract_appendix/get_top_salary_increase"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                if (response.length > 0) {
                    $.each(response, function(i, item) {
                        html += '<tr>';
                        html += '<td>' + (i + 1) + '</td>';
                        html += '<td>' + item.staff_name + '</td>';
                        html += '<td class="text-center">' + item.appendix_count + '</td>';
                        html += '<td class="text-right bold text-success">' + formatMoney(item.total_increase) + '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="4" class="text-center">Không có dữ liệu</td></tr>';
                }
                $('#topSalaryIncreaseList').html(html);
            }
        });
    }
    
    // Load approval rate by user
    function loadApprovalRate() {
        $.ajax({
            url: '<?php echo admin_url("dashboard_contract_appendix/get_approval_rate_by_user"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var html = '';
                if (response.length > 0) {
                    $.each(response, function(i, item) {
                        var rateClass = item.approval_rate >= 80 ? 'text-success' : (item.approval_rate >= 50 ? 'text-warning' : 'text-danger');
                        html += '<tr>';
                        html += '<td>' + item.user_name + '</td>';
                        html += '<td class="text-center">' + item.total + '</td>';
                        html += '<td class="text-center text-success">' + item.approved + '</td>';
                        html += '<td class="text-center text-danger">' + item.rejected + '</td>';
                        html += '<td class="text-center bold ' + rateClass + '">' + item.approval_rate + '%</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center">Không có dữ liệu</td></tr>';
                }
                $('#approvalRateList').html(html);
            }
        });
    }
    
    // Helper functions
    function formatMoney(number) {
        if (!number || number == 0) return '0';
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
    loadAppendixByStatus();
    loadChangeDistribution();
    loadAppendixByMonth();
    loadSalaryChanges();
    loadPendingAppendix();
    loadRecentApproved();
    loadTopContracts();
    loadTopSalaryIncrease();
    loadApprovalRate();
});
</script>

<?php init_tail(); ?>
