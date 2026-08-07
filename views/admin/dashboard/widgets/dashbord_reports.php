  <link href="" rel="stylesheet">
  <!-- <link href="<?= base_url('assets/css_ch/vendor/bootstrap/css/bootstrap.min.css')?>" rel="stylesheet"> -->
  <!-- Custom fonts for this template-->

  <!-- Page level plugin CSS-->
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
      padding: 0.5rem;
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
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="row">
        <div style="height: 100px;" class="col-md-4">
          <div class="card text-white bg-warning o-hidden h-100">
            <div class="card-body">
              <img src="<?= base_url('assets/images/circle.svg')?>" class="card-img-absolute" alt="circle-image" />
              <h3 class="mr-5"><span style="font-size: 20px"> Doanh Thu </span><br><span id="subtotal">0</span></h3>
            </div>
          </div>
        </div>
        <div style="height: 100px;" class="col-md-4">
          <div class="card text-white bg-success o-hidden h-100">
            <div class="card-body">
              <img src="<?= base_url('assets/images/circle.svg')?>" class="card-img-absolute" alt="circle-image" />
              <h3 class="mr-5"><span style="font-size: 20px"> Giá Vốn </span><br><span id="subtotal1">0</span></h3>
            </div>
          </div>
        </div>
        <div style="height: 100px;" class="col-md-4">
          <div class="card text-white bg-primary o-hidden h-100" style="background-color: #ff4015;">
            <div class="card-body">
              <img src="<?= base_url('assets/images/circle.svg')?>" class="card-img-absolute" alt="circle-image" />
              <h3 class="mr-5"><span style="font-size: 20px"> Lợi nhuận </span><br><span id="subtotal2">0</span></h3>
            </div>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="row">
        <div class="col-md-4 hide">
            <div class="bg-light-gray border-radius-4">
                <div class="p8">
                    <div class="form-group " id="report-time">
                        <label for="months-report"><?php echo _l('Lọc theo ngày'); ?></label><br />
                        <select class="selectpicker" name="months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
                           <option value="day"><?php echo _l('Hôm nay'); ?></option>
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
        <div class="col-lg-12">
          <!-- Example Pie Chart Card-->
          <div class="card mb-3">
            <div class="card-body">
              <canvas id="myBarChart" width="1200" height="400"></canvas>
            </div>
            <div class="card-footer small Updated text-muted">Updated <?=_dt(date('Y-m-d H:i:s'))?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
    <script type="text/javascript">
    function get_total_limit() {

        
        var months_report = $('select[name="months-report"]').val();
        var report_to = $('input[name="report-to"]').val();
        var report_from = $('input[name="report-from"]').val();
        var customers_ch = '';
        var search_id_staff = '';
        dataString = {[csrfData['token_name']] : csrfData['hash'],months_report:months_report,report_from:report_from,report_to:report_to,search_id_staff:search_id_staff,customers_ch:customers_ch};
        jQuery.ajax({
            type: "post",
            url: "<?=admin_url()?>reports/count_all/",
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
function number_format(number, decimals, dec_point, thousands_sep) {
// *     example: number_format(1234.56, 2, ',', ' ');
// *     return: '1 234,56'
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
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
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
    // line chart data
    function init_dashboard_report() {
        var months_report = $('select[name="months-report"]').val();
        var report_to = $('input[name="report-to"]').val();
        var report_from = $('input[name="report-from"]').val();
        var search_id_staff = '';
        var customers_ch = '';
        dataString = {[csrfData['token_name']] : csrfData['hash'],months_report:months_report,report_from:report_from,report_to:report_to,search_id_staff:search_id_staff,customers_ch:customers_ch};
        $.post(admin_url + 'reports/dashboard_report/',dataString, function(response) {
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
        label: "Doanh thu",
        borderColor: "#3e95cd",
        fill: false,
        lineTension: 0
      },{ 
        data: datas_cost,
        label: "Giá vốn",
        borderColor: "#8e5ea2",
        fill: false,
        lineTension: 0
      },{ 
        data: datas,
        label: "Lợi nhuận",
        borderColor: "#c45850",
        fill: false,
        lineTension: 0
      }
    ]
  },
  options: {
    title: {
      display: true,
      text: 'Báo cáo doanh thu'
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

    </script>