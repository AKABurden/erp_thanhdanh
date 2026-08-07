<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=3.3') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">

    <style>
    .as-container {
        display: flex;
        justify-content: center;
        gap: 15px;
        /*padding: 20px;*/
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        flex-direction: column;
        align-items: center;
    }

    .btn-as {
        display: inline-flex;
        align-items: center;
        padding: 5px 5px;
        background-color: #ffffff;
        color: #2563eb;
        text-decoration: none;
        /*border: 2px solid #2563eb;*/
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        white-space: nowrap;
    }

    .btn-as .icon {
        margin-right: 8px;
    }

    .btn-as:hover {
        background-color: #2563eb;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }
</style>
    <div id="wrapper">
       <div class="panel_s mbot10 H_scroll" id="H_scroll">
          <div class="panel-body ">
             <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?=$title ?? ''?></span>
                 <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right hide" href="javascript:void(0)">Xuất Excel</a>
                 <a href="<?=admin_url('recruitment/importEprofile')?>" class="tnh-modal pull-right mright5 btn btn-info H_action_button">
                     Import Excel
                 </a>
                <?php if ($this->preAddEprofile) { ?>
                    <div class="pull-right mright5 H_border">
                      <a href="<?=admin_url('recruitment/detail_eprofile')?>" class="btn btn-info test H_action_button c_modal">
                         <?php echo _l('create_add_new'); ?></a>
                    </div>
                <?php } ?>
              </div>
              <div class="clearfix"></div>
          </div>
       </div>
       <div class="content">
          <div class="row">
             <div class="col-md-12">
                <div class="panel_s">
                   <div class="panel-body">

                    <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                      <div class="clearfix mtop20"></div>
                      <?php $table_data = array(
//                                _l('<div class="text-center" style="width: 30px;"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child-all fa fa-caret-right"></a></div>'),
                                _l('STT'),
                                _l('Mã phiếu YC'),
                                _l('Cấp bậc - vai trò'),
                                _l('Nguồn ứng tuyển'),
                                _l('Avatar'),
                                _l('Họ và Tên'),
                                _l('Email'),
                                _l('Ngày Sinh'),
                                _l('Giới tính'),
                                _l('Hôn nhân'),
                                _l('Địa chỉ'),
                                _l('CMND/CCCD'),
                                _l('Ngày cấp'),
                                _l('Trình độ học vấn'),
                                _l('Trường đào tạo'),
                                _l('Xếp loại'),
//                                _l('Công ty đã làm'),
//                                _l('Chức danh'),
//                                _l('Thành tựu nổi bật'),
                                _l('Thông tin khác'),
                                _l('HR ghi chú'),
                                _l('Tổng Năm kinh nghiệm'),
                                _l('Mức lương mong muốn'),
                                _l('Link CV'),
                                _l('Phiếu đánh giá'),
                                _l('Phiếu đề xuất Offer'),
                                _l('CheckList Hồ Sơ (Onsite)'),
                                _l('Phiếu đánh giá NV thử việc'),
                                _l('Kết quả đánh giá thử việc'),
                                _l('ch_option'),
                        );
                        render_datatable($table_data,'eprofile');
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
    $('.H_filter').click(function(e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-id');
        target.parent().parent().find('li').removeClass('active');
        target.parent().addClass('active');
        $('input[name="filterStatus"]').val(value);
        $('input[name="filterStatus"]').change();
    });
    var modal_requirements = true;

    var filterList = {
        'filterStatus' : '[name="filterStatus"]',
    };
    var tAPI;
    var oTable;
    $(function(){
        oTable = tAPI = initDataTable('.table-eprofile', admin_url+'recruitment/table_eprofile', [1], [4,5,6,7,8,9,10,11,12,13], filterList, [0, 'desc']);
    });

    $.each(filterList, function(i, filter){
        $(filter).on('change', function(e){
            if($('.table-eprofile').hasClass('dataTable'))
            {
                $('.table-eprofile').DataTable().ajax.reload();
            }
        })
    })
    $('.table-eprofile').on('draw.dt', function() {
        // countAll();
    })


    function countAll() {
        $.get(admin_url + 'recruitment/count_all_eprofile', function(result) {
            result = JSON.parse(result);
            $('.count_status').text(0);
            $.each(result, function(i, obj) {
                $('.' + i).text(obj);
            });
        })
    }


    function exportExcel() {
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/recruitment/exportExcelEprofile',
            data: {
                csrf_token_name: hash,
                export_excel: 1,
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

    $('.table-eprofile tbody').on('click', 'td .rows-child', function() {
        var tr = $(this).closest('tr');
        var row = tAPI.row(tr);
        if (row.child.isShown()) {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            $(this).removeClass('fa-caret-right');
            $(this).addClass('fa-caret-down');
            row.child(loadItemsTasks(row.data())).show();
            tr.addClass('shown');
        }
    });

    $('.table-eprofile thead').on('click', '.rows-child-all', function() {
        if ($(this).hasClass('fa-caret-right')) {
            $(this).addClass('fa-caret-down');
            $(this).removeClass('fa-caret-right');
            var rows = $('td .rows-child');
            $.each(rows, function(index, value) {
                var tr = $(value).parents('tr');
                var row = tAPI.row(tr);
                $(value).removeClass('fa-caret-right');
                $(value).addClass('fa-caret-down');
                row.child(loadItemsTasks(row.data())).show();
                tr.addClass('shown');
            })
        } else {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            var rows = $('td .rows-child');
            $.each(rows, function(index, value) {
                var tr = $(value).parents('tr');
                var row = tAPI.row(tr);
                $(value).removeClass('fa-caret-down');
                $(value).addClass('fa-caret-right');
                row.child.hide();
                tr.removeClass('shown');
            })
        }

    });

    function loadItemsTasks(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        // cHtml = cData[16];
        // return `<div>${cHtml}</div>`;
    }

</script>
