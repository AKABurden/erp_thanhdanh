<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=3.3') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<style>
    .fraction-group {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .fraction-main {
        font-size: 14px;
        font-weight: 600;
        color: #527fe9;
        line-height: 1;
    }
    .fraction-separator {
        width: 1.5px;
        height: 24px;
        background-color: #cbd5e1;
        transform: rotate(20deg);
    }
    .fraction-sub {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .fraction-top {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        line-height: 1.1;
    }
    .fraction-bottom {
        font-size: 14px;
        font-weight: 500;
        color: #94a3b8;
        border-top: 1px solid #e2e8f0;
        padding-top: 1px;
        margin-top: 1px;
        line-height: 1.1;
    }
</style>
<div id="wrapper">
   <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body ">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title ?? ''?></span>
                 <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>
             <a href="<?=admin_url('kpi_targets_clients/modal_excel_import')?>" class=" tnh-modal pull-right mright5 btn btn-info H_action_button">
                 Import Excel
             </a>
          </div>
          <div class="clearfix"></div>
      </div>
   </div>
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                   <div class="row" style="margin-bottom:5px">
                       <div class="col-md-3">
                           <label for="year_search">Năm</label>
                           <select class="year_search" id="year_search" name="year_search" style="width: 100%">
                               <?php foreach (getYear() as $key => $value){ ?>
                                   <option <?= date('Y') == $value ? 'selected' : '' ?> value="<?= $value ?>"><?= $value ?></option>
                               <?php } ?>
                           </select>
                       </div>
                   </div>
                <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                  <div class="clearfix mtop20"></div>
                  <?php $table_data = array(
                            _l('STT'),
                            _l('Mã khách hàng'),
                            _l('Tên khách hàng'),
                            _l('Nhóm khách hàng'),
                            _l('Số báo giá / <span class="cYear">'.date('Y').'</span>'),
                            _l('Báo giá đã duyệt / <span class="cYear">'.date('Y').'</span>'),
                            _l('Báo giá chưa duyệt / <span class="cYear">'.date('Y').'</span>'),
                            _l('Đơn hàng có / <span class="cYear">'.date('Y').'</span>'),
                            _l('Đơn hàng không có / <span class="cYear">'.date('Y').'</span>'),
                            _l('Phát triển mới có đơn / <span class="cYear">'.date('Y').'</span>'),
                            _l('Phát triển mới không đơn / <span class="cYear">'.date('Y').'</span>'),
                            _l('Số lượng khiếu nại/<span class="cYear">'.date('Y').'</span>'),
                            _l('Mẫu lần 1/<span class="cYear">'.date('Y').'</span>'),
                            _l('Mẫu lần 2/<span class="cYear">'.date('Y').'</span>'),
                            _l('Điểm cộng/<span class="cYear">'.date('Y').'</span>'),
                            _l('Điểm trừ/<span class="cYear">'.date('Y').'</span>'),
                            _l('Tổng điểm/<span class="cYear">'.date('Y').'</span>'),
                            _l('Trạng thái khách hàng/<span class="cYear"></span>'),
                            _l('Hành động chăm sóc'),
                            _l('Thao tác'),
                    );
                    render_datatable($table_data,'kpi_targets_clients');
                  ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<div id="view_other_payslips_coupon"></div>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>

    $("#year_search").select2();
    $('.H_filter').click(function(e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-id');
        target.parent().parent().find('li').removeClass('active');
        target.parent().addClass('active');
        $('input[name="filterStatus"]').val(value);
        $('input[name="filterStatus"]').change();
    });

    $(document).on('change',
        '#year_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
            $('.cYear').text($(this).val());
        });


    var filterList = {
        'filterStatus' : '[name="filterStatus"]',
        'year_search' : '[name="year_search"]',
    };
    var oTable;
    $(function(){
        oTable = initDataTable('.table-kpi_targets_clients', admin_url + 'kpi_targets_clients/table', [1], [4,5,6,7,8,9], filterList, [0, 'desc']);
    });

    $.each(filterList, function(i, filter){
        $(filter).on('change', function(e){
            if($('.table-kpi_targets_clients').hasClass('dataTable'))
            {
                $('.table-kpi_targets_clients').DataTable().ajax.reload();
            }
        })
    })
    $('.table-kpi_targets_clients').on('draw.dt', function(e, settings) {
        var dt_danger = $('.td-danger');
        $.each(dt_danger, function(index, value) {
            $(value).parents('td').addClass('bg-danger');
        })
        var dt_warning = $('.td-warning');

        $.each(dt_warning, function(index, value) {
            $(value).parents('td').addClass('bg-warning');
        })
    })


    function exportExcel() {
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/kpi_targets_clients/export_excel',
            data: {
                csrf_token_name: hash,
                export_excel: 1,
                year_search: $('#year_search').val(),
            },
            dataType: "json",
            success: function (response) {
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
