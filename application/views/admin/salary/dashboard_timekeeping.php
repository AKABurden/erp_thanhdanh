<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<style>
#tb-add-timekeeping tr th {
    text-align: center !important;
}

.tb-add-timekeeping-new > thead > tr > th {
    background: #d9edf7 !important;
    color: #0e5dab !important;
}

.table-new > thead > tr > th {
    background: #d9edf7 !important;
    color: #0e5dab !important;
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
                                    <option <?= date('m') == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?>
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
                                    <option <?= date('Y') == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?>
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
                                        <?= $value['firstname'].' ( '. $value['name_departments'] .' ) ' ?>
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
                                    <option value="<?= $value['departmentid'] ?>">
                                        <?= $value['name_departments'] .' ('.$value['code'].')'; ?>
                                    </option>
                                    <?php endforeach ?>
                                    <?php endif ?>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12" style="padding: 1px;margin-top:10px; ">
                            <table class="table-new table table-hover tnh-table dataTable">
                                <thead>
                                    <tr height="36px">
                                        <th colspan="8" class="text-center"><?= lang('Ký hiệu chấm công') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?= lang('Có phép') ?></td>
                                        <td class="text-center">CP</td>
                                        <td><?= lang('Phép 1/2 ngày') ?></td>
                                        <td class="text-center">P 1/2</td>
                                        <td><?= lang('Phép không lương') ?></td>
                                        <td class="text-center">PKL</td>
                                    </tr>
                                    <tr>
                                        <td><?= lang('Phép không lương 1/2 ngày') ?></td>
                                        <td class="text-center">PKL 1/2</td>
                                        <td><?= lang('Cưới hỏi') ?></td>
                                        <td class="text-center">CH</td>
                                        <td><?= lang('Không phép') ?></td>
                                        <td class="text-center">KP</td>
                                    </tr>
                                    <tr>
                                        <td><?= lang('Ốm đau') ?></td>
                                        <td class="text-center">OD</td>
                                        <td><?= lang('Thai sản') ?></td>
                                        <td class="text-center">TS</td>
                                        <td><?= lang('Tết dương lịch') ?></td>
                                        <td class="text-center">TDL</td>

                                    </tr>
                                    <tr>
                                        <td><?= lang('Tết âm lịch') ?></td>
                                        <td class="text-center">TAL</td>
                                        <td><?= lang('Ngày chiến thắng') ?></td>
                                        <td class="text-center">NCT</td>
                                        <td><?= lang('Ngày quốc tế lao động') ?></td>
                                        <td class="text-center">QTLĐ</td>
                                    </tr>
                                    <tr>
                                        <td><?= lang('Quốc khánh') ?></td>
                                        <td class="text-center">QK</td>
                                        <td><?= lang('Ngày giỗ tổ hùng vương') ?></td>
                                        <td class="text-center">GTHV</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="page" id="page" class="form-control" value="1">
                        <div class="col-md-12 view-table-dashboard-timekeeping" style="padding: 1px; margin-top: 10px">

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
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
var oTable = '';
var workDayDefault = 0;
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});


function loadDashBoardTimekeeping() {
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
            url: site.base_url + 'admin/salary/loadDashBoardTimekeeping',
            data: {
                month: month,
                year: year,
                staff: staff,
                page:page,
                department: department,
                csrf_token_name: hash
            },
            dataType: "html",
            success: function(response) {
                if (response) {
                    $('.view-table-dashboard-timekeeping').html(response);
                }
                // loadPersonnelTimekeeping();
            }
        });
    }
}

