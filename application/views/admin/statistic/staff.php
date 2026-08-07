<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    #menu {
        width: 0% !important;
    }
    #wrapper {
        margin-left: 0px !important;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="row" style="margin-bottom:5px">
                                <div id="search-tnh" class="collapse in" aria-expanded="true">
                                    <div class="col-md-3">
                                        <?= lang('Ngày bắt đầu (Ngày Tạo)', 'start_date_search') ?>
                                        <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>"
                                               id="start_date_search" class="start_date_search datepicker form-control"
                                               style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('Ngày kết thúc (Ngày Tạo)', 'end_date_search') ?>
                                        <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>"
                                               id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;"
                                               value="">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-staff" class="table dt-tnh table-staff" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('STT') ?></th>
                                            <th class="text-center"><?= lang('Phòng Ban') ?></th>
                                            <th class="text-center"><?= lang('Mã Vị Trí') ?></th>
                                            <th class="text-center"><?= lang('Họ Và Tên Nhân Viên') ?></th>
                                            <th class="text-center"><?= lang('Mã Nhân Viên') ?></th>
                                            <th class="text-center"><?= lang('Số Điện Thoại') ?></th>
                                            <th class="text-center"><?= lang('Địa Chỉ Nơi Cư Trú') ?></th>
                                            <th class="text-center"><?= lang('Ngày Sinh') ?></th>
                                            <th class="text-center"><?= lang('Nơi Sinh') ?></th>
                                            <th class="text-center"><?= lang('Nguyên Quán') ?></th>
                                            <th class="text-center"><?= lang('CCCD') ?></th>
                                            <th class="text-center"><?= lang('Ngày Cấp') ?></th>
                                            <th class="text-center"><?= lang('Nơi Cấp') ?></th>
                                            <th class="text-center"><?= lang('Tình Trạng Hôn Nhân') ?></th>
                                            <th class="text-center"><?= lang('Quốc Tịch') ?></th>
                                            <th class="text-center"><?= lang('Dân Tộc') ?></th>
                                            <th class="text-center"><?= lang('Tài Khoản Ngân Hàng') ?></th>
                                            <th class="text-center"><?= lang('Tên Ngân Hàng-Chi Nhánh') ?></th>
                                            <th class="text-center"><?= lang('Ngày Thử Việc') ?></th>
                                            <th class="text-center"><?= lang('Thời Gian Thử Việc') ?></th>
                                            <th class="text-center"><?= lang('Mã Số Hợp Đồng') ?></th>
                                            <th class="text-center"><?= lang('Ngày Ký Hợp Đồng') ?></th>
                                            <th class="text-center"><?= lang('Thời Hạn Hợp Đồng') ?></th>
                                            <th class="text-center"><?= lang('Ngày Tái Ký Hợp Đồng') ?></th>
                                            <th class="text-center"><?= lang('Mức Lương Cơ Bản') ?></th>
                                            <th class="text-center"><?= lang('Mức Lương Năng Lực') ?></th>
                                            <th class="text-center"><?= lang('Phụ Cấp') ?></th>
                                            <th class="text-center"><?= lang('BHXH') ?></th>
                                            <th class="text-center"><?= lang('Số Sổ BHXH') ?></th>
                                            <th class="text-center"><?= lang('Thuế TNCN') ?></th>
                                            <th class="text-center"><?= lang('Mã Số Thuế Cá Nhân') ?></th>
                                            <th class="text-center"><?= lang('BHYT') ?></th>
                                            <th class="text-center"><?= lang('Số Thẻ BHYT') ?></th>
                                            <th class="text-center"><?= lang('Số Ngày Phép Năm') ?></th>
                                            <th class="text-center"><?= lang('Số Năm Thâm Niên') ?></th>
                                            <th class="text-center"><?= lang('Đang Hoạt Động') ?></th>
                                            <th class="text-center"><?= lang('Ngưng Sử Dụng') ?></th>
                                            <th class="text-center"><?= lang('Ngày Tạo') ?></th>
                                            <th class="text-center"><?= lang('Ngày Điều Chỉnh') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="99"></td>
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
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    var oTable = '';
    var SuppliersServerParams = {
        'filterStatus' : '[name="filterStatus"]',
        'start_date' : '[name="start_date_search"]',
        'end_date' : '[name="end_date_search"]',
    };
    oTable = initDataTableCustom('.table-staff', admin_url + 'statistic/getStaff', [0], [0], SuppliersServerParams,[0, 'desc'], fixedColumns = {leftColumns: 0, rightColumns: 0});

    $(document).on('change', '#end_date_search, #start_date_search', function(event) {
        event.preventDefault();
        oTable.draw();
    });

    function exportExcel() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/statistic/getStaffExcel',
            data: {
                csrf_token_name: hash,
                start_date_search: start_date_search,
                end_date_search: end_date_search,
                export_excel: 1,
            },
            dataType: "json",
            success: function(response) {
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