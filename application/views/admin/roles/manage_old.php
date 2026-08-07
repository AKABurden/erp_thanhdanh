<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
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
                                <th class="text-center" style="width: 50px"><?= _l('Hội-Ban') ?></th>
                                <th class="text-center" style="width: 50px"><?= _l('Khối') ?></th>
                                <th class="text-center" style="width: 50px"><?= _l('Phòng') ?></th>
                                <th class="text-center" style="width: 50px"><?= _l('Tổ') ?></th>
                                <th class="text-center" style="width: 50px"><?= _l('Nhóm') ?></th>
                                <th class="text-center" style="width: 50px"><?= _l('Mã Vị Trí') ?></th>
                                <th class="text-center" style="width: 250px"><?= _l('Tên Vị Trí') ?></th>
                                <th class="text-center" style="width: 150px"><?= _l('Tên Chức Vụ') ?></th>
                                <th class="text-center" style="width: 150px"><?= _l('ch_categories_level') ?></th>
                                <th class="text-center" style="width: 150px"><?= _l('Bộ Phận') ?></th>
                                <th class="text-center" style="width: 150px"><?= _l('Nhân Viên') ?></th>
                                <th class="text-center" style="width: 150px"><?= _l('Email') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Bậc Lương') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Hệ Lương') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Mức Lương') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Hệ Số Tăng Ca') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Phép') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Vật Tư - Trang Thiết Bị') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Mô Tả Công Việc') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('KPI') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Hợp Đồng Lao Động') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Ngày Thử Việc') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Ngày Kết Thúc') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Ngày Ký HĐ') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Loại Hợp Đồng') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Ngày Hiệu Lực') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Thời Gian Tái Ký') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Thời gian xét tăng lương') ?></th>
                                <th class="text-center" style="width: 150px;"><?= _l('Thời Gian Khám Sức Khỏe') ?></th>
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
        initialState: 'collapsed',
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
                    console.log($(listRows[i].children[6]).find('.code_role').html());
                    var str = bodauTiengViet($(listRows[i].children[6]).find('.code_role').html().toLowerCase());
                    if (str.search(search_string) >= 0) {
                        $(listRows[i]).attr('tsearch', 'ok');
                    }
                }
                tpanigationNew(elTable, 1, 1);
            }
            createPanigation(elTable, elPanigation, 1);
        });
    }
</script>
</body>
</html>
