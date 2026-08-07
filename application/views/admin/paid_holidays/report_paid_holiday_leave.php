<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .bg-group {
        background: #daeaf9;
    }

    .group {
        background-color: #f9f9f9;
    }

    .staff-profile-image-small {
        width: 20px !important;
        height: 20px !important;
    }

    #table-paid-holiday-leave th {
        background: #d9edf7 !important;
        color: #0e5dab !important;
        border: 1px solid #93b4d6 !important;
    }

    #table-suggest-payslip tr td:nth-child(5) {
        min-width: 120px;
        white-space: unset;
        text-align: left;
    }

    .tag-cs-red {
        font-size: 11px;
        font-style: italic;
        font-weight: 400;
        padding: 0.3em 0.7em 0.3em;
        background: 0 0;
        border: 1px solid red;
        color: red;
    }

    .tag-cs-color {
        font-size: 11px;
        font-style: italic;
        font-weight: 400;
        padding: 0.3em 0.7em 0.3em;
        background: 0 0;
        border: 1px solid #2886e7;
        color: #2886e7;
    }

    .staff-profile-image-small {
        width: 25px;
        height: 25px;
        margin-top: 5px;
    }

    .tag-cs-danger {
        font-size: 11px;
        font-style: italic;
        font-weight: 400;
        padding: 0.3em 0.7em 0.3em;
        background: 0 0;
        border: 1px solid #fb101b;
        color: #fb101b;
    }

    .tag-cs-primary {
        font-size: 11px;
        font-style: italic;
        font-weight: 400;
        padding: 0.3em 0.7em 0.3em;
        background: 0 0;
        border: 1px solid #17adf1;
        color: #17adf1;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row col-md-12">
                            <div class="row" style="margin-bottom:5px">
                                <div id="search-tnh" class="collapse in" aria-expanded="true">
                                    <div class="col-md-3">
                                        <?= lang('Nhân viên', 'staff_search') ?>
                                        <select class="selectpicker staff_search form-control" name="staff_search[]"
                                                id="staff_search"
                                                data-live-search="true"
                                                multiple
                                                title='<?php echo _l('Nhân viên'); ?>'
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <?php if (!empty($staff)) { ?>
                                                <?php foreach ($staff as $key => $value) { ?>
                                                    <optgroup label="<?= $value['name'] ?>">
                                                        <?php if (!empty($value['staffs'])) : ?>
                                                            <?php foreach ($value['staffs'] as $k => $v) : ?>
                                                                <option data-subtext="<?= $v['name_roles'] ?>" <?= (!empty($arrSelect) && in_array($v['staffid'],
                                                                        $arrSelect)) ? 'selected' : '' ?>
                                                                        value="<?= $v['staffid'] ?>"><?= $v['staff_name'] ?></option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </optgroup>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('Tháng', 'month_search') ?>
                                        <select class="selectpicker month_search form-control" name="month_search"
                                                id="month_search"
                                                <?php foreach (getMonth() as $key => $value){ ?>
                                                    <option <?= $value == date('m') ? 'selected' : '' ?> value="<?= $value ?>"><?= $value ?></option>
                                                <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('Năm', 'year_search') ?>
                                        <select class="selectpicker year_search form-control" name="year_search"
                                                id="year_search"
                                        <?php foreach (getYear() as $key => $value){ ?>
                                            <option <?= $value == date('Y') ? 'selected' : '' ?> value="<?= $value ?>"><?= $value ?></option>
                                        <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                        <table id="table-paid-holiday-leave-report"
                               class="table dt-tnh table-paid-holiday-leave-report-new" style="">
                            <thead>
                            <tr>
                                <th rowspan="2" class="text-center"><?= lang('STT') ?></th>
                                <th rowspan="2" class="text-center"><?= lang('Mã nhân viên') ?></th>
                                <th rowspan="2" class="text-center"><?= lang('Họ và tên') ?></th>
                                <th rowspan="2" class="text-center"><?= lang('Bộ phận') ?></th>
                                <th colspan="3" class="text-center"><?= lang('Nghỉ hưởng lương') ?></th>
                                <th colspan="4" class="text-center"><?= lang('Nghỉ không hương lương') ?></th>
                                <th rowspan="2" class="text-center"><?= lang('Tổng số ngày nghỉ') ?></th>
                            </tr>
                            <tr>
                                <th class="text-center"><?= lang('Phép năm') ?></th>
                                <th class="text-center"><?= lang('Hiếu hỉ') ?></th>
                                <th class="text-center"><?= lang('Lễ, tết') ?></th>
                                <th class="text-center"><?= lang('Ốm đau') ?></th>
                                <th class="text-center"><?= lang('Thai sản') ?></th>
                                <th class="text-center"><?= lang('Nghỉ không hương lương') ?></th>
                                <th class="text-center"><?= lang('Nghỉ không phép') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="99"></td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr class="bold">
                                <td></td>
                                <td></td>
                                <td class="bold uppercase">Tổng cộng</td>
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
<div id="view_add_paid_holiday_leave"></div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowGroup.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowsGroup.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script>
    var oTable = '';
    var oTableItemsNew = '';
    var fnserverparamsitems = {
        'year_search': '#year_search',
        'month_search': '#month_search',
        'staff_search[]': '#staff_search'
    };

    function loadTableNew() {
        oTable = tnhInitDataTable('#table-paid-holiday-leave-report',
            '<?= site_url('admin/paid_holidays/getPaidHolidayLeaveReport') ?>', {
                'order': [
                    [0, 'desc'],
                ],
                "ordering": false,
                "ajax": {
                    "url": '<?= site_url('admin/paid_holidays/getPaidHolidayLeaveReport') ?>',
                    "type": "POST",
                    "data": function (d) {
                        if (typeof (csrfData) !== 'undefined') {
                            d[csrfData['token_name']] = csrfData['hash'];
                        }
                        for (var key in fnserverparamsitems) {
                            d[key] = $(fnserverparamsitems[key]).val();
                        }
                        if (table.attr('data-last-order-identifier')) {
                            d['last_order_identifier'] = table.attr('data-last-order-identifier');
                        }
                    },
                    "dataSrc": function (json) {
                        $('#table-paid-holiday-leave-report tfoot tr td:nth-child(5)').html('<div class="text-center">'+(json.totalQuantityPhep)+'</div>');
                        $('#table-paid-holiday-leave-report tfoot tr td:nth-child(6)').html('<div class="text-center">'+(json.totalQuantityCH)+'</div>');
                        $('#table-paid-holiday-leave-report tfoot tr td:nth-child(7)').html('<div class="text-center">'+(json.totalQuantityLT)+'</div>');
                        $('#table-paid-holiday-leave-report tfoot tr td:nth-child(8)').html('<div class="text-center">'+(json.totalQuantityOD)+'</div>');
                        $('#table-paid-holiday-leave-report tfoot tr td:nth-child(9)').html('<div class="text-center">'+(json.totalQuantityTS)+'</div>');
                        $('#table-paid-holiday-leave-report tfoot tr td:nth-child(10)').html('<div class="text-center">'+(json.totalQuantityKP)+'</div>');
                        $('#table-paid-holiday-leave-report tfoot tr td:nth-child(11)').html('<div class="text-center">'+(json.totalQuantityKPNew)+'</div>');
                        $('#table-paid-holiday-leave-report tfoot tr td:nth-child(12)').html('<div class="text-center">'+(json.total_number_date_all)+'</div>');
                        return json.aaData;
                    }
                },
                "createdRow": function (row, data, index) {
                },
                "columnDefs": [],
            });
    }

    $(document).ready(function () {
        loadTableNew();
    });

    $(document).on('change', '#month_search,#staff_search,#year_search', function (
        event) {
        event.preventDefault();
        oTable.draw();
    });

    $('#table-paid-holiday-leave').on('draw.dt', function () {
        get_total();
    });


</script>