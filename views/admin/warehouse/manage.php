<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .wrap-box-warehouse {
        border: 1px solid #d8d8d8;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .wrap-icon-warehouse {
        height: 70px;
        padding: 10px 0;
        margin: 0 10px;
        border-bottom: 1px solid #d8d8d8;
    }

    .wrap-icon-warehouse-i i {
        font-size: 26px;
        font-weight: bold;
        color: #005aff;
    }

    .wrap-icon-warehouse-t {
        margin-left: 5px;
        font-size: 14px;
        font-weight: 500;
        color: #005aff;
    }

    .wrap-detail-warehouse {
        padding: 10px 0 0 0;
        margin: 0 10px;
    }

    .wrap-detail-warehouse-code {
        font-style: italic;
        color: #404040;
    }

    .wrap-detail-warehouse-name {
        font-style: italic;
        color: #404040;
    }

    .wrap-detail-warehouse.last-child {
        padding-bottom: 10px;
        border-bottom: 1px solid #d8d8d8;
    }

    .wrap-content-warehouse {
        padding: 0 0 10px 0;
    }

    .wrap-action-warehouse {
        padding: 10px 0;
        margin: 0 10px;
    }

    .wrap-btn-detail {
        cursor: pointer;
        text-align: center;
        color: #55a9ff;
        border: 1px solid #55a9ff;
        padding: 10px 0;
        border-radius: 5px;
    }

    .wrap-btn-detail:hover {
        background: #eff4ff;
    }

    .wrap-btn-action-e {
        padding: 10px 0 0 0;
        display: flex;
        justify-content: center;
    }

    .wrap-btn-edit {
        cursor: pointer;
        background: #ffb938;
        padding: 5px 15px;
        color: #fff;
        border-radius: 4px;
        margin-right: 10px;
    }

    .wrap-btn-delete {
        cursor: pointer;
        background: #ff4545;
        padding: 5px 15px;
        color: #fff;
        border-radius: 4px;
    }
