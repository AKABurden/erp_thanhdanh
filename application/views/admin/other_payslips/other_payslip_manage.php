<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .bg-group {
        background: #daeaf9;
    }
    .popover {
        max-width: 2500px;
        height: 140px;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right mleft5" href="javascript:void(0)">Xuất Excel</a>
                <a class="search_person btn btn-info pull-right mleft5 H_action_button option_barcode">
                    <span style="font-size: 16px;margin-bottom: 3px;" class="lnr lnr-funnel"></span>
                    <?php echo _l('ch_seach_statistical'); ?>
                </a>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="row" style="margin-bottom:5px">
                                <div id="search-tnh" class="collapse in" aria-expanded="true">
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-other-payslip-manager" class="table dt-tnh table-other-payslip-manager-new" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('Mã Chứng Từ') ?></th>
                                        <th class="text-center"><?= lang('Ngày Lập') ?></th>
                                        <th class="text-center"><?= lang('Nhóm Chi') ?></th>
                                        <th class="text-center"><?= lang('Chi Tiết') ?></th>
                                        <th class="text-center"><?= lang('Nhà Cung Cấp') ?></th>
                                        <th class="text-center"><?= lang('Thông Tin Đơn Vị Thụ Hưởng') ?></th>
                                        <th class="text-center"><?= lang('HTTT') ?></th>
                                        <th class="text-center"><?= lang('Loại Chi Phí Quản Lý') ?></th>
                                        <th class="text-center"><?= lang('Tổng Số Tiền') ?></th>
                                        <th class="text-center"><?= lang('QR') ?></th>
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
</div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    var oTable = '';

    var fnserverparams = {
        'objects_idd': '[name="objects_idd"]',
        'objects_ids': '[name="objects_ids"]',
        'objects_texts': '[name="objects_texts"]',
        'search_date': '[name="search_date"]',
    };
    oTable = tnhInitDataTable('#table-other-payslip-manager',
        '<?= site_url('admin/other_payslips/getOtherPayslipManager') ?>', {
            'order': [
                [0, 'desc'],
            ],
            'fixedHeader': {
                header: true,
            },
            scrollX:true,
            "ajax": {
                "url": '<?= site_url('admin/other_payslips/getOtherPayslipManager') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
            "createdRow": function(row, data, index) {
            },
            "columnDefs": [
            ],
        });

    $(document).ready(function() {
        init_ajax_searchs('purchases', '#search_code');
        ajaxSelectCallBack($('#custom_item_select'), "<?= admin_url('purchases/SearchItems') ?>", 0);
    });
    
    function ajaxSelectCallBack(element, url, id, types = '') {
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: url + '/' + id + '/' + types,
                        dataType: "json",
                        success: function(data) {
                            callback(data.results[0].children[0]);
                        }
                    });
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: -1,
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
                formatResult: repoFormatSelection_ch,
                formatSelection: repoFormatSelection_ch,
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
                    url: url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: -1,
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
                formatResult: repoFormatSelection_ch,
                formatSelection: repoFormatSelection_ch,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        }
    }

    function init_ajax_searchs(e, t, a, i) {
        var n = $("body").find(t);
        var h = t;
        if (n.length) {
            var s = {
                ajax: {
                    url: void 0 === i ? admin_url + "misc/get_relation_data" : i,
                    data: function() {
                        var t = {
                            [csrfData.token_name]: csrfData.hash
                        };
                        return t.type = e, t.rel_id = "", t.q = "{{{q}}}", void 0 !== a && jQuery.extend(t, a), t
                    }
                },
                locale: {
                    emptyTitle: app.lang.search_ajax_empty,
                    statusInitialized: app.lang.search_ajax_initialized,
                    statusSearching: app.lang.search_ajax_searching,
                    statusNoResults: app.lang.not_results_found,
                    searchPlaceholder: app.lang.search_ajax_placeholder,
                    currentlySelected: app.lang.currently_selected
                },
                requestDelay: 500,
                cache: !1,
                preprocessData: function(e) {
                    var t = [];
                    var _temp_all = {
                        'value': 'all',
                        'text': 'Tất cả',
                    };
                    t.push(_temp_all);
                    for (var a = e.length, i = 0; i < a; i++) {
                        var n = {
                            value: e[i].id,
                            text: e[i].name
                        };
                        t.push(n)
                    }
                    return t;
                },
                preserveSelectedPosition: "after",
                preserveSelected: !0
            };
            n.data("empty-title") && (s.locale.emptyTitle = n.data("empty-title")), n.selectpicker().ajaxSelectPicker(s);
        }
    }

    var inner_popover_template = '<div class="popover" style="width:1300px;"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';
    $(document).on('click', '.search_person', function(e) {
        $('.add_contact_person_invoice').popover('hide');
        var dropdown_menu = '\
            <div class="col-md-2 col-xs-2 border-right">\
                <h4 class="bold text-muted all_orther">\
                    0\
                </h4>\
                <span style="color:red" class="text-danger">\
                    <?= _l('Tổng phiếu') ?>\
                </span>\
            </div>\
            <div class="col-md-2 col-xs-2 border-right">\
                <h4 class="bold text-muted payment">\
                    0\
                </h4>\
                <span style="color:red" class="text-danger">\
                    <?= _l('Tổng tiền') ?>\
                </span>\
            </div>\
            <div class="col-md-2">\
            <?php $list_objectss = array(array('id' => 1, 'name' => _l('ch_IN_client')), array('id' => 2, 'name' => _l('ch_IN_suppliers')), array('id' => 3, 'name' => _l('ch_IN_staff')),  array('id' => 4, 'name' => _l('ch_IN_other')),); ?>\
            <?php echo render_select('objects_idd', $list_objectss, array('id', 'name'), 'Loại đối tượng'); ?>\
            </div>\
            <div class="col-md-3 append_id_objects">\
            <div class="form-group id ">\
                    <label for="objects_ids" class="control-label"><?= _l('ch_list_objects') ?></label>\
                    <input data-placeholder="<?= _l('ch_list_objects') ?>" name="objects_ids" style="width: 100%" id="objects_ids">\
                </div>\
            </div>\
            <div class="col-md-3">\
            <div class="form-group">\
               <label for="search_date" class="control-label"><?= _l('ch_date_p') ?></label>\
               <div class="input-group">\
                  <input type="text" id="search_date" name="search_date" class="form-control search_date" aria-invalid="false">\
                  <div class="input-group-addon">\
                     <i class="fa fa-calendar calendar-icon"></i>\
                  </div>\
               </div>\
            </div>\
            </div><br>';
        $(this).popover({
            html: true,
            container: 'body',
            placement: "bottom",
            trigger: 'click focus',
            // trigger: 'focus',
            title: '<?= _l('tnh_seach_statistical') ?><button type="button" class="close close_pay">&times;</button>',
            content: function() {
                return dropdown_menu;
            },
            template: inner_popover_template
        });

        init_selectpicker();
        ch_daterangepicker();
        ajaxSelectCallBacks($('#objects_ids'), "<?= admin_url('other_payslips/SearchClient') ?>", 0);
        oTable.draw('page');
        $("#objects_idd").change(function() {
            $('#objects_ids').selectpicker('refresh');
            var id = $('#objects_idd').val();
            var id_objects_id = 0;
            if (id == 1) {
                var html = '<div class="form-group id ">\
                              <label for="objects_ids" class="control-label"><?= _l('ch_list_objects') ?></label>\
                              <input data-placeholder="Khách hàng" name="objects_ids" style="width: 100%" value="' + id_objects_id + '" id="objects_ids">\
                          </div>';
                $('.append_id_objects').html(html);
                ajaxSelectCallBacks($('#objects_ids'), "<?= admin_url('other_payslips/SearchClient') ?>", id_objects_id);
            } else if (id == 2) {
                var html = '<div class="form-group id ">\
                              <label for="objects_ids" class="control-label"><?= _l('ch_list_objects') ?></label>\
                              <input data-placeholder="Nhà cung cấp" name="objects_ids" style="width: 100%" value="' + id_objects_id + '" id="objects_ids">\
                          </div>';
                $('.append_id_objects').html(html);
                ajaxSelectCallBacks($('#objects_ids'), "<?= admin_url('other_payslips/SearchClient') ?>", id_objects_id);
            } else if (id == 3) {
                var html = '<div class="form-group id ">\
                              <label for="objects_ids" class="control-label"><?= _l('ch_list_objects') ?></label>\
                              <input data-placeholder="Nhân viên" name="objects_ids" style="width: 100%" value="' + id_objects_id + '" id="objects_ids">\
                          </div>';
                $('.append_id_objects').html(html);
                ajaxSelectCallBacks($('#objects_ids'), "<?= admin_url('other_payslips/SearchClient') ?>", id_objects_id);
            } else if (id == 4) {

                var html1 = '<div class="form-group id">\
                              <label for="objects_texts" class="control-label"><small class="req text-danger">* </small><?= _l('ch_list_objects') ?></label>\
                              <input type="text" id="objects_texts" name="objects_texts" class="form-control objects_texts" value="<?= (!empty($items) ? $items->objects_texts : '') ?>">\
                      </div>';
                $('.append_id_objects').html(html1);
            }
        });
    });
    $(document).on('click', '.close', function(e) {
        $('.search_person').popover('hide');
    });
    $(document).on('change', '#objects_idd', function(e) {
        oTable.draw('page');
    });
    $(document).on('change', '#objects_ids', function(e) {
        oTable.draw('page');
    });
    $(document).on('change', '#search_date', function(e) {
        oTable.draw('page');
    });
    $(document).on('change', '#objects_texts', function(e) {
        oTable.draw('page');
    });
    var ch_daterangepicker = () => {
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

    function ajaxSelectCallBacks(element, url, id, types = '') {
        console.log(id);
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: url + '/' + id + '/' + $('#objects_idd').val(),
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
                            type: $('#objects_idd').val(),
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
                formatResult: repoFormatSelections,
                formatSelection: repoFormatSelections,
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
                    url: url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: $('#objects_idd').val(),
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
                formatResult: repoFormatSelections,
                formatSelection: repoFormatSelections,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        }
    }

    function repoFormatSelections(state) {
        var id = $('#objects').val();
        if (id == 3) {
            return state.text;
        }
        return '[' + state.code_client + '] ' + state.text;
    }

    function exportExcel() {
        objects_idd = $('[name="objects_idd"]').val();
        objects_ids = $('[name="objects_ids"]').val();
        objects_texts = $('[name="objects_texts"]').val();
        search_date = $('[name="search_date"]').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/other_payslips/exportExcelManager',
            data: {
                csrf_token_name: hash,
                objects_idd: objects_idd,
                objects_ids: objects_ids,
                objects_texts: objects_texts,
                search_date: search_date,
                export_excel: 1,
                type: 1,
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
</script>