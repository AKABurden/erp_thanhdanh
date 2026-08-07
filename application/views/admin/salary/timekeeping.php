<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<style>
    .tb-add-timekeeping-new > thead > tr > th {
        background: #d9edf7 !important;
        color: #0e5dab !important;
        /*border: 1px solid #93b4d6 !important;*/
    }

    #tb-add-timekeeping tr th {
        text-align: center !important;
    }

    .view-table-timekeeping tr th,
    .view-table-timekeeping tr td {
        border: 1px solid #cedae6 !important;
    }

    .select-custom::-ms-expand {
        display: none;
    }

    .select-custom {
        -webkit-appearance: none;
        appearance: none;
        padding: 0px;
        width: 25px !important;
        height: 15px !important;
    }

    .select-custom {
        /* width: 100% !important; */
        /* border: none !important; */
        background: none;
    }

    .select-custom {
        text-align: center;
        text-align-last: center;
        font-size: 13px !important;
        cursor: pointer;
        /* webkit*/
    }

    .view-timekeeping .caret {
        display: none;
    }

    .view-timekeeping .filter-option {
        padding: 0px !important;
        text-align: center !important;
    }

    .view-timekeeping .bootstrap-select .btn-default {
        border: none;
    }

    /* @-moz-document url-prefix() {
            .ui-select {
                border: 1px solid #CCC;
                border-radius: 4px;
                box-sizing: border-box;
                position: relative;
                overflow: hidden;
            }

            .ui-select select {
                width: 110%;
                background-position: right 30px center !important;
                border: none !important;
            }
        } */
</style>
<?php echo form_open('admin/salary/add_timekeeping', array('id' => 'timekeeping')); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <a class="btn btn-info pull-right mright5 H_action_button hide" onclick="pdf(); return false;">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('IN PDF'); ?>
            </a>
            <a class="btn btn-info pull-right mright5 H_action_button" onclick="excel(); return false;">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('XUẤT EXCEL'); ?>
            </a>
        </div>
    </div>
    <!-- ae-content -->
    <div class="content view-timekeeping">
        <div class="row">
            <div class="col-md-12">
                <div class="panel" style="margin-bottom: 3px;">
                    <div class="panel-body" style="padding: 0px;">
                        <div class="row" style="padding: 1px;">
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
                                <?= lang('Nhân viên', 'staff') ?>
                                <select name="staff" id="staff" class="" data-placeholder="<?= lang('Nhân viên') ?>"
                                        style="width: 100%;" style="width: 100%;">
                                    <option value=""></option>
                                    <?php

                                    $tbDepartment = "(
                                        SELECT
                                            tblstaff_departments.staffid as staffid,
                                            GROUP_CONCAT(tbldepartments.name) as name_department
                                        FROM tbldepartments
                                        JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
                                        GROUP BY tblstaff_departments.staffid
                                    ) tb_department";

                                    $this->db->select('tblstaff.staffid as staffid, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as firstname,tb_department.name_department as name_departments ');
                                    $this->db->from('tblstaff');
                                    $this->db->where('active',1);
                                    $this->db->join($tbDepartment,
                                        'tb_department.staffid = tblstaff.staffid ', 'left');
                                    $staffs = $this->db->get()->result_array();
                                    ?>
                                    <?php if (!empty($staffs)) : ?>
                                        <?php foreach ($staffs as $key => $value) : ?>
                                            <option value="<?= $value['staffid'] ?>">
                                                <?= $value['firstname'] . ' ( ' . $value['name_departments'] . ' ) ' ?>
                                            </option>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <?= lang('Phòng ban', 'department') ?>
                                <select name="department" id="department" class="department"
                                        data-placeholder="<?= lang('Phòng ban') ?>" style="width: 100%;">
                                    <option value=""></option>
                                    <?php
                                    $this->db->select('tbldepartments.name as name_departments,tbldepartments.code as code,tbldepartments.departmentid as departmentid ');
                                    $this->db->from('tbldepartments');
                                    $departments = $this->db->get()->result_array();
                                    ?>
                                    <?php if (!empty($departments)) : ?>
                                        <?php foreach ($departments as $key => $value) : ?>
                                            <option <?= $key == 0 ? '' : '' ?>
                                                    value="<?= $value['departmentid'] ?>">
                                                <?= $value['name_departments'] .' ('.$value['code'].')'; ?>
                                            </option>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </select>
                            </div>
                        </div>
                        <div style="margin-top:5px;margin-bottom:10px">
                            <span style="color:#008ece"> <i class="fa fa-circle" aria-hidden="true"></i> : <span>Giờ
                                    Vào</span></span>
                            | <span style="color:red"><i class="fa fa-circle" aria-hidden="true"></i> : <span>Giờ
                                    Ra</span></span>
                        </div>
                        <input type="hidden" name="page" id="page" class="form-control" value="1">
                        <div class="col-md-12 view-table-timekeeping" style="padding: 1px; margin-top: 10px">

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowGroup.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowsGroup.js') ?>"></script>

