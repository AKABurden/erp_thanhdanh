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
                <button class="btn btn-info pull-right H_action_button dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
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
                        <a onclick="print_pdf_new()" class="test btn-search-tnh hide">
                            <i class="fa fa-file-pdf-o"></i> <?php echo _l('In phiếu lương'); ?></a>
                    </li>
                </ul>
            </div>
            <?php if (has_permission('payroll_salary', '', 'delete')) { ?>
                <div class="pull-right mright5 H_border">
                    <a class="btn btn-info test H_action_button" onclick="deletePayroll(); return false;"> <i class="fa fa-remove width-icon-actions"></i><?php echo _l('Xoá bảng lương'); ?></a>
                </div>
            <?php } ?>
            <?php if (has_permission('payroll_salary', '', 'edit')) { ?>
                <div class="pull-right mright5 H_border hide">
                    <a href="<?= base_url('admin/payroll/load_view_edit_chose') ?>" class="tnh-modal btn btn-info test H_action_button" data-tnh="modal" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit width-icon-actions"></i><?php echo _l('Sửa bảng lương'); ?></a>
                </div>
            <?php } ?>
            <?php if (has_permission('payroll_salary', '', 'create')) { ?>
                <div class="pull-right mright5 H_border">
                    <a href="<?= admin_url('payroll/add_payroll_salary') ?>" class="btn btn-info test H_action_button">
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
                                    <select name="year" id="year" class="" data-placeholder="<?= lang('year') ?>" style="width: 100%;" style="width: 100%;">
                                        <?php if (!empty(getYear())) : ?>
                                            <?php foreach (getYear() as $key => $value) : ?>
                                                <option <?= date('Y') == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?>
                                                </option>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <?= lang('month', 'month') ?>
                                    <select name="month" id="month" class="" data-placeholder="<?= lang('month') ?>" style="width: 100%;" style="width: 100%;">
                                        <?php if (!empty(getMonth())) : ?>
                                            <?php foreach (getMonth() as $key => $value) : ?>
                                                <option <?= date('m') == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?>
                                                </option>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <?= lang('Nhân viên', 'staff') ?>
                                    <select class="selectpicker staff form-control" name="staff[]" id="staff" data-live-search="true" multiple title='<?php echo _l('Nhân viên'); ?>' data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <?php if (!empty($staff)) { ?>
                                            <?php foreach ($staff as $key => $value) { ?>
                                                <optgroup label="<?= $value['name'] ?>">
                                                    <?php if (!empty($value['staffs'])) : ?>
                                                        <?php foreach ($value['staffs'] as $k => $v) : ?>
                                                            <option data-subtext="<?= $v['name_roles'] ?>" value="<?= $v['staffid'] ?>"><?= $v['staff_name'] ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </optgroup>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <?= lang('Phòng ban', 'department') ?>
                                    <select name="department" id="department" class="department" data-placeholder="<?= lang('Phòng ban') ?>" style="width: 100%;">
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
                                    <select class="selectpicker branch form-control" name="branch_search" id="branch_search" data-live-search="true" title='<?php echo _l('Chi nhánh'); ?>' data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
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
                                        <th rowspan="2">
                                            <div class="checkbox mass_select_all_wrap text-center"><input type="checkbox" id="mass_select_all" data-to-table="salary-new"><label for="mass_select_all"></label></div>
                                            <br>(1)
                                        </th>
                                        <th rowspan="2" class="text-center" style="width: 100px;">
                                            <?= lang('Mã NV ') ?><br>(2)</th>
                                        <th rowspan="2" class="text-center" style="width: 180px;">
                                            <?= lang('Họ Tên') ?><br>(3)</th>
                                        <th rowspan="2" class="text-center" style="width: 150px;">
                                            <?= lang('Chức vụ') ?><br>(4)</th>
                                        <th rowspan="2" class="text-center" style="min-width: 100px;">
                                            <?= lang('Ngày vào làm') ?><br>(5)</th>
                                        <th rowspan="2" class="text-center" style="min-width: 100px;">
                                            <?= lang('Trạng thái') ?><br>(6)</th>
                                        <th rowspan="2" class="text-center" style="min-width: 100px;">
                                            <?= lang('Tổng Mức Thu Nhập Theo KPI (P1+P2+P3)') ?><br>(7)</th>
                                        <th colspan="3" class="text-center" style="min-width: 100px;">
                                            <?= lang('Mức P1 (BHXH Theo Qui Chế Vùng)') ?></th>
                                        <th colspan="1" class="text-center" style="min-width: 100px;">
                                            <?= lang('Mức P2 (Phụ Cấp Năng Lực Theo KPI)') ?></th>
                                        <th colspan="4" class="text-center" style="min-width: 100px;">
                                            <?= lang('Mức P3 (Thu Nhập Cống Hiến Theo KPI)') ?></th>
                                        <th rowspan="2" class="text-center" style="min-width: 80px;">
                                            <?= lang('Tổng Thu Nhập (Thỏa thuận)') ?><br>(16)</th>
                                        <th colspan="2" class="text-center" style="min-width: 80px;">
                                            <?= lang('Giờ Làm Thực Tế') ?></th>
                                        <th colspan="4" class="text-center" style="min-width: 80px;">
                                            <?= lang('Ngày nghỉ') ?></th>
                                        <th rowspan="2" class="text-center" style="min-width: 80px;">
                                            <?= lang('Tổng ngày công hưởng lương') ?><br>(23)</th>
                                        <th colspan="1" class="text-center" style="min-width: 80px;">
                                            <?= lang('P1') ?></th>
                                        <th colspan="2" class="text-center" style="min-width: 80px;">
                                            <?= lang('P2') ?></th>
                                        <th colspan="3" class="text-center" style="min-width: 80px;">
                                            <?= lang('P3') ?></th>
                                        <th rowspan="2" class="text-center" style="min-width: 80px;">
                                            <?= lang('Tổng Thu Nhập Thực Tế Theo KPI (P1+P2+P3)') ?><br>(30)</th>
                                        <th colspan="2" class="text-center" style="min-width: 80px;">
                                            <?= lang('Trừ đi trễ về sớm') ?></th>
                                        <?php $sttNew = 32;
                                            $sttNew = 32 + $colspanAllowance;
                                        ?>
                                        <th colspan="<?= $colspanAllowance ?>" class="text-center" style="min-width: 100px;">
                                            <?= lang('Phụ cấp khác') ?></th>
                                        <?php $sttNew++; ?>
                                        <th rowspan="2" class="text-center" style="min-width: 80px;">
                                            <?= lang('Tổng phụ cấp') ?><br>(<?= $sttNew ?>)</th>
                                        <th colspan="6" class="text-center" style="min-width: 80px;">
                                            <?= lang('Tăng ca') ?></th>
                                        <?php $sttNew += 6; ?>
                                        <th rowspan="2" class="text-center" style="min-width: 80px;">
                                            <?= lang('BHDN ('.BHDN.' %)') ?><br>(<?= $sttNew ?>)</th>
                                        <?php $sttNew ++; ?>
                                        <?php $sttNew += $colspanReduce; ?>
                                        <th colspan="<?= $colspanReduce ?>" class="text-center" style="min-width: 80px;">
                                            <?= lang('Khấu trừ') ?></th>
                                        <?php $sttNew++; ?>
                                        <th colspan="6" class="text-center" style="min-width: 80px;">
                                            <?= lang('Khấu trừ thuế TNCN') ?></th>
                                        <?php $sttNew += 6; ?>
                                        <?php $sttNew++; ?>
                                        <th colspan="2" class="text-center" style="min-width: 80px;">
                                            <?= lang('Khen thưởng kỷ luật') ?></th>
                                        <?php $sttNew++; ?>
                                        <th rowspan="2" class="text-center" style="min-width: 80px;"><?=lang('Bù lương')?><br>(<?= $sttNew ?>)</th>
                                        <?php $sttNew++; ?>
                                        <th rowspan="2" class="text-center" style="min-width: 80px;"><?=lang('Điều chỉnh khác')?><br>(<?= $sttNew ?>)</th>
                                        <?php $sttNew++; ?>
                                        <th rowspan="2" class="text-center" style="min-width: 80px;">
                                            <?= lang('Hoàn phép năm') ?><br>(<?= $sttNew ?>)</th>
                                        <?php $sttNew++; ?>
                                        <th colspan="4" class="text-center" style="min-width: 80px;">
                                            <?= lang('Thực lãnh') ?><br>(<?= $sttNew ?>)</th>
                                        <?php $sttNew += 2; ?>
                                        <?php $sttNew++; ?>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Hệ số lương vị trí') ?><br>(8)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Hệ số lương chức vụ') ?><br>(9)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Mức lương vị trí (LCB)') ?><br>(10)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Mức P2 (Theo năng lực)') ?><br>(11)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Thu Nhập Cống Hiến') ?><br>(12)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Thâm niên') ?><br>(13)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Mức chuyên cần') ?><br>(14)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Mức P3') ?><br>(15)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Số giờ công') ?><br>(17)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Số ngày công') ?><br>(18)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Phép năm') ?><br>(19)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Lễ tết') ?><br>(20)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('VR hưởng lương (hiếu hỉ)') ?><br>(21)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Nghỉ không hưởng lương (không lương/không phép)') ?><br>(22)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Lương P1') ?><br>(24)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Điểm KPI') ?><br>(25)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Thu nhập P2 thực tế') ?><br>(26)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Chuyên cần thực tế') ?><br>(27)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Mở P3') ?><br>(28)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Thu nhập P3 thực tế') ?><br>(29)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Số giờ') ?><br>(31)</th>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Số tiền') ?><br>(32)</th>
                                        <?php $stt = 33; if (!empty($dtAllowance)) { ?>
                                            <?php foreach ($dtAllowance as $key => $value) { ?>
                                                <th class="text-center" style="min-width: 50px;"><?= $value['name'] ?><br>(<?= ($stt) ?>)</th>
                                            <?php $stt++; } ?>
                                        <?php } ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('Ngày cơm hành chính') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('Ngày cơm tăng ca') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('Tổng Tiền cơm') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Giờ TC ngày thường(1.5)') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Giờ TC chủ nhật(2.0)') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Giờ TC lễ tết(3.0)') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Giờ TC đêm thường ('.get_option('coefficient_default_night').')') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Giờ TC đêm chủ nhật ('.get_option('coefficient_sunday_night').')') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 50px;"><?= lang('Lương tăng ca') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <?php if (!empty($dtReduce)) { ?>
                                            <?php foreach ($dtReduce as $key => $value) { ?>
                                                <th class="text-center" style="min-width: 50px;"><?= $value['name'] ?><br>(<?= $stt ?>)</th>
                                                <?php $stt++; } ?>
                                        <?php } ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('8% BHXH') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('1,5% BHYT') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('1% BHTN') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('0.5% Đoàn phí') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('Tổng khấu trừ BHXH + Đoàn phí + Khấu trừ') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('Thuế suất') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('Giảm trừ gia cảnh') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('Thu nhập miễn thuế') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('Thu nhập chịu thuế') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('Thu nhập tính thuế') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('Thuế TNCN') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('Khen thưởng KPIs') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('Kỷ luật KPIs') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <?php $stt++; ?>
                                        <?php $stt++; ?>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('Tổng thực lãnh') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('Đã tạm ứng') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('Thực lãnh P1+P2') ?><br>(<?= $stt ?>)</th>
                                        <?php $stt++; ?>
                                        <th class="text-center" style="min-width: 70px;"><?= lang('Thực lãnh P3') ?><br>(<?= $stt ?>)</th>
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
        '<?= site_url('admin/payroll/getPayroll') ?>', {
            'order': [
                [2, 'asc'],
            ],
            fixedColumns: {
                leftColumns: 0,
                rightColumns: 0
            },
            scrollX: true,
            "ajax": {
                "url": '<?= site_url('admin/payroll/getPayroll') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {

                    $('.table-salary-new tfoot tr td:nth-child(7)').html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_salary_kpi)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(10)').html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_salary_bhxh)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(11)').html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_salary_p2)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(12)').html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_salary_p3)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(13)').html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_tham_nien)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(14)').html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_diligence_salary)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(15)').html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_salary_p3_new)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(16)').html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_salary)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(17)').html(`<div class="text-center">${tnhFormatNumber(json.footer_day_number)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(18)').html(`<div class="text-center">${(json.footer_day_number_new)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(19)').html(`<div class="text-center">${(json.footer_day_number_holiday)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(20)').html(`<div class="text-center">${(json.footer_day_number_lt)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(21)').html(`<div class="text-center">${(json.footer_day_number_ch)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(22)').html(`<div class="text-center">${(json.footer_day_number_off)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(23)').html(`<div class="text-center">${(json.footer_total_day_number)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(24)').html(`<div class="text-center">${tnhFormatMoneyNew(json.footer_salary_income)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(26)').html(`<div class="text-center">${tnhFormatMoneyNew(json.footer_salary_p2_real)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(27)').html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_diligence)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(29)').html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_salary_p3_real)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(30)').html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_salary_kpi_real)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(31)').html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_hour_late)}</div>`);
                    $('.table-salary-new tfoot tr td:nth-child(32)').html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_money_hour_late)}</div>`);
                    keyNew = 32;
                    <?php if (!empty($dtAllowance)) { ?>
                        <?php foreach ($dtAllowance as $key => $value) { ?>
                            keyNew++;
                            idcheck = <?= $value['id'] ?>;
                            footer_total_allowance = `footer_total_allowance_${idcheck}`
                            footer_total_allowance_new = json.arrFooter;

                        <?php } ?>
                    <?php } ?>
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-center">${tnhFormatMoneyNew(json.footer_allowance_rice)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-center">${tnhFormatMoneyNew(json.footer_allowance_rice_tc)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_allowance_rice_money)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_total_allowance_other)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-center"></div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-center"></div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-center"></div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-center"></div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-center"></div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_allowance_business_fee)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_bhxh_company)}</div>`);
                    <?php if (!empty($dtReduce)) { ?>
                        <?php foreach ($dtReduce as $key => $value) { ?>
                            keyNew++;
                            idcheck = <?= $value['id'] ?>;
                        <?php } ?>
                    <?php } ?>
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_deduct_bhxh)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_deduct_bhyt)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_deduct_bhtn)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_deduct_union)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_total_reduce_other)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right"></div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_family_deduction)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_tax_exemption)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_taxable_income)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_tax_collection)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_total_vat)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_grand_total_kt)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_grand_total_kl)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_salary_compensation)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_other_adjustments)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_complete_permission)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_total_real)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_deduct_advance)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_salary_real_p1_p2)}</div>`);
                    keyNew++;
                    $(`.table-salary-new tfoot tr td:nth-child(${keyNew})`).html(`<div class="text-right">${tnhFormatMoneyNew(json.footer_salary_real_p3)}</div>`);
                    keyNew++;
                    return json.aaData;
                }
            },
            "createdRow": function(row, data, index) {
                check_p3 = data[27];
                check_3p = check_p3.split('__');
                $('td', row).eq(27).css('background-color', check_3p[1]);
            },
            "columnDefs": [{
                    "render": function(data, type, row) {
                        return `<div style="text-align:center">${data}</div>`;
                    },
                    "targets": 0,
                    "name": 'id',
                    'orderable': false
                },
                {
                    "render": function(data, type, row) {
                        return `<div style="text-align:left">${data}</div>`;
                    },
                    "targets": 1,
                    "width": '80px',
                },
                {
                    "render": function(data, type, row) {
                        data = data.split('__');
                        return `<div class="text-center"><a target="_blank" href="<?= base_url('admin/kpi/staff_kpi_evaluation?type=list&staff_id=') ?>${data[2]}&month=${data[3]}&year=${data[4]}">${data[0]}</a></div>`;
                    },
                    "targets": 27,
                },
            ],
        });
    $(document).ready(function() {
        init_selectpicker();
    });
    $(document).on('change', '#year, #month, #staff, #department,#branch_search', function(event) {
        event.preventDefault();
        oTable.draw();
    });
    $(document).ready(function() {
        $('#month').select2();
        $('#year').select2();
        $('#department').select2({
            allowClear: true
        });
    });

    function deletePayroll() {
        var ids = '';
        var rows = $('.table-salary-new').find('tbody tr');
        $.each(rows, function() {
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
                "<?php echo _l('Bạn có chắc muốn xóa dữ liệu!'); ?>");
            if (r == false) {
                oTable.draw('page');
                return false;
            } else {
                $.ajax({
                        url: site.base_url + 'admin/payroll/deletePayroll',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                            ids: ids,
                        },
                    })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            oTable.draw();
                        } else {
                            alert_float('danger', data.message);
                            oTable.draw('false');
                        }
                    })
                    .fail(function(data) {
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
        window.open(admin_url + 'payroll/export_excel' + get, '_blank');
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
        window.open(admin_url + 'payroll/print_pdf_payroll_salary_new' + get, '_blank');
    }
</script>