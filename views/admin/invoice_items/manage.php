<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .table-invoice-items tr td:nth-child(4) {
        max-width: 250px;
        min-width: 250px;
        white-space: unset;
    }

    .table-invoice-items tr td:nth-child(7) {
        max-width: 300px;
        min-width: 300px;
        white-space: unset;
    }

    .table-striped tbody tr:not(:last-child) {
        border-bottom: 2px solid #eaeaea;
    }

    .td-input-field {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
    }

    .delete_input_field i {
        color: #949494;
        font-size: 1.5em;
    }

    .delete_input_field i:hover {
        color: #000;
        cursor: pointer;
    }

    .panel_box {
        margin: 0;
        box-shadow: 0 3px 1px -2px rgba(0, 0, 0, .2), 0 2px 2px 0 rgba(0, 0, 0, .14), 0 1px 5px 0 rgba(0, 0, 0, .12);
    }

    .head-setting {
        font-weight: 500;
    }

    .line-head-setting {
        border-bottom: 1px solid #ccc;
    }

    .table-invoice-items thead tr th {
        text-align: center;
    }

    .table-invoice-items tr td:nth-child(2) {
        min-width: 70px;
        white-space: unset;
        text-align: center;
    }

    .table-invoice-items tr td:nth-child(3) {
        min-width: 120px;
        white-space: unset;
        text-align: center;
    }

    .table-invoice-items tr td:nth-child(5) {
        min-width: 150px;
        white-space: unset;
    }

    .table-invoice-items tr td:nth-child(4) {
        min-width: 100px;
        white-space: unset;
    }

    .table-invoice-items tr td:nth-child(6) {
        min-width: 50px;
        white-space: unset;
        text-align: center;
    }

    .table-invoice-items tr td:nth-child(7) {
        min-width: 70px;
        white-space: unset;
        text-align: center;
    }

    .table-invoice-items tr td:nth-child(8) {
        min-width: 70px;
        white-space: unset;
        text-align: center;
    }

    .table-invoice-items tr td:nth-child(9) {
        min-width: 100px;
        white-space: unset;
    }

    .table-invoice-items tr td:nth-child(10) {
        min-width: 100px;
        white-space: unset;
    }

    .table-invoice-items tr td:nth-child(11) {
        min-width: 130px;
        white-space: unset;
    }

    .table-invoice-items tr td:nth-child(12) {
        min-width: 100px;
        white-space: unset;
        text-align: center;
    }

    .table-invoice-items tr td:nth-child(13) {
        min-width: 120px;
        white-space: unset;
    }

    .table-invoice-items tr td:nth-child(14) {
        min-width: 140px;
        white-space: unset;
    }

    .table-invoice-items tr td:nth-child(15) {
        min-width: 80px;
        white-space: unset;
        text-align: center;
    }

    .table-invoice-items tr td:nth-child(16) {
        min-width: 200px;
        white-space: unset;
        text-align: center;
    }

    .ch_ch_hover:hover {
        cursor: pointer;
    }

    .table .tooltip-inner {
        min-width: 300px;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="dropdown pull-right">
                <button class="btn btn-info pull-right H_action_button dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                    <?= lang('actions') ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 200px;">
                    <?php if (has_permission('invoice_items', '', 'create')) { ?>
                        <li>
                            <a href="<?php echo admin_url('invoice_items/import'); ?>" class=""><i class="fa fa-upload"></i> <?php echo _l('Import excel'); ?></a>
                        </li>
                    <?php } ?>
                    <?php if (is_admin()) { ?>
                        <li>
                            <a href="#" class=" option_barcode" data-toggle="modal" data-target="#print_barcode"><i class="fa fa-barcode"></i> <?php echo _l('print_barcode'); ?></a>
                        </li>
                        <li>
                            <a href="#" class="" data-toggle="modal" data-target="#brand"><i class="fa fa-align-justify"></i> <?php echo _l('item_brand'); ?></a>
                        </li>
                        <li>
                            <a href="#" class="" data-toggle="modal" data-target="#groups"><i class="fa fa-object-group"></i> <?php echo _l('item_groups'); ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <?php if (has_permission('invoice_items', '', 'create')) { ?>

                <a href="<?php echo admin_url('invoice_items/item'); ?>" class="btn btn-info mright5 test pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('create_add_new'); ?>
                </a>
            <?php } ?>

        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (has_permission('items', '', 'delete')) { ?>
                            <a href="#" data-toggle="modal" data-table=".table-invoice-items" data-target="#items_bulk_actions" class="hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>
                            <div class="modal fade bulk_actions" id="items_bulk_actions" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php if (has_permission('leads', '', 'delete')) { ?>
                                                <div class="checkbox checkbox-danger">
                                                    <input type="checkbox" name="mass_delete" id="mass_delete">
                                                    <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                                            <a href="#" class="btn btn-info" onclick="items_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                                        </div>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                            <!-- /.modal -->
                        <?php } ?>
                        <?php hooks()->do_action('before_items_page_content'); ?>
                        <?php
                        $table_data = [];

                        if (has_permission('invoice_items', '', 'delete')) {
                            $table_data[] = '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="invoice-items"><label></label></div>';
                        }

                        $table_data = array_merge($table_data, array(
                            _l('item_avatar'),
                            _l('ch_categories'),
                            _l('item_code'),
                            _l('item_name'),
                            _l('tnh_dvt'),
                            _l('ch_warehouse_reports'),
                            _l('ch_color'),
                            _l('ch_packaging'),
                            _l('item_price'),
                            _l('item_product_features'),
                            _l('leads_dt_assigned'),
                            _l('item_group_id'),
                            _l('minimum_quantity'),
                            _l('project_activity'),
                            _l('promotion_type_sales_gift'),
                        ));

                        $cf = get_custom_fields('items');
                        foreach ($cf as $custom_field) {
                            array_push($table_data, $custom_field['name']);
                        }
                        render_datatable($table_data, 'invoice-items'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/invoice_items/item'); ?>
<div class="modal fade" id="brand" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('item_brand'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <?php if (has_permission('items', '', 'create')) { ?>
                    <div class="input-group">
                        <input type="text" name="item_brand_name" id="item_brand_name" class="form-control" placeholder="<?php echo _l('item_brand_name'); ?>">
                        <span class="input-group-btn">
                            <button class="btn btn-info p7" type="button" id="new-item-brand-insert"><?php echo _l('new_item_brand'); ?></button>
                        </span>
                    </div>
                    <hr />
                <?php } ?>
                <div class="row">
                    <div class="container-fluid ">
                        <?php
                        $table_data = [];
                        $table_data = array_merge($table_data, array(
                            _l('id'),
                            _l('item_brand_name'),
                            _l('ch_option')
                        ));
                        render_datatable($table_data, 'items-brands dont-responsive-table'); ?>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="groups" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('item_groups'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <?php if (has_permission('items', '', 'create')) { ?>
                    <div class="input-group">
                        <input type="text" name="item_group_name" id="item_group_name" class="form-control" placeholder="<?php echo _l('item_group_name'); ?>">
                        <span class="input-group-btn">
                            <button class="btn btn-info p7" type="button" id="new-item-group-insert"><?php echo _l('new_item_group'); ?></button>
                        </span>
                    </div>
                    <hr />
                <?php } ?>
                <div class="row">
                    <div class="container-fluid">
                        <?php
                        $table_data = [];
                        $table_data = array_merge($table_data, array(
                            _l('id'),
                            _l('item_group_name'),
                            _l('ch_option')
                        ));
                        render_datatable($table_data, 'items-groups dont-responsive-table'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="print_barcode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('print_barcode'); ?>
                </h4>
            </div>
            <?php echo form_open('admin/invoice_items/pdf', array('id' => 'print_pdf')); ?>
            <div class="modal-body" style="background: #f1f1f1">
                <div class="col-md-8">
                    <div class="panel_s panel_box">
                        <div class="panel-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= _l('item_name') ?></th>
                                        <th style="text-align: center;">
                                            <?= _l('item_quantity') ?>
                                            (<span class="checkbox-primary">
                                                <label for="check_change_all" data-toggle="tooltip" data-original-title="" title="">
                                                    <?= _l('all') ?>:
                                                </label>
                                                <input type="checkbox" id="check_change_all" name="check_change_all" value="1">
                                            </span>)
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="content-print">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel_s panel_box">
                        <div class="panel-body">
                            <div class="print-size">
                                <div class="head-setting line-head-setting">
                                    <?= _l('printing_size') ?>
                                </div>
                                <div class="radio radio-primary mtop10">
                                    <input type="radio" name="type_size" value="0" checked>
                                    <label for="type_size">1 <?= _l('stamp') ?></label>
                                </div>
                                <div class="radio radio-primary mtop10">
                                    <input type="radio" name="type_size" value="1">
                                    <label for="type_size">2 <?= _l('stamp') ?></label>
                                </div>
                                <div class="radio radio-primary mtop10">
                                    <input type="radio" name="type_size" value="2">
                                    <label for="type_size">3 <?= _l('stamp') ?></label>
                                </div>
                                <div class="radio radio-primary mtop10">
                                    <input type="radio" name="type_size" value="3">
                                    <label for="type_size">100 <?= _l('stamp') ?></label>
                                </div>
                            </div>
                            <div class="print-show">
                                <div class="head-setting line-head-setting">
                                    <?= _l('show') ?>
                                </div>
                                <div class="radio radio-primary mtop10">
                                    <input type="radio" name="type_show" value="0">
                                    <label for="type_show"><?= _l('only_code') ?></label>
                                </div>
                                <div class="radio radio-primary mtop10">
                                    <input type="radio" name="type_show" value="1">
                                    <label for="type_show"><?= _l('code_and_name') ?></label>
                                </div>
                                <div class="radio radio-primary mtop10">
                                    <input type="radio" name="type_show" value="2">
                                    <label for="type_show"><?= _l('code_and_amount') ?></label>
                                </div>
                                <div class="radio radio-primary mtop10">
                                    <input type="radio" name="type_show" value="3" checked>
                                    <label for="type_show"><?= _l('full_show') ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer" style="background: #f1f1f1">
                <button type="submit" class="btn btn-info" target="_blank"><?php echo _l('print_barcode'); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<div id="items_view_data"></div>
<?php init_tail(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    function int_items_view(id = null) {
        $('#items_view_data').html('');
        $.get(admin_url + 'invoice_items/int_items_view/' + id).done(function(response) {
            $('#items_view_data').html(response);
            $('#items_view').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#items_view', function() {
        $('#items_view_data').html('');
    });
    var tAPI;
    $(function() {

        var notSortableAndSearchableItemColumns = [];
        <?php if (has_permission('items', '', 'delete')) { ?>
            notSortableAndSearchableItemColumns.push(0);
        <?php } ?>
        tAPI = initDataTableCustom('.table-invoice-items', admin_url + 'invoice_items/table', [0], [0], 'undefined', <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(1, 'desc'))); ?>, fixedColumns = {
            leftColumns: 3,
            rightColumns: 0
        });
        // initDataTable('.table-invoice-items', admin_url+'invoice_items/table', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns,'undefined',[1,'desc']);
        initDataTable('.table-items-brands', admin_url + 'invoice_items/table_brands', [0], [0], 'undefined', [0, 'desc']);
        initDataTable('.table-items-groups', admin_url + 'invoice_items/table_groups', [0], [0], 'undefined', [0, 'desc']);

        if (get_url_param('groups_modal')) {
            // Set time out user to see the message
            setTimeout(function() {
                $('#groups').modal('show');
            }, 1000);
        }
        $('#new-item-brand-insert').on('click', function() {
            var brand_name = $('#item_brand_name').val();
            if (brand_name != '') {
                $.post(admin_url + 'invoice_items/add_brand', {
                    name: brand_name,
                    [csrfData['token_name']]: csrfData['hash']
                }).done(function(response) {
                    $('#item_brand_name').val('');
                    response = JSON.parse(response);
                    alert_float(response.alert_type, response.message);
                    $('.table-items-brands').DataTable().ajax.reload();
                });
            }
        });
        $('#new-item-group-insert').on('click', function() {
            var group_name = $('#item_group_name').val();
            if (group_name != '') {
                $.post(admin_url + 'invoice_items/add_group', {
                    name: group_name,
                    [csrfData['token_name']]: csrfData['hash']
                }).done(function(response) {
                    $('#item_group_name').val('');
                    response = JSON.parse(response);
                    alert_float(response.alert_type, response.message);
                    $('.table-items-groups').DataTable().ajax.reload();
                });
            }
        });
        $('body').on('click', '.edit-item-brand', function(e) {
            e.preventDefault();
            var tr = $(this).parents('tr');
            tr.find('.brand_name_plain_text').toggleClass('hide');
            tr.find('.brand_edit').toggleClass('hide');
            tr.find('.brand_edit #brand_name').val(tr.find('.brand_name_plain_text').text());
        });
        $('body').on('click', '.edit-item-group', function(e) {
            e.preventDefault();
            var tr = $(this).parents('tr'),
                group_id = tr.attr('data-group-row-id');
            tr.find('.group_name_plain_text').toggleClass('hide');
            tr.find('.group_edit').toggleClass('hide');
            tr.find('.group_edit #group_name').val(tr.find('.group_name_plain_text').text());
        });
        $('body').on('click', '.update-item-brand', function() {
            var tr = $(this).parents('tr');
            var brand_id = tr.find('.brand_edit #brand_id').val();
            name = tr.find('.brand_edit #brand_name').val();
            if (name != '') {
                $.post(admin_url + 'invoice_items/update_brand/' + brand_id, {
                    name: name,
                    [csrfData['token_name']]: csrfData['hash']
                }).done(function(response) {
                    response = JSON.parse(response);
                    alert_float(response.alert_type, response.message);
                    $('.table-items-brands').DataTable().ajax.reload();
                });
            }
        });
        $('body').on('click', '.update-item-group', function() {
            var tr = $(this).parents('tr');
            var group_id = tr.find('.group_edit #group_id').val();
            var name = tr.find('.group_edit #group_name').val();
            if (name != '') {
                $.post(admin_url + 'invoice_items/update_group/' + group_id, {
                    name: name,
                    [csrfData['token_name']]: csrfData['hash']
                }).done(function(response) {
                    response = JSON.parse(response);
                    alert_float(response.alert_type, response.message);
                    $('.table-items-groups').DataTable().ajax.reload();
                });
            }
        });
    });

    function items_bulk_action(event) {
        if (confirm_delete()) {
            var mass_delete = $('#mass_delete').prop('checked');
            var ids = [];
            var data = {
                [csrfData['token_name']]: csrfData['hash']
            };

            if (mass_delete == true) {
                data.mass_delete = true;
            }

            var rows = $('.table-invoice-items').find('tbody tr');
            $.each(rows, function() {
                var checkbox = $($(this).find('td').eq(0)).find('input');
                if (checkbox.prop('checked') === true) {
                    ids.push(checkbox.val());
                }
            });
            data.ids = ids;
            $(event).addClass('disabled');
            setTimeout(function() {
                $.post(admin_url + 'invoice_items/bulk_action', data).done(function() {
                    window.location.reload();
                }).fail(function(data) {
                    alert_float('danger', data.responseText);
                });
            }, 200);
        }
    }

    function delete_items(id) {
        $.get('<?= admin_url('invoice_items/check_items/') ?>' + id, function(response) {
            response = JSON.parse(response);

            if (response.type == 1) {
                alert_float(response.alert_type, response.message);
            } else if (response.type == 3) {
                var r = confirm("<?php echo _l('ch_exsit_combo'); ?> <?php echo _l('ch_yes_no_delete'); ?>");
                if (r == false) {
                    return false;
                } else {
                    $.get('<?= admin_url('invoice_items/delete/') ?>' + id, function(response) {
                        alert_float(response.alert_type, response.message);
                        tAPI.draw('page');
                    }, 'json');
                }
                return false;
            } else if (response.type == 2) {
                alert_float(response.alert_type, response.message);

            } else if (response.type == 5) {

                $.get('<?= admin_url('invoice_items/delete/') ?>' + id, function(responses) {
                    alert_float(responses.alert_type, responses.message);
                    tAPI.draw('page');
                }, 'json');
            }
        });
    }
    $(document).on('click', '.delete-remind', function() {
        var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
        if (r == false) {
            return false;
        } else {
            $.get($(this).attr('href'), function(response) {
                alert_float(response.alert_type, response.message);
                tAPI.draw('page');
            }, 'json');
        }
        return false;
    });
    $(document).on('click', '.delete-remind_gb', function() {
        var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
        if (r == false) {
            return false;
        } else {
            $.get($(this).attr('href'), function(response) {
                alert_float(response.alert_type, response.message);
                $('.table-items-brands').DataTable().ajax.reload();
                $('.table-items-groups').DataTable().ajax.reload();
            }, 'json');
        }
        return false;
    });
    //Hoàng CRM bổ xung
    $(document).on('keyup', '.quantity_print', function(e) {
        var current = $(e.currentTarget);
        var checkbox = $('[name="check_change_all"]');
        if (checkbox.is(':checked')) {
            current.parent().parent().parent().parent().find('.quantity_print').val(current.val());
        }
    });
    $(document).on('click', '.delete_input_field', function(e) {
        var current = $(e.currentTarget);
        current.parent().parent().remove();
    });

    $(document).on('click', '.option_barcode', function(e) {
        $('.content-print').html('');
        var arr_id = [];
        var rows = $('.table-invoice-items').find('tbody tr');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') === true) {
                arr_id.push(checkbox.val());
            }
        });

        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['arrID'] = arr_id;
        $.post(admin_url + 'invoice_items/getList_items', data).done(function(response) {
            var html = '';
            response = JSON.parse(response);
            $.each(response, function(k, v) {
                var stt = k + 1;
                html += '<tr>\
                      <td>' + stt + '</td>\
                      <td>' + v.name + '</td>\
                      <td class="td-input-field">\
                        <div class="input_infix">\
                          <input type="number" name="item[' + k + '][quantity_print]" class="quantity_print H_input" value="1">\
                          <input type="hidden" name="item[' + k + '][id_item]" class="id_item" value="' + v.id + '">\
                        </div>\
                        <div class="delete_input_field">\
                          <i class="fa fa-times"></i>\
                        </div>\
                      </td>\
                    </tr>';
            });
            $('.content-print').append(html);
        });
    });
    //end
</script>
</body>

</html>