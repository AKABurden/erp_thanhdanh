<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if ($type_check == 'list'){ ?>
    <?php init_head(true); ?>
<?php } else { ?>
    <?php init_head(false); ?>
<?php } ?>
<style type="text/css">
    #wrapper {
        <?php if ($type_check == 'list'){ ?>
        <?php } else { ?>
            margin-left: 10px!important;
        <?php } ?>
    }
    #table-category-kpi tr td:nth-child(1) {
        width: 80px;
        white-space: unset;
        text-align: center;
    }
    #table-category-kpi tr td:nth-child(5) {
        width: 150px;
        white-space: unset;
        text-align: center;
    }
    .content-text {
        display: block;
        max-height: 35px; /* Hạn chế chiều cao ban đầu */
        overflow: hidden; /* Ẩn nội dung thừa */
        text-overflow: ellipsis; /* Thêm dấu "..." */
        transition: max-height 0.3s ease;
    }
    .title-header{
        font-size: 16px;
        font-weight: 500;
    }
    .table tbody tr:first-child {
         border-top: 1px solid #cedae6 !important;
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
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
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
        <div class="col-md-12">
            <div class="col-md-3" style="margin-bottom: 10px">
                <?= lang('Phòng ban', 'department_search') ?>
                <select name="department_search" id="department_search" class="" data-placeholder="<?= lang('Phòng ban') ?>"
                        style="width: 100%;">
                    <option value=""></option>
                    <?php if (!empty($dtDepartment)) : ?>
                        <?php foreach ($dtDepartment as $key => $value) : ?>
                            <option value="<?= $value['departmentid'] ?>"><?= $value['name'] ?>
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

                    $this->db->select('tblstaff.staffid as staffid, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as firstname');
                    $this->db->from('tblstaff');
                    $this->db->where('active',1);
                    $staffs = $this->db->get()->result_array();
                    ?>
                    <?php if (!empty($staffs)) : ?>
                        <?php foreach ($staffs as $key => $value) : ?>
                            <option <?= !empty($staff_id_selected) ? ($staff_id_selected == $value['staffid'] ? 'selected' : '') : '' ?> value="<?= $value['staffid'] ?>">
                                <?= $value['firstname']  ?>
                            </option>
                        <?php endforeach ?>
                    <?php endif ?>
                </select>
            </div>
            <div class="col-md-2" style="margin-bottom: 10px">
                <?= lang('month', 'filter_month_new') ?>
                <select name="filter_month_new" id="filter_month_new" class="" data-placeholder="<?= lang('month') ?>"
                        style="width: 100%;">
                    <?php if (!empty(getMonth())) : ?>
                        <?php foreach (getMonth() as $key => $value) : ?>
                            <option <?= !empty($month_selected) ? ($month_selected == $key ? 'selected' : '') : (date('m') == $key ? 'selected' : '') ?>
                                value="<?= $key ?>"><?= $value ?>
                            </option>
                        <?php endforeach ?>
                    <?php endif ?>
                </select>
            </div>
            <div class="col-md-2">
                <?= lang('year', 'year') ?>
                <select name="year" id="year" class="" data-placeholder="<?= lang('year') ?>"
                        style="width: 100%;" style="width: 100%;">
                    <?php if (!empty(getYear())) : ?>
                        <?php foreach (getYear() as $key => $value) : ?>
                            <option <?= !empty($year_selected) ? ($year_selected == $key ? 'selected' : '') : (date('Y') == $key ? 'selected' : '') ?>
                                    value="<?= $key ?>"><?= $value ?>
                            </option>
                        <?php endforeach ?>
                    <?php endif ?>
                </select>
            </div>
            <div class="col-md-2">
                <?= lang('Quý', 'precious') ?>
                <select name="precious" id="precious" class="" data-placeholder="<?= lang('Quý') ?>"
                        style="width: 100%;" style="width: 100%;">
                    <?php if (!empty(getPrecious())) : ?>
                        <?php foreach (getPrecious() as $key => $value) : ?>
                            <option value="<?= $key ?>"><?= $value ?>
                            </option>
                        <?php endforeach ?>
                    <?php endif ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="view-switch" style="margin-bottom: 5px">
                                <button class="view-btn active" data-view="list_room">
                                    Vi phạm
                                </button>
                                <button class="view-btn" data-view="category_room">
                                    Đánh Giá - Xếp Loại - Khen Thưởng
                                </button>
                            </div>
                            <div class="clearfix"></div>
                            <div class="view_violation">
                                <table id="table-list-criteria-department" class="table table-list-criteria-department table-hover dataTable dont-responsive-table">
                                    <thead>
                                    <tr>
                                        <th class="text-center" width="50px">STT</th>
                                        <th class="text-center">Nhân viên</th>
                                        <th class="text-center" style="width: 100px">Số phiếu <br>công việc</th>
                                        <th class="text-center" style="width: 100px">Số phiếu <br>BCKPH đã có</th>
                                        <th class="text-center" style="width: 100px">Số phiếu <br>BCKPH</th>
                                        <th class="text-center" style="width: 100px">Số phiếu <br>vi phạm đã có</th>
                                        <th class="text-center violate pointer" style="width: 100px">Số phiếu <br>vi phạm</th>
                                        <th class="text-center vuot pointer" style="width: 100px">Số phiếu <br>vượt</th>
                                        <th class="text-center" style="width: 100px">Số phiếu <br>BCKPH P1</th>
                                        <th class="text-center" style="width: 100px">Số phiếu <br>BCKPH P2</th>
                                        <th class="text-center" style="width: 100px">Số phiếu <br>BCKPH P3</th>
                                        <th class="text-center" style="width: 100px">Điểm số</th>
                                        <th class="text-center" style="width: 100px">Xếp loại</th>
                                        <th class="text-center" style="width: 150px">Hướng xử lý</th>
                                        <th class="text-center" style="width: 100px">% P2 OKR: <br>Mức độ Tuân thủ</th>
                                        <th class="text-center" style="width: 100px">Mở P3</th>
                                    </tr>
                                    </thead>
                                    <tbody id="html_view_kpi_evaluation">

                                    </tbody>
                                </table>
                            </div>
                            <div class="view_rating hide">
                                <table class="table table-hover dataTable dont-responsive-table">
                                    <tbody>
                                    <tr style="background-color: #FEF7E2">
                                        <td class="text-center">Điểm Số Hoàn Thành</td>
                                        <?php foreach (ratingKpiDepartment() as $key => $value){ ?>
                                            <td class="text-center"><?= $value['point_min'].'-'.$value['point_max'] ?></td>
                                        <?php } ?>
                                    </tr>
                                    <tr style="background-color: #FEF7E2">
                                        <td class="text-center">Xếp Loại</td>
                                        <?php foreach (ratingKpiDepartment() as $key => $value){ ?>
                                            <td class="text-center"><?= $value['name'] ?></td>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Thưởng +</td>
                                        <?php foreach (ratingKpiDepartment() as $key => $value){ ?>
                                            <?php
                                            $htmlBonus = '';
                                            if (!empty($value['bonus'])) {
                                                foreach ($value['bonus'] as $k => $v) {
                                                    $htmlBonus .= '<div>-'.$v['name'].'</div>';
                                                }
                                            }
                                            ?>
                                            <td class="text-left"><?= $htmlBonus?></td>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Kỷ Luật -</td>
                                        <?php foreach (ratingKpiDepartment() as $key => $value){ ?>
                                            <?php
                                            $htmlDiscipline = '';
                                            if (!empty($value['discipline'])) {
                                                foreach ($value['discipline'] as $k => $v) {
                                                    $htmlDiscipline .= '<div>-'.$v['name'].'</div>';
                                                }
                                            }
                                            ?>
                                            <td class="text-left"><?= $htmlDiscipline?></td>
                                        <?php } ?>
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
    $('th.violate').click(function () {
        var table = $(this).parents('.table-list-criteria-department').eq(0)
        var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))
        this.asc = !this.asc
        if (!this.asc) {
            rows = rows.reverse()
        }
        for (var i = 0; i < rows.length; i++) {
            table.append(rows[i])
        }
        getTotalAll();
    })

    function comparer(index) {
        return function (a, b) {
            var valA = getCellValue(a, index), valB = getCellValue(b, index)
            return $.isNumeric(valA) && $.isNumeric(valB) ? (valB) - (valA) : valA.toString().localeCompare(valB)
        }
    }

    function getCellValue(row, index) {
        return $(row).children('td').eq(index).find('input.violate_input').val();
    }
    function view_kpi_evaluation_new() {
        var filter_month = $("select#filter_month_new").val();
        var year = $("select#year").val();
        var staff = $("select#staff").val();
        var precious = $("select#precious").val();
        var department_search = $("select#department_search").val();
        if (filter_month && year) {
            $.ajax({
                type: 'POST',
                url: admin_url+'kpi/view_kpi_evaluation',
                data: {
                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                    filter_month: filter_month,
                    year: year,
                    staff: staff,
                    department_search: department_search,
                    precious: precious,
                },
                dataType: "JSON",
                success: function (response) {
                    $('tbody#html_view_kpi_evaluation').html(response.html);
                    $("th.violate").each(function () {
                        this.asc = false;
                    });
                    $("th.violate").click();
                }
            });
        }
    }

    function getTotalAll(){
        tb = '#table-list-criteria-department tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        for (ii = 0; ii < n; ii++)
        {
            stt++;
            element = $(tb)[ii];
            $(element).find('.stt_all').html(stt);
        }
    }

    $(document).ready(function() {
        $("select#filter_month_new").select2();
        $("select#department_search").select2({
            allowClear: true,
        });
        $("select#year").select2();
        $("select#precious").select2();
        $("select#staff").select2({
            allowClear: true,
        });
        view_kpi_evaluation_new();
        $(document).on('change', '#filter_month_new,#year,#staff,#department_search,#precious', function (event) {
            view_kpi_evaluation_new();
        });
    });

    function toggleContent(_this) {
        var text = $(_this).prev('.content-text');
        console.log(text)
        var btn = $(_this);

        if (text.css("max-height") === "none") {
            text.css("max-height", "35px");
            btn.text("Xem thêm");
        } else {
            text.css("max-height", "none");
            btn.text("Thu gọn");
        }
    }
    $(document).on('change', '#department_search', function (event) {
        event.preventDefault();
        department_search = $(this).val();
        $('select#staff').val(0).trigger('change');
        $.ajax({
            type: 'POST',
            url: admin_url+'kpi/get_staff_by_department',
            data: {
                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                department_search: department_search,
            },
            dataType: "JSON",
            success: function (response) {
                option = '<option value=""></option>';
                if (response.staffs) {
                    $.each(response.staffs, function (key, value) {
                        option += `<option value="${value.staffid}">${value.firstname}</option>`;
                    });
                }
                $('select#staff').html(option);
            }
        });
    });

    function exportExcel() {
        var filter_month = $("select#filter_month_new").val();
        var year = $("select#year").val();
        var staff = $("select#staff").val();
        var precious = $("select#precious").val();
        var department_search = $("select#department_search").val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/kpi/export_excel_kpi_evaluation',
            data: {
                csrf_token_name: hash,
                filter_month: filter_month,
                year: year,
                staff: staff,
                department_search: department_search,
                precious: precious,
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

    $(document).on('click', '.view-btn', function () {
        $('.view-btn').removeClass('active');

        $(this).addClass('active');

        // lấy view
        const view = $(this).data('view');
        if (view == 'list_room'){
            $(".view_violation").removeClass('hide');
            $(".view_rating").addClass('hide');
            view_kpi_evaluation_new();
        } else {
            $(".view_violation").addClass('hide');
            $(".view_rating").removeClass('hide');
        }
    });
</script>