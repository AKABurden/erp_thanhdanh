<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<style>
    #tb-add-timekeeping tr th {
        text-align: center !important;
    }

    .table-salary-new thead tr th {
        background: #d9edf7 !important;
        color: #0e5dab !important;
        border: 1px solid #93b4d6 !important;
    }

</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="dropdown pull-right">
                <button class="btn btn-info pull-right H_action_button dropdown-toggle nav-link" type="button"
                        id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                    <?= lang('actions') ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 200px;">
                    <li>
                        <a onclick="export_excel()" class="test btn-search-tnh">
                            <i class="fa fa-file-excel-o"></i> <?php echo _l('Xuất excel'); ?></a>
                    </li>
                    <li>
                        <a onclick="print_pdf()" class="test btn-search-tnh hide">
                            <i class="fa fa-file-pdf-o"></i> <?php echo _l('In bảng lương'); ?></a>
                    </li>
                    <li>
                        <a onclick="print_pdf_new()" class="test btn-search-tnh">
                            <i class="fa fa-file-pdf-o"></i> <?php echo _l('In phiếu lương'); ?></a>
                    </li>
                </ul>
            </div>
            <?php if (has_permission('payroll_salary', '', 'delete')) { ?>
                <div class="pull-right mright5 H_border">
                    <a class="btn btn-info test H_action_button" onclick="deletePayroll(); return false;"> <i
                            class="fa fa-remove width-icon-actions"></i><?php echo _l('Xoá bảng lương'); ?></a>
                </div>
            <?php } ?>
            <?php if (has_permission('payroll_salary', '', 'edit')) { ?>
                <div class="pull-right mright5 H_border hide">
                    <a href="<?= base_url('admin/payroll/load_view_edit_chose') ?>"
                       class="tnh-modal btn btn-info test H_action_button" data-tnh="modal" data-toggle="modal"
                       data-target="#myModal"><i
                            class="fa fa-edit width-icon-actions"></i><?php echo _l('Sửa bảng lương'); ?></a>
                </div>
            <?php } ?>
            <?php if (has_permission('payroll_salary', '', 'create')) { ?>
                <div class="pull-right mright5 H_border">
                    <a href="<?= admin_url('payroll/add_payroll_salary_audit') ?>" class="btn btn-info test H_action_button">
                        <?php echo _l('create_add_new'); ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="content ae-content view-timekeeping">
        <div class="row">
            <div class="col-md-12">
                <div class="panel" style="margin-bottom: 3px;">
                    <div class="panel-body" style="padding: 0px;">
                        <div class="row" style="padding: 1px;">
                            <div id="search-tnh" class="collapse in" aria-expanded="true">
                                <div class="col-md-3">
                                    <?= lang('year', 'year') ?>
                                    <select name="year" id="year" class="" data-placeholder="<?= lang('year') ?>"
                                            style="width: 100%;" style="width: 100%;">
                                        <?php if (!empty(getYear())) : ?>
                                            <?php foreach (getYear() as $key => $value) : ?>
                                                <option <?= date('Y') == $key ? 'selected' : '' ?>
                                                    value="<?= $key ?>"><?= $value ?>
                                                </option>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <?= lang('month', 'month') ?>
                                    <select name="month" id="month" class="" data-placeholder="<?= lang('month') ?>"
                                            style="width: 100%;" style="width: 100%;">
                                        <?php if (!empty(getMonth())) : ?>
                                            <?php foreach (getMonth() as $key => $value) : ?>
                                                <option <?= date('m') == $key ? 'selected' : '' ?>
                                                    value="<?= $key ?>"><?= $value ?>
                                                </option>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <?= lang('Nhân viên', 'staff') ?>
                                    <select class="selectpicker staff form-control" name="staff[]" id="staff"
                                            data-live-search="true"
                                            multiple
                                            title='<?php echo _l('Nhân viên'); ?>'
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <?php if (!empty($staff)) { ?>
                                            <?php foreach ($staff as $key => $value) { ?>
                                                <optgroup label="<?= $value['name'] ?>">
                                                    <?php if (!empty($value['staffs'])) : ?>
                                                        <?php foreach ($value['staffs'] as $k => $v) : ?>
                                                            <option data-subtext="<?= $v['name_roles'] ?>"
                                                                    value="<?= $v['staffid'] ?>"><?= $v['staff_name'] ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </optgroup>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <?= lang('Phòng ban', 'department') ?>
                                    <select name="department" id="department" class="department"
                                            data-placeholder="<?= lang('Phòng ban') ?>" style="width: 100%;">
                                        <option value=""></option>
                                        <?php
                                        $this->db->select('tbldepartments.name as name_departments,tbldepartments.departmentid as departmentid ');
                                        $this->db->from('tbldepartments');
                                        $departments = $this->db->get()->result_array();
                                        ?>
                                        <?php if (!empty($departments)) : ?>
                                            <?php foreach ($departments as $key => $value) : ?>
                                                <option value="<?= $value['departmentid'] ?>">
                                                    <?= $value['name_departments']; ?>
                                                </option>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <?= lang('Chi nhánh', 'branch') ?>
                                    <select class="selectpicker branch form-control" name="branch_search"
                                            id="branch_search" data-live-search="true"
                                            title='<?php echo _l('Chi nhánh'); ?>'
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option></option>
                                        <?php if (!empty($branch)) { ?>
                                            <?php foreach ($branch as $key => $value) { ?>
                                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="clearfix"></div>
                        <div class="col-md-12 table-responsive">
                            <table id="table-salary-new" class="table dt-tnh table-hover table-salary-new">
                                <thead>
                                <tr>
                                    <?php $stt = 1; ?>
                                    <th rowspan="2">
                                        <div class="checkbox mass_select_all_wrap text-center"><input
                                                type="checkbox" id="mass_select_all"
                                                data-to-table="salary-new"><label
                                                for="mass_select_all"></label><br>(<?= $stt ?>)</div>
                                    </th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="width: 100px;">
                                        <?= lang('Mã NV') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="width: 180px;">
                                        <?= lang('Họ Tên') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="width: 150px;">
                                        <?= lang('Chức vụ') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 100px;">
                                        <?= lang('Ngày vào làm') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 100px;">
                                        <?= lang('Lương P1') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <?php $sttNew = $stt; ?>
                                    <?php $stt ++; ?>
                                    <th colspan="2" class="text-center" style="min-width: 100px;">
                                        <?= lang('Lương năng lực') ?></th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 100px;">
                                        <?= lang('Lương đóng BHXH') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Kiêm nhiệm') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Doanh số') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Công tác phí') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Điện thoại') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Xăng xe đi lại') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Nhà trọ') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Tổng phụ cấp') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Thâm niên') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Số giờ công') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Số ngày công') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <?php $sttNewVs1 = $stt; ?>
                                    <?php $stt ++; ?>
                                    <?php $stt ++; ?>
                                    <th colspan="3" class="text-center" style="min-width: 100px;">
                                        <?= lang('Ngày nghỉ có lương') ?></th>
                                    <?php $stt ++; ?>
                                    <?php $stt ++; ?>
                                    <th colspan="2" class="text-center" style="min-width: 100px;">
                                        <?= lang('Nghỉ không lương') ?></th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Tổng ngày công tính lương') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Thu nhập') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <?php $sttNewVs2 = $stt; ?>
                                    <?php $stt ++; ?>
                                    <?php $stt ++; ?>
                                    <?php $stt ++; ?>
                                    <?php $stt ++; ?>
                                    <th colspan="5" class="text-center" style="min-width: 80px;">
                                        <?= lang('Số tiếng tăng ca') ?></th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Tổng tiền tăng ca') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <?php $stt += $colspanAllowance; ?>
                                    <th colspan="<?= $colspanAllowance ?>" class="text-center"
                                        style="min-width: 100px;">
                                        <?= lang('Các khoản phải trả') ?></th>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Bù lương') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Tổng các khoản phải trả') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Tổng thu nhập') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <?php $stt += $colspanReduce; ?>
                                    <th colspan="<?= $colspanReduce ?>" class="text-center" style="min-width: 80px;">
                                        <?= lang('Các khoản trừ') ?></th>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Tổng các khoản trừ') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <?php $stt += 3; ?>
                                    <th colspan="4" class="text-center" style="min-width: 80px;">
                                        <?= lang('Khoản trừ BHXH') ?></th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Tổng khấu trừ BHXH') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;"><?=lang('Khen thưởng KPIs')?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;"><?=lang('Kỹ luật KPIs')?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Giảm trừ gia cảnh') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Lương ngoài giờ miễn thuế') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Các khoản miễn thuế') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Hoàn phép năm') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Thu nhập chịu thuế') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Thu nhập tính thuế') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Thuế TNCN') ?><br>(<?= $stt ?>)</th>
                                    <?php $stt ++; ?>
                                    <th rowspan="2" class="text-center" style="min-width: 80px;">
                                        <?= lang('Tổng thực lãnh') ?><br>(<?= $stt ?>)</th>
                                </tr>
                                <tr>
                                    <th class="text-center" style="min-width: 50px;"><?= lang('Lương P2') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew++; ?>
                                    <th class="text-center" style="min-width: 50px;"><?= lang('Lương P3') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew = $sttNewVs1; ?>
                                    <th class="text-center" style="min-width: 50px;"><?= lang('Phép năm') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew++; ?>
                                    <th class="text-center" style="min-width: 50px;"><?= lang('Lễ tết') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew++; ?>
                                    <th class="text-center"
                                        style="min-width: 50px;"><?= lang('VR hưởng lương ( hiếu hỉ)') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew++; ?>
                                    <th class="text-center"
                                        style="min-width: 50px;"><?= lang('Nghỉ việc riêng') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew++; ?>
                                    <th class="text-center"
                                        style="min-width: 50px;"><?= lang('Ốm đau') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew = $sttNewVs2; ?>
                                    <th class="text-center"
                                        style="min-width: 50px;"><?= lang('Ngày thường (1.5)') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew++; ?>
                                    <th class="text-center" style="min-width: 50px;"><?= lang('Chủ nhật (2.0)') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew++; ?>
                                    <th class="text-center"
                                        style="min-width: 50px;"><?= lang('Ngày lễ tết (3.0)') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew++; ?>
                                    <th class="text-center"
                                        style="min-width: 50px;"><?= lang('Đêm thường ('.get_option('coefficient_default_night').')') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew++; ?>
                                    <th class="text-center"
                                        style="min-width: 50px;"><?= lang('Đêm chủ nhật ('.get_option('coefficient_sunday_night').')') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew++; ?>
                                    <?php $sttNew++; ?>
                                    <?php if (!empty($dtAllowance)) { ?>
                                        <?php foreach ($dtAllowance as $key => $value) { ?>
                                            <th class="text-center" style="min-width: 50px;"><?= $value['name'] ?><br>(<?= $sttNew ?>)</th>
                                        <?php $sttNew++; } ?>
                                    <?php } ?>
                                    <th class="text-center"
                                        style="min-width: 70px;"><?= lang('Ngày cơm hành chính') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew++; ?>
                                    <th class="text-center"
                                        style="min-width: 70px;"><?= lang('Tiền ăn tăng ca') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew++; ?>
                                    <th class="text-center" style="min-width: 70px;"><?= lang('Tiền ăn hành chính') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew++; ?>
                                    <?php $sttNew++; ?>
                                    <?php $sttNew++; ?>
                                    <?php $sttNew++; ?>
                                    <?php if (!empty($dtReduce)) { ?>
                                        <?php foreach ($dtReduce as $key => $value) { ?>
                                            <th class="text-center" style="min-width: 50px;"><?= $value['name'] ?><br>(<?= $sttNew ?>)</th>
                                        <?php $sttNew++; } ?>
                                    <?php } ?>
                                    <th class="text-center" style="min-width: 80px;">
                                        <?= lang('Khấu trừ khác(tạm ứng)') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew++; ?>
                                    <?php $sttNew++; ?>
                                    <th class="text-center" style="min-width: 70px;"><?= lang('8% BHXH') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew++; ?>
                                    <th class="text-center" style="min-width: 70px;"><?= lang('1,5% BHYT') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew++; ?>
                                    <th class="text-center" style="min-width: 70px;"><?= lang('1% BHTN') ?><br>(<?= $sttNew ?>)</th>
                                    <?php $sttNew++; ?>
                                    <th class="text-center" style="min-width: 70px;"><?= lang('1% Đoàn phí') ?><br>(<?= $sttNew ?>)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="99"></td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <?php if (!empty($dtAllowance)) { ?>
                                        <?php foreach ($dtAllowance as $key => $value) { ?>
                                            <td></td>
                                        <?php } ?>
                                    <?php } ?>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <?php if (!empty($dtReduce)) { ?>
                                        <?php foreach ($dtReduce as $key => $value) { ?>
                                            <td></td>
                                        <?php } ?>
                                    <?php } ?>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>

