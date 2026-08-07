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

    #table-paid-holiday-follow th {
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
                                        <?= lang('Năm', 'year_search') ?>
                                        <select class="selectpicker year_search form-control" name="year_search" id="year_search"
                                            data-live-search="true"
                                            title='<?php echo _l('Năm'); ?>'
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <?php foreach (getYear() as $key => $value) { ?>
                                                <option <?= $key == date('Y') ? 'selected' : '' ?> value="<?= $key ?>"> <?= $value ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <?= lang('Nhân viên', 'staff_search') ?>
                                    <select class="selectpicker staff_search form-control" name="staff_search[]" id="staff_search"
                                        data-live-search="true"
                                        multiple
                                        title='<?php echo _l('Nhân viên'); ?>'
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <?php if (!empty($staff)) { ?>
                                            <?php foreach ($staff as $key => $value) { ?>
                                                <optgroup label="<?= $value['name'] ?>">
                                                    <?php if (!empty($value['staffs'])) : ?>
                                                        <?php foreach ($value['staffs'] as $k => $v) : ?>
                                                            <option data-subtext="<?= $v['name_roles'] ?>" <?= (!empty($arrSelect) && in_array(
                                                                                                                $v['staffid'],
                                                                                                                $arrSelect
                                                                                                            )) ? 'selected' : '' ?>
                                                                value="<?= $v['staffid'] ?>"><?= $v['staff_name'] ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </optgroup>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <a href="javascript:void(0)" onclick="exportExcelOrders()" class="btn btn-success" style="margin-top: 25px;"><?php echo lang('Xuất excel'); ?></a>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                        <div style="margin-top: 10px" class="tbl-view-paid-holiday-follow"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div id="view_add_setup_paid_holiday"></div>
<div id="view_setup_paid_holiday"></div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowGroup.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowsGroup.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script>
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';

    function exportExcelOrders() {
        var dataPOST = {};
        dataPOST[token] = hash;
        dataPOST['year_search'] = $("#year_search").val();
        dataPOST['staff_search[]'] = $("#staff_search").val(),

            $.ajax({
                type: "POST",
                url: site.base_url + 'admin/reports_leave_day/exportExcelOrders',
                data: dataPOST,
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

    function loadTable() {
        $('#table-paid-holiday-follow tbody').html('');
        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/reports_leave_day/loadPaidHolidayFollows',
            data: {
                csrf_token_name: hash,
                year_search: $("#year_search").val(),
                'staff_search[]': $("#staff_search").val(),
            },
            dataType: "html",
            success: function(response) {
                if (response) {
                    $('.tbl-view-paid-holiday-follow').html(response);
                }

            }
        });
    }

    $(document).on('change', '#year_search, #staff_search', function(
        event) {
        event.preventDefault();
        loadTable();
    });

    $(document).ready(function() {
        loadTable();
    });
</script>