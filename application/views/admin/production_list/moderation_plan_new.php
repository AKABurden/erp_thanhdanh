<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<style class="">
    table tr td {
        vertical-align: middle !important;
        word-wrap: break-word;
    }

    table tr th {
        /* border: 1px solid !important; */
    }

    #table-moderation-plan_wrapper tr th:nth-child(1),
    #table-moderation-plan_wrapper tr td:nth-child(1) {
        width: 50px !important;
        min-width: 50px !important;
        max-width: 50px !important;
    }

    #table-moderation-plan_wrapper tr th,
    #table-moderation-plan_wrapper tr td {
        width: 150px !important;
        min-width: 150px !important;
        max-width: 150px !important;
    }

    .dataTables_length option:last-child {
        display: none;
    }

    .buttons-collection {
        display: none;
    }

    /* Readonly styles for input */
    input.readonly,
    input[readonly] {
        background-color: #e9ecef;
        cursor: not-allowed;
        opacity: 0.6;
    }

    /* Readonly styles for select2 */
    .select2-container--default.select2-readonly .select2-selection--single,
    .select2-container--default.select2-readonly .select2-selection--multiple {
        background-color: #e9ecef;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .select2-container--default.select2-readonly .select2-selection__arrow,
    .select2-container--default.select2-readonly .select2-selection__clear {
        display: none;
    }

    /* Readonly styles for selectpicker */
    .bootstrap-select.readonly button,
    .bootstrap-select[readonly] button {
        background-color: #e9ecef !important;
        cursor: not-allowed !important;
        opacity: 0.6;
        pointer-events: none;
    }

    /* Alternative: using disabled attribute styling */
    select.readonly + .select2-container .select2-selection,
    select[readonly] + .select2-container .select2-selection {
        background-color: #e9ecef;
        cursor: not-allowed;
        pointer-events: none;
    }

    td.readonly {
        background-color: #e9ecef !important;
        cursor: not-allowed !important;
        opacity: 0.6 !important;
        pointer-events: none !important;
    }
</style>
<?php echo form_open('', ['id' => 'form-moderation-plan']); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="pull-right mright5 H_border hide">
                <a href="<?= base_url('admin/production_list/handling') ?>" class="btn btn-info H_action_button">
                    <?php echo _l('add'); ?>
                </a>
            </div>
            <div class="pull-right mright5 H_border hide">
                <a href="<?= base_url('admin/production_list/moderation_plan_bk?group='.$_GET['group']) ?>" class="btn btn-info mright5 pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('Chuyển sang điều độ cũ'); ?>
                </a>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-2 hide">
                    <?= lang('date', 'date_search') ?>
                    <input type="text" name="date_search" autocomplete="off" placeholder="<?= lang('date') ?>" id="date_search" class="date_search datepicker form-control" style="width: 100%;" value="">
                </div>
                <div class="col-md-2">
                    <?= lang('date_start', 'date_start') ?>
                    <input type="text" name="date_start" autocomplete="off" placeholder="<?= lang('date_start') ?>" id="date_start" class="date_start datepicker form-control" style="width: 100%;" value="<?= date('d/m/Y') ?>">
                </div>
                <div class="col-md-2">
                    <?= lang('date_end', 'date_end') ?>
                    <input type="text" name="date_end" autocomplete="off" placeholder="<?= lang('date_end') ?>" id="date_end" class="date_end datepicker form-control" style="width: 100%;" value="<?= date('d/m/Y') ?>">
                </div>
                <div class="col-md-2">
                    <?= lang('tnh_reference_productions_orders', 'productions_orders_search') ?>
                    <!-- <input type="text" name="productions_orders_search" data-placeholder="<?//= lang('tnh_reference_productions_orders') ?>" id="productions_orders_search" class="productions_orders_search" style="width: 100%;" value=""> -->
                    <select name="productions_orders_search[]" id="productions_orders_search" data-live-search="true" data-actions-box="true" data-none-selected-text="<?= lang('tnh_reference_productions_orders') ?>" class="form-control" multiple>
                    </select>
                </div>
                <div class="col-md-2">
                    <?= lang('Ngày BĐ dự kiến', 'date_start_expected') ?>
                    <input type="text" name="date_start_expected" autocomplete="off" placeholder="<?= lang('Ngày bắt đầu dự kiến') ?>" id="date_start_expected" class="date_start_expected datepicker form-control" style="width: 100%;" value="">
                </div>
                <div class="col-md-2">
                    <?= lang('Ngày KT dự kiến', 'date_end_expected') ?>
                    <input type="text" name="date_end_expected" autocomplete="off" placeholder="<?= lang('Ngày kết thúc dự kiến') ?>" id="date_end_expected" class="date_end_expected datepicker form-control" style="width: 100%;" value="">
                </div>
                <div class="row">
                    <div class="col-md-12 mtop5">
                        <div class="col-md-2">
                            <?= lang('Ngày BĐ hoàn thành', 'date_start_finished') ?>
                            <input type="text" name="date_start_finished" autocomplete="off" placeholder="<?= lang('Ngày BĐ hoàn thành') ?>" id="date_start_finished" class="date_start_finished datepicker form-control" style="width: 100%;" value="">
                        </div>
                        <div class="col-md-2">
                            <?= lang('Ngày KT hoàn thành', 'date_end_finished') ?>
                            <input type="text" name="date_end_finished" autocomplete="off" placeholder="<?= lang('Ngày KT hoàn thành') ?>" id="date_end_finished" class="date_end_finished datepicker form-control" style="width: 100%;" value="">
                        </div>
                        <div class="col-md-4">
                            <div class="mtop25" style="margin-top: 28px;">
                                <?php if($this->perViewProductionList): ?>
                                    <a href="javascript:void(0)" onclick="loadDataPlan()" class="btn btn-primary"><?= lang('filter') ?></a>
                                <?php endif; ?>
                                <?php if($this->perEditProductionList || $this->perUpdateProductionList): ?>
                                    <a href="javascript:void(0)" onclick="saveModerationPlan(this)" class="btn btn-success"><?= lang('save') ?></a>
                                <?php endif; ?>
                                <?php if($this->perViewProductionList): ?>
                                    <a href="javascript:void(0)" onclick="exportExcelModerationPlan()" class="btn btn-warning exportExcelModerationPlan"><?= lang('c_export_excel') ?></a>
                                <?php endif; ?>
                                <?php if($this->perEditProductionList || $this->perUpdateProductionList): ?>
                                    <a href="<?= base_url('admin/production_list/import_excel_po') ?>" class="btn btn-default tnh-modal"><?= lang('Import excel') ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-2 hide">
                    <div class="form-group" app-field-wrapper="category_stages">
                        <label for="category_stages" class="control-label">Nhóm</label>
                        <select name="category_stages" id="category_stages" data-placeholder="<?= lang('Nhóm') ?>" class="category_stages" style="width: 100%;">
                            <?php foreach ($category_stages as $key => $value) { ?>
                                <option <?= ($value['id'] == $group ? 'selected' : '') ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 hide">
                    <div class="form-group" app-field-wrapper="machine_id_new">
                        <label for="machine_id_new" class="control-label">Máy móc</label>
                        <select name="machine_id_new" id="machine_id_new" data-placeholder="<?= lang('Máy móc') ?>" class="machine_id_new" style="width: 100%;">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="mtop25">

                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-12">
                    <div role="tabpanel">
                        <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 0;">
                            <li role="presentation">
                                <a href="#home" aria-controls="home" role="tab" onclick="filterProduction(this, '<?= 'ALL' ?>')" value="ALL" data-toggle="tab"><?= lang('all') ?></a>
                            </li>
                            <?php foreach(optionStatusProductionList() as $key => $value): ?>
                                <li role="presentation" class="<?= $value['id'] == "CHT" ? 'active' : '' ?>">
                                    <a href="#tab" aria-controls="tab" role="tab" onclick="filterProduction(this, '<?= $value['id'] ?>')" value="<?= $value['id'] ?>" data-toggle="tab"><?= $value['name'] ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <input type="hidden" name="status_filter" id="status_filter" class="form-control status_filter" value="CHT">
                    </div>
                </div>
            </div>
            <div class="col-md-12 hide">
                <div class="col-md-2">
                    <?= lang('tnh_start_date_delivery_expected', 'start_date_delivery') ?>
                    <input type="text" name="start_date_delivery" autocomplete="off" placeholder="<?= lang('tnh_start_date_delivery_expected') ?>" id="start_date_delivery" class="start_date_delivery datepicker form-control" style="width: 100%;" value="">
                </div>
                <div class="col-md-2">
                    <?= lang('tnh_end_date_delivery_expected', 'end_date_delivery') ?>
                    <input type="text" name="end_date_delivery" autocomplete="off" placeholder="<?= lang('tnh_end_date_delivery_expected') ?>" id="end_date_delivery" class="end_date_delivery datepicker form-control" style="width: 100%;" value="">
                </div>
                <div class="col-md-2">
                    <?= lang('Ngày bắt đầu kế hoạch', 'start_date_bd_kh') ?>
                    <input type="text" name="start_date_bd_kh" autocomplete="off" placeholder="<?= lang('Ngày bắt đầu kế hoạch') ?>" id="start_date_bd_kh" class="start_date_bd_kh datepicker form-control" style="width: 100%;" value="">
                </div>
                <div class="col-md-2">
                    <?= lang('Ngày kết thúc bắt đầu kế hoạch', 'end_date_bd_kh') ?>
                    <input type="text" name="end_date_bd_kh" autocomplete="off" placeholder="<?= lang('Ngày kết thúc bắt đầu kế hoạch') ?>" id="end_date_bd_kh" class="end_date_bd_kh datepicker form-control" style="width: 100%;" value="">
                </div>
            </div>
            <div class="col-md-12 mtop5 hide">
                <div class="col-md-2">
                    <?= lang('Ngày bắt đầu thực tế', 'start_date_reality') ?>
                    <input type="text" name="start_date_reality" autocomplete="off" placeholder="<?= lang('Ngày bắt đầu thực tế') ?>" id="start_date_reality" class="start_date_reality datepicker form-control" style="width: 100%;" value="">
                </div>
                <div class="col-md-2">
                    <?= lang('Ngày kết thúc bắt đầu thực tế', 'end_date_reality') ?>
                    <input type="text" name="end_date_reality" autocomplete="off" placeholder="<?= lang('Ngày kết thúc bắt đầu thực tế') ?>" id="end_date_reality" class="end_date_reality datepicker form-control" style="width: 100%;" value="">
                </div>
                <div class="col-md-2">
                    <?= lang('Ngày bắt đầu kết thúc thực tế', 'start_date_reality_kt') ?>
                    <input type="text" name="start_date_reality_kt" autocomplete="off" placeholder="<?= lang('Ngày bắt đầu thực tế') ?>" id="start_date_reality_kt" class="start_date_reality_kt datepicker form-control" style="width: 100%;" value="">
                </div>
                <div class="col-md-2">
                    <?= lang('Ngày kết thúc thực tế', 'end_date_reality_kt') ?>
                    <input type="text" name="end_date_reality_kt" autocomplete="off" placeholder="<?= lang('Ngày kết thúc bắt đầu thực tế') ?>" id="end_date_reality_kt" class="end_date_reality_kt datepicker form-control" style="width: 100%;" value="">
                </div>
                
                <input type="hidden" name="group" id="group" class="form-control" value="<?= $_GET['group'] ? $_GET['group'] : 0 ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="div-data-plan"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>

<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>

<script type="text/javascript">
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        // date_search: "#date_search",
        // status_table: "#status_table",
        date_start: "#date_start",
        date_end: "#date_end",
        group: "#group",
        productions_orders_search: "#productions_orders_search",
        status_filter: "#status_filter",
        date_start_expected: "#date_start_expected",
        date_end_expected: "#date_end_expected",
        date_start_finished: "#date_start_finished",
        date_end_finished: "#date_end_finished",
        // category_stages: "#category_stages",
        // machine_id_new: "#machine_id_new",
        // status_table_stages: '#status_table_stages',
        // start_date_delivery: '#start_date_delivery',
        // end_date_delivery: '#end_date_delivery',
        // start_date_bd_kh: '#start_date_bd_kh',
        // end_date_bd_kh: '#end_date_bd_kh',
        // productions_orders_search: '#productions_orders_search',
        // start_date_reality: '#start_date_reality',
        // end_date_reality: '#end_date_reality',
        // start_date_reality_kt: '#start_date_reality_kt',
        // end_date_reality_kt: '#end_date_reality_kt',
    };
    var oTable = '';

    function loadDataPlan() {
        var dataPOST = {};
        var group = $('#group').val();
        dataPOST[token] = hash;

        for (var key in fnserverparams) {
            dataPOST[key] = $(fnserverparams[key]).val();
        }

        if (group) {
            $.ajax({
                type: "POST",
                url: site.base_url+'admin/production_list/loadDataPlan',
                data: dataPOST,
                dataType: "html",
                success: function (response) {
                    $('.div-data-plan').html(response);
                }
            });
        }
    }

    function totalProductionList(_c_type_productionlist_id = 0, change_date = 0) {
        tb = '#tb-items tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var so_luong_may = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.so_luong_may').val());
        var nhom_tho = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.nhom_tho').val());
        var nang_suat_may = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.nang_suat_may').val());
        var _thoi_gian_canh_bai = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('._thoi_gian_canh_bai').val());
        var thoi_gian_lam_viec_chuan = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.thoi_gian_lam_viec_chuan').val());
        var thoi_gian_lam_viec_ot = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.thoi_gian_lam_viec_ot').val());
        
        var so_luong_tho = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.so_luong_tho').val());

        var nang_suat_may_in_300 = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.nang_suat_may_in_300').val());
        var nang_suat_may_in_600 = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.nang_suat_may_in_600').val());

        var nang_suat_dau_in_trang_den = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.nang_suat_dau_in_trang_den').val());
        var nang_suat_dau_in_mau = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.nang_suat_dau_in_mau').val());
        var thoi_gian_canh_bai_in_trang_den = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.thoi_gian_canh_bai_in_trang_den').val());
        var thoi_gian_canh_bai_in_mau = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.thoi_gian_canh_bai_in_mau').val());

        var nang_suat_keo_tay = intVal($('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.nang_suat_keo_tay').val());

        if (_c_type_productionlist_id == 1) {
            capacity_1 = nhom_tho * nang_suat_may * thoi_gian_lam_viec_chuan;
            capacity_2 = nhom_tho * nang_suat_may * thoi_gian_lam_viec_ot;
            $('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.td-capacity-2').html(tnhFormatNumber(capacity_1));
            $('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.td-capacity-3').html(tnhFormatNumber(capacity_2));
        } else if (_c_type_productionlist_id == 2) {
            capacity_1 = so_luong_tho * nang_suat_may * thoi_gian_lam_viec_chuan;
            capacity_2 = so_luong_tho * nang_suat_may * thoi_gian_lam_viec_ot;

            $('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.td-capacity-2').html(tnhFormatNumber(capacity_1));
            $('.div-type_productionlist_id-'+_c_type_productionlist_id).find('.td-capacity-3').html(tnhFormatNumber(capacity_2));
        } else if (_c_type_productionlist_id == 3) {
            $('.thoi_gian_canh_bai').attr('readonly', 'readonly');
            $('.thoi_gian_canh_bai').val(_thoi_gian_canh_bai);
        } else if (_c_type_productionlist_id == 3) {
            $('.thoi_gian_canh_bai').attr('readonly', 'readonly');
        }

        nang_suat_may_boi_mot_mat = intVal($('.nang_suat_may_boi_mot_mat').val());
        nang_suat_may_boi_hai_mat = intVal($('.nang_suat_may_boi_hai_mat').val());

        nang_suat_may_be_giay_thuong = intVal($('.nang_suat_may_be_giay_thuong').val());
        nang_suat_may_demi_be_giay_boi_pet = intVal($('.nang_suat_may_demi_be_giay_boi_pet').val());

        //
        var thay_size = intVal($('.thay_size').val());
        var rua_may = intVal($('.rua_may').val());
        var thay_size_gio = formatNumberFixed(thay_size/60, 3);
        var rua_may_gio = formatNumberFixed(rua_may/60, 3);
        $('.td-thay-size-gio').html(thay_size_gio);
        $('.td-rua-may-gio').html(rua_may_gio);

        var tong_thoi_gian_xu_ly = 0;
        var tong_tua_in = 0;
        var tong_tua_in_con_lai = 0;
        var list_machines = {};

        for (ii = 0; ii < n; ii++)
        {
            element = $(tb)[ii];
            so_mat_in = intVal($(element).find('.so_mat_in').val());
            to_in = intVal($(element).find('.td-to-in').html());

            var tong_tua = 0;
            var loai_canh_bai = $(element).find('select[data-class="loai_canh_bai"]').val();
            if (_c_type_productionlist_id == 1) {
                tong_tua = so_mat_in * to_in;
                $(element).find('.td-tong-tua').html(tnhFormatNumber(tong_tua));

                thoi_gian_in = 0;
                if (nang_suat_may > 0) {
                    thoi_gian_in = tong_tua/nang_suat_may;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));

                var thoi_gian_canh_bai = 0;
                var phut_canh_bai = $('.canh_bai_'+loai_canh_bai).val();
                if (typeof phut_canh_bai !== 'undefined' && phut_canh_bai) {
                    thoi_gian_canh_bai = formatNumberFixed(phut_canh_bai/60, 2) * 1;
                }
                $(element).find('.thoi_gian_canh_bai').val(thoi_gian_canh_bai);

                // thoi_gian_canh_bai = intVal($(element).find('.thoi_gian_canh_bai').val());

                var so_lan_thay_size = intVal($(element).find('.so_lan_thay_size').val());
                var so_lan_rua_may = intVal($(element).find('.so_lan_rua_may').val());

                var thoi_gian_thay_size = 0;
                var thoi_gian_rua_may = 0;
                if (so_lan_thay_size) {
                    thoi_gian_thay_size = so_lan_thay_size * thay_size_gio;
                }

                if (so_lan_rua_may) {
                    thoi_gian_rua_may = so_lan_rua_may * rua_may_gio;
                }
                $(element).find('.thoi_gian_thay_size').val(thoi_gian_thay_size);
                $(element).find('.thoi_gian_rua_may').val(thoi_gian_rua_may);

                thoi_gian_khac = intVal($(element).find('.thoi_gian_khac').val());
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai + thoi_gian_khac + thoi_gian_thay_size + thoi_gian_rua_may;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_hien_tai = formatDate(nowDate(), "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_hien_tai, 11);
                } else if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);
                
                to_in = intVal($(element).find('.td-to-in').html());
                so_con_tren_to_in = intVal($(element).find('.td-so-con-tren-to-in').html());
                so_con_tren_kb_offset = intVal($(element).find('.td-so-con-tren-kb-offset').html());
                tua_sau_in = (so_con_tren_kb_offset > 0 ? (so_con_tren_to_in/so_con_tren_kb_offset*to_in) : to_in);

                $(element).find('.td-tua-sau-in').html(tnhFormatNumber(tua_sau_in));
            } else if (_c_type_productionlist_id == 2) {
                //flexo
                so_luong_san_xuat = intVal($(element).find('.td-so-luong-san-xuat').html());
                tong_tua = so_mat_in * to_in;
                $(element).find('.td-tong-tua').html(tnhFormatNumber(tong_tua));

                // thoi_gian_canh_bai = intVal($(element).find('.thoi_gian_canh_bai').val());
                var thoi_gian_canh_bai = 0;
                var phut_canh_bai = $('.canh_bai_'+loai_canh_bai).val();
                if (typeof phut_canh_bai !== 'undefined' && phut_canh_bai) {
                    thoi_gian_canh_bai = formatNumberFixed(phut_canh_bai/60, 2) * 1;
                }
                $(element).find('.thoi_gian_canh_bai').val(thoi_gian_canh_bai);

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_hien_tai = formatDate(nowDate(), "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_hien_tai, 11);
                } else if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

                so_con_tren_kb_flexo = intVal($(element).find('.td-so-con-tren-kb-flexo').html());
                so_tua_in = 0;
                if (so_con_tren_kb_flexo > 0) {
                    so_tua_in = so_luong_san_xuat/so_con_tren_kb_flexo;
                }
                tong_tua = so_tua_in;
                $(element).find('.td-tong-tua').html(tnhFormatNumber(so_tua_in));
                // $(element).find('.td-so-tua-in-flexo').html(tnhFormatNumber(so_tua_in));

                thoi_gian_in = 0;
                loai_in_flexo = $(element).find('select[data-class="loai_in_flexo"]').val();
                nang_suat_may = intVal($('.nang_suat_may_'+loai_in_flexo).val());
                if (nang_suat_may > 0) {
                    thoi_gian_in = so_tua_in/nang_suat_may;
                }

                var so_lan_thay_size = intVal($(element).find('.so_lan_thay_size').val());
                var so_lan_rua_may = intVal($(element).find('.so_lan_rua_may').val());

                var thoi_gian_thay_size = 0;
                var thoi_gian_rua_may = 0;
                if (so_lan_thay_size) {
                    thoi_gian_thay_size = so_lan_thay_size * thay_size_gio;
                }

                if (so_lan_rua_may) {
                    thoi_gian_rua_may = so_lan_rua_may * rua_may_gio;
                }
                $(element).find('.thoi_gian_thay_size').val(thoi_gian_thay_size);
                $(element).find('.thoi_gian_rua_may').val(thoi_gian_rua_may);

                thoi_gian_khac = intVal($(element).find('.thoi_gian_khac').val());

                // thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai;
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai + thoi_gian_thay_size + thoi_gian_rua_may + thoi_gian_khac;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));
            } else if (_c_type_productionlist_id == 3) {
                //barcode
                so_luong_san_xuat = intVal($(element).find('.td-so-luong-san-xuat').html());
                so_con_tren_to_in = intVal($(element).find('.td-so-con-tren-to-in').html());
                dau_in = intVal($(element).find('select[data-class="dau_in"]').val());
                nang_suat = (dau_in == 300) ? nang_suat_may_in_300 : nang_suat_may_in_600;
                $(element).find('.td-nang-suat').html(tnhFormatNumber(nang_suat));

                so_tua_in = so_luong_san_xuat;
                $(element).find('.td-so-tua-in').html(tnhFormatNumber(so_tua_in));

                tong_tua = so_tua_in;
                $(element).find('.td-tong-tua').html(tnhFormatNumber(tong_tua));

                thoi_gian_in = 0;
                if (nang_suat > 0) {
                    thoi_gian_in = so_tua_in/nang_suat;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));

                // thoi_gian_canh_bai = _thoi_gian_canh_bai;
                var thoi_gian_canh_bai = 0;
                var phut_canh_bai = $('.canh_bai_'+loai_canh_bai).val();
                if (typeof phut_canh_bai !== 'undefined' && phut_canh_bai) {
                    thoi_gian_canh_bai = formatNumberFixed(phut_canh_bai/60, 2) * 1;
                }
                $(element).find('.thoi_gian_canh_bai').val(thoi_gian_canh_bai);

                var so_lan_thay_size = intVal($(element).find('.so_lan_thay_size').val());
                var so_lan_rua_may = intVal($(element).find('.so_lan_rua_may').val());

                var thoi_gian_thay_size = 0;
                var thoi_gian_rua_may = 0;
                if (so_lan_thay_size) {
                    thoi_gian_thay_size = so_lan_thay_size * thay_size_gio;
                }

                if (so_lan_rua_may) {
                    thoi_gian_rua_may = so_lan_rua_may * rua_may_gio;
                }

                $(element).find('.thoi_gian_thay_size').val(thoi_gian_thay_size);
                $(element).find('.thoi_gian_rua_may').val(thoi_gian_rua_may);

                thoi_gian_khac = intVal($(element).find('.thoi_gian_khac').val());

                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai + thoi_gian_thay_size + thoi_gian_khac;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_hien_tai = formatDate(nowDate(), "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_hien_tai, 11);
                } else if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

            } else if (_c_type_productionlist_id == 40) {
                so_to_in = intVal($(element).find('.td-to-in').html());
                so_mat_in = intVal($(element).find('.so_mat_in').val());
                loai = $(element).find('.loai').val();

                nang_suat = (loai == "T/D") ? nang_suat_dau_in_trang_den : nang_suat_dau_in_mau;
                $(element).find('.td-nang-suat').html(tnhFormatNumber(nang_suat));
                so_tua_in = so_to_in * so_mat_in;
                $(element).find('.td-so-tua-in').html(tnhFormatNumber(so_tua_in));

                thoi_gian_in = 0;
                if (nang_suat > 0) {
                    thoi_gian_in = so_tua_in/nang_suat;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));
                
                thoi_gian_canh_bai = (loai == "T/D") ? thoi_gian_canh_bai_in_trang_den : thoi_gian_canh_bai_in_mau;
                $(element).find('.thoi_gian_canh_bai').val(tnhFormatNumber(thoi_gian_canh_bai));
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

            } else if (_c_type_productionlist_id == 50) {
                so_to_in = intVal($(element).find('.td-to-in').html());
                so_mat_in = intVal($(element).find('.so_mat_in').val());
                so_mau_in = intVal($(element).find('.so_mau_in').val());
                so_tua_in = so_to_in * so_mat_in * so_mau_in;
                $(element).find('.td-so-tua-in').html(tnhFormatNumber(so_tua_in));

                nang_suat = nang_suat_keo_tay;
                thoi_gian_in = 0;
                if (nang_suat > 0) {
                    thoi_gian_in = so_tua_in/nang_suat;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));
                thoi_gian_canh_bai = _thoi_gian_canh_bai;
                $(element).find('.thoi_gian_canh_bai').val(tnhFormatNumber(thoi_gian_canh_bai));
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

            } else if (_c_type_productionlist_id == 6) {
                //Cán màng
                tong_tua = so_mat_in * to_in;
                $(element).find('.td-tong-tua').html(tnhFormatNumber(tong_tua));

                thoi_gian_in = 0;
                if (nang_suat_may > 0) {
                    thoi_gian_in = tong_tua/nang_suat_may;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));

                var thoi_gian_canh_bai = 0;
                var phut_canh_bai = $('.canh_bai_'+loai_canh_bai).val();
                if (typeof phut_canh_bai !== 'undefined' && phut_canh_bai) {
                    thoi_gian_canh_bai = formatNumberFixed(phut_canh_bai/60, 2) * 1;
                }
                $(element).find('.thoi_gian_canh_bai').val(thoi_gian_canh_bai);

                var so_lan_thay_size = intVal($(element).find('.so_lan_thay_size').val());
                var so_lan_rua_may = intVal($(element).find('.so_lan_rua_may').val());

                var thoi_gian_thay_size = 0;
                var thoi_gian_rua_may = 0;
                if (so_lan_thay_size) {
                    thoi_gian_thay_size = so_lan_thay_size * thay_size_gio;
                }

                if (so_lan_rua_may) {
                    thoi_gian_rua_may = so_lan_rua_may * rua_may_gio;
                }
                $(element).find('.thoi_gian_thay_size').val(thoi_gian_thay_size);
                $(element).find('.thoi_gian_rua_may').val(thoi_gian_rua_may);

                thoi_gian_khac = intVal($(element).find('.thoi_gian_khac').val());
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai + thoi_gian_khac + thoi_gian_thay_size + thoi_gian_rua_may;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_hien_tai = formatDate(nowDate(), "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_hien_tai, 11);
                } else if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

            } else if (_c_type_productionlist_id == 10) {
                //Định hình
                tong_tua = so_mat_in * to_in;
                $(element).find('.td-tong-tua').html(tnhFormatNumber(tong_tua));

                thoi_gian_in = 0;
                if (nang_suat_may > 0) {
                    thoi_gian_in = tong_tua/nang_suat_may;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));

                var thoi_gian_canh_bai = 0;
                var phut_canh_bai = $('.canh_bai_'+loai_canh_bai).val();
                if (typeof phut_canh_bai !== 'undefined' && phut_canh_bai) {
                    thoi_gian_canh_bai = formatNumberFixed(phut_canh_bai/60, 2) * 1;
                }
                $(element).find('.thoi_gian_canh_bai').val(thoi_gian_canh_bai);

                var so_lan_thay_size = intVal($(element).find('.so_lan_thay_size').val());
                var so_lan_rua_may = intVal($(element).find('.so_lan_rua_may').val());

                var thoi_gian_thay_size = 0;
                var thoi_gian_rua_may = 0;
                if (so_lan_thay_size) {
                    thoi_gian_thay_size = so_lan_thay_size * thay_size_gio;
                }

                if (so_lan_rua_may) {
                    thoi_gian_rua_may = so_lan_rua_may * rua_may_gio;
                }
                $(element).find('.thoi_gian_thay_size').val(thoi_gian_thay_size);
                $(element).find('.thoi_gian_rua_may').val(thoi_gian_rua_may);

                thoi_gian_khac = intVal($(element).find('.thoi_gian_khac').val());
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai + thoi_gian_khac + thoi_gian_thay_size + thoi_gian_rua_may;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_hien_tai = formatDate(nowDate(), "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_hien_tai, 11);
                } else if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);
                
            } else if (_c_type_productionlist_id == 11) {
                //cắt demi
                var so_lan_canh_dao = $(element).find('.so_lan_canh_dao').val();
                tong_tua = so_mat_in * to_in;
                $(element).find('.td-tong-tua').html(tnhFormatNumber(tong_tua));

                thoi_gian_in = 0;
                if (nang_suat_may > 0) {
                    thoi_gian_in = tong_tua/nang_suat_may;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));

                var thoi_gian_canh_bai = 0;
                var phut_canh_bai = $('.canh_bai_'+loai_canh_bai).val();
                if (typeof phut_canh_bai !== 'undefined' && phut_canh_bai) {
                    thoi_gian_canh_bai = formatNumberFixed(phut_canh_bai/60, 2) * 1;
                }
                $(element).find('.thoi_gian_canh_bai').val(thoi_gian_canh_bai);

                var so_lan_thay_size = intVal($(element).find('.so_lan_thay_size').val());
                var so_lan_rua_may = intVal($(element).find('.so_lan_rua_may').val());

                var thoi_gian_thay_size = 0;
                var thoi_gian_rua_may = 0;
                if (so_lan_thay_size) {
                    thoi_gian_thay_size = so_lan_thay_size * thay_size_gio;
                }

                if (so_lan_rua_may) {
                    thoi_gian_rua_may = so_lan_rua_may * rua_may_gio;
                }
                $(element).find('.thoi_gian_thay_size').val(thoi_gian_thay_size);
                $(element).find('.thoi_gian_rua_may').val(thoi_gian_rua_may);

                thoi_gian_khac = intVal($(element).find('.thoi_gian_khac').val());
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai + thoi_gian_khac + thoi_gian_thay_size + thoi_gian_rua_may;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_hien_tai = formatDate(nowDate(), "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_hien_tai, 11);
                } else if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

            } else if (_c_type_productionlist_id == 12) {
                //cán băng keo
                tong_tua = so_mat_in * to_in;
                $(element).find('.td-tong-tua').html(tnhFormatNumber(tong_tua));

                thoi_gian_in = 0;
                if (nang_suat_may > 0) {
                    thoi_gian_in = tong_tua/nang_suat_may;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));

                var thoi_gian_canh_bai = 0;
                var phut_canh_bai = $('.canh_bai_'+loai_canh_bai).val();
                if (typeof phut_canh_bai !== 'undefined' && phut_canh_bai) {
                    thoi_gian_canh_bai = formatNumberFixed(phut_canh_bai/60, 2) * 1;
                }
                $(element).find('.thoi_gian_canh_bai').val(thoi_gian_canh_bai);

                var so_lan_thay_size = intVal($(element).find('.so_lan_thay_size').val());
                var so_lan_rua_may = intVal($(element).find('.so_lan_rua_may').val());

                var thoi_gian_thay_size = 0;
                var thoi_gian_rua_may = 0;
                if (so_lan_thay_size) {
                    thoi_gian_thay_size = so_lan_thay_size * thay_size_gio;
                }

                if (so_lan_rua_may) {
                    thoi_gian_rua_may = so_lan_rua_may * rua_may_gio;
                }
                $(element).find('.thoi_gian_thay_size').val(thoi_gian_thay_size);
                $(element).find('.thoi_gian_rua_may').val(thoi_gian_rua_may);

                thoi_gian_khac = intVal($(element).find('.thoi_gian_khac').val());
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai + thoi_gian_khac + thoi_gian_thay_size + thoi_gian_rua_may;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_hien_tai = formatDate(nowDate(), "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_hien_tai, 11);
                } else if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

            } else if (_c_type_productionlist_id == 7) {
                //phun bóng
                so_to_in = intVal($(element).find('.td-to-in').html());
                so_mat_phun_bong = intVal($(element).find('.so_mat_phun_bong').val());
                so_lan_phun_bong = intVal($(element).find('.so_lan_phun_bong').val());
                so_tua_in = so_to_in * so_mat_phun_bong * so_lan_phun_bong;
                $(element).find('.td-tong-tua').html(tnhFormatNumber(so_tua_in));

                loai_may = intVal($(element).find('select[data-class="loai_may"]').val());
                nang_suat = intVal($('.nang_suat_may_'+loai_may).val());
                $(element).find('.td-nang-suat').html(tnhFormatNumber(nang_suat));

                thoi_gian_in = 0;
                if (nang_suat > 0) {
                    thoi_gian_in = so_tua_in/nang_suat;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));

                var thoi_gian_canh_bai = 0;
                var phut_canh_bai = $('.canh_bai_'+loai_canh_bai).val();
                if (typeof phut_canh_bai !== 'undefined' && phut_canh_bai) {
                    thoi_gian_canh_bai = formatNumberFixed(phut_canh_bai/60, 2) * 1;
                }
                $(element).find('.thoi_gian_canh_bai').val(thoi_gian_canh_bai);

                var so_lan_thay_size = intVal($(element).find('.so_lan_thay_size').val());
                var so_lan_rua_may = intVal($(element).find('.so_lan_rua_may').val());

                var thoi_gian_thay_size = 0;
                var thoi_gian_rua_may = 0;
                if (so_lan_thay_size) {
                    thoi_gian_thay_size = so_lan_thay_size * thay_size_gio;
                }

                if (so_lan_rua_may) {
                    thoi_gian_rua_may = so_lan_rua_may * rua_may_gio;
                }

                $(element).find('.thoi_gian_thay_size').val(thoi_gian_thay_size);
                $(element).find('.thoi_gian_rua_may').val(thoi_gian_rua_may);

                thoi_gian_khac = intVal($(element).find('.thoi_gian_khac').val());

                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai + thoi_gian_thay_size + thoi_gian_rua_may + thoi_gian_khac;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                tong_tua = so_tua_in;
                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_hien_tai = formatDate(nowDate(), "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_hien_tai, 11);
                } else if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

            } else if (_c_type_productionlist_id == 8) {
                // Bồi
                so_to_in = intVal($(element).find('.td-to-in').html());
                so_con_tren_to_in = intVal($(element).find('.td-so-con-tren-to-in').html());
                so_lan_van_hanh = intVal($(element).find('.so_lan_van_hanh').val());
                tong_tua = so_to_in * so_lan_van_hanh;

                $(element).find('.td-tong-tua').html(tnhFormatNumber(tong_tua));
                loai_boi = $(element).find('select[data-class="loai_boi"]').val();
                nang_suat = 0;
                if (loai_boi == 2 || loai_boi == '2') {
                    nang_suat = nang_suat_may_boi_hai_mat;
                } else {
                    nang_suat = nang_suat_may_boi_mot_mat;
                }

                thoi_gian_in = 0;
                if (nang_suat > 0) {
                    thoi_gian_in = tong_tua/nang_suat;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));

                var thoi_gian_canh_bai = 0;
                var phut_canh_bai = $('.canh_bai_'+loai_canh_bai).val();
                if (typeof phut_canh_bai !== 'undefined' && phut_canh_bai) {
                    thoi_gian_canh_bai = formatNumberFixed(phut_canh_bai/60, 2) * 1;
                }
                $(element).find('.thoi_gian_canh_bai').val(thoi_gian_canh_bai);

                var so_lan_thay_size = intVal($(element).find('.so_lan_thay_size').val());
                var so_lan_rua_may = intVal($(element).find('.so_lan_rua_may').val());

                var thoi_gian_thay_size = 0;
                var thoi_gian_rua_may = 0;
                if (so_lan_thay_size) {
                    thoi_gian_thay_size = so_lan_thay_size * thay_size_gio;
                }

                if (so_lan_rua_may) {
                    thoi_gian_rua_may = so_lan_rua_may * rua_may_gio;
                }
                $(element).find('.thoi_gian_thay_size').val(thoi_gian_thay_size);
                $(element).find('.thoi_gian_rua_may').val(thoi_gian_rua_may);

                thoi_gian_khac = intVal($(element).find('.thoi_gian_khac').val());
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai + thoi_gian_khac + thoi_gian_thay_size + thoi_gian_rua_may;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_hien_tai = formatDate(nowDate(), "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_hien_tai, 11);
                } else if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

            } else if (_c_type_productionlist_id == 9) {
                //Bế
                so_to_in = intVal($(element).find('.td-to-in').html());
                so_con_tren_to_in = intVal($(element).find('.td-so-con-tren-to-in').html());
                so_lan_van_hanh = intVal($(element).find('.so_lan_van_hanh').val());
                tong_tua = so_to_in * so_lan_van_hanh;

                $(element).find('.td-tong-tua').html(tnhFormatNumber(tong_tua));
                loai_giay = $(element).find('select[data-class="loai_giay"]').val();
                nang_suat = 0;
                if (loai_giay == 1) {
                    nang_suat = nang_suat_may_be_giay_thuong;
                } else {
                    nang_suat = nang_suat_may_demi_be_giay_boi_pet;
                }

                thoi_gian_in = 0;
                if (nang_suat > 0) {
                    thoi_gian_in = tong_tua/nang_suat;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));

                var thoi_gian_canh_bai = 0;
                var phut_canh_bai = $('.canh_bai_'+loai_canh_bai).val();
                if (typeof phut_canh_bai !== 'undefined' && phut_canh_bai) {
                    thoi_gian_canh_bai = formatNumberFixed(phut_canh_bai/60, 2) * 1;
                }
                $(element).find('.thoi_gian_canh_bai').val(thoi_gian_canh_bai);

                var so_lan_thay_size = intVal($(element).find('.so_lan_thay_size').val());
                var so_lan_rua_may = intVal($(element).find('.so_lan_rua_may').val());

                var thoi_gian_thay_size = 0;
                var thoi_gian_rua_may = 0;
                if (so_lan_thay_size) {
                    thoi_gian_thay_size = so_lan_thay_size * thay_size_gio;
                }

                if (so_lan_rua_may) {
                    thoi_gian_rua_may = so_lan_rua_may * rua_may_gio;
                }
                $(element).find('.thoi_gian_thay_size').val(thoi_gian_thay_size);
                $(element).find('.thoi_gian_rua_may').val(thoi_gian_rua_may);

                thoi_gian_khac = intVal($(element).find('.thoi_gian_khac').val());
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai + thoi_gian_khac + thoi_gian_thay_size + thoi_gian_rua_may;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_hien_tai = formatDate(nowDate(), "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_hien_tai, 11);
                } else if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);

            } else if(_c_type_productionlist_id == 13) {
                //Xả TP
                so_to_in = intVal($(element).find('.td-to-in').html());
                so_con_tren_to_in = intVal($(element).find('.td-so-con-tren-to-in').html());
                so_duong_dao_cat = intVal($(element).find('.so_duong_dao_cat').val());
                tong_tua = so_to_in * so_duong_dao_cat;

                $(element).find('.td-tong-tua').html(tnhFormatNumber(tong_tua));
                loai_xa = $(element).find('select[data-class="loai_xa"]').val();
                nang_suat = 0;
                if (loai_xa == 1) {
                    nang_suat = nang_suat_may_be_giay_thuong;
                } else {
                    nang_suat = nang_suat_may_demi_be_giay_boi_pet;
                }

                thoi_gian_in = 0;
                if (nang_suat > 0) {
                    thoi_gian_in = tong_tua/nang_suat;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));

                var thoi_gian_canh_bai = 0;
                var phut_canh_bai = $('.canh_bai_'+loai_canh_bai).val();
                if (typeof phut_canh_bai !== 'undefined' && phut_canh_bai) {
                    thoi_gian_canh_bai = formatNumberFixed(phut_canh_bai/60, 2) * 1;
                }
                $(element).find('.thoi_gian_canh_bai').val(thoi_gian_canh_bai);

                var so_lan_thay_size = intVal($(element).find('.so_lan_thay_size').val());
                var so_lan_rua_may = intVal($(element).find('.so_lan_rua_may').val());

                var thoi_gian_thay_size = 0;
                var thoi_gian_rua_may = 0;
                if (so_lan_thay_size) {
                    thoi_gian_thay_size = so_lan_thay_size * thay_size_gio;
                }

                if (so_lan_rua_may) {
                    thoi_gian_rua_may = so_lan_rua_may * rua_may_gio;
                }
                $(element).find('.thoi_gian_thay_size').val(thoi_gian_thay_size);
                $(element).find('.thoi_gian_rua_may').val(thoi_gian_rua_may);

                thoi_gian_khac = intVal($(element).find('.thoi_gian_khac').val());
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai + thoi_gian_khac + thoi_gian_thay_size + thoi_gian_rua_may;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_hien_tai = formatDate(nowDate(), "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_hien_tai, 11);
                } else if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);
            } else if (_c_type_productionlist_id == 14) {
                //Khoan lỗ
                so_to_in = intVal($(element).find('.td-to-in').html());
                so_con_tren_to_in = intVal($(element).find('.td-so-con-tren-to-in').html());
                so_luong_san_xuat = intVal($(element).find('.so_luong_san_xuat').val());
                tong_tua = so_luong_san_xuat;

                $(element).find('.td-tong-tua').html(tnhFormatNumber(tong_tua));
                loai_xa = $(element).find('select[data-class="loai_xa"]').val();
                nang_suat = nang_suat_may;

                thoi_gian_in = 0;
                if (nang_suat > 0) {
                    thoi_gian_in = tong_tua/nang_suat;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));

                var thoi_gian_canh_bai = 0;
                var phut_canh_bai = $('.canh_bai_'+loai_canh_bai).val();
                if (typeof phut_canh_bai !== 'undefined' && phut_canh_bai) {
                    thoi_gian_canh_bai = formatNumberFixed(phut_canh_bai/60, 2) * 1;
                }
                $(element).find('.thoi_gian_canh_bai').val(thoi_gian_canh_bai);

                var so_lan_thay_size = intVal($(element).find('.so_lan_thay_size').val());
                var so_lan_rua_may = intVal($(element).find('.so_lan_rua_may').val());

                var thoi_gian_thay_size = 0;
                var thoi_gian_rua_may = 0;
                if (so_lan_thay_size) {
                    thoi_gian_thay_size = so_lan_thay_size * thay_size_gio;
                }

                if (so_lan_rua_may) {
                    thoi_gian_rua_may = so_lan_rua_may * rua_may_gio;
                }
                $(element).find('.thoi_gian_thay_size').val(thoi_gian_thay_size);
                $(element).find('.thoi_gian_rua_may').val(thoi_gian_rua_may);

                thoi_gian_khac = intVal($(element).find('.thoi_gian_khac').val());
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai + thoi_gian_khac + thoi_gian_thay_size + thoi_gian_rua_may;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_hien_tai = formatDate(nowDate(), "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_hien_tai, 11);
                } else if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);
            } else if (_c_type_productionlist_id == 15) {
                //Gở bế
                so_to_in = intVal($(element).find('.td-to-in').html());
                so_con_tren_to_in = intVal($(element).find('.td-so-con-tren-to-in').html());
                so_luong_san_xuat = intVal($(element).find('.so_luong_san_xuat').val());
                tong_tua = so_luong_san_xuat;

                $(element).find('.td-tong-tua').html(tnhFormatNumber(tong_tua));
                loai_xa = $(element).find('select[data-class="loai_xa"]').val();
                nang_suat = nang_suat_may;

                thoi_gian_in = 0;
                if (nang_suat > 0) {
                    thoi_gian_in = tong_tua/nang_suat;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));

                var thoi_gian_canh_bai = 0;
                var phut_canh_bai = $('.canh_bai_'+loai_canh_bai).val();
                if (typeof phut_canh_bai !== 'undefined' && phut_canh_bai) {
                    thoi_gian_canh_bai = formatNumberFixed(phut_canh_bai/60, 2) * 1;
                }
                $(element).find('.thoi_gian_canh_bai').val(thoi_gian_canh_bai);

                var so_lan_thay_size = intVal($(element).find('.so_lan_thay_size').val());
                var so_lan_rua_may = intVal($(element).find('.so_lan_rua_may').val());

                var thoi_gian_thay_size = 0;
                var thoi_gian_rua_may = 0;
                if (so_lan_thay_size) {
                    thoi_gian_thay_size = so_lan_thay_size * thay_size_gio;
                }

                if (so_lan_rua_may) {
                    thoi_gian_rua_may = so_lan_rua_may * rua_may_gio;
                }
                $(element).find('.thoi_gian_thay_size').val(thoi_gian_thay_size);
                $(element).find('.thoi_gian_rua_may').val(thoi_gian_rua_may);

                thoi_gian_khac = intVal($(element).find('.thoi_gian_khac').val());
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai + thoi_gian_khac + thoi_gian_thay_size + thoi_gian_rua_may;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_hien_tai = formatDate(nowDate(), "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_hien_tai, 11);
                } else if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);
            } else if (_c_type_productionlist_id == 16) {
                //Soạn
                so_to_in = intVal($(element).find('.td-to-in').html());
                so_con_tren_to_in = intVal($(element).find('.td-so-con-tren-to-in').html());
                so_luong_san_xuat = intVal($(element).find('.so_luong_san_xuat').val());
                tong_tua = so_luong_san_xuat;

                $(element).find('.td-tong-tua').html(tnhFormatNumber(tong_tua));
                loai_xa = $(element).find('select[data-class="loai_xa"]').val();
                nang_suat = nang_suat_may;

                thoi_gian_in = 0;
                if (nang_suat > 0) {
                    thoi_gian_in = tong_tua/nang_suat;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));

                var thoi_gian_canh_bai = 0;
                var phut_canh_bai = $('.canh_bai_'+loai_canh_bai).val();
                if (typeof phut_canh_bai !== 'undefined' && phut_canh_bai) {
                    thoi_gian_canh_bai = formatNumberFixed(phut_canh_bai/60, 2) * 1;
                }
                $(element).find('.thoi_gian_canh_bai').val(thoi_gian_canh_bai);

                var so_lan_thay_size = intVal($(element).find('.so_lan_thay_size').val());
                var so_lan_rua_may = intVal($(element).find('.so_lan_rua_may').val());

                var thoi_gian_thay_size = 0;
                var thoi_gian_rua_may = 0;
                if (so_lan_thay_size) {
                    thoi_gian_thay_size = so_lan_thay_size * thay_size_gio;
                }

                if (so_lan_rua_may) {
                    thoi_gian_rua_may = so_lan_rua_may * rua_may_gio;
                }
                $(element).find('.thoi_gian_thay_size').val(thoi_gian_thay_size);
                $(element).find('.thoi_gian_rua_may').val(thoi_gian_rua_may);

                thoi_gian_khac = intVal($(element).find('.thoi_gian_khac').val());
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai + thoi_gian_khac + thoi_gian_thay_size + thoi_gian_rua_may;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_hien_tai = formatDate(nowDate(), "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_hien_tai, 11);
                } else if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);
            } else if (_c_type_productionlist_id == 4 || _c_type_productionlist_id == 5 || _c_type_productionlist_id == 17 || _c_type_productionlist_id == 20 || _c_type_productionlist_id == 19 || _c_type_productionlist_id == 26) {
                //HP
                tong_tua = so_mat_in * to_in;
                $(element).find('.td-tong-tua').html(tnhFormatNumber(tong_tua));

                thoi_gian_in = 0;
                if (nang_suat_may > 0) {
                    thoi_gian_in = tong_tua/nang_suat_may;
                }
                $(element).find('.td-thoi-gian-in').html(tnhFormatNumber(thoi_gian_in));

                var thoi_gian_canh_bai = 0;
                var phut_canh_bai = $('.canh_bai_'+loai_canh_bai).val();
                if (typeof phut_canh_bai !== 'undefined' && phut_canh_bai) {
                    thoi_gian_canh_bai = formatNumberFixed(phut_canh_bai/60, 2) * 1;
                }
                $(element).find('.thoi_gian_canh_bai').val(thoi_gian_canh_bai);

                // thoi_gian_canh_bai = intVal($(element).find('.thoi_gian_canh_bai').val());

                var so_lan_thay_size = intVal($(element).find('.so_lan_thay_size').val());
                var so_lan_rua_may = intVal($(element).find('.so_lan_rua_may').val());

                var thoi_gian_thay_size = 0;
                var thoi_gian_rua_may = 0;
                if (so_lan_thay_size) {
                    thoi_gian_thay_size = so_lan_thay_size * thay_size_gio;
                }

                if (so_lan_rua_may) {
                    thoi_gian_rua_may = so_lan_rua_may * rua_may_gio;
                }
                $(element).find('.thoi_gian_thay_size').val(thoi_gian_thay_size);
                $(element).find('.thoi_gian_rua_may').val(thoi_gian_rua_may);

                thoi_gian_khac = intVal($(element).find('.thoi_gian_khac').val());
                thoi_gian_xu_ly = thoi_gian_in + thoi_gian_canh_bai + thoi_gian_khac + thoi_gian_thay_size + thoi_gian_rua_may;
                $(element).find('.td-thoi-gian-xu-ly').html(tnhFormatNumber(thoi_gian_xu_ly));

                ngay_giao_hang = $(element).find('.ngay_giao_hang').val();
                ngay_bat_dau_du_kien = $(element).find('.ngay_bat_dau_du_kien').val();
                thoi_gian_con_lai = 0;

                if (ngay_giao_hang) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_hien_tai = formatDate(nowDate(), "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_hien_tai, 11);
                } else if (ngay_giao_hang && ngay_bat_dau_du_kien) {
                    ngay_giao_hang = formatDate(ngay_giao_hang, "dd/mm/yyyy", "yyyy-mm-dd");
                    ngay_bat_dau_du_kien = formatDate(ngay_bat_dau_du_kien, "dd/mm/yyyy", "yyyy-mm-dd");
                    thoi_gian_con_lai = workingDaysBetweenDates(ngay_giao_hang, ngay_bat_dau_du_kien, 11);
                }
                $(element).find('.td-thoi-gian-con-lai').html(tnhFormatNumber(thoi_gian_con_lai));
                $(element).find('.thoi_gian_con_lai').val(thoi_gian_con_lai);
                
                to_in = intVal($(element).find('.td-to-in').html());
                so_con_tren_to_in = intVal($(element).find('.td-so-con-tren-to-in').html());
                so_con_tren_kb_offset = intVal($(element).find('.td-so-con-tren-kb-offset').html());
                tua_sau_in = (so_con_tren_kb_offset > 0 ? (so_con_tren_to_in/so_con_tren_kb_offset*to_in) : to_in);

                $(element).find('.td-tua-sau-in').html(tnhFormatNumber(tua_sau_in));
            }

            //
            var may_in = $(element).find('select[data-class="may_in"]').val();
            if (may_in) {
                may_in_text = $(element).find('select[data-class="may_in"] option:selected').text();
                if (typeof list_machines[may_in] === 'undefined') {
                    list_machines[may_in] = {'id': may_in, 'text': may_in_text, 'tong_so_tua': 0, 'tong_thoi_gian': 0};
                }
            }

            if (!ngay_bat_dau_du_kien) {
                tong_thoi_gian_xu_ly+= thoi_gian_xu_ly;
                tong_tua_in_con_lai+= tong_tua;
                tong_tua_in+= to_in;
            } else {
                if (may_in) {
                    list_machines[may_in]['tong_so_tua'] = list_machines[may_in]['tong_so_tua'] + tong_tua;
                    list_machines[may_in]['tong_thoi_gian'] = list_machines[may_in]['tong_thoi_gian'] + thoi_gian_xu_ly;
                }
            }
            
        }

        if (change_date) {
            $.each($('.div-type_productionlist_id-'+_c_type_productionlist_id+' .table-date tr.tr-sum td'), function (index, value) { 
                _date = $(value).attr('data-date');
                total_thoi_gian_xu_ly = 0;
                $.each($(tb), function (iTB, vTB) { 
                    ngay_bat_dau_du_kien = $(vTB).find('.ngay_bat_dau_du_kien').val();
                    if (_date == ngay_bat_dau_du_kien) {
                        total_thoi_gian_xu_ly+= intVal($(vTB).find('.td-thoi-gian-xu-ly').html());
                    }
                });
                $(value).html(total_thoi_gian_xu_ly > 0 ? tnhFormatNumber(total_thoi_gian_xu_ly, 2) : '');
            });
        }

        //danh sách máy móc
        var htmlMachines = '';
        if (list_machines) {
            $.each(list_machines, function (index, value) { 
                htmlMachines+= `<tr>
                    <td>${value.text}</td>
                    <td class="text-right">${tnhFormatNumber(value.tong_so_tua)}</td>
                    <td class="text-right">${tnhFormatNumber(value.tong_thoi_gian)}</td>
                </tr>`;
            });
        }
        $('#tb-machines tbody').html(htmlMachines);

        //tfoot
        $('tfoot .tfoot-thoi_gian_xu_ly').html(`<div class="text-right">${tnhFormatNumber(tong_thoi_gian_xu_ly)}</div>`);
        $('.td-tong_thoi_gian_con_lai').html(`<div class="text-right">${tnhFormatNumber(tong_thoi_gian_xu_ly)}</div>`);
        $('.td-tong_tua_in').html(`<div class="text-right">${tnhFormatNumber(tong_tua_in)}</div>`);
        $('.td-tong_tua_in_con_lai').html(`<div class="text-right">${tnhFormatNumber(tong_tua_in_con_lai)}</div>`);
    }

    function changeCanhBai(_this, _id_canh_bai, _c_type_productionlist_id) {
        var _cTr = $(_this).closest('tr');
        var _value = $(_this).val();
        var _hour = formatNumberFixed(_value/60, 2);
        _cTr.find('.td-gio').html(_hour);
        totalProductionList(tnhFormatNumber(_c_type_productionlist_id));
    }

    function saveModerationPlan(_this) {
        var date_start = $('#date_start').val();
        var date_end = $('#date_end').val();

        if (!date_start || !date_end) {
            alert_float('danger', 'Vui lòng chọn ngày bắt đầu và kết thúc');
            return;
        }

        $(_this).attr('disabled', 'disabled');
        totalProductionList($('#type_productionlist_id').val());
        
        setTimeout(function() {
            var form = $('#form-moderation-plan'),
                formData = new FormData(),
                formParams = form.serializeArray();
            
            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });

            $.ajax({
                url : site.base_url+'admin/production_list/handlingModerationPlan',
                type : 'POST',
                dataType: 'JSON',
                cache : false,
                contentType : false,
                processData : false,
                data: formData,
            })
            .done(function(data) {
                if (data.result) {
                    alert_float('success', data.message);
                    $('#reference_no').val(data.reference_no);
                    $('#production_list_id').val(data.production_list_id);
                } else {
                    alert_float('danger', data.message);
                }

                $(_this).removeAttr('disabled', 'disabled');
            })
            .fail(function() {
                alert_float('danger', lang_core['errors']);
                $(_this).removeAttr('disabled', 'disabled');
            });
            return false;
        }, 1000);
    }

    function exportExcelModerationPlan() {
        var dataPOST = {};
        var date_start = $('#date_start').val();
        var date_end = $('#date_end').val();
        var group = $('#group').val();
        var productions_orders_search = $('#productions_orders_search').val();
        var status_filter = $('#status_filter').val();
        var date_start_expected = $('#date_start_expected').val();
        var date_end_expected = $('#date_end_expected').val();
        var date_start_finished = $('#date_start_finished').val();
        var date_end_finished = $('#date_end_finished').val();

        dataPOST[csrf_token_name] = hash;
        dataPOST['date_start'] = date_start;
        dataPOST['date_end'] = date_end;
        dataPOST['group'] = group;
        dataPOST['productions_orders_search'] = productions_orders_search;
        dataPOST['status_filter'] = status_filter;
        dataPOST['date_start_expected'] = date_start_expected;
        dataPOST['date_end_expected'] = date_end_expected;
        dataPOST['date_start_finished'] = date_start_finished;
        dataPOST['date_end_finished'] = date_end_finished;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/production_list/export_excel_moderation_plan_new',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                console.log(response);
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }

    function filterProduction(_this, _value) {
        $('#status_filter').val(_value);
        loadDataPlan();
    }

    $(document).ready(function() {
        // ajaxSelectParamsCallback('#productions_orders_search', 'admin/manufactures/searchProductionsOrders', 0, false, true);
        selectAjaxV2('#productions_orders_search', {}, 'admin/manufacture/searchProductionsOrders');
        $('#category_stages').select2();
        $('#machine_id_new').select2({
            allowClear: true
        });

        loadDataPlan();
    });
</script>