function changeTypeTimekeeping(ctimekeepingId, cpersonnel_id, cday, cidTimekeepingDetail, ctype, _this) {
    $.ajax({
        type: "POST",
        url: site.base_url + 'admin/salary/changeTypeTimekeeping',
        data: {
            timekeepingId: ctimekeepingId,
            personnel_id: cpersonnel_id,
            day: cday,
            idTimekeepingDetail: cidTimekeepingDetail,
            type: ctype,
            csrf_token_name: hash
        },
        dataType: "json",
        success: function(data) {
            if (data) {
                if (data.result) {
                    alert_float('success', data.message);
                } else {
                    alert_float('danger', data.message, 10000);
                }
                // $(_this).find('option[value="' + data.type + '"]').prop('selected', true);
                // cTd = $(_this).closest('td');
                // if (data.type == 'R' || data.type == 'O_K_BHXH' || data.type == 'RO_HT_50') {
                //     cTd.find('.edit-note').show();
                // } else {
                //     cTd.find('.edit-note').hide();
                // }
                // loadTypeTimekeeping(ctimekeepingId, cpersonnel_id, cdate, _this)
                loadDashBoardTimekeeping();
            }
        }
    });
}

function changeTimekeeping(timekeepingId, personnel_id, day, idTimekeepingDetail, _this, _event) {
    typeTimeKeeping = $(_this).val();
    if (typeTimeKeeping == "R" || typeTimeKeeping == "O_K_BHXH" || typeTimeKeeping == "Ro_HT_50") {
        loadModalTimekeepingDetailNote(timekeepingId, personnel_id, day, idTimekeepingDetail, typeTimeKeeping, _this);
    } else {
        changeTypeTimekeeping(timekeepingId, personnel_id, day, idTimekeepingDetail, typeTimeKeeping, _this);
    }
    // changeTypeTimekeeping(timekeepingId, personnel_id, day, idTimekeepingDetail, typeTimeKeeping, _this);

}

function loadModalTimekeepingDetailNote(timekeepingId, personnel_id, day, idTimekeepingDetail, typeTimeKeeping, _this) {
    thiss = _this;
    $.ajax({
            url: site.base_url + 'admin/salary/createdNoteTimekeeping',
            type: 'GET',
            dataType: 'html',
            data: {
                timekeepingId: timekeepingId,
                personnel_id: personnel_id,
                day: day,
                typeTimeKeeping: typeTimeKeeping,
                idTimekeepingDetail: idTimekeepingDetail,
                csrf_token_name: hash,
            },
        })
        .done(function(data) {
            $('.modal-select2').select2('close');
            $('#tnhModal2').html(data);
        })
        .fail(function() {
            console.log("error");
        });

    $('#tnhModal2').modal({
        backdrop: 'static',
        keyboard: false
    });
}

function loadModalOvertime(timekeepingId, personnel_id, day, idTimekeepingDetail, typeTimeKeeping, _this) {
    thiss = _this;
    $.ajax({
        url: site.base_url + 'admin/salary/loadModalOvertime',
        type: 'GET',
        dataType: 'html',
        data: {
            timekeepingId: timekeepingId,
            personnel_id: personnel_id,
            day: day,
            typeTimeKeeping: typeTimeKeeping,
            idTimekeepingDetail: idTimekeepingDetail,
            csrf_token_name: hash,
        },
    })
        .done(function(data) {
            $('.modal-select2').select2('close');
            $('#tnhModal2').html(data);
        })
        .fail(function() {
            console.log("error");
        });

    $('#tnhModal2').modal({
        backdrop: 'static',
        keyboard: false
    });
}

function pdf() {
    month = $('#month').val();
    year = $('#year').val();
    department = $('#department').val();
    staff = $('#staff').val();

    if (month && year) {

        window.open(site.base_url + 'admin/salary/print_dashboard_timekeeping?month=' + month + '&year=' + year +
            '&department=' + department + '&staff=' + staff, "_blank");
    }
}

function excel() {
    month = $('#month').val();
    year = $('#year').val();
    department = $('#department').val();
    staff = $('#staff').val();

    if (month && year) {

        window.open(site.base_url + 'admin/salary/exportExcelTimekeeping?month=' + month + '&year=' + year +
            '&department=' + department + '&staff=' + staff, "_blank");
    }
}

$(document).ready(function() {
    // $('.action-menu').click();
    $('#month').select2();
    $('#year').select2();
    department
    $('#staff').select2({
        allowClear: true
    });
    $('#department').select2({
        allowClear: true
    });
    loadDashBoardTimekeeping();


    $(document).on('change', '#month, #year, #staff, #department', function(event) {
        loadDashBoardTimekeeping();
    });
});
</script>