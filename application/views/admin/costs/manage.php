<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" href="<?= base_url('assets/treegrid/') ?>css/jquery.treegrid.css">
<style>
    #table-costs tr th:nth-child(7) {
        width: 25px;
    }
    #table-costs tr th:nth-child(9) {
        width: 60px;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <div class="line-sp"></div>
                <a href="" onclick="new_costs(); return false;" class="btn btn-info mright5 test pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('create_add_new'); ?></a>
                <div class="line-sp"></div>
                <a href="<?= admin_url('costs/detail') ?>" class="btn btn-info mright5 test pull-right H_action_button">
                    <?php echo _l('ch_plan'); ?></a>
                <a href="<?= base_url('admin/costs/modal_excel_import') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                    <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('IMPORT EXCEL'); ?>
                </a>
                <a href="<?= base_url('admin/costs/excel_export') ?>" class="btn btn-info pull-right mright10 H_action_button">
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
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-3 row">
                            <div class="form-group">
                                <?= lang('type', 'type') ?>
                                <select name="type_cost" id="type_cost" class="form-control selectpicker type_cost" data-none-selected-text="<?= lang('Chọn loại') ?>" data-placeholder="<?= lang('Chọn loại') ?>">
                                    <option value="0"></option>
                                    <?php foreach ($dtType as $key => $value){?>
                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <table id="table-costs" class="table dt-tnh table-hover table-cost-new" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center"><?= lang('Mã Loại') ?></th>
                                    <th class="text-center"><?= lang('Tên Loại') ?></th>
                                    <th class="text-center"><?= lang('Mã Chi Phí Cha') ?></th>
                                    <th class="text-center"><?= lang('Tên Chi Phí Cha') ?></th>
                                    <th class="text-center"><?= lang('Mã Chi Phí') ?></th>
                                    <th class="text-center"><?= lang('Tên Chi Phí') ?></th>
                                    <th class="text-center"><?= lang('STT') ?></th>
                                    <th class="text-center"><?= lang('Mô Tả') ?></th>
                                    <th class="text-center"><?= lang('Phòng Ban') ?></th>
                                    <th class="text-center"><?= lang('Tác vụ') ?></th>
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
    <div class="modal fade" id="type" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <?php echo form_open(admin_url('financial_control/add'), array('id' => 'id_type')); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <span class="edit-title"><?php echo _l('ch_edit'); ?></span>
                        <span class="add-title"><?php echo _l('ch_add'); ?></span>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div id="additional"></div>
                        <div class="col-md-12">
                            <?php echo render_input('code', 'ch_code_costs', '', '', array('autocomplete' => 'off')); ?>
                        </div>
                        <div class="col-md-12">
                            <?php echo render_input('name', 'ch_name_costs', '', '', array('autocomplete' => 'off')); ?>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang('Phòng ban', 'type') ?>
                                <select name="department_id[]" class="form-control selectpicker" multiple data-none-selected-text="<?= lang('Phòng ban') ?>" data-placeholder="<?= lang('Phòng ban') ?>">
                                    <option value="0"></option>
                                    <?php foreach ($dtDepartment as $key => $value){?>
                                        <option value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang('type', 'type') ?>
                                <select name="type" class="form-control selectpicker" data-none-selected-text="<?= lang('type') ?>" data-placeholder="<?= lang('type') ?>">
                                    <option value="0"></option>
                                    <?php foreach ($dtType as $key => $value){?>
                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <?php echo render_select('costs_parent', $costs, array('id', 'name'), 'ch_chose_parent'); ?>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang('Loại danh mục', 'type_cost') ?>
                                <select name="type_cost" class="form-control selectpicker" onchange="typeCost(this)"  data-placeholder="<?= lang('type') ?>">
                                    <option value=""></option>
                                    <?php foreach ($dtTypeCost as $key => $value){?>
                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang('Danh mục', 'object_id') ?>
                                <select name="object_id" class="form-control selectpicker object_id" data-placeholder="<?= lang('danh mục') ?>" data-live-search="true">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <div class="modal fade" id="modal_delete_category" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <?php echo form_open(admin_url('costs/delete_costs'), array('id' => 'delete_type')); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <span class="delete-title"><?php echo _l('Xóa loại'); ?></span>

                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="id" id="id_delete" />
                            <p class="text-danger"><?php echo _l('Khi xóa thì các danh mục con sẽ được chuyển cho danh mục cha cùng cấp'); ?></p>

                        </div>
                        <div class="col-md-12">
                            <?php echo render_select('id_new', '', array('id', 'category'), 'Danh mục cha'); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
        < </div>
            <?php init_tail(); ?>
            <script type="text/javascript" src="<?= base_url('assets/treegrid/') ?>js/jquery.treegrid.js"></script>
            <script type="text/javascript">
                $('.tree').treegrid({
                    initialState: 'collapsed',
                });
            </script>
            <script>

                function typeCost(_this){
                    type_cost = $(_this).val();
                    $.ajax({
                        url: site.base_url+'admin/costs/getDataCategory',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            csrf_token_name: hash,
                            type_cost: type_cost
                        },
                    })
                        .done(function(data) {
                            html = '';
                            if (data.dtObject.length > 0) {
                                $.each(data.dtObject,function (k,v){
                                    html += `<option value="${v.id}">${v.name}</option>`;
                                })
                                $("select.object_id").html(html)
                                $("select.object_id").selectpicker('refresh')
                            }
                        })
                        .fail(function() {
                            console.log("error");
                        });
                }

                $(function() {

                    var fnserverparams = {
                        'type_cost': '#type_cost'
                    };
                    var filterValue = {};
                    for (var key in fnserverparams) {
                        var elementId = fnserverparams[key];
                        var element = document.querySelector(elementId);
                        element.onchange = function() {
                            filterValue[key] = $(fnserverparams[key]).val();
                            oTable.draw('page');
                        };
                    }
                    oTable = tnhInitDataTable('#table-costs', '<?= site_url('admin/costs/getCosts') ?>', {
                        'order': false,
                        'fixedHeader': {
                            header: true,
                        },
                        "ajax": {
                            "url": '<?= site_url('admin/costs/getCosts') ?>',
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
                        name: 'required',
                        type: 'required',
                        type_cost: 'required',
                        category: 'required',
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
                            location.reload();
                            $('#type').modal('hide');
                        });
                        return false;
                    }
                });

                function delete_costs(id = "") {
                    if (id != "") {
                        $.ajax({
                                url: admin_url + 'costs/get_exsit/' + id,
                                dataType: 'json',
                            })
                            .done(function(data) {

                                $.each(data, function(key, value) {
                                    id_new.append('<option value="' + value.id + '">' + value.vallue + '</option>');
                                });

                                id_new.selectpicker('refresh');
                            });
                    }
                    location.reload();
                    return false;
                }

                function new_costs() {
                    $('#type').modal('show');
                    $('.edit-title').addClass('hide');
                    var type_cost = $('select#type_cost').val();
                    jQuery('#name').val('');
                    jQuery('#detail').val('');
                    jQuery('[name="type"]').val(type_cost).change();
                    $("select.object_id").html('');
                    $("select.object_id").selectpicker('refresh')
                    jQuery('[name="type_cost"]').val('').change();
                    jQuery('#id_type').prop('action', admin_url + 'costs/add');
                }

                function edit_costs(id, code, name, parent_id, cType,type_cost,object_id,department_id) {
                    department_id = department_id.split(',');
                    $('#type').modal('show');
                    $('.edit-title').removeClass('hide');
                    $('.add-title').addClass('hide');
                    $('#additional').append(hidden_input('id', id));
                    $('#type input[name="code"]').val(code);
                    $('#type input[name="name"]').val(name);
                    $('#type select[name="type_cost"]').val(type_cost).selectpicker('refresh');
                    $('#type select[name="type"]').val(cType).selectpicker('refresh');
                    $('#type select[name="department_id[]"]').val(department_id).selectpicker('refresh');
                    $('#type').find('#costs_parent').selectpicker('val', parent_id);
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

                    $.ajax({
                        url: site.base_url+'admin/costs/getDataCategory',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            csrf_token_name: hash,
                            type_cost: type_cost
                        },
                    })
                        .done(function(data) {
                            html = '<option></option>';
                            if (data.dtObject.length > 0) {
                                $.each(data.dtObject,function (k,v){
                                    html += `<option ${v.id == object_id ? 'selected' : ''} value="${v.id}">${v.name}</option>`;
                                })
                                $("select.object_id").html(html)
                                $("select.object_id").selectpicker('refresh')
                            }
                        })
                        .fail(function() {
                            console.log("error");
                        });
                }
            </script>