<script>
    var oTable = '';
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        year: "#year",
        month: "#month",
        staff: "#staff",
        department: "#department",
        branch_search: "#branch_search",
    };
    oTable = tnhInitDataTable('#table-salary-new',
        '<?= site_url('admin/payroll/getPayrollAudit') ?>', {
            'order': [
                [2, 'asc'],
            ],
            fixedColumns: {
                leftColumns: 0,
                rightColumns: 0
            },
            scrollX: true,
            "ajax": {
                "url": '<?= site_url('admin/payroll/getPayrollAudit') ?>',
                "type": "POST",
                "data": function (d) {
                    if (typeof (csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function (json) {

                    $('.table-salary-new tfoot tr td:nth-child(6)').html(`<div class="text-right">${tnhFormatMoney(json.footer_salary_bhxh)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(7)').html(`<div class="text-right">${tnhFormatMoney(json.footer_salary_position)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(8)').html(`<div class="text-right">${tnhFormatMoney(json.footer_salary_responsibility)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(9)').html(`<div class="text-right">${tnhFormatMoney(json.footer_salary)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(10)').html(`<div class="text-right">${tnhFormatMoney(json.footer_concurrently)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(11)').html(`<div class="text-right">${tnhFormatMoney(json.footer_sales)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(12)').html(`<div class="text-right">${tnhFormatMoney(json.footer_business_fee_staff)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(13)').html(`<div class="text-right">${tnhFormatMoney(json.footer_phone)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(14)').html(`<div class="text-right">${tnhFormatMoney(json.footer_gasonline_cars)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(15)').html(`<div class="text-right">${tnhFormatMoney(json.footer_motel)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(16)').html(`<div class="text-right">${tnhFormatMoney(json.footer_tong_phu_cap)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(17)').html(`<div class="text-right">${tnhFormatMoney(json.footer_seniority)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(18)').html(`<div class="text-center">${tnhFormatNumber(json.footer_day_number)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(19)').html(`<div class="text-center">${(json.footer_day_number_new)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(20)').html(`<div class="text-center">${(json.footer_day_number_holiday)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(21)').html(`<div class="text-center">${(json.footer_day_number_lt)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(22)').html(`<div class="text-center">${(json.footer_day_number_ch)}</div>`);;
                    $('.table-salary-new tfoot tr td:nth-child(23)').html(`<div class="text-center">${(json.footer_total_number_day_kp)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(24)').html(`<div class="text-center">${(json.footer_total_number_day_od)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(25)').html(`<div class="text-center">${(json.footer_total_number_day)}</div>`);;
                    $('.table-salary-new tfoot tr td:nth-child(26)').html(`<div class="text-right">${tnhFormatMoney(json.footer_salary_income)}</div>`);
                    $(`.table-salary-new tfoot tr td:nth-child(27)`).html(`<div class="text-center"></div>`);
                    $(`.table-salary-new tfoot tr td:nth-child(28)`).html(`<div class="text-center"></div>`);
                    $(`.table-salary-new tfoot tr td:nth-child(29)`).html(`<div class="text-right"></div>`);
                    $(`.table-salary-new tfoot tr td:nth-child(30)`).html(`<div class="text-right"></div>`);
                    $(`.table-salary-new tfoot tr td:nth-child(31)`).html(`<div class="text-right"></div>`);
                    $(`.table-salary-new tfoot tr td:nth-child(32)`).html(`<div class="text-right"></div>`);
                    keyNew = 32;
                    <?php if (!empty($dtAllowance)){ ?>
                    <?php foreach ($dtAllowance as $key => $value){ ?>
                    keyNew++;
                    idcheck = <?= $value['id'] ?>;
                    footer_total_allowance = `footer_total_allowance_${idcheck}`
                    footer_total_allowance_new = json.arrFooter;
                    <?php } ?>
                    <?php } ?>
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-center">${tnhFormatMoney(json.footer_allowance_rice)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_allowance_rice_tc)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_allowance_rice_money)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_salary_compensation)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_total_allowance_other)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_total)}</div>`);
                    <?php if (!empty($dtReduce)){ ?>
                    <?php foreach ($dtReduce as $key => $value){ ?>
                    keyNew++;
                    idcheck = <?= $value['id'] ?>;
                    <?php } ?>
                    <?php } ?>
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_deduct_advance)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_total_reduce_other)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_deduct_bhxh)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_deduct_bhyt)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_deduct_bhtn)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_deduct_union)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_total_reduce_bhxh)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_grand_total_kt)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_grand_total_kl)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_allowance_family)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_business_fee_difference)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_tax_exemption)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_complete_permission)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_income_taxes)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_taxable_income)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_total_vat)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoney(json.footer_total_real)}</div>`);
                    return json.aaData;
                }
            },
            "createdRow": function (row, data, index) {
            },
            "columnDefs": [
                {
                    "render": function (data, type, row) {
                        return `<div style="text-align:center">${data}</div>`;
                    },
                    "targets": 0,
                    "name": 'id',
                    'orderable': false
                },
                {
                    "render": function (data, type, row) {
                        return `<div style="text-align:left">${data}</div>`;
                    },
                    "targets": 1,
                    "width": '80px',
                },
            ],
        });
    $(document).ready(function () {
        init_selectpicker();
    });
    $(document).on('change', '#year, #month, #staff, #department,#branch_search', function (event) {
        event.preventDefault();
        oTable.draw();
    });
    $(document).ready(function () {
        $('#month').select2();
        $('#year').select2();
        $('#department').select2({
            allowClear: true
        });
    });

    function deletePayroll() {
        var ids = '';
        var rows = $('.table-salary-new').find('tbody tr');
        $.each(rows, function () {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids += checkbox.val() + ',';
            }
        });
        if (!ids) {
            bootbox.alert('Xin vui lòng chọn bảng lương cần xoá');
            return;
        }
        if (ids) {
            var r = confirm(
                "<?php echo _l('Bạn có chắc muốn xóa dữ liệu!');?>");
            if (r == false) {
                oTable.draw('page');
                return false;
            } else {
                $.ajax({
                    url: site.base_url + 'admin/payroll/deletePayrollAudit',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        ids: ids,
                    },
                })
                    .done(function (data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            oTable.draw();
                        } else {
                            alert_float('danger', data.message);
                            oTable.draw('false');
                        }
                    })
                    .fail(function (data) {
                        alert_float('danger', 'errors');
                    })
            }
        }

    }

    function export_excel() {
        var year = $('#year').val();
        var month = $('#month').val();
        var staff = $('#staff').val();
        var branch_search = $('#branch_search').val();
        var department = $('#department').val();
        var get = "?data=true";
        get += '&year=' + year;
        get += '&month=' + month;
        get += '&staff=' + staff;
        get += '&department=' + department;
        get += '&branch_search=' + branch_search;
        window.open(admin_url + 'payroll/export_excel_audit' + get, '_blank');
    }

    function print_pdf() {
        var year = $('#year').val();
        var month = $('#month').val();
        var staff = $('#staff').val();
        var department = $('#department').val();
        var get = "?data=true";
        get += '&year=' + year;
        get += '&month=' + month;
        get += '&staff=' + staff;
        get += '&department=' + department;
        window.open(admin_url + 'payroll/print_pdf_payroll_salary' + get, '_blank');
    }

    function print_pdf_new() {
        var year = $('#year').val();
        var month = $('#month').val();
        var staff = $('#staff').val();
        var department = $('#department').val();
        var branch_search = $('#branch_search').val();
        var get = "?data=true";
        get += '&year=' + year;
        get += '&month=' + month;
        get += '&staff=' + staff;
        get += '&department=' + department;
        get += '&branch_search=' + branch_search;
        window.open(admin_url + 'payroll/print_pdf_payroll_salary_audit' + get, '_blank');
    }
</script>