<script>
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var oTable = '';
    var workDayDefault = 0;

    function calTimekeeping() {

        tb = '#tb-add-timekeeping tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var grandTotalBasicSalary = 0;
        var grandTotalWorkday = 0;
        var grandTotalSalary = 0;
        var grandTotalGas = 0;
        var grandTotalRice = 0;
        var grandTotalCar = 0;
        var grandTotalAllowance = 0;
        var grandTotalAllowanceOther = 0;
        var grandTotalCs = 0;
        var grandTotalBhxh = 0;
        var grandTotalBhyt = 0;
        var grandTotalBhtn = 0;
        var grandTotalMinusSales = 0;
        var grandTotalReal = 0;

        for (ii = 0; ii < n; ii++) {
            _row = $($(tb)[ii]);

            basicSalary = intVal(_row.find('.td-basic-salary').html());
            priceADay = basicSalary / workDayDefault;
            workDay = intVal(_row.find('.work_day').val());
            dayOff = workDayDefault - workDay;
            salary = basicSalary - (priceADay * dayOff);
            _row.find('.td-salary').html(tnhFormatMoney(salary));

            gas = intVal(_row.find('.td-gas').html());
            rice = intVal(_row.find('.td-rice').html());
            car = intVal(_row.find('.td-car').html());
            allowance = intVal(_row.find('.td-allowance').html());
            allowance_other = intVal(_row.find('.td-allowance_other').html());
            cs = intVal(_row.find('.cs').val());
            bhxh = intVal(_row.find('.td-bhxh').html());
            bhyt = intVal(_row.find('.td-bhyt').html());
            bhtn = intVal(_row.find('.td-bhtn').html());
            minus_sales = intVal(_row.find('.minus_sales').val());

            totalReal = salary + gas + rice + car + allowance + allowance_other + cs - bhxh - bhyt - bhtn - minus_sales;
            _row.find('.td-total-real').html(tnhFormatMoney(totalReal));

            grandTotalBasicSalary += basicSalary;
            grandTotalWorkday += workDay;
            grandTotalSalary += salary;
            grandTotalGas += gas;
            grandTotalRice += rice;
            grandTotalCar += car;
            grandTotalAllowance += allowance;
            grandTotalAllowanceOther += allowance_other;
            grandTotalCs += cs;
            grandTotalBhxh += bhxh;
            grandTotalBhyt += bhyt;
            grandTotalBhtn += bhtn;
            grandTotalMinusSales += minus_sales;
            grandTotalReal += totalReal;
        }

        $('.grandTotalBasicSalary').html(tnhFormatMoney(grandTotalBasicSalary));
        $('.grandTotalWorkday').html(grandTotalWorkday);
        $('.grandTotalSalary').html(tnhFormatMoney(grandTotalSalary));
        $('.grandTotalGas').html(tnhFormatMoney(grandTotalGas));
        $('.grandTotalRice').html(tnhFormatMoney(grandTotalRice));
        $('.grandTotalCar').html(tnhFormatMoney(grandTotalCar));
        $('.grandTotalAllowance').html(tnhFormatMoney(grandTotalAllowance));
        $('.grandTotalAllowanceOther').html(tnhFormatMoney(grandTotalAllowanceOther));
        $('.grandTotalCs').html(tnhFormatMoney(grandTotalCs));
        $('.grandTotalBhxh').html(tnhFormatMoney(grandTotalBhxh));
        $('.grandTotalBhyt').html(tnhFormatMoney(grandTotalBhyt));
        $('.grandTotalBhtn').html(tnhFormatMoney(grandTotalBhtn));
        $('.grandTotalMinusSales').html(tnhFormatMoney(grandTotalMinusSales));
        $('.grandTotalReal').html(tnhFormatMoney(grandTotalReal));

    }

    function loadPersonnelTimekeeping() {
        $('#tb-add-timekeeping tbody').html('');
        $('#tb-add-timekeeping tfoot').html('');
        month = $('#month').val();
        year = $('#year').val();
        staff = $('#staff').val();
        department = $('#department').val();
        page = $('#page').val();

        if (month && year) {
            $.ajax({
                type: "GET",
                url: site.base_url + 'admin/salary/loadPersonnelTimekeeping',
                data: {
                    month: month,
                    year: year,
                    staff: staff,
                    department: department,
                    page: page,
                    csrf_token_name: hash
                },
                dataType: "html",
                success: function (response) {
                    if (response) {
                        $('.view-table-timekeeping').html(response);
                    }
                    // loadPersonnelTimekeeping();

                }
            });
        }
    }


    function changeTypeTimekeeping(ctimekeepingId, cpersonnel_id, cdate, ctype, _this) {
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/salary/changeTypeTimekeeping',
            data: {
                timekeepingId: ctimekeepingId,
                personnel_id: cpersonnel_id,
                date: cdate,
                type: ctype,
                csrf_token_name: hash
            },
            dataType: "json",
            success: function (data) {
                if (data) {
                    if (data.result) {
                        alert_float('success', data.message);
                    } else {
                        alert_float('danger', data.message, 10000);
                    }
                    // $(_this).find('option[value="' + data.type + '"]').prop('selected', 'selected');
                    loadTypeTimekeeping(ctimekeepingId, cpersonnel_id, cdate, _this)
                }
            }
        });
    }

    function loadTypeTimekeeping(ctimekeepingId, cpersonnel_id, cdate, _this) {
        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/salary/loadTypeTimekeeping',
            data: {
                timekeepingId: ctimekeepingId,
                personnel_id: cpersonnel_id,
                date: cdate,
                csrf_token_name: hash
            },
            dataType: "json",
            success: function (data) {
                if (data) {
                    // $(_this).find('option[value="' + data.type + '"]').attr('selected', 'selected');
                    $(_this).find('option[value="' + data.type + '"]').prop('selected', 'selected');
                    cTd = $(_this).closest('td');
                    if (data.type != '' && data.type != 'X' && data.type != 'V') {
                        cTd.find('.edit-note').show();
                    } else {
                        cTd.find('.edit-note').hide();
                    }
                }
            }
        });
    }

    function loadModalTimekeepingDetailNote(timekeepingId, personnel_id, date, typeTimeKeeping) {
        $.ajax({
            url: site.base_url + 'admin/salary/createdNoteTimekeeping',
            type: 'GET',
            dataType: 'html',
            data: {
                timekeepingId: timekeepingId,
                personnel_id: personnel_id,
                date: date,
                typeTimeKeeping: typeTimeKeeping,
                csrf_token_name: hash,
            },
        })
            .done(function (data) {
                $('.modal-select2').select2('close');
                $('#tnhModal2').html(data);
            })
            .fail(function () {
                console.log("error");
            });

        $('#tnhModal2').modal({
            backdrop: 'static',
            keyboard: false
        });
    }

    function changeTimekeeping(timekeepingId, personnel_id, date, _this, _event) {
        typeTimeKeeping = $(_this).val();
        if (typeTimeKeeping == "X" || typeTimeKeeping == "V") {
            changeTypeTimekeeping(timekeepingId, personnel_id, date, typeTimeKeeping, _this);
        } else {
            bootbox.confirm({
                message: 'Bạn có muốn tạo phiếu lý do ?',
                buttons: {
                    confirm: {
                        label: lang_core['yes'],
                        className: 'btn-success'
                    },
                    cancel: {
                        label: lang_core['no'],
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if (result) {
                        loadModalTimekeepingDetailNote(timekeepingId, personnel_id, date, typeTimeKeeping);
                        loadTypeTimekeeping(timekeepingId, personnel_id, date, _this)
                    } else {
                        changeTypeTimekeeping(timekeepingId, personnel_id, date, typeTimeKeeping, _this);
                    }
                }
            });
        }
    }

    function pdf() {
        month = $('#month').val();
        year = $('#year').val();
        department = $('#department').val();
        staff = $('#staff').val();

        if (month && year) {

            window.open(site.base_url + 'admin/salary/print_timekeeping?month=' + month + '&year=' + year +
                '&department=' + department + '&staff=' + staff, "_blank");
        }
    }


    function excel() {
        month = $('#month').val();
        year = $('#year').val();
        department = $('#department').val();
        staff = $('#staff').val();

        if (month && year) {

            window.open(site.base_url + 'admin/salary/exportExcelSaleListing?month=' + month + '&year=' + year +
                '&department=' + department + '&staff=' + staff, "_blank");
        }
    }

    $(document).ready(function () {
        // $('.action-menu').click();
        $('#month').select2();
        $('#year').select2();
        $('#staff').select2({
            allowClear: true
        });
        $('#department').select2({
            allowClear: true
        });
        loadPersonnelTimekeeping();

        $(document).on('change', '.work_day, .cs, .minus_sales', function (event) {
            // _row = $(this).closest('tr');
            calTimekeeping();
        });

        $(document).on('change', '#month, #year, #staff, #department', function (event) {
            loadPersonnelTimekeeping();
        });
    });
    $('html, body').animate({
        scrollTop: $('#btn-example-load-more').offset().top
    }, 1000);

    function deleteHourOut(staff_id = '', type_hour = '', type_check = '', id_timekeeping_detail_hour_out = '', id_timekeeping_detail = '', type_check_delete = '') {
        var r = confirm("<?php echo _l('Bạn có chắc muốn xoá giờ công');?>");
        if (r == false) {
            return false;
        } else {
            $.ajax({
                type: "POST",
                url: site.base_url + 'admin/salary/deleteHourOut',
                data: {
                    staff_id: staff_id,
                    type_hour: type_hour,
                    type_check: type_check,
                    id_timekeeping_detail_hour_out: id_timekeeping_detail_hour_out,
                    id_timekeeping_detail: id_timekeeping_detail,
                    type_check_delete: type_check_delete,
                    csrf_token_name: hash
                },
                dataType: "json",
                success: function (data) {
                    if (data) {
                        if (data.result) {
                            alert_float('success', data.message);
                        } else {
                            alert_float('danger', data.message, 10000);
                        }
                        loadPersonnelTimekeeping();
                    }
                }
            });
        }
    }

    function deleteHourOutNew(staff_id = '', type_hour = '', type_check = '', id_timekeeping_detail_hour_in = '', id_timekeeping_detail = '') {
        var r = confirm("<?php echo _l('Bạn có chắc muốn xoá giờ công');?>");
        if (r == false) {
            return false;
        } else {
            $.ajax({
                type: "POST",
                url: site.base_url + 'admin/salary/deleteHourOutNew',
                data: {
                    staff_id: staff_id,
                    type_hour: type_hour,
                    type_check: type_check,
                    id_timekeeping_detail_hour_in: id_timekeeping_detail_hour_in,
                    id_timekeeping_detail: id_timekeeping_detail,
                    csrf_token_name: hash
                },
                dataType: "json",
                success: function (data) {
                    if (data) {
                        if (data.result) {
                            alert_float('success', data.message);
                        } else {
                            alert_float('danger', data.message, 10000);
                        }
                        loadPersonnelTimekeeping();
                    }
                }
            });
        }
    }
</script>