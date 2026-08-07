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
</style>
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="pull-right mright5 H_border">
                <a href="<?= base_url('admin/production_list/handling') ?>" class="btn btn-info H_action_button">
                    <?php echo _l('add'); ?>
                </a>
            </div>
            <div class="pull-right mright5 H_border">
                <a href="<?= base_url('admin/production_list/import_excel') ?>" class="btn btn-info mright5 pull-right H_action_button tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('Import excel'); ?>
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
                    <div class="form-group" app-field-wrapper="category_stages">
                        <label for="category_stages" class="control-label">Nhóm</label>
                        <select name="category_stages" id="category_stages" data-placeholder="<?= lang('Nhóm') ?>" class="category_stages" style="width: 100%;">
                            <?php foreach ($category_stages as $key => $value) { ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group" app-field-wrapper="machine_id_new">
                        <label for="machine_id_new" class="control-label">Máy móc</label>
                        <select name="machine_id_new" id="machine_id_new" data-placeholder="<?= lang('Máy móc') ?>" class="machine_id_new" style="width: 100%;">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mtop25">
                        <a href="javascript:void(0)" onclick="filterModerationPlan()" class="btn btn-primary"><?= lang('filter') ?></a>
                        <a href="javascript:void(0)" onclick="exportExcel()" class="btn btn-success"><?= lang('c_export_excel') ?></a>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="mtop25">
                        
                    </div>
                </div>
            </div>
            <div class="col-md-12">
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
                <div class="col-md-2">
                    <?= lang('tnh_reference_productions_orders', 'productions_orders_search') ?>
                    <input type="text" name="productions_orders_search" data-placeholder="<?= lang('tnh_reference_productions_orders') ?>" id="productions_orders_search" class="productions_orders_search" style="width: 100%;" value="">
                </div>
            </div>
            <div class="col-md-12 mtop5">
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
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="">
                            <div data-toggle="btn" class="">
                                <div class="">
                                    <div class="horizontal-tabs hide">
                                        <ul class="nav nav-tabs nav-tabs-horizontal status-table" style="margin-bottom: 5px;" role="tablist">
                                            <?php $_status = 0; ?>
                                            <?php if (!empty($type_productionlist)) { ?>
                                                <?php foreach ($type_productionlist as $key => $value) { ?>
                                                    <?php
                                                    if ($key == 0) {
                                                        $_status = $value['id'];
                                                    }
                                                    ?>
                                                    <li <?= $key == 0 ? 'class="active"' : '' ?> role="presentation" style="max-height: 42px;height: 42px;">
                                                        <a style="padding: 3px;">
                                                            <button style="font-size: 11px; color: #fff; " type="button" data-toggle="tab" class="btn btn-info btn-search" data-value="<?= $value['id'] ?>">
                                                                <?= $value['code'] ?>
                                                                <span class="badge menu-badge bg-warning" id="group_<?= $value['id'] ?>" style="position: absolute;top: 1px; right: -3px; background-color: #ff6f00;color: white;font-size: 14px;font-weight: bold;"></span>
                                                                <span class="check-show" style="float: left; margin-right: 5px;">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="9pt" height="9pt" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">
                                                                        <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="white" stroke="none">
                                                                            <path d="M2370 5114 c-19 -2 -78 -9 -130 -14 -330 -36 -695 -160 -990 -336 -375 -224 -680 -529 -904 -904 -173 -290 -294 -643 -336 -980 -13 -109 -13 -531 0 -640 96 -778 555 -1476 1240 -1884 670 -400 1508 -465 2245 -174 112 44 352 166 401 203 50 38 78 105 71 172 -11 101 -79 169 -180 180 -50 5 -58 2 -187 -65 -338 -176 -627 -256 -975 -269 -950 -37 -1827 576 -2124 1483 -96 294 -130 585 -101 875 33 330 156 694 320 943 291 445 681 750 1156 905 625 204 1283 121 1841 -233 51 -32 109 -62 130 -67 156 -36 291 130 224 276 -23 50 -43 68 -161 142 -315 199 -668 324 -1050 373 -88 12 -429 21 -490 14z" />
                                                                            <path d="M4843 4698 c-17 -6 -45 -22 -62 -37 -16 -14 -501 -613 -1077 -1331 -576 -718 -1056 -1313 -1068 -1322 -34 -28 -81 -33 -120 -12 -19 11 -269 241 -554 512 -286 271 -530 501 -543 511 -79 60 -185 52 -257 -19 -72 -73 -81 -182 -19 -259 34 -43 1023 -982 1097 -1042 205 -166 490 -155 676 26 66 64 2148 2650 2171 2697 79 158 -73 331 -244 276z" />
                                                                            <path d="M4760 3334 c-106 -46 -140 -141 -105 -293 46 -195 65 -462 46 -661 -33 -346 -138 -659 -321 -960 -76 -126 -85 -171 -47 -253 19 -42 35 -61 68 -80 58 -34 110 -42 163 -27 74 20 116 71 232 280 296 536 387 1186 249 1786 -20 87 -32 118 -57 148 -57 69 -151 94 -228 60z" />
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                            </button>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            <?php } ?>
                                        </ul>
                                        <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="<?= $_status ?>">
                                    </div>

                                    <div class="horizontal-tabs hide">
                                        <ul class="nav nav-tabs nav-tabs-horizontal status-table-stages" style="margin-bottom: 5px;" role="tablist">
                                            <?php $_status_table_stages = 0; ?>
                                            <?php if (!empty($category_stages)) { ?>
                                                <?php foreach ($category_stages as $key => $value) { ?>
                                                    <?php
                                                    if ($key == 0) {
                                                        $_status_table_stages = $value['id'];
                                                    }
                                                    ?>
                                                    <li <?= $key == 0 ? 'class="active"' : '' ?> role="presentation" style="max-height: 42px;height: 42px;">
                                                        <a style="padding: 3px;">
                                                            <button style="font-size: 11px; color: #fff; " type="button" data-toggle="tab" class="btn btn-info btn-search" data-value="<?= $value['id'] ?>">
                                                                <?= $value['name'] ?>
                                                                <span class="badge menu-badge bg-warning" id="group_<?= $value['id'] ?>" style="position: absolute;top: 1px; right: -3px; background-color: #ff6f00;color: white;font-size: 14px;font-weight: bold;"></span>
                                                                <span class="check-show" style="float: left; margin-right: 5px;">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="9pt" height="9pt" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">
                                                                        <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="white" stroke="none">
                                                                            <path d="M2370 5114 c-19 -2 -78 -9 -130 -14 -330 -36 -695 -160 -990 -336 -375 -224 -680 -529 -904 -904 -173 -290 -294 -643 -336 -980 -13 -109 -13 -531 0 -640 96 -778 555 -1476 1240 -1884 670 -400 1508 -465 2245 -174 112 44 352 166 401 203 50 38 78 105 71 172 -11 101 -79 169 -180 180 -50 5 -58 2 -187 -65 -338 -176 -627 -256 -975 -269 -950 -37 -1827 576 -2124 1483 -96 294 -130 585 -101 875 33 330 156 694 320 943 291 445 681 750 1156 905 625 204 1283 121 1841 -233 51 -32 109 -62 130 -67 156 -36 291 130 224 276 -23 50 -43 68 -161 142 -315 199 -668 324 -1050 373 -88 12 -429 21 -490 14z" />
                                                                            <path d="M4843 4698 c-17 -6 -45 -22 -62 -37 -16 -14 -501 -613 -1077 -1331 -576 -718 -1056 -1313 -1068 -1322 -34 -28 -81 -33 -120 -12 -19 11 -269 241 -554 512 -286 271 -530 501 -543 511 -79 60 -185 52 -257 -19 -72 -73 -81 -182 -19 -259 34 -43 1023 -982 1097 -1042 205 -166 490 -155 676 26 66 64 2148 2650 2171 2697 79 158 -73 331 -244 276z" />
                                                                            <path d="M4760 3334 c-106 -46 -140 -141 -105 -293 46 -195 65 -462 46 -661 -33 -346 -138 -659 -321 -960 -76 -126 -85 -171 -47 -253 19 -42 35 -61 68 -80 58 -34 110 -42 163 -27 74 20 116 71 232 280 296 536 387 1186 249 1786 -20 87 -32 118 -57 148 -57 69 -151 94 -228 60z" />
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                            </button>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            <?php } ?>
                                        </ul>
                                        <input type="hidden" name="status_table_stages" id="status_table_stages" class="form-status_table_stages status_table" value="<?= $_status_table_stages ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <table id="table-moderation-plan" class="table dataTable table-moderation-plan" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" rowspan="2"><?= lang('STT') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Ngày mở lệnh') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Mã LSX') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Mã sản phẩm') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Tên sản phẩm') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Tổng số con') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Công đoạn') ?><span class="txt-category-stages"></span></th>
                                        <th class="text-center" rowspan="2"><?= lang('Ngày giao hàng dự kiến') ?></th>
                                        <th class="text-center"><?= lang('Số con') ?></th>
                                        <th class="text-center"><?= lang('Tổng số') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Số mặt in') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Máy móc') ?></th>
                                        <th class="text-center"><?= lang('Tổng TG dự kiến') ?></th>
                                        <th class="text-center" colspan="2"><?= lang('Kế hoạch') ?></th>
                                        <th class="text-center" colspan="4"><?= lang('Thực tế') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Trạng thái') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('actions') ?></th>
                                    </tr>
                                    <tr>
                                        <th class="text-center"><?= lang('Tờ') ?></th>
                                        <th class="text-center"><?= lang('Tờ') ?></th>
                                        <th class="text-center"><?= lang('H') ?></th>
                                        <th class="text-center"><?= lang('Bắt đầu') ?><br><?= lang('(Ngày - H)') ?></th>
                                        <th class="text-center"><?= lang('Kết thúc') ?><br><?= lang('(Ngày - H)') ?></th>
                                        <th class="text-center"><?= lang('Bắt đầu') ?><br><?= lang('(Ngày - H)') ?></th>
                                        <th class="text-center"><?= lang('Kết thúc') ?><br><?= lang('(Ngày - H)') ?></th>
                                        <th class="text-center"><?= lang('Số lượng') ?></th>
                                        <th class="text-center"><?= lang('Số giờ thực tế') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3"><b>Tổng Cộng</b></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b class="sumTotal total_tong_thoi_gian" style="padding-left: 14px;"></b></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b class="sumTotal total_tong_so_luong" style="padding-left: 14px;"></b></td>
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
        date_search: "#date_search",
        status_table: "#status_table",
        date_start: "#date_start",
        date_end: "#date_end",
        category_stages: "#category_stages",
        machine_id_new: "#machine_id_new",
        status_table_stages: '#status_table_stages',
        start_date_delivery: '#start_date_delivery',
        end_date_delivery: '#end_date_delivery',
        start_date_bd_kh: '#start_date_bd_kh',
        end_date_bd_kh: '#end_date_bd_kh',
        productions_orders_search: '#productions_orders_search',
        start_date_reality: '#start_date_reality',
        end_date_reality: '#end_date_reality',
        start_date_reality_kt: '#start_date_reality_kt',
        end_date_reality_kt: '#end_date_reality_kt',
    };
    var oTable = '';

    function filterModerationPlan() {
        date_start = $('#date_start').val();
        date_end = $('#date_end').val();
        if (!date_start || !date_end) {
            alert_float('danger', '<?= lang('Vui lòng chọn ngày bắt đầu và kết thúc') ?>');
            return;
        }

        category_stages_name = $('#category_stages').find(':selected').text();
        $('.txt-category-stages').html(' '+category_stages_name);
        oTable.draw();
    }

    // function updatePlan(_this, _name, _id) {
    //     var dataPOST = {};
    //     dataPOST[csrfData['token_name']] = csrfData['hash'];
    //     dataPOST['_name'] = _name;
    //     dataPOST['_value'] = $(_this).val();
    //     dataPOST['_id'] = _id;

    //     $.ajax({
    //         type: "POST",
    //         url: site.base_url+'admin/production_list/updatePlan',
    //         data: dataPOST,
    //         dataType: "json",
    //         success: function (response) {
    //             if (response.result == 1) {
    //                 alert_float('success', response.message);
    //             } else if (response.result == 0) {
    //                 alert_float('danger', response.message);
    //                 oTable.draw(false);
    //             }
    //         }
    //     });
    // }

    var dataTables_scrollBody = 0;

    document.addEventListener('scroll', function (event) {
        if ($(event.target).hasClass('dataTables_scrollBody')) { // or any other filtering condition
            dataTables_scrollBody = $('#table-moderation-plan_wrapper').find('.dataTables_scrollBody').scrollTop();
        }
    }, true);

    function updateModerationPlan(_this, _name, _po_id, _item_id, _type_productionlist_id, _stage_id, _tong_so_to_in = 0, _so_mat_in = 0) {
        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['_name'] = _name;
        dataPOST['_value'] = $(_this).val();
        dataPOST['_po_id'] = _po_id;
        dataPOST['_item_id'] = _item_id;
        dataPOST['_type_productionlist_id'] = _type_productionlist_id;
        dataPOST['_stage_id'] = _stage_id;
        dataPOST['_tong_so_to_in'] = _tong_so_to_in;
        dataPOST['_so_mat_in'] = _so_mat_in;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/production_list/updateModerationPlan',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                if (response.result == 1) {
                    alert_float('success', response.message);
                    if (_name == 'mat_in' || _name == 'machine_id') {
                        // oTable.draw('page');
                    }
                    oTable.draw('page');
                } else if (response.result == 0) {
                    alert_float('danger', response.message);
                    oTable.draw('page');
                }
            }
        });
    }

    $(document).ready(function() {
        ajaxSelectParamsCallback('#productions_orders_search', 'admin/manufactures/searchProductionsOrders', 0, false, true);
        $('#category_stages').select2();
        $('#machine_id_new').select2({
            allowClear: true
        });

        oTable = tnhInitDataTable('#table-moderation-plan', '', {
            // 'order': [
            //     [1, 'desc']
            // ],
            'ordering': false,
            'searching': false,
            // 'fixedHeader': {
            //     header: true,
            // },
            scrollY: '400px',
            scrollX: true,
            // 'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/production_list/getModerationPlanNew') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                },
                "dataSrc": function(json) {
                    // $('#table-orders tfoot tr td:nth-child(7)').html('<div class="text-right">' + tnhFormatMoney(json.grand_total) + '</div>');

                    return json.aaData;
                }
            },
            "columnDefs": [{
                "targets": 20,
                "name": 'actions',
                'sortable': false,
                'searchable': false,
                'visible': false,
                'width': '80px',
            }, ],
        });

        $('#table-moderation-plan').on('draw.dt', function(event) {
            var paymentReceivedReportsTable = $(this).DataTable();
            var total = paymentReceivedReportsTable.ajax.json().total;
            $('tfoot').find('.sumTotal').text(0);
            $.each(total, function(index, value) {
                $('tfoot').find(`.total_${index}`).text(tnhFormatNumber(value));
            })
            init_datepicker();
            
            
            $(document).on({
                ajaxStop: function () {
                    console.log(dataTables_scrollBody);
                    $('#table-moderation-plan_wrapper').find('.dataTables_scrollBody').scrollTop(dataTables_scrollBody);
                    $('#table-moderation-plan_wrapper').find('.DTFC_LeftBodyLiner').scrollTop(dataTables_scrollBody);
                }
            });
            
            
            $('select.machine_id').select2({
                'allowClear': true
            });
        });

        $('#date_search').change(function(event) {
            oTable.draw('page');
        });

        $(document).on('click', '.status-table li a', function(event) {
            status_table = $(this).find('.btn-search').attr('data-value');
            $('#status_table').val(status_table);
            oTable.draw('page');
        });

        $(document).on('click', '.status-table-stages li a', function(event) {
            status_table = $(this).find('.btn-search').attr('data-value');
            $('#status_table_stages').val(status_table);
            oTable.draw('page');
        });

        filterModerationPlan();
    });

    function exportExcel() {
        date_start = $("#date_start").val();
        date_end = $("#date_end").val();
        status_table_stages = $('#status_table_stages').val();
        category_stages = $('#category_stages').val();
        machine_id_new = $('#machine_id_new').val();
        start_date_delivery = $('#start_date_delivery').val();
        end_date_delivery = $('#end_date_delivery').val();
        start_date_bd_kh = $('#start_date_bd_kh').val();
        end_date_bd_kh = $('#end_date_bd_kh').val();
        productions_orders_search = $('#productions_orders_search').val();
        start_date_reality = $('#start_date_reality').val();
        end_date_reality = $('#end_date_reality').val();
        start_date_reality_kt = $('#start_date_reality_kt').val();
        end_date_reality_kt = $('#end_date_reality_kt').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/production_list/export_excel_moderation_plan',
            data: {
                csrf_token_name: hash,
                date_start: date_start,
                date_end: date_end,
                category_stages: category_stages,
                machine_id_new: machine_id_new,
                status_table_stages: status_table_stages,
                start_date_delivery: start_date_delivery,
                end_date_delivery: end_date_delivery,
                start_date_bd_kh: start_date_bd_kh,
                end_date_bd_kh: end_date_bd_kh,
                productions_orders_search: productions_orders_search,
                start_date_reality: start_date_reality,
                end_date_reality: end_date_reality,
            },
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

    $(document).on('change', '#category_stages', function(e) {
        var category_stages = $('#category_stages').val();
        $('#machine_id_new').find('option:gt(0)').remove();
        $('#machine_id_new').val('').change();

        $.post(admin_url + "production_list/getmachine", {
            category_stages: category_stages,
            [csrfData['token_name']]: csrfData['hash']
        }, function(data) {
            $('#machine_id_new').html(data);
        })
    });
    $('#category_stages').change();
</script>