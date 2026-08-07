<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    tr.success {
	    color: #24ad3c!important;
    }
    tr.success th{
	    color: #24ad3c!important;
	    background: #dff0d8!important;
    }
</style>
<link rel="stylesheet" href="<?= base_url('assets/treegrid/') ?>css/jquery.treegrid.css">

<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
                <div class="line-sp"></div>
                <a href="" onclick="add(''); return false;" class="btn btn-info mright5 test pull-right H_action_button hide">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('create_add_new'); ?></a>
                <div class="line-sp"></div>
                <a href="<?= base_url('admin/recommended_list/modal_excel_import') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                    <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('IMPORT EXCEL'); ?>
                </a>
                <a href="<?= base_url('admin/recommended_list/export_excel') ?>" class="btn btn-info pull-right mright10 H_action_button">
                    <i class="fa fa-download" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('EXPORT EXCEL'); ?>
                </a>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="">
                    <div class="">
                        <table id="data-table" class="table dataTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center"><?= lang('STT') ?></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"><?= lang('tnh_code_recommended_list') ?></th>
                                    <th class="text-center"><?= lang('tnh_name_recommended_list') ?></th>
                                    <th class="text-center"><?= lang('Tên công việc') ?></th>
                                    <th class="text-center"><?= lang('type') ?></th>
                                    <th class="text-center"><?= lang('Loại kế hoạch') ?></th>
                                    <th class="text-center"><?= lang('Phiếu yêu cầu') ?></th>
                                    <th class="text-center"><?= lang('action') ?></th>
                                    <th class="text-center"><?= lang('items') ?></th>
                                    <th class="text-center"><?= lang('process') ?></th>
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
<div id="modal"></div>
<?php init_tail(); ?>
<script>
    // $('body').on('click', '.H_filter', function(e) {
    //     $('.H_filter').parent('li').removeClass('active');
    //     $(this).parent('li').addClass('active');
    //     $('input[name="filterType"]').val($(this).attr('data-id')).trigger('change');
    //     oTable.draw();
    // });

    var oTable = '';

    $('#data-table tbody').on('click', 'td .rows-child', function() {
        var id = $(this).data('id');
        var tr = $(this).closest('tr');
        var row = oTable.row(tr);
        if (row.child.isShown()) {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            row.child.hide();
            tr.removeClass('shown');
        } else {
            $(this).removeClass('fa-caret-right');
            $(this).addClass('fa-caret-down');
            row.child(loadSubItems(row.data(), id)).show();
            tr.addClass('shown');
            $(`.table.table-child-one-${id}`).DataTable({
                filter: false,
                deferRender: false,
                scroller: false,
                order : false,
                searching : false,
                paging : false,
                info : false,
                "columnDefs": [
                    {
                        "targets": 7,
                        'sortable': false,
                        'searchable': false,
                        'visible': false,
                    }
                ]
            });
            $(`.table.table-child-one-${id}`).DataTable().column(7).visible( false );
            $('.table-loading').removeClass('table-loading');
        }
    });
    $('#data-table tbody').on('click', 'td .rows-child-2', function() {
        var id = $(this).data('id');
        var id_parent = $(this).data('parent');
        var tr = $(this).closest('tr');
        var row = $(`.table.table-child-one-${id_parent}`).DataTable().row(tr);
        if (row.child.isShown()) {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            $(this).removeClass('fa-caret-right');
            $(this).addClass('fa-caret-down');
            row.child(loadSubItemsChild(row.data(), id)).show();
            tr.addClass('shown');
            $(`.table.table-child-two-${id}`).DataTable({
                filter: false,
                deferRender: false,
                scroller: false,
                order : false,
                searching : false,
                paging : false,
                info : false,
                "columnDefs": [
                    {
                        "targets": 7,
                        'sortable': false,
                        'searchable': false,
                        'visible': false,
                    }
                ],
            });
            $('.table-loading').removeClass('table-loading');
        }
    });

    function loadSubItems(cData, id = 0) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        items = cData[9];
        process = cData[10];
        cHtml = '';
        
        // <td class="bold text-center"><?= lang('STT') ?></td>
        tr1 = `<tr class="success">
            <th class="bold text-center"></td>
            <th class="bold text-center"><?= lang('tnh_code_recommended_list') ?></td>
            <th class="bold text-center"><?= lang('tnh_name_recommended_list') ?></td>
            <th class="bold text-center"><?= lang('type') ?></td>
            <th class="bold text-center"><?= lang('note') ?></td>
            <th class="bold text-center"><?= lang('Có file excel') ?></td>
            <th class="bold text-center" style="width: 100px;"><?= lang('actions') ?></td>
        </tr>`;
        tablechild = `<div class="scrolling-stone pr-3 position-absolute h-100 w-100 overflow-auto max-height">
            <table class="table table-child-one-${id} table-bordered dataTable" style="width: 95% !important; float: right;">
                <thead>${tr1}</thead>
                <tbody>
                    ${items}
                </tbody>
            </table>
        </div>`;
        tablechild+="<br>";
        tr2 = `<tr class="success">
            <th class="bold text-center"><?= lang('Tên quy trình') ?></td>
            <th class="bold text-center"><?= lang('Quy trình người duyệt') ?></td>
            <th class="bold text-center"><?= lang('Mã vị trí') ?></td>
            <th class="bold text-center"><?= lang('Quy Chuẩn Công Việc') ?></td>
            <th class="bold text-center"><?= lang('Quy Chuẩn Kiểm Tra') ?></td>
            <th class="bold text-center"><?= lang('Quy Chuẩn Kiểm Soát Hoàn Thành') ?></td>
            <th class="bold text-center"><?= lang('Điểm trừ') ?></td>
            <th class="bold text-center"><?= lang('Điểm cộng') ?></td>
            <th class="bold text-center"><?= lang('Cảnh báo') ?></td>
        </tr>`;
        tablechild += `<div class="scrolling-stone pr-3 position-absolute h-100 w-100 overflow-auto max-height">
            <table class="table table-child-process-${id} table-bordered dataTable" style="width: 95% !important; float: right;">
                <thead>${tr2}</thead>
                <tbody>
                    ${process}
                </tbody>
            </table>
        </div>`;
        return tablechild;
    }


    function loadSubItemsChild(cData, id = 0) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        items = b64DecodeUnicode(cData[6]);
        cHtml = '';
        // <td class="bold text-center"><?= lang('STT') ?></td>
       var trHeader = `<tr class="success">
            <td class="bold text-center"></td>
            <td class="bold text-center"><?= lang('tnh_code_recommended_list') ?></td>
            <td class="bold text-center"><?= lang('tnh_name_recommended_list') ?></td>
            <td class="bold text-center"><?= lang('type') ?></td>
            <td class="bold text-center"><?= lang('note') ?></td>
            <td class="bold text-center"><?= lang('Có file excel') ?></td>
            <td class="bold text-center" style="width: 100px;"><?= lang('actions') ?></td>
        </tr>`;
    
        return `<div class="scrolling-stone pr-3 position-absolute h-100 w-100 overflow-auto max-height">
            <table class="table table-bordered dataTable table-child-two-${id}" style="width: 95% !important; float: right;">
                <thead>${trHeader}</thead>
                <tbody>
                    ${items}
                </tbody>
            </table>
        </div>`;
    }
    function b64DecodeUnicode(str) {
        // Going backwards: from bytestream, to percent-encoding, to original string.
        return decodeURIComponent(atob(str).split('').map(function(c) {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        }).join(''));
    }

    $(function() {
        var fnserverparams = {
            'filterType': '[name="filterType"]',
        };

        oTable = tnhInitDataTable('#data-table', '', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/recommended_list/getRecommendedList') ?>',
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
            "columnDefs": [
                {
                    "targets": 7,
                    'sortable': false,
                    'searchable': false,
                    'visible': false,
                },
                {
                    "targets": 9,
                    'sortable': false,
                    'searchable': false,
                    'visible': false,
                },
                {
                    "targets": 10,
                    'sortable': false,
                    'searchable': false,
                    'visible': false,
                }
            ],
        });

        // _validate_form($('form'), {
        //     code: 'required',
        //     name: 'required'
        // }, manage);

        // function manage(form) {
        //     var data = $(form).serialize();
        //     var url = form.action;
        //     $.post(url, data).done(function(response) {
        //         response = JSON.parse(response);
        //         if (response.success == true) {
        //             alert_float('success', response.message);
        //         } else {
        //             alert_float('danger', response.message);
        //         }
        //         // location.reload();
        //         oTable.draw();
        //         $('#type').modal('hide');
        //     });
        //     return false;
        // }
    });

    $('#data-table').on('draw.dt', function() {
        var total_tr = $('#data-table').find('tbody').find('tr');
        $.each(total_tr, function(i, v) {
            $("#category_recommended_id_" + i).select2({
                'allowClear': true
            });
        });
    });

    // function add(id) {
    //     $('#modal').html('');
    //     $.get(admin_url + 'measurement/modal_add/' + id).done(function(response) {
    //         $('#modal').html(response);
    //         $('#modal_add').modal({
    //             show: true,
    //             backdrop: 'static'
    //         });
    //         // $('#modal_add select[name="type"]').selectpicker('refresh');
    //         init_selectpicker();
    //         init_datepicker();
    //     }).fail(function(error) {
    //         var response = JSON.parse(error.responseText);
    //         alert_float('danger', response.message);
    //     });
    // }

    // function deleting(id = "") {
    //     var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
    //     if (r == false) {
    //         return false;
    //     } else {
    //         $.get(admin_url + 'Measurement/delete/' + id, function(response) {
    //             if (response.success) {
    //                 alert_float('success', response.message);
    //                 oTable.draw();
    //             } else {
    //                 alert_float('danger', response.message);
    //             }
    //         }, 'json');
    //     }
    //     return false;
    // };

    $(document).on('change', '.category_recommended_id', function(e) {
        var recommended_id = $(this).attr('data-recommended_id');
        var category_recommended_id = $(this).val();
        var athis = $(this);
        var data = {};
        data['recommended_id'] = recommended_id;
        data['category_recommended_id'] = category_recommended_id;
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url + 'recommended_list/setCategoryRecomended', data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == 0) {
                athis.select2('val', '');
            }
            alert_float(response.alert_type, response.message);
        });
    });
</script>