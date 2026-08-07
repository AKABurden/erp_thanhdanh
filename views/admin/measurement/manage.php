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
                <a href="" onclick="add(''); return false;" class="btn btn-info mright5 test pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('create_add_new'); ?></a>
                <div class="line-sp"></div>
                <a href="<?= base_url('admin/measurement/modal_excel_import') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                    <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('IMPORT EXCEL'); ?>
                </a>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- data table -->
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- tab type -->
                        <div class="btn-group mbot10" style="width: 100%;">
                            <div class="horizontal-scrollable-tabs">
                                <div class="scroller scroller-left arrow-left disabled" style="display: block;">
                                    <i class="fa fa-angle-left"></i>
                                </div>
                                <div class="scroller scroller-right arrow-right" style="display: block;">
                                    <i class="fa fa-angle-right"></i>
                                </div>
                                <div class="horizontal-tabs">
                                    <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                        <li class="active">
                                            <a class="H_filter" data-id="1">
                                                <?= _l('tnh_longs') ?> <b class="filter_1"></b>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="H_filter" data-id="2">
                                                <?= _l('tnh_wide') ?> <b class="filter_2"></b>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="H_filter" data-id="3">
                                                <?= _l('tnh_height').'(mm)' ?> <b class="filter_3"></b>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="filterType" id="filterType" value="1">
                        <!-- table -->
                        <table id="data-table" class="table dt-tnh table-hover table-cost-new" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center"><?= lang('STT') ?></th>
                                    <th class="text-center"><?= lang('measurement_value').'(mm)' ?></th>
                                    <th class="text-center"><?= lang('action') ?></th>
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
    $('body').on('click', '.H_filter', function(e) {
        $('.H_filter').parent('li').removeClass('active');
        $(this).parent('li').addClass('active');
        $('input[name="filterType"]').val($(this).attr('data-id')).trigger('change');
        oTable.draw();
    });

    $(function() {
        var fnserverparams = {
            'filterType': '[name="filterType"]',
        };

        oTable = tnhInitDataTable('#data-table', '<?= site_url('admin/measurement/table') ?>', {
            'order': [
                [1, 'asc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/measurement/table') ?>',
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
            "columnDefs": [],
        });


        _validate_form($('form'), {
            code: 'required',
            name: 'required'
        }, manage);

        function manage(form) {
            var data = $(form).serialize();
            var url = form.action;
            $.post(url, data).done(function(response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }
                // location.reload();
                oTable.draw();
                $('#type').modal('hide');
            });
            return false;
        }
    });

    function add(id) {
        $('#modal').html('');
        $.get(admin_url + 'measurement/modal_add/' + id).done(function(response) {
            $('#modal').html(response);
            $('#modal_add').modal({
                show: true,
                backdrop: 'static'
            });
            // $('#modal_add select[name="type"]').selectpicker('refresh');
            init_selectpicker();
            init_datepicker();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    function deleting(id = "") {
        var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
        if (r == false) {
            return false;
        } else {
            $.get(admin_url + 'Measurement/delete/' + id, function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    oTable.draw();
                } else {
                    alert_float('danger', response.message);
                }
            }, 'json');
        }
        return false;
    };
</script>