<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .treegrid-expander treegrid-expander-expanded{
        display: none;
    }
    .view-switch {
        display: inline-flex;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 4px;
        gap: 4px;
    }

    .view-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 8px;
        border: 1px solid transparent;
        background: transparent;
        font-size: 14px;
        color: #555;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .view-btn i {
        font-size: 14px;
    }

    /* Hover */
    .view-btn:hover {
        background: #f5f7fa;
    }

    /* Active */
    .view-btn.active {
        background: #eaf2ff;
        border-color: #3b82f6;
        color: #2563eb;
        font-weight: 500;
    }
</style>
<link rel="stylesheet" href="<?= base_url('assets/treegrid/') ?>css/jquery.treegrid.css">
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="<?php echo admin_url('roles/role'); ?>" class="btn btn-info pull-left hide"><?php echo _l('new_role'); ?></a>
							<?php if (has_permission('roles', '', 'export')) { ?>
                                <a onclick="export_excel();" class="btn btn-info mright5 test pull-right H_action_button"><?php echo _l('c_export_excel'); ?></a>
							<?php } ?>
                            <a href="<?= admin_url('roles/modal_excel_import') ?>" class="btn btn-info mright5 test pull-right H_action_button c_modal"><?php echo _l('c_import_excel'); ?></a>
                            <a href="<?= admin_url('roles/modal_excel_import_permission') ?>" class="btn btn-info mright5 test pull-right H_action_button c_modal"><?php echo _l('Xuất excel phân quyền'); ?></a>
                            <a href="<?= admin_url('roles/modal_import_permission') ?>" class="btn btn-info mright5 test pull-right H_action_button c_modal"><?php echo _l('Nhập excel phân quyền'); ?></a>
                        </div>
                        <h4><?=_l('all_roles')?></h4>
                        <div class="clearfix"></div>
                        <hr/>
                        <div class="clearfix"></div>
                        <div class="row mtop5">
                            <div class="col-md-3">
								<?= lang('Mã Vị Trí', 'code_search') ?>
                                <input type="text" name="code_search" id="code_search" style="width: 100%;" class="code_search form-control" placeholder="Nhập mã vị trí" value="">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table class="table table-role tree dataTable" style="width: 2300px">
                                <thead>
                                <th class="text-center" style="width: 50px"></th>
                                <th class="text-center" style="width: 50px"><?= _l('Mã vị trí') ?></th>
                                <th class="text-center" style="width: 150px"><?= _l('Tên vị trí') ?></th>
                                <th class="text-center" style="width: 150px"><?= _l('Chức danh') ?></th>
                                <th class="text-center" style="width: 150px"><?= _l('Phòng ban') ?></th>
                                <th class="text-center" style="width: 150px"><?= _l('Cấp quản lý') ?></th>
                                <th class="text-center" style="width: 150px"><?= _l('Email') ?></th>
                                <th class="text-center" style="width: 150px"><?= _l('Nhân Viên') ?></th>
                                <th class="text-center" style="width: 150px"><?= _l('Ngân sách<br>(VND/Năm)') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Số lượng người cho vị trí') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Trạng thái') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Thời gian hiệu lực từ') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Thời gian hiệu lực đến') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Vòng đời đánh giá (số ngày)') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Link bảng tài sản') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Link phân quyền FOSO') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Link Đường dẫn/Workspace') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Nội dung JD và yêu cầu tuyển dụng') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Tiêu chí loại hồ sơ ứng viên') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Tiêu chuẩn phỏng vấn v1') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Tiêu chuẩn phỏng vấn v2') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Tiêu chuẩn 5 giá trị cốt lõi') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Tiêu chuẩn khi CEO phỏng vấn') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Các chỉ số tính thưởng P3') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Lộ trình phát triển') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Bậc lương') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Pass/Quy ước đăng nhập') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Lương vị trí') ?></th>
                                <th class="text-center" style="width: 100px;"><?= _l('options') ?></th>
                                </thead>
                                <tbody>
                                <?php @get_categories_roles($full_categories); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= base_url('assets/treegrid/') ?>js/jquery.treegrid.js"></script>
<script type="text/javascript">
    $('.tree').treegrid({
        initialState: 'expanded',
    });
    
    function export_excel() {
        var get = "?data=true";
        window.open(admin_url + 'roles/export_excel' + get, '_blank');
    }
</script>
<script>
    $(document).ready(function () {
        searchTableCustomNew('.table-role', '.code_search', '.tpagination');
    });

    function tpanigationNew(elTable, pageCurrent, iCall = 0) {
        if (iCall == 0) {
            $('' + elTable + ' tbody tr').attr('tsearch', 'ok');
        }
        numberPage = 1000;
        $('' + elTable + ' tbody tr[tsearch="notok"]').css('display', 'none');
        $('' + elTable + ' tbody tr[tsearch="ok"]').css('display', 'block');
        sum = $('' + elTable + ' tbody tr[tsearch="ok"]').length;
        numPages = Math.ceil(sum / numberPage);
        start = (pageCurrent - 1) * numberPage;
        end = numberPage * pageCurrent - 1;
        listRows = $('' + elTable + ' tbody tr[tsearch="ok"]');
        for (i = 0; i < listRows.length; i++) {
            if (i >= start && i <= end) {
                listRows[i].style.display = '';
            } else {
                listRows[i].style.display = 'none';
            }
        }
        soNut = numPages;
    }

    function searchTableCustomNew(elTable, elSearch, elPanigation) {
        $(elSearch).keyup(function (event) {
            var search_string = bodauTiengViet($.trim($(elSearch).val()).replace(/ +/g, ' ').toLowerCase());
            if (search_string == '') {
                $('' + elTable + ' tbody tr').attr('tsearch', 'ok');
                tpanigationNew(elTable, 1, 1);
            } else {
                var listRows = $('' + elTable + ' tbody tr');
                $(listRows).attr('tsearch', 'notok');
                for (i = 0; i < listRows.length; i++) {
                    var str = bodauTiengViet($(listRows[i].children[1]).find('.code_role').html().toLowerCase());
                    if (str.search(search_string) >= 0) {
                        $(listRows[i]).attr('tsearch', 'ok');
                    }
                }
                tpanigationNew(elTable, 1, 1);
            }
            createPanigation(elTable, elPanigation, 1);
        });
    }

    $(document).on('click', '.view-btn', function () {
        $('.view-btn').removeClass('active');

        $(this).addClass('active');

        // lấy view
        const view = $(this).data('view');
        console.log('Đang chọn:', view);

        if (view == 'list_room'){
            $(".table-category-room").addClass('hide');
            $(".table-list-room").removeClass('hide');
            if (typeof oTable != 'undefined' && oTable != '') {
                oTable.draw();
            } else {
                loadListTable();
            }
        } else {
            $(".table-category-room").removeClass('hide');
            $(".table-list-room").addClass('hide');
            if (typeof tAPI != 'undefined' && tAPI != '') {
                tAPI.draw();
            } else {
                loadCategoryTable();
            }
        }
    });
</script>
</body>
</html>
