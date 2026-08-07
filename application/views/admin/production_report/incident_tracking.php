<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    /* .table-procadure_detail_4 tbody tr td:nth-child(3) {
        white-space: unset;
        width: 300px;
    } */
    /*.table-production_report img {*/
    /*    height: 20px;*/
    /*    width: 20px;*/
    /*}*/
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
            <div class="pull-right mright5 H_border">
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <div class="tab-content" id="tab_content_procadure">
                            <div class="row">
                                <div class="col-md-3">
									<?php echo render_date_input('date_start', 'Ngày bắt đầu', date('d/m/Y')) ?>
                                </div>
                                <div class="col-md-3">
									<?php echo render_date_input('date_end', 'Ngày kết thúc', date('d/m/Y')) ?>
                                </div>
                                <div class="col-md-3">
									<?php echo render_select('role_id', $arrRole, ['roleid', 'name'], 'Bộ phận') ?>
                                </div>
                                <div class="col-md-3">
                                    <?= lang('customers', 'customers') ?>
                                    <input type="text" name="customer_search" data-placeholder="<?= lang('customers') ?>"
                                        id="customer_search" class="customer_search" style="width: 100%;" value="">
                                </div>
                            </div>
                            <!-- <hr/> -->
                            <div class="clearfix"></div>
                                                
                            <div id="table_html"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(document).on('click', '.status-table li a button', function(event) {
        status_table = $(this).attr('data-value');
        $('#status_table').val(status_table);
        TableData.draw();
    });
    var CustomersServerParams = {
        'date_start': '[name="date_start"]',
        'date_end': '[name="date_end"]',
        'role_id': '[name="role_id"]',
        'customer_search': "#customer_search",
    };
    var TableData;
    $(function () {
        // TableData = initDataTable('.table-production_report', admin_url + 'production_report/get_incident_tracking', [0], [0], CustomersServerParams, [0, 'desc']);
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);
        
        $.each(CustomersServerParams, function (filterIndex, filterItem) {
            $('' + filterItem).on('change', function () {
                // TableData.draw('page');
                if ($('[name="date_start"]').val() != '') {
                    date_start = '&filter[date_start]='+$('[name="date_start"]').val();
                } else {
                    date_start = '';
                }
                if ($('[name="date_end"]').val() != '') {
                    date_end = '&filter[date_end]='+$('[name="date_end"]').val();
                } else {
                    date_end = '';
                }
                if ($('[name="date_end"]').val() != '') {
                    date_end = '&filter[date_end]='+$('[name="date_end"]').val();
                } else {
                    date_end = '';
                }
                if ($('[name="role_id"]').val() != '') {
                    role_id = '&filter[role_id]='+$('[name="role_id"]').val();
                } else {
                    role_id = '';
                }
                if ($('[name="customer_search"]').val() != '') {
                    customer = '&filter[customer]='+$('[name="customer_search"]').val();
                } else {
                    customer = '';
                }
                view_table(date_start, date_end, role_id, customer);
            });
        });
            
        // $.ajax({
        //     type: "POST",
        //     url: actionUrl,
        //     data: form.serialize(), // serializes the form's elements.
        //     success: function(data)
        //     {
        //     alert(data); // show response from the php script.
        //     }
        // });
        // &filter[role_id]=<?//= $arrRole[0]['roleid'] ?>
        view_table(date_start = '&filter[date_start]=<?= date('d/m/Y') ?>', date_end = '&filter[date_end]=<?= date('d/m/Y') ?>', role_id = '', customer = '');
    });
    function view_table(date_start = '', date_end = '', role_id = '', customer = '') {
        $('#table_html').html('');
        $.get(admin_url + 'production_report/get_incident_tracking_table/?'+date_start+date_end+role_id+customer).done(function(response) {
            $('#table_html').html(response);
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('click', '.remove_production_report', function () {
        var id = $(this).data('id');
        if (confirm('Bạn có chắc muốn xóa phiếu báo cáo này?')) {
            $.get(admin_url + 'production_report/delete/' + id, function (result) {
                result = JSON.parse(result);
                alert_float(result.alert_type, result.message);
                TableData.draw('page');
            }).fail(function (error) {
                alert_float('danger', error.responseText);
            });
        }
    })
    $('.table-production_report').on('draw.dt', function () {
        viewChart();
    });
    function viewChart() {
        var canvasChart = $('body').find('.canvasChart');
        $.each(canvasChart, function (index, value) {
            var chart = $(value);
            if (chart.length > 0) {
                data = $(chart).attr('data-json');
                data = JSON.parse(data);
                new Chart(chart, {
                    type: 'doughnut',
                    data: data,
                    options: {
                        maintainAspectRatio: false,
                        onClick: function (evt) {
                        }
                    }
                });
            }
        })
    }
</script>
</body>
</html>
