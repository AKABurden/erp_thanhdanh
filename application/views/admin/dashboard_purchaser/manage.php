<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
  <style type="text/css">
  .row {
      display: -ms-flexbox;
      -ms-flex-wrap: wrap;
      flex-wrap: wrap;
      margin-right: -15px;
      margin-left: -15px;
      margin-bottom: 25px;
  }
  *, ::after, ::before {
      box-sizing: border-box;
  }
  .fa-fw_ch {
      width: 4.285714em !important;
      text-align: center;
  }
  .card-body-icon {
      position: absolute;
      z-index: 0;
      top: -17px;
      right: -89px;
      font-size: 5rem;
      -webkit-transform: rotate(15deg);
      -ms-transform: rotate(15deg);
      transform: rotate(15deg);
  }
  .card-body {
      -ms-flex: 1 1 auto;
      flex: 1 1 auto;
      padding: 1.25rem;
  }
  .card-footer:last-child {
      border-radius: 0 0 calc(.25rem - 1px) calc(.25rem - 1px);
  }
  .z-1 {
      z-index: 1;
  }
  .text-white {
      color: #fff!important;
  }
  .card-footer {
      padding: .75rem 1.25rem;
      background-color: rgba(0,0,0,.03);
      border-top: 1px solid rgba(0,0,0,.125);
  }
  .small, small {
      font-size: 80%;
      font-weight: 400;
  }
  .mb-3, .my-3 {
      margin-bottom: 1rem!important;
  }
  .o-hidden {
      overflow: hidden !important;
  }
  .text-white {
      color: #fff!important;
  }
  .h-100 {
      height: 100%!important;
  }
  .bg-primary {
      background: linear-gradient(to right, #84d9d2, #07cdae) !important;
  }
  .bg-warning {
      background: linear-gradient(to right, #ffbf96, #fe7096) !important;
  }
  .bg-success {
      background: linear-gradient(to right, #90caf9, #047edf 99%) !important;
  }
  .card {
      position: relative;
      display: -ms-flexbox;
      display: flex;
      -ms-flex-direction: column;
      flex-direction: column;
      min-width: 0;
      word-wrap: break-word;
      background-color: #fff;
      background-clip: border-box;
      border: 1px solid rgba(0,0,0,.125);
      border-radius: .25rem;
  }
  .card_ch{
    padding-top: 10px;
  }
  .card-img-absolute
  {
      position: absolute;
      top: 0;
      right: 0;
      height: 100%;
  }
  .wrap_box {
    height: 380px;
    background-color: #fff;
    border: 1px solid #dfdfdf;
    border-radius: 7px;
  }
  .wrap_container {
    display: flex;
    align-items: flex-end;
    padding: 0 10px;
    font-size: 15px
  }
  .wrap_line:not(:last-child) {
    margin: 15px 10px;
    height: 3px;
    background: linear-gradient(to right, #33a3ff, #14b900 99%);
  }
  </style>
<script src="<?= base_url('assets/css_ch/Chart.min.js')?>"></script>
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="<?=!empty($dataLog) ? 'col-md-9' : 'col-md-12'?>">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="content-wrapper">
                            <div class="container-fluid">
                              <div class="row">
                                <div style="height: 118px;" class="col-md-4">
                                  <div class="card text-white bg-warning o-hidden h-100">
                                    <div class="card-body">
                                      <img src="<?= base_url('assets/images/circle.svg')?>" class="card-img-absolute" alt="circle-image" />
                                      <h3 class="mr-5"> YCMH <br><span style="font-size: 16px" id="subtotal">0</span></h3>
                                    </div>
                                  </div>
                                </div>
                                <div style="height: 118px;" class="col-md-4">
                                  <div class="card text-white bg-success o-hidden h-100">
                                    <div class="card-body">
                                      <img src="<?= base_url('assets/images/circle.svg')?>" class="card-img-absolute" alt="circle-image" />
                                      <h3 class="mr-5">Đơn Hàng<br><span style="font-size: 16px" id="subtotal1">0</span></h3>
                                    </div>
                                  </div>
                                </div>
                                <div style="height: 118px;" class="col-md-4">
                                  <div class="card text-white bg-primary o-hidden h-100" style="background-color: #ff4015;">
                                    <div class="card-body">
                                      <img src="<?= base_url('assets/images/circle.svg')?>" class="card-img-absolute" alt="circle-image" />
                                      <h3 class="mr-5">Nhập Hàng<br><span style="font-size: 16px" id="subtotal2">0</span></h3>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="clearfix"></div>
                                <div class="col-md-4 hide">
                                    <div class="bg-light-gray border-radius-4">
                                        <div class="p8">
                                            <div class="form-group " id="report-time">
                                                <label for="months-report"><?php echo _l('Lọc theo ngày'); ?></label><br />
                                                <select class="selectpicker" name="months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                   <option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
                                                   <option selected value="week"><?php echo _l('Tuần này'); ?></option>
                                                   <option  value="this_month"><?php echo _l('this_month'); ?></option>
                                                   <option value="1"><?php echo _l('last_month'); ?></option>
                                                   <option  value="this_year"><?php echo _l('Năm nay'); ?></option>
                                                   <option value="last_year"><?php echo _l('Năm trước'); ?></option>
                                                   <option value="custom"><?php echo _l('period_datepicker'); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="date-range" class="hide mbot15">
                                        <div class="row">
                                           <div class="col-md-6">
                                              <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
                                              <div class="input-group date">
                                                 <input type="text" class="form-control datepicker" id="report-from" name="report-from">
                                                 <div class="input-group-addon">
                                                    <i class="fa fa-calendar calendar-icon"></i>
                                                 </div>
                                              </div>
                                           </div>
                                           <div class="col-md-6">
                                              <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
                                              <div class="input-group date">
                                                 <input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
                                                 <div class="input-group-addon">
                                                    <i class="fa fa-calendar calendar-icon"></i>
                                                 </div>
                                              </div>
                                           </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 hide">
                                    <div class="bg-light-gray border-radius-4">
                                        <div class="p8">
                                            <div class="form-group ">
                                                <?php echo render_select('search_id_staff[]',$dataStaff,array('staffid','name'),'Nhân viên','',array('data-actions-box'=>1,'multiple'=>true),array(),'','',false);?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <br>
                                <div class="col-lg-12">
                                <div class="col-lg-12">
                                  <div class="card mb-3">
                                    <div class="card-body">
                                      <canvas id="mycotChart" width="auto" height="100"></canvas>
                                    </div>
                                    <div class="card-footer small Updated text-muted">Updated <?=_dt(date('Y-m-d H:i:s'))?></div>
                                  </div>
                                </div>
                                <div class="col-lg-12">
                                  <div class="card mb-3">
                                    <div class="card-body">
                                      <canvas id="myBarChart" width="auto" height="100"></canvas>
                                    </div>
                                    <div class="card-footer small Updated text-muted">Updated <?=_dt(date('Y-m-d H:i:s'))?></div>
                                  </div>
                                </div>
                            </div>
                            </div>
                          </div>
                    </div>
                </div>
            </div>
            <div class="<?=!empty($dataLog) ? 'col-md-3' : 'hide'?>">
                <div class="panel panel-primary">
                    <div class="panel-heading"><?=_l('activity_log_puchases')?></div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="date_space_activity"><?=_l('cong_automations_time')?></label>
                            <div class="input-group" style="width: 100%;">
                                <input type="text" id="date_space_activity" class="form-control date_space_activity" aria-invalid="false" data-module='purchase'>
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo render_select('staff_activity',$staff,array('staffid','name'),'by_staff_log','',array('data-module'=>'purchase')); ?>
                        </div>
                        <hr />
                        <div class="activity-container" style="max-height: 600px;">
                            <?php foreach ($dataLog as $key => $value) { ?>
                                <div class="feed-item">
                                    <div class="activity-text">
                                        <?= staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small'); ?> <?= get_staff_full_name($value['staff_id']); ?>
                                    </div>
                                    <div class="activity-time">
                                        <?= time_ago($value['date']) ?> <span class="activity-module"><?=_l($value['table_obj'])?></span>
                                    </div>
                                    <div>
                                        <?=$value['content']?>
                                    </div>
                                </div>
                          <?php } ?>
                        </div>
                        <div class="text-center">
                            <a class="btn btn-info more_log" onclick="load_more_log('purchase'); return false;"><?=_l('load_more')?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>

<script>
    $(function(){
        active_daterangepicker();
    });
    var active_daterangepicker = () => {
        $('.date_space_activity').daterangepicker({
            opens: 'left',
            autoUpdateInput: false, 
            isInvalidDate: false,
            "locale": {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": lang_daterangepicker.applyLabel,
                "cancelLabel": lang_daterangepicker.cancelLabel,
                "fromLabel": lang_daterangepicker.fromLabel,
                "toLabel": lang_daterangepicker.toLabel,
                "customRangeLabel": lang_daterangepicker.customRangeLabel,
                "daysOfWeek": lang_daterangepicker.daysOfWeek,
                "monthNames": lang_daterangepicker.monthNames
            },
        }, function(start, end, label) {
        });
        $('.date_space_activity').val('').datepicker("refresh");
        $('.date_space_activity').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            $( "#date_space_activity" ).trigger( "change" );
        });
    };
</script>
<script type="text/javascript">
        $(document).on('change', 'input[name="report-from"]', (e)=>{
            var val = $('input[name="report-from"]').val();
            var report_to_val = $('input[name="report-to"]').val();
            if (val != '') {
                if (report_to_val != '') {
                 init_dashboard_report();
                 init_dashboard_report_cot();
                 get_total_limit();
               }
               $('input[name="report-to"]').attr('disabled', false);
            } else {
               $('input[name="report-to"]').attr('disabled', true);
            }
        });
        $('input[name="report-to"]').on('change', function() {
            var val = $('input[name="report-to"]').val();
            if (val != '') {
                init_dashboard_report();
                init_dashboard_report_cot();
                get_total_limit();
            }
        });
        $('select[name="search_id_staff[]"]').on('change', function() {
            var val = $('select[name="search_id_staff[]"]').val();
                init_dashboard_report();
                init_dashboard_report_cot();
                get_total_limit();
        });
        $('[name="customers_ch"]').on('change', function() {
                init_dashboard_report();
                init_dashboard_report_cot();
                get_total_limit();
        });
        $('select[name="months-report"]').on('change', function() {
            var val = $(this).val();
            $('input[name="report-to"]').val('');
            $('input[name="report-from"]').val('');
            if (val == 'custom') {
                $('#date-range').addClass('fadeIn').removeClass('hide');
                return;
            } else {
                if (!$('#date-range').hasClass('hide')) {
                    $('#date-range').removeClass('fadeIn').addClass('hide');
                }
            }
            init_dashboard_report();
            init_dashboard_report_cot();
            get_total_limit();
       });
    function get_total_limit() {
        var months_report = $('select[name="months-report"]').val();
        var report_to = $('input[name="report-to"]').val();
        var report_from = $('input[name="report-from"]').val();
        var customers_ch = $('[name="customers_ch"]').val();
        var search_id_staff = $('select[name="search_id_staff[]"]').val();
        dataString = {[csrfData['token_name']] : csrfData['hash'],months_report:months_report,report_from:report_from,report_to:report_to,search_id_staff:search_id_staff,customers_ch:customers_ch};
        jQuery.ajax({
            type: "post",
            url: "<?=admin_url()?>dashboard_purchaser/count_all/",
            data: dataString,
            cache: false,
            success: function (data) {
              data = JSON.parse(data);
              $('#subtotal').html(data.subtotal);
              $('#subtotal1').html(data.subtotal1);
              $('#subtotal2').html(data.subtotal2);  
              $('.Updated').html(data.date_update);       
              }
        });
    }
    get_total_limit();
    var myLineChart;
    var myCotChart;

function number_format(number, decimals, dec_point, thousands_sep) {

    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
    function init_dashboard_report_cot() {
        var months_report = $('select[name="months-report"]').val();
        var report_to = $('input[name="report-to"]').val();
        var report_from = $('input[name="report-from"]').val();
        var search_id_staff = $('select[name="search_id_staff[]"]').val();
        dataString = {[csrfData['token_name']] : csrfData['hash'],months_report:months_report,report_from:report_from,report_to:report_to,search_id_staff:search_id_staff};
        $.post(admin_url + 'dashboard_purchaser/dashboard_report_cot/',dataString, function(response) {
        var response = JSON.parse(response);
        if (typeof(myCotChart) !== 'undefined') {  
            myCotChart.destroy();
        }
        var ctx = document.getElementById("mycotChart");
            myCotChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: response.labels,
                    datasets: [
                    {
                        label: "Tổng tiền",
                        backgroundColor: "#3e95cd",
                        borderColor: response.backgroundColor,
                        data: response.data,
                    }
                    ]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: "Biểu đồ mua theo hoạch định sản xuất và dữ trữ kho"
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                return Number(tooltipItem.yLabel).toFixed(0).replace(/./g, function(c, i, a) {
                                    return i > 0 && c !== "." && (a.length - i) % 3 === 0 ? "," + c : c;
                                });
                            }
                        }
                    },
                    scales: {
                        axisX:{
                            ticks: {
                              labelAngle: 100,
                              labelMaxWidth: 20,
                            }
                         },
                        xAxes: [{
                            time: {
                                unit: "month"
                            },
                            gridLines: {
                                display: !1
                            },
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function(value, index, values) {
                                    return number_format(value);
                                },
                                min: 0,
                                max: (Number(response.max) + 100000),
                                maxTicksLimit: 100
                            },
                            gridLines: {
                                display: !0
                            }
                        }]
                    },
                    legend: {
                        display: !0
                    }
                }
            });
        });
    }
    // line chart data
    function init_dashboard_report() {
        var months_report = $('select[name="months-report"]').val();
        var report_to = $('input[name="report-to"]').val();
        var report_from = $('input[name="report-from"]').val();
        var search_id_staff = $('select[name="search_id_staff[]"]').val();
        var customers_ch = $('[name="customers_ch"]').val();
        dataString = {[csrfData['token_name']] : csrfData['hash'],months_report:months_report,report_from:report_from,report_to:report_to,search_id_staff:search_id_staff,customers_ch:customers_ch};
        $.post(admin_url + 'dashboard_purchaser/dashboard_report/',dataString, function(response) {
        var response = JSON.parse(response);
        if (typeof(myLineChart) !== 'undefined') {  
            myLineChart.destroy();
        }
        labels_cot = [];
        data_payment     = [];
        datas_cost     = [];
        datas     = [];
        $.each(response.labels, function(key,value){
            labels_cot.push(value);
        });
        $.each(response.datas_cost, function(key,value){
            datas_cost.push(value);
        });
        $.each(response.datas_payment, function(key,value){
            data_payment.push(value);
        });
        $.each(response.data, function(key,value){
            datas.push(value);
        });
        var ctx = document.getElementById("myBarChart");
            myLineChart = new Chart(ctx, {
                type: 'line',
  data: {
    labels: labels_cot,
    datasets: [{ 
        data: data_payment,
        label: "Mua nguyên vật liệu",
        borderColor: "#3e95cd",
        fill: false,
        lineTension: 0
      },{ 
        data: datas_cost,
        label: "Mua bán thành phẩm",
        borderColor: "#8e5ea2",
        fill: false,
        lineTension: 0
      },{ 
        data: datas,
        label: "Mua công cụ dụng cụ ",
        borderColor: "#c45850",
        fill: false,
        lineTension: 0
      }
    ]
  },
  options: {
    title: {
      display: true,
      text: 'Biểu đồ mua hàng'
    },
    tooltips: {
        callbacks: {
            label: function(tooltipItem, data) {
                return Number(tooltipItem.yLabel).toFixed(0).replace(/./g, function(c, i, a) {
                    return i > 0 && c !== "." && (a.length - i) % 3 === 0 ? "," + c : c;
                });
            }
        }
    },
    scales: {
        yAxes: [{
            ticks: {
                    beginAtZero: true,
                    callback: function(value, index, values) {
                        return number_format(value);
                    }
            },
            gridLines: {
                display: !0
            }
        }]
    },
    }
            });
        });
    }
init_dashboard_report();
init_dashboard_report_cot();
    </script>