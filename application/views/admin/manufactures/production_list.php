<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<style class="">
    table tr td {
        vertical-align: middle !important;
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
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-2">
                    <?= lang('start_date', 'start_date_search') ?>
                    <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
                </div>
                <div class="col-md-2">
                    <?= lang('end_date', 'end_date_search') ?>
                    <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
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
                                    <div class="horizontal-tabs">
                                        <ul class="nav nav-tabs nav-tabs-horizontal status-table" style="margin-bottom: 5px;" role="tablist">
                                            <li class="active" style="max-height: 42px;height: 42px;">
                                                <a style="padding: 3px;">
                                                    <button style=" font-size: 11px;" type="button" id="btndata_all" data-toggle="tab" class="btn btn-info btn-search" data-value="">
                                                        <?= _l('leads_all') ?>
                                                        <span class="badge menu-badge bg-warning" id="all_status" style="position: absolute;top: 1px; right: -3px; background-color: #ff6f00;color: white;font-size: 14px;font-weight: bold;"></span>
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
                                            <?php if (!empty($type_productionlist)) { ?>
                                                <?php foreach ($type_productionlist as $key => $value) { ?>
                                                    <li role="presentation" style="max-height: 42px;height: 42px;">
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
                                        <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="all">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <table id="table-production-lists" class="table dataTable table-production-lists" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <div class="checkbox mass_select_all_wrap text-center checkbox-info">
                                                <input type="checkbox" id="mass_select_all" data-to-table="production-lists">
                                                <label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th class="text-center"><?= lang('tnh_date_production_list') ?></th>
                                        <th class="text-center"><?= lang('start_date') ?></th>
                                        <th class="text-center"><?= lang('end_date') ?></th>
                                        <th class="text-center"><?= lang('tnh_reference_no_production_list') ?></th>
                                        <th class="text-center"><?= lang('stages') ?></th>
                                        <th class="text-center"><?= lang('tnh_printer') ?></th>
                                        <th class="text-center"><?= lang('status') ?></th>
                                        <th class="text-center"><?= lang('tnh_created_by') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
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
        start_date_search: "#start_date_search",
        end_date_search: "#end_date_search",
        status_table: "#status_table",
    };
    var oTable = '';

    $(document).ready(function() {
        oTable = tnhInitDataTable('#table-production-lists', '<?= site_url('admin/orders/getOrders') ?>', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/production_list/getProductionLists') ?>',
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
                    "render": function(data, type, row) {
                        return '<div class="checkbox checkbox-info"><input type="checkbox" name="production_list_id[]" id="check-item' +
                            data + '" value="' + data + '"><label for="check-item' + data +
                            '"></label></div>';
                    },
                    "targets": 0,
                    "name": 'id',
                    'orderable': false,
                    'width': '40px'
                },
                {
                    "targets": 6,
                    "name": 'printer',
                    'sortable': false,
                    'searchable': false,
                    'width': '160px',
                },
                {
                    "targets": 7,
                    "name": 'status',
                    'sortable': false,
                    'searchable': false,
                    'width': '100px',
                    'visible': false
                },
                {
                    "targets": 9,
                    "name": 'actions',
                    'sortable': false,
                    'searchable': false,
                    'width': '80px',
                },
            ],
        });

        $('#start_date_search, #end_date_search').change(function(event) {
            oTable.draw();
        });

        $(document).on('click', '.status-table li a', function(event) {
            status_table = $(this).find('.btn-search').attr('data-value');
            $('#status_table').val(status_table);
            oTable.draw();
        });
    });
</script>