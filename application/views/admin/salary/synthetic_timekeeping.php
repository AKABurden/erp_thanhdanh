<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<style>
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

.table>thead>tr:nth-child(2)>th {
    border-top: unset !important;
}

.table>thead>tr:nth-child(4)>th {
    border-bottom: unset !important;
}

.table>thead>tr:nth-child(3)>th:nth-child(1) {
    border-bottom: unset !important;
}

.table>thead>tr:nth-child(3)>th:nth-child(2) {
    border-bottom: unset !important;
}


.table>thead:first-child>tr:first-child>th:nth-child(1) {
    border-bottom: unset !important;
}

.table>thead:first-child>tr:first-child>th:nth-child(2) {
    border-bottom: unset !important;
}

.table>thead:first-child>tr:first-child>th:nth-child(3) {
    border-bottom: unset !important;
}

.table>thead:first-child>tr:first-child>th:nth-child(5) {
    border-bottom: unset !important;
}

.table>thead:first-child>tr:first-child>th:nth-child(6) {
    border-bottom: unset !important;
}

.table>thead:first-child>tr:first-child>th:nth-child(8) {
    border-bottom: unset !important;
}

.table>thead:first-child>tr:first-child>th:nth-child(9) {
    border-bottom: unset !important;
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
            <a class="btn btn-info pull-right mright5 H_action_button" onclick="pdf(); return false;">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('IN PDF'); ?>
            </a>
            <a class="btn btn-info pull-right mright5 H_action_button" onclick="excel(); return false;">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('XUẤT EXCEL'); ?>
            </a>
        </div>
    </div>

    <div class="content ae-content view-timekeeping">
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
                                        $this->db->select('tblstaff.staffid as staffid,tblstaff.firstname as firstname,tbldepartments.name as name_departments ');
                                        $this->db->from('tblstaff');
                                        $this->db->join('tblstaff_departments','tblstaff_departments.staffid = tblstaff.staffid ','left');
                                        $this->db->join('tbldepartments','tbldepartments.departmentid = tblstaff_departments.departmentid ','left');
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
                                    data-placeholder="<?= lang('Tổ') ?>" style="width: 100%;">
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
                        </div>
                        <br>
                        <div class="col-md-12 view-table-synthetic-timekeeping" style="padding: 1px; margin-top: 10px">

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


function loadSyntheticTimekeeping() {
    $('#tb-add-timekeeping tbody').html('');
    $('#tb-add-timekeeping tfoot').html('');
    month = $('#month').val();
    year = $('#year').val();
    staff = $('#staff').val();
    department = $('#department').val();

    if (month && year) {
        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/salary/loadSyntheticTimekeeping',
            data: {
                month: month,
                year: year,
                staff: staff,
                department: department,
                csrf_token_name: hash
            },
            dataType: "html",
            success: function(response) {
                if (response) {
                    $('.view-table-synthetic-timekeeping').html(response);
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
                $(_this).find('option[value="' + data.type + '"]').prop('selected', true);
                // loadTypeTimekeeping(ctimekeepingId, cpersonnel_id, cdate, _this)
            }
        }
    });
}

function changeTimekeeping(timekeepingId, personnel_id, day, idTimekeepingDetail, _this, _event) {
    typeTimeKeeping = $(_this).val();
    changeTypeTimekeeping(timekeepingId, personnel_id, day, idTimekeepingDetail, typeTimeKeeping, _this);

}

function pdf() {
    month = $('#month').val();
    year = $('#year').val();
    department = $('#department').val();
    staff = $('#staff').val();

    if (month && year) {

        window.open(site.base_url + 'admin/salary/print_pdf_synthetic_timekeeping?month=' + month + '&year=' + year +
            '&department=' + department + '&staff=' + staff, "_blank");
    }
}

function excel() {
    month = $('#month').val();
    year = $('#year').val();
    department = $('#department').val();
    staff = $('#staff').val();

    if (month && year) {

        window.open(site.base_url + 'admin/salary/exportExcelSyntheticTimekeeping?month=' + month + '&year=' + year +
            '&department=' + department + '&staff=' + staff, "_blank");
    }
}

$(document).ready(function() {
    // $('.action-menu').click();
    $('#month').select2();
    $('#year').select2();
    $('#staff').select2({
        allowClear: true
    });
    $('#department').select2({
        allowClear: true
    });
    loadSyntheticTimekeeping();


    $(document).on('change', '#month, #year, #staff, #department', function(event) {
        loadSyntheticTimekeeping();
    });
});
</script>