<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
</style>
<link rel="stylesheet" href="<?= base_url('assets/treegrid/') ?>css/jquery.treegrid.css">

<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <div class="line-sp"></div>
                <a href="" onclick="add(''); return false;" class="btn btn-info mright5 test pull-right H_action_button hide">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('create_add_new'); ?></a>
                <div class="line-sp"></div>
                <a href="<?= base_url('admin/recommended_list/modal_excel_import') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                    <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('IMPORT EXCEL'); ?>
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
                                    <th class="text-center"><?= lang('type') ?></th>
                                    <th class="text-center"><?= lang('note') ?></th>
                                    <th class="text-center"><?= lang('action') ?></th>
                                    <th class="text-center"><?= lang('items') ?></th>
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
        var tr = $(this).closest('tr');
        var row = oTable.row(tr);
        if (row.child.isShown()) {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            $(this).removeClass('fa-caret-right');
            $(this).addClass('fa-caret-down');
            row.child(loadSubItems(row.data())).show();
            tr.addClass('shown');
        }
    });

    function loadSubItems(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        items = cData[7];
        cHtml = '';
        
        // <td class="bold text-center"><?= lang('STT') ?></td>
        tr1 = `<tr class="success">
            <td class="bold text-center"><?= lang('tnh_code_recommended_list') ?></td>
            <td class="bold text-center"><?= lang('tnh_name_recommended_list') ?></td>
            <td class="bold text-center"><?= lang('type') ?></td>
            <td class="bold text-center"><?= lang('note') ?></td>
            <td class="bold text-center" style="width: 100px;"><?= lang('actions') ?></td>
        </tr>`;

        return `<div class="scrolling-stone pr-3 position-absolute h-100 w-100 overflow-auto max-height">
            <table class="table table-bordered dataTable" style="width: 90% !important; float: right;">
                <tbody>
                    ${tr1}
                    ${items}
                </tbody>
            </table>
        </div>`;
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
                    "targets": 6,
                    'sortable': false,
                    'searchable': false,
                },
                {
                    "targets": 7,
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
</script>