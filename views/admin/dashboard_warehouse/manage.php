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
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="<?=!empty($dataLog) ? 'col-md-9' : 'col-md-12'?>">
                <div class="content-wrapper">
                    <div class="container-fluid">
                      <div class="row">
                        <div style="height: 118px;" class="col-md-4">
                          <div class="card text-white bg-warning o-hidden h-100">
                            <div class="card-body">
                              <img src="<?= base_url('assets/images/circle.svg')?>" class="card-img-absolute" alt="circle-image" />
                              <h3 class="mr-5"> Tổng Tồn <br><span style="font-size: 16px" id="stock">0</span></h3>
                            </div>
                          </div>
                        </div>
                        <div style="height: 118px;" class="col-md-4">
                          <div class="card text-white bg-success o-hidden h-100">
                            <div class="card-body">
                              <img src="<?= base_url('assets/images/circle.svg')?>" class="card-img-absolute" alt="circle-image" />
                              <h3 class="mr-5">Tổng giá trị<br><span style="font-size: 16px" id="total">0</span></h3>
                            </div>
                          </div>
                        </div>
                        <div style="height: 118px;" class="col-md-4">
                          <div class="card text-white bg-primary o-hidden h-100" style="background-color: #ff4015;">
                            <div class="card-body">
                              <img src="<?= base_url('assets/images/circle.svg')?>" class="card-img-absolute" alt="circle-image" />
                              <h3 class="mr-5">Tổng Kho<br><span style="font-size: 16px" id="warehouse">0</span></h3>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="clearfix"></div>
                        <br>
                        <div class="col-lg-12">
                        <div class="col-lg-6">
                          <div class="card mb-3">
                            <div class="card-body">
                              <canvas id="mycotChart" width="auto" height="200"></canvas>
                            </div>
                            <div class="card-footer small Updated text-muted">Updated <?=_dt(date('Y-m-d H:i:s'))?></div>
                          </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="card mb-3">
                            <div class="card-body">
                              <canvas id="myPieChart" width="auto" height="200"></canvas>
                            </div>
                            <div class="card-footer small Updated text-muted">Updated <?=_dt(date('Y-m-d H:i:s'))?></div>
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
                                <input type="text" id="date_space_activity" class="form-control date_space_activity" aria-invalid="false" data-module='warehouse'>
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo render_select('staff_activity',$staff,array('staffid','name'),'by_staff_log','',array('data-module'=>'warehouse')); ?>
                        </div>
                        <hr />
                        <div class="activity-container" style="max-height: 320px;">
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
                            <a class="btn btn-info more_log" onclick="load_more_log('warehouse'); return false;"><?=_l('load_more')?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
        dataString = {[csrfData['token_name']] : csrfData['hash']};
        jQuery.ajax({
            type: "post",
            url: "<?=admin_url()?>dashboard_warehouse/count_all/",
            data: dataString,
            cache: false,
            success: function (data) {
              data = JSON.parse(data);
              $('#stock').html(data.stock);
              $('#total').html(data.total);
              $('#warehouse').html(data.warehouse);  
              $('.Updated').html(data.date_update);       
              }
        });
    }
    get_total_limit();
    var myLineChart;
    var myCotChart;
    var myPieChart;
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
        dataString = {[csrfData['token_name']] : csrfData['hash']};
        $.post(admin_url + 'dashboard_warehouse/dashboard_report_tron/',dataString, function(response) {
        var response = JSON.parse(response);
        if (typeof(myCotChart) !== 'undefined') {  
            myCotChart.destroy();
        }
        var ctx = document.getElementById("mycotChart");
            myCotChart = new Chart(ctx, {
                type: "pie",
                data: {
                    labels: response.labels,
                    datasets: [
                    {
                        label: response.labels,
                        backgroundColor: response.color,
                        data: response.data,
                    }
                    ]
                },
                options: {
                    title: {
                    display: true,
                    text: 'Thống kê số lượng theo loại hàng',
                    position: 'top'
                    },
                    rotation: -0.7 * Math.PI
                }
            });
        });
    }
    function init_dashboard_report_pie() {
        dataString = {[csrfData['token_name']] : csrfData['hash']};
        $.post(admin_url + 'dashboard_warehouse/dashboard_report_pie/',dataString, function(response) {
        var response = JSON.parse(response);
        if (typeof(myPieChart) !== 'undefined') {  
            myPieChart.destroy();
        }
        var ctx = document.getElementById("myPieChart");
            myPieChart = new Chart(ctx, {
                type: "pie",
                data: {
                    labels: response.labels,
                    datasets: [
                    {
                        label: response.labels,
                        backgroundColor: response.color,
                        data: response.data,
                    }
                    ]
                },
                options: {
                    title: {
                    display: true,
                    text: 'Thống kê số lượng theo kho',
                    position: 'top'
                    },
                    rotation: -0.7 * Math.PI
                }
            });
        });
    }    
    // line chart data
    function init_dashboard_report() {
        dataString = {[csrfData['token_name']] : csrfData['hash']};
        $.post(admin_url + 'dashboard_warehouse/dashboard_report_tron/',dataString, function(response) {
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
                type: 'pie',
      data: {
        labels: labels_cot,
        datasets: [{ 
            data: datas_cost,
            label: "Công nợ",
            borderColor: "#3e95cd",
            fill: false,
            lineTension: 0
          },{ 
            data: data_payment,
            label: "Thanh toán",
            borderColor: "#8e5ea2",
            fill: false,
            lineTension: 0
          }
        ]
      },
      options: {
        title: {
          display: true,
          text: 'Biểu đồ bán hàng'
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
    // init_dashboard_report();
    init_dashboard_report_cot();
    init_dashboard_report_pie();
</script>
</body>
</html>
