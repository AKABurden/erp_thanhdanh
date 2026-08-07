<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=3.3') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<style>
    .progressbar li:not(.initli) {
        /*min-width: 300px;*/
    }
    .progressbar li.active:not(.initli) i {
        color: green;
    }
</style>
<style>
    /* ========================================= */
    /* WORKFLOW STEPPER / PROGRESS BAR STYLES    */
    /* ========================================= */

    .stepper-wrapper {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 20px 0;
        padding: 0 10px;
    }

    /* Đường kẻ nền (Màu xám) chạy dọc phía sau */
    .stepper-wrapper::before {
        content: "";
        position: absolute;
        top: 15px; /* Căn chỉnh để nằm giữa vòng tròn */
        left: 0;
        width: 100%;
        height: 4px;
        background-color: #eaecf4;
        z-index: 1;
        border-radius: 2px;
    }

    /* Từng bước (Item) */
    .stepper-item {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        z-index: 2;
    }

    /* Đường kẻ nối trạng thái đã qua (Màu xanh/đỏ) */
    .stepper-item::before {
        content: "";
        position: absolute;
        top: 15px;
        left: -50%;
        width: 100%;
        height: 4px;
        background-color: #eaecf4; /* Mặc định xám */
        z-index: -1; /* Nằm sau vòng tròn */
        transition: all 0.3s ease;
    }
    .stepper-item:first-child::before {
        content: none; /* Bước đầu tiên không cần đường nối bên trái */
    }

    /* Vòng tròn số/Icon */
    .step-counter {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background-color: #fff;
        border: 3px solid #eaecf4;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: 800;
        color: #858796;
        margin-bottom: 8px;
        font-size: 13px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        z-index: 3; /* Nổi lên trên đường kẻ */
    }

    /* Tên bước */
    .step-name {
        font-size: 11px;
        font-weight: 700;
        color: #b7b9cc;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: center;
        transition: all 0.3s ease;
    }

    /* --- TRẠNG THÁI: COMPLETED (Hoàn thành) --- */
    .stepper-item.completed .step-counter {
        background-color: #1cc88a; /* Xanh lá */
        border-color: #1cc88a;
        color: #fff;
    }
    .stepper-item.completed .step-name {
        color: #1cc88a;
    }
    .stepper-item.completed::before {
        background-color: #1cc88a; /* Tô màu xanh cho đường nối */
    }
    /* Mẹo: Nếu bước hiện tại active, đường nối từ bước trước (completed) tới nó cũng phải xanh */
    .stepper-item.completed + .stepper-item.active::before {
        background-color: #1cc88a;
    }

    /* --- TRẠNG THÁI: ACTIVE (Đang xử lý) --- */
    .stepper-item.active .step-counter {
        background-color: #4e73df; /* Xanh dương */
        border-color: #4e73df;
        color: #fff;
        box-shadow: 0 0 0 4px rgba(78, 115, 223, 0.2); /* Hiệu ứng tỏa sáng */
        transform: scale(1.1);
    }
    .stepper-item.active .step-name {
        color: #4e73df;
        font-weight: 800;
    }

    /* --- TRẠNG THÁI: REJECTED (Từ chối) --- */
    .stepper-item.rejected .step-counter {
        background-color: #e74a3b; /* Đỏ */
        border-color: #e74a3b;
        color: #fff;
    }
    .stepper-item.rejected .step-name {
        color: #e74a3b;
    }
    .stepper-item.rejected::before {
        background-color: #e74a3b; /* Đường nối màu đỏ */
    }
</style>
    <div id="wrapper">
       <div class="panel_s mbot10 H_scroll" id="H_scroll">
          <div class="panel-body ">
             <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?=$title ?? ''?></span>
