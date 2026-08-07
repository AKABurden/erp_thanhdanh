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
                <!-- <a href="<?= admin_url('costs/detail') ?>" class="btn btn-info mright5 test pull-right H_action_button">
                    <?php echo _l('ch_plan'); ?></a> -->
                <a href="<?= base_url('admin/collect_categories/modal_excel_import') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                    <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('IMPORT EXCEL'); ?>
                </a>
                <!-- <a href="<?= base_url('admin/costs/excel_export') ?>" class="btn btn-info pull-right mright10 H_action_button">
                    <i class="fa fa-download" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('EXPORT EXCEL'); ?>
                </a> -->
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
                        <table id="table-costs" class="table dt-tnh table-hover table-cost-new" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center"><?= lang('STT') ?></th>
                                    <th class="text-center"><?= lang('colcat_code') ?></th>
                                    <th class="text-center"><?= lang('colcat_name') ?></th>
                                    <th class="text-center"><?= lang('colcat_parent_code') ?></th>
                                    <th class="text-center"><?= lang('colcat_parent_name') ?></th>
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
    $(function() {
        var fnserverparams = {};

        oTable = tnhInitDataTable('#table-costs', '<?= site_url('admin/collect_categories/table') ?>', {
            'order': [
                [1, 'asc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/collect_categories/table') ?>',
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
        }, manage_costs);

        function manage_costs(form) {
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
        $.get(admin_url + 'collect_categories/add_modal/' + id).done(function(response) {
            $('#modal').html(response);
            $('#add_modal').modal({
                show: true,
                backdrop: 'static'
            });
            // $('#add_modal select[name="type"]').selectpicker('refresh');
            init_selectpicker();
            init_datepicker();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    function edit_collect_categories(id, code, name, parent_id, cType) {
        $('#add_modal').modal('show');
        // $('.edit-title').removeClass('hide');
        // $('.add-title').addClass('hide');
        $('#additional').append(hidden_input('id', id));
        $('#add_modal input[name="code"]').val(code);
        $('#add_modal input[name="name"]').val(name);
        $('#add_modal select[name="type"]').val(cType).selectpicker('refresh');
        $('#add_modal').find('#costs_parent').selectpicker('val', parent_id);
        jQuery('#id_type').prop('action', admin_url + 'costs/update/' + id);
        var costs_parent = $('#costs_parent');
        costs_parent.find('option:gt(0)').remove();
        costs_parent.selectpicker('refresh');
        if (costs_parent.length) {
            $.ajax({
                    url: admin_url + 'costs/get_parent/' + id,
                    dataType: 'json',
                })
                .done(function(data) {

                    $.each(data.data, function(key, value) {
                        var text = '';
                        if (data.costs_parent == value.id) {
                            text = 'selected="selected"';
                        }
                        costs_parent.append('<option ' + text + ' value="' + value.id + '">' + value.name + '</option>');
                    });

                    costs_parent.selectpicker('refresh');
                });
        }
    }


    function delete_collect_categories(id = "") {
        var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
        if (r == false) {
            return false;
        } else {
            $.get(admin_url + 'Collect_categories/delete/' + id, function(response) {
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