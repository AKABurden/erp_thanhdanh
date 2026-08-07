<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .wap-compare-container {
        position: relative;
    }

    .wap-compare {
        cursor: pointer;
        position: absolute;
        width: 35px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: bold;
        background: #e0e0e0;
        border: 1px solid #bfbfbf;
    }

    .wap-compare-container .wap-compare:nth-child(2) {
        border-left: 0;
        border-right: 0;
        left: 35px;
    }

    .wap-compare-container .wap-compare:nth-child(3) {
        left: 70px;
    }

    .wap-compare.active {
        background: #3e99d7;
        color: #fff;
    }

    .table-suggestion img {
        height: 20px;
        width: 20px;
    }

    .table-suggestion thead tr th {
        text-align: center;
    }

    .table-suggestion tr td:nth-child(2) {
        width: 120px;
        white-space: unset;
    }

    .table-suggestion tr td:nth-child(3) {
        width: 100px;
        white-space: unset;
        text-align: center;
    }

    .table-suggestion tr td:nth-child(4) {
        width: 100px;
        white-space: unset;
        text-align: center;
    }

    .table-suggestion tr td:nth-child(5) {
        width: 100px;
        white-space: unset;
        text-align: center;
    }

    .table-suggestion tr td:nth-child(6) {
        width: 140px;
        white-space: unset;
        text-align: center;
    }

    .table-suggestion tr td:nth-child(7) {
        width: 100px;
        white-space: unset;
        text-align: right;
    }

    .table-suggestion tr td:nth-child(8) {
        width: 120px;
        white-space: unset;
    }

    .table-suggestion tr td:nth-child(9) {
        width: 120px;
        white-space: unset;
        text-align: center;
    }

    .table-suggestion tr td:nth-child(10) {
        width: 120px;
        white-space: unset;
        text-align: center;
    }

    .table-suggestion tr td:nth-child(11) {
        width: 100px;
        white-space: unset;
        text-align: center;
    }

    .table-suggestion tr td:nth-child(12) {
        width: 120px;
        white-space: unset;
        text-align: center;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <div class="pull-right mright5 H_border">
                    <a href="<?= admin_url('suggestion/detail') ?>" class="btn btn-info mright5 test pull-right H_action_button">
                        <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                        <?php echo _l('create_add_new'); ?></a>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-2">
                <label for="staffid" class="control-label"><?php echo _l('ch_staff_suggestion'); ?></label>
                <input data-placeholder="<?= _l('ch_staff_suggestion') ?>" value="" name="staffid" style="width: 100%" id="staffid">
            </div>
            <div class="col-md-2">
                <?php
                $type_items = array(
                    array(
                        'id' => 1,
                        'name' => 'Gấp',
                        'sub' => 'Xử lý lập tức'
                    ),
                    array(
                        'id' => 2,
                        'name' => 'Bình Thường',
                        'sub' => 'Xủ lý trong vòng 2 ngày làm việc'
                    )
                );
                ?>
                <div class="form-group">
                    <label for="status" class="control-label"><?= _l('Trạng thái') ?></label>
                    <select data-placeholder="Trạng thái" name="status" style="width: 200px" class="status" id="status" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value=""></option>
                        <?php foreach ($type_items as $t) { ?>
                            <option value="<?php echo $t['id']; ?>"><?= $t['name'] ?> </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <?php
                $status_dn = array(
                    array(
                        'id' => 1,
                        'name' => 'Chưa duyệt',
                    ),
                    array(
                        'id' => 2,
                        'name' => 'Đã duyệt'
                    )
                );
                ?>
                <div class="form-group">
                    <label for="status_dn" class="control-label"><?= _l('Duyệt người đề nghị') ?></label>
                    <select data-placeholder="Duyệt người đề nghị" name="status_tp" style="width: 200px" class="status_dn" id="status_dn" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value=""></option>
                        <?php foreach ($status_dn as $t) { ?>
                            <option value="<?php echo $t['id']; ?>"><?= $t['name'] ?> </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <?php
                $status_tp = array(
                    array(
                        'id' => 1,
                        'name' => 'Chưa duyệt',
                    ),
                    array(
                        'id' => 2,
                        'name' => 'Đã duyệt'
                    )
                );
                ?>
                <div class="form-group">
                    <label for="status_tp" class="control-label"><?= _l('Duyệt trưởng phòng') ?></label>
                    <select data-placeholder="Duyệt trưởng phòng" autocomplete="off" name="status_dn" style="width: 200px" class="status_tp" id="status_tp" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value=""></option>
                        <?php foreach ($status_tp as $t) { ?>
                            <option value="<?php echo $t['id']; ?>"><?= $t['name'] ?> </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="search_date" class="control-label"><?= _l('ch_date_p') ?></label>
                    <div class="input-group">
                        <input type="text" id="search_date" name="search_date" class="form-control search_date" aria-invalid="false">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar calendar-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li class="active">
                                        <a class="H_filter" data-id="all">
                                            <?= _l('leads_all') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="1">
                                            <?= _l('Mua vật tư') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="2">
                                            <?= _l('Tạm ứng') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="3">
                                            <?= _l('Thanh toán') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="4">
                                            <?= _l('Tạm ứng & thanh toán') ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value="" />
                        <div class="clearfix"></div>
                        <?php render_datatable_tfoot_ch(array(
                            _l('#'),
                            _l('Ngày chứng từ'),
                            _l('Mã chứng từ'),
                            _l('Loại'),
                            _l('Trạng thái'),
                            _l('Người đề xuất'),
                            _l('Số tiền đề xuất'),
                            _l('Tổng chi'),
                            _l('Nội dung đề xuất'),
                            _l('Người đề xuất duyệt'),
                            _l('Trưởng phòng duyệt'),
                            _l('Tác vụ'),
                        ), 'suggestion'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    $('.table-suggestion').on('draw.dt', function() {
        var itemsTable = $(this).DataTable();
        var sums = itemsTable.ajax.json().sums;
        $('.dataTables_scrollFoot').find('tfoot').addClass('bold');
        $('.dataTables_scrollFoot').find('tfoot td').eq(1).html('<div class="text-center">Tổng</div>');
        $('.DTFC_LeftFootWrapper').css("background", "#ffff");
        $('.dataTables_scrollFoot').find('tfoot td').eq(6).html('<div class="text-right">' + sums.total + '</div>');
        $('.dataTables_scrollFoot').find('tfoot td').eq(7).html('<div class="text-right">' + sums.pay + '</div>');
    });
    $('.H_filter').click(function(e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-id');
        target.parent().parent().find('li').removeClass('active');
        target.parent().addClass('active');
        $('input[name="filterStatus"]').val(value);
        $('input[name="filterStatus"]').change();
    });
    var tAPI;
    $(function() {
        var CustomersServerParams = {
            'filterStatus': '[name="filterStatus"]',
            'staffid': '[name="staffid"]',
            'status': '[name="status"]',
            'status_dn': '[name="status_dn"]',
            'status_tp': '[name="status_tp"]',
            'search_date': '[name="search_date"]',
        };
        tAPI = initDataTableCustom('.table-suggestion', admin_url + 'suggestion/table_suggestion', [0], [0], CustomersServerParams, <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(1, 'desc'))); ?>);

        $.each(CustomersServerParams, function(filterIndex, filterItem) {
            $('' + filterItem).on('change', function() {
                tAPI.draw('page');
            });
        });
    });
    $(document).on('click', '.delete-reminders', function() {
        var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
        if (r == false) {
            return false;
        } else {
            $.get($(this).attr('href'), function(response) {
                alert_float(response.alert_type, response.message);
                $('.table-suggestion').DataTable().ajax.reload();
            }, 'json');
        }
        return false;
    });
    //end
    function var_status(status, id) {
        dataString = {
            id: id,
            status: status,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>suggestion/update_status",
            data: dataString,
            cache: false,
            success: function(response) {
                response = JSON.parse(response);
                tAPI.draw('page');
                alert_float(response.alert_type, response.message);
            }
        });
        return false;
    }

    function var_status_tp(status, id) {
        dataString = {
            id: id,
            status: status,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>suggestion/update_status_tp",
            data: dataString,
            cache: false,
            success: function(response) {
                response = JSON.parse(response);
                tAPI.draw('page');
                alert_float(response.alert_type, response.message);
            }
        });
        return false;
    }

    function unvar_status(status, id) {
        dataString = {
            id: id,
            status: status,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>suggestion/unupdate_status",
            data: dataString,
            cache: false,
            success: function(response) {
                response = JSON.parse(response);
                tAPI.draw('page');
                alert_float(response.alert_type, response.message);
            }
        });
        return false;
    }

    function unvar_status_tp(status, id) {
        dataString = {
            id: id,
            status: status,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>suggestion/unupdate_status_tp",
            data: dataString,
            cache: false,
            success: function(response) {
                response = JSON.parse(response);
                tAPI.draw('page');
                alert_float(response.alert_type, response.message);
            }
        });
        return false;
    }

    function ajaxSelectCallBack_hau(element, url, id, types = '') {
        if (id > 0) {
            $(element).val(id).select2({
                width: 'resolve',
                allowClear: true,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: site.base_url + url + '/' + $(element).val(),
                        dataType: "json",
                        success: function(data) {
                            callback(data.results[0]);
                        }
                    });
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: $('#type_items').val(),
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                ajax: {
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: $('#type_items').val(),
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    code_client: '',
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        }
    }
    $(function(e) {
        ajaxSelectCallBack_hau($('#staffid'), "admin/suggestion/SearchStaff", 0);
        search_daterangepicker();
        $('#status').select2({
            'allowClear': true
        });
        $('#status_dn').select2({
            'allowClear': true
        });
        $('#status_tp').select2({
            'allowClear': true
        });
    });
    var search_daterangepicker = () => {
        $('input[name="search_date"]').daterangepicker({
            opens: 'left',
            autoUpdateInput: false,
            isInvalidDate: false,
            "locale": {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": lang_daterangepicker.applyLabel,
                "cancelLabel": lang_daterangepicker.cancelLabel,
                "fromLabel": lang_daterangepicker.fromLabel,
                "toLabel": lang_daterangepicker.toLabel,
                "customRangeLabel": lang_daterangepicker.customRangeLabel,
                "daysOfWeek": lang_daterangepicker.daysOfWeek,
                "monthNames": lang_daterangepicker.monthNames
            },
        }, function(start, end, label) {});
        $('input[name="search_date"]').val('').datepicker("refresh");
        $('input[name="search_date"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            $("#search_date").trigger("change");
        });
        $('input[name="search_date"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $("#search_date").trigger("change");
        });
    };
</script>