<!--                 <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>-->
                 <a href="<?=admin_url('recruitment/importRequirements')?>" class=" tnh-modal pull-right mright5 btn btn-info H_action_button">
                     Import Excel
                 </a>
                <?php if ($this->preAddRequirements) { ?>
                <div class="pull-right mright5 H_border">
                  <a href="<?=admin_url('recruitment/detail_requirements')?>" class="btn btn-info test H_action_button c_modal">
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
                    <div class="horizontal-scrollable-tabs">
                      <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                      <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                      <div class="horizontal-tabs">
                          <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                            <li class="active">
                                <a class="H_filter" data-id="all">
                                  <?=_l('leads_all')?>(<span class="count_status all">0</span>)
                                </a>
                            </li>
                            <li>
                                <a class="H_filter" data-id="draft">
                                  <?=_l('Bản nháp')?>(<span class="count_status draft">0</span>)
                                </a>
                            </li>
                            <li>
                                <a class="H_filter" data-id="pending">
                                  <?=_l('Chờ duyệt')?>(<span class="count_status pending">0</span>)
                                </a>
                            </li>
                            <li>
                                <a class="H_filter" data-id="approved">
                                  <?=_l('Đã duyệt')?>(<span class="count_status approved">0</span>)
                                </a>
                            </li>
                            <li>
                                <a class="H_filter" data-id="rejected">
                                  <?=_l('Từ chối')?>(<span class="count_status rejected">0</span>)
                                </a>
                            </li>
                            <li>
                                <a class="H_filter" data-id="closed">
                                  <?=_l('Đóng')?>(<span class="count_status closed">0</span>)
                                </a>
                            </li>
                          </ul>
                      </div>
                  </div>
                       <!-- Container chính -->

                    <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                      <div class="clearfix mtop20"></div>
                      <?php $table_data = array(
                                _l('<div class="text-center" style="width: 30px;"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child-all fa fa-caret-right"></a></div>'),
                                _l('Mã số phiếu'),
                                _l('Ngày lập phiếu'),
                                _l('Chi Nhánh'),
                                _l('Phòng Ban'),
                                _l('Vị trí'),
                                _l('Ngày Nhận Việc'),
                                _l('Mức độ ưu tiên'),
                                _l('Trạng Thái/Gate Duyệt'),
                                _l('Tổng Số E-Profile'),
                                _l('Tổng đánh giá'),
                                _l('Tổng Đề Xuất Offer'),
//
//                                _l('Mô tả (JD)'),
//                                _l('Ngân sách'),
//                                _l('Deadline'),
//                                _l('Trạng thái'),
//                                _l('Đề Xuất bởi'),
//                                _l('Quản lý tuyển dụng'),
//                                _l('Người duyệt'),
//                                _l('Ngày duyệt'),
                                _l('ch_option'),
                        );
                        render_datatable($table_data,'requirements');
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


    var filterList = {
        'filterStatus' : '[name="filterStatus"]',
    };
    var tAPI;
    $(function(){
        tAPI = initDataTable('.table-requirements', admin_url + 'recruitment/table_requirements', [1], [4,5,6,7,8,9], filterList, [0, 'desc']);
    });

    $.each(filterList, function(i, filter){
        $(filter).on('change', function(e){
            if($('.table-requirements').hasClass('dataTable'))
            {
                $('.table-requirements').DataTable().ajax.reload();
            }
        })
    })
    $('.table-requirements').on('draw.dt', function() {
        countAll();
    })


    function countAll() {
        $.get(admin_url + 'recruitment/count_all_requirements', function(result) {
            result = JSON.parse(result);
            $('.count_status').text(0);
            $.each(result, function(i, obj) {
                $('.' + i).text(obj);
            });
        })
    }


    $(document).on('click', '.status-agree', function(event) {
        event.preventDefault();
        index = this;
        id = $(this).attr('data-id'); console.log(id);
        status = $(this).attr('value');
        $(index).attr('disabled', 'disabled');
        $('.po').popover('hide');
        if (id) {
            $.ajax({
                url: site.base_url + 'admin/recruitment/change_status_requirements',
                type: 'GET',
                dataType: 'JSON',
                data: {
                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                    id: id,
                    status: status
                },
            })
                .done(function(data) {
                    alert_float(data.alert_type, data.message);
                    if(data.url_task) {
                        new_task(data.url_task);
                    }
                    tAPI.draw('page');
                })
                .fail(function(data) {
                    alert_float('danger', 'errors');
                    $(index).removeAttr('disabled');
                })
        }
    });

    // function exportExcel() {
    //     $.ajax({
    //         type: "POST",
    //         url: site.base_url + 'admin/recruitment/exportExcelRequirements',
    //         data: {
    //             csrf_token_name: hash,
    //             export_excel: 1,
    //         },
    //         dataType: "json",
    //         success: function (response) {
    //             if (response.result) {
    //                 alert_float('success', response.message);
    //                 download(response.filename, response.file);
    //             } else {
    //                 alert_float('danger', response.message);
    //             }
    //         }
    //     });
    // }

    $('.table-requirements tbody').on('click', 'td .rows-child', function() {
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

    $('.table-requirements thead').on('click', '.rows-child-all', function() {
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
        cHtml = cData['detail_html'];
        return `<div>${cHtml}</div>`;
    }

</script>