</style>
<div id="wrapper">

        <div class="panel_s mbot10 H_scroll" id="H_scroll">
            <div class="panel-body _buttons">

                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
				<?php if (has_permission('warehouse', '', 'export')) { ?>
                    <div class="pull-right mright5 H_border">
                        <a href="#" class="btn btn-info H_action_button" onclick="export_excel(); return false;">
                            <?php echo _l('c_export_excel'); ?>
                        </a>
                    </div>
				<?php } ?>
				<?php if (has_permission('warehouse', '', 'create')) { ?>
                    <div class="pull-right mright5 H_border">
                        <a href="#" class="btn btn-info H_action_button" onclick="add(); return false;">
                            <?php echo _l('create_add_new'); ?>
                        </a>
                    </div>
				<?php } ?>
            </div>
        </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- <?php // render_datatable(array(
                                // _l('#'),
                                // _l('code_group'),
                                // _l('code'),
                                // _l('name'),
                                // _l('note'),
                                // _l('options'),
                                // ),'warehouse'); 
                                ?> -->

                        <!-- hoàng crm bổ sung -->
                        <?php
                        $numOfCols = 4;
                        $rowCount = 0;
                        $bootstrapColWidth = 12 / $numOfCols;
                        ?>
                        <div class="wrap-container-warehouse">
                            <?php if (!$warehouse || count($warehouse) == 0) { ?>
                                <div class="panel panel-warning">
                                    <div class="panel-body">Không tìm thấy kho hàng nào!</div>
                                </div>
                            <?php } else { ?>
                                <?php foreach ($warehouse as $key => $value) { ?>
                                    <?php $getGroup = get_table_where('tblgroup_warehouse', array('id' => $value['id_group_warehouse']), '', 'row'); ?>
                                    <?php
                                    $this->db->select('SUM(tblwarehouse_items.product_quantity) as totalQuantity');
                                    $this->db->where('tblwarehouse_items.warehouse_id', $value['id']);
                                    $getTotalQuantity = $this->db->get('tblwarehouse_items')->row();
                                    ?>
                                    <?php
                                    $this->db->select('COUNT(DISTINCT tblwarehouse_items.type_items, tblwarehouse_items.id_items) as totalType');
                                    $this->db->where('tblwarehouse_items.warehouse_id', $value['id']);
                                    $this->db->where('tblwarehouse_items.product_quantity >', 0);
                                    $getTotalType = $this->db->get('tblwarehouse_items')->row();

                                    $dtBranch = $this->site_model->getBranchById($value['id_branch']);
                                    ?>
                                    <div class="col-md-<?php echo $bootstrapColWidth; ?>">
                                        <div class="wrap-box-warehouse">
                                            <div class="wrap-icon-warehouse">
                                                <span class="wrap-icon-warehouse-i"><i class="lnr lnr-home menu-icon"></i></span>
                                                <span class="wrap-icon-warehouse-t"><?= $value['name'] ?></span>
                                            </div>
                                            <div class="wrap-content-warehouse">
                                                <div class="wrap-detail-warehouse">
                                                    <span class="wrap-detail-warehouse-code">Mã nhóm: </span>
                                                    <span class="wrap-detail-warehouse-name"><?= $getGroup->name ?></span>
                                                </div>
                                                <div class="wrap-detail-warehouse">
                                                    <span class="wrap-detail-warehouse-code">Mã kho: </span>
                                                    <span class="wrap-detail-warehouse-name"><?= $value['code'] ?></span>
                                                </div>
                                                <div class="wrap-detail-warehouse">
                                                    <span class="wrap-detail-warehouse-code"><?= lang('tnh_branch') ?>: </span>
                                                    <span class="wrap-detail-warehouse-name"><?= $dtBranch['name'] ?></span>
                                                </div>
                                                <div class="wrap-detail-warehouse">
                                                    <span class="wrap-detail-warehouse-code">Tổng mặt hàng: </span>
                                                    <span class="wrap-detail-warehouse-name"><?= number_format($getTotalType->totalType) ?></span>
                                                </div>
                                                <div class="wrap-detail-warehouse last-child">
                                                    <span class="wrap-detail-warehouse-code">Tổng tồn kho: </span>
                                                    <span class="wrap-detail-warehouse-name"><?= number_format($getTotalQuantity->totalQuantity) ?></span>
                                                </div>
                                            </div>
                                            <div class="wrap-action-warehouse">
                                                <a href="<?= admin_url('warehouse/detail_warehouse/' . $value['id']); ?>" target="_blank">
                                                    <div class="wrap-btn-detail">
                                                        Xem chi tiết
                                                    </div>
                                                </a>
                                                <div class="wrap-btn-action-e">
                                                    <div class="wrap-btn-edit" onclick="edit(<?= $value['id'] ?>); return false;">
                                                        Sửa
                                                    </div>
                                                    <?php if (($value['id'] != WAREHOUSES_CAPACITY && $value['id'] != WAREHOUSES_HOLD && $value['id'] != WAREHOUSES_ERRORS && $value['id'] != WAREHOUSES_TAMP)) { ?>
                                                        <div class="wrap-btn-delete delete-remind" onclick="delete_main(<?= $value['id'] ?>); return false;">
                                                            Xóa
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    $rowCount++;
                                    if ($rowCount % $numOfCols == 0) echo '</div><div class="row">';
                                    ?>
                                <?php } ?>
                            <?php } ?>
                            <div class="clearfix"></div>
                        </div>
                        <!-- end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="warehouse_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="add-title"><?php echo _l('warehouse_add_heading'); ?></span>
                    <span class="edit-title"><?php echo _l('warehouse_edit_heading'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/warehouse/group_detail', array('id' => 'warehouse-group-modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_select('id_group_warehouse', $group, array('id', 'name'), 'group_warehouse'); ?>
                        <?php echo render_input('code', 'code'); ?>
                        <?php echo render_input('name', 'name'); ?>
                        <?php echo render_input('address', 'address'); ?>
                        <?php echo render_textarea('note', 'note'); ?>
                        <div class="form-group">
                            <?= lang('staff', 'staff_id') ?>
                            <select name="staff_id[]" id="staff_id" data-actions-box="1" class="form-control selectpicker" data-live-search="true" data-none-selected-text="<?= lang('staff') ?>" multiple>
                                <?php if (!empty($staff)) : ?>
                                    <?php foreach ($staff as $key => $value) : ?>
                                        <option value="<?= $value['staffid'] ?>"><?= $value['firstname'] ?> <?= $value['lastname'] ?></option>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <?= lang('tnh_branch', 'id_branch') ?>
                            <select name="id_branch" id="id_branch" class="form-control selectpicker" data-live-search="true" data-none-selected-text="<?= lang('tnh_branch') ?>" multiple>
                                <?php if (!empty($branch)) : ?>
                                    <?php foreach ($branch as $key => $value) : ?>
                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    // $('.H_filter').click(function(e) {
    //   var target = $(e.currentTarget);
    //   var value = target.attr('data-id');
    //   target.parent().parent().find('li').removeClass('active');
    //   target.parent().addClass('active');
    //   $('input[name="filterStatus"]').val(value);
    //   $('input[name="filterStatus"]').change();
    // });
    var CustomersServerParams = {};
    $(function() {
        //   var CustomersServerParams = {
        //     'filterStatus' : '[name="filterStatus"]',
        //   };
        //   initDataTable('.table-warehouse', admin_url+'warehouse/table_warehouse', [0], [0],CustomersServerParams,[0,'asc']);
        appValidateForm($('#warehouse-group-modal'), {
            id_group_warehouse: 'required',
            code: 'required',
            name: 'required',
            address: 'required'
        }, manage_warehouse);
        //   $.each(CustomersServerParams, function(filterIndex, filterItem){
        //     $(filterItem).on('change', function(){
        //       $('.table-warehouse').DataTable().ajax.reload();
        //     });
        //   });
    });

    // $('.table-warehouse').on('draw.dt', function() {
    //     get_total_limit();
    // });
    //  function get_total_limit() {
    //   dataString = {[csrfData['token_name']] : csrfData['hash']};
    //     jQuery.ajax({
    //         type: "post",
    //         url: "<?= admin_url() ?>warehouse/count_all/",
    //         data: dataString,
    //         cache: false,
    //         success: function (data) {
    //           data = JSON.parse(data);
    //           $('.all').html(data.all);
    //           $('.warehouse_cty').html(data.warehouse_cty);
    //           $('.warehouse_gcong').html(data.warehouse_gcong);
    //           }
    //     });
    // }
    function add() {
        $('.add-title').removeClass('hide');
        $('.edit-title').addClass('hide');
        $('#warehouse-group-modal').attr("action", "<?= admin_url('warehouse/detail') ?>");
        $('#id_group_warehouse').selectpicker('val', '');
        $('#name').val('');
        $('#address').val('');
        $('#code').val('');
        $('#note').val('');
        $('#staff_id').selectpicker('val', '');
        $('#id_branch').selectpicker('val', '');
        $('#warehouse_modal').modal('show');
    }

    function edit(id) {
        $('.add-title').addClass('hide');
        $('.edit-title').removeClass('hide');
        $('#warehouse-group-modal').attr("action", "<?= admin_url('warehouse/detail/') ?>" + id);
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url + 'warehouse/getData/' + id, data).done(function(response) {
            response = JSON.parse(response);
            $('#id_group_warehouse').selectpicker('val', response.id_group_warehouse);
            $('#name').val(response.name);
            $('#address').val(response.address);
            $('#code').val(response.code);
            $('#note').val(response.note);

            if (response.staff_warehouse) {
                staff_id = [];
                $.each(response.staff_warehouse, function(index, el) {
                    staff_id.push(el.staff_id);
                });

                $('#staff_id').selectpicker('val', staff_id);
            }
            $('#id_branch').selectpicker('val', response.id_branch);
            $('#warehouse_modal').modal('show');
        });
    }

    function delete_main(id) {
        var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
        if (r == false) {
            return false;
        } else {
            var data = {};
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            $.post(admin_url + 'warehouse/delete_main/' + id, data).done(function(response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    // $('.table-warehouse').DataTable().ajax.reload();
                    alert_float(response.alert_type, response.message);
                    location.reload();
                } else {
                    alert_float(response.alert_type, response.message)
                }
            });
        }
        return false;
    }

    function manage_warehouse(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                $('#id_group_warehouse').selectpicker('val', '');
                $('#name').val('');
                $('#address').val('');
                $('#code').val('');
                $('#note').val('');
                // $('.table-warehouse').DataTable().ajax.reload();
                alert_float(response.alert_type, response.message);
            }
            $('#warehouse_modal').modal('hide');
            location.reload();
        });
        return false;
    }

    function export_excel() {
        var get = "?data=true";
        $.each(CustomersServerParams, function(index, value) {
            var dataItems = $(value).val();
            if(dataItems) {
                if ($.isArray(dataItems)) {
                    $.each(dataItems, function (i, v) {
                        get += '&' + index + '[]=' + v;
                    })
                } else {
                    get += '&' + index + '=' + dataItems;
                }
            }
        })
        window.open(admin_url + 'warehouse/export_excel' + get, '_blank');
    }
</script>
</body>

</html>