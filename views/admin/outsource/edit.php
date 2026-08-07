<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/outsource/edit/'.$id, array('id' => 'outsource')); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <?= $this->load->view('admin/breadcrumb') ?>
        </div>
    </div>
    <div class="content ae-content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('info') ?></h3>
                    </div>
                    <div class="panel-body">
                        <table class="tnh-tb table-bordered table-hover">
                            <tbody>
                                <tr>
                                    <td style="width: 15%;">
                                        <?= lang('tnh_reference_outsource', 'reference_no') ?>
                                    </td>
                                    <td style="width: 35%;">
                                        <div class="form-group">
                                            <input type="text" name="reference_no" class="form-control"
                                                id="reference_no" value="<?= $outsource['reference_no'] ?>" readonly=""
                                                aria-invalid="false">
                                        </div>
                                    </td>
                                    <td style="width: 15%;">
                                        <?= lang('date', 'date') ?>
                                    </td>
                                    <td style="width: 35%;">
                                        <?= form_input('date', set_value('date') ? set_value('date') : _d($outsource['date']), 'id="date" class="form-control datetimepicker" placeholder="'.lang('date').'" required ') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= lang('tnh_supplies', 'supplies') ?></td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" name="supplies"
                                                data-placeholder="<?= lang('tnh_supplies') ?>" id="supplies"
                                                class="supplies" required style="width: 100%;"
                                                value="<?= $outsource['supplier_id'] ?>">
                                        </div>
                                    </td>
                                    <!-- <td><?= lang('tnh_warehouses', 'warehouses') ?></td> -->
                                    <!-- <td>
                                        <div class="form-group">
                                            <select name="warehouses" id="warehouses"
                                                data-placeholder="<?= lang('tnh_warehouses') ?>" class=""
                                                style="width: 100%;" required="required">
                                                <option value=""></option>
                                                <?php foreach ($warehouses as $key => $value): ?>
                                                <option
                                                    <?= $value['id'] == $outsource['warehouse_id'] ? 'selected' : '' ?>
                                                    value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </td> -->
                                    <td><?= lang('Chi nhánh xưởng', '') ?></td>
                                    <td>
                                        <?php $branchs = get_table_where('tblbranch',['id !='=> 1],'','result_array'); 
                                         $branch_id = 0;
                                         $staff = get_table_where('tblstaff',['staffid'=>get_staff_user_id()],'','row_array');
                                         $branch_id = $staff['id_branch'];?>
                                        <select name="id_branch" id="id_branch"
                                            class="id_branch <?= $branch_id !=1 ? 'none-event' : '' ?>"
                                            required="required" data-placeholder="<?= lang('Chi nhánh xưởng') ?>"
                                            style="width: 100%;">
                                            <option value=""></option>
                                            <?php if(!empty($branchs)) {?>
                                            <?php foreach($branchs as $key => $value){?>
                                            <option <?= $outsource['id_branch'] == $value['id'] ? 'selected' : '' ?>
                                                value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>

                                    <td><?= lang('note', 'note') ?></td>
                                    <td colspan="3">
                                        <textarea name="note" id="note" class="form-control note"
                                            rows="3"><?= $outsource['note'] ?></textarea>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="tabset">
                    <!-- Tab 1 -->
                    <input type="radio" name="tabset" id="tab1" aria-controls="marzen" checked>
                    <label for="tab1"><?= lang('Theo lệnh tổng') ?></label>

                    <input type="radio" name="tabset" id="tab2" aria-controls="marzen">
                    <label for="tab2"><?= lang('tnh_items') ?></label>
                    <!-- Tab 2 -->
                    <input type="radio" name="tabset" id="tab2" aria-controls="items-export">
                    <label class="hide" for="tab2"><?= lang('tnh_materials_export') ?></label>

                    <div class="tab-panels">
                        <section id="tab1" class="tab-panel">
                            <div class="row mbot10">
                                <div class="col-md-5">
                                    <input type="text" name="productions" id="productions"
                                        data-placeholder="<?= lang('Lệnh tổng') ?>" class="productions modal-select2"
                                        value="" style="width: 100%;height:35px">
                                </div>

                                <div class="col-md-3">
                                    <button type="button" onclick="refershTable()"
                                        class="btn btn-danger ev-referesh"><?= lang('tnh_referesh') ?></button>
                                </div>
                            </div>
                        </section>
                        <section id="tab2" class="tab-panel">
                            <div class="row mbot10">
                                <div class="col-md-5">
                                    <input type="text" data-placeholder="<?= lang('tnh_items') ?>" name="" id="items"
                                        class="items modal-select2" value="" style="width: 100%;height:35px">
                                </div>
                                <div class="col-md-3">
                                    <button type="button" onclick="refershTable()"
                                        class="btn btn-danger ev-referesh"><?= lang('tnh_referesh') ?></button>
                                </div>
                            </div>
                        </section>
                        <div class="tb-height">
                            <div class="table-responsive">
                                <table id="tb-deliveries" class="dt-tnh table table-bordered table-hover"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 30px;"><?= lang('tnh_numbers') ?>
                                            </th>
                                            <th style="width: 120px;"><?= lang('Tên/Mã thành phẩm') ?></th>
                                            <th style="width: 50px"><?= lang('tnh_images') ?></th>
                                            <th style="width: 50px;"><?= lang('tnh_unit') ?></th>
                                            <th style="width: 100px;"><?= lang(' Công đoạn') ?></th>
                                            <!-- <th style="width: 50px;"><?= lang('quantity') ?></th> -->
                                            <!-- <th style="width: 60px;"><?= lang('tnh_quantity_had_outsource') ?></th> -->
                                            <th style="width: 80px;"><?= lang('tnh_quantity_outsource') ?></th>
                                            <th style="width: 80px;"><?= lang('price') ?></th>
                                            <th style="width: 80px;"><?= lang('tnh_subtotal') ?></th>
                                            <th style="width: 100px;"><?= lang('note') ?></th>
                                            <th style="width: 50px;"><?= lang('actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?= $bodyItems ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bold">
                                            <th colspan="3" class="text-center uppercase">
                                                <?= lang('tnh_grand_total') ?></th>
                                            <th></th>
                                            <th></th>
                                            <th class="th-quantity-processing text-center">
                                                <?= formatNumber($outsource['total_quantity']) ?></th>
                                            <th></th>
                                            <th class="th-subtotal text-right">
                                                <?= formatMoney($outsource['grand_total']) ?></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <section id="items-export" class="tab-panel">
                            <div class="tb-height-more">
                                <div class="table-responsive">
                                    <div class="mbot10 text-right">
                                        <button type="button" onclick="loadBom(this)"
                                            class="btn btn-primary ev-bom-all"><?= lang('tnh_load_bom') ?></button>
                                        <button type="button" onclick="refershItemsTable()"
                                            class="btn btn-danger ev-bom-referesh"><?= lang('tnh_referesh') ?></button>
                                    </div>
                                    <table id="tb-items-export" class="dt-tnh table table-bordered table-hover"
                                        style="width: 1350px;">
                                        <thead>
                                            <th class="text-center" style="width: 30px;">
                                                <!--                                            <a class="btn btn-info btn-icon add-row"><i class="fa fa-plus"></i></a>-->
                                            </th>
                                            <th style="width: 250px;"><?= lang('tnh_item_code') ?></th>
                                            <th style="width: 150px;"><?= lang('tnh_item_name') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_unit') ?></th>
                                            <th style="width: 150px;"><?= lang('quantity') ?></th>
                                            <!-- <th style="width: 150px;" ><?= lang('price') ?></th> -->
                                            <!-- <th style="width: 100px;" ><?= lang('tnh_subtotal') ?></th> -->
                                            <th style="width: 200px;"><?= lang('note') ?></th>
                                            <!--                                        <th style="width: 100px;">-->
                                            <?//= lang('actions') ?>
                                            <!--</th>-->
                                        </thead>
                                        <tbody>
                                            <?= $bodyMaterial ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                <input type="hidden" name="edit" id="" class="form-control" value="1">
                <button type="submit" class="btn btn-info only-save customer-form-submiter add">
                    <?php echo _l( 'submit'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript">
var dt = '';
var lang_outsource =
    <?= json_encode(['tnh_please_chosen_customer' => lang('tnh_please_chosen_customer'), 'tnh_expected_date' => lang('tnh_expected_date'), 'tnh_quantity_outsource_less' => lang('tnh_quantity_outsource_less'), 'tnh_do_you_load_bom' => lang('tnh_do_you_load_bom')]) ?>;
var token = "<?= $this->security->get_csrf_token_name() ?>";
var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
var hash = "<?= $this->security->get_csrf_hash() ?>";
var edit = 1;
var counter = <?= $counter ?>;
var count_errors = 0;
var outsource_id = <?= $id ?>;

var counterMaterial = <?= $counterMaterial ?>;
var dtMaterial = '';
var pod_id = "<?= $outsource['pod_id'] ?>";
</script>

<script type="text/javascript" src="<?= js('outsource.js?vs=1.6') ?>"></script>