<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open('admin/manufacture/add', array('id' => 'add-productions-orders')); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?= $this->load->view('admin/breadcrumb') ?>
        </div>
    </div>
    <div class="content">
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
                                        <?= lang('Số lệnh', 'reference_no') ?>
                                    </td>
                                    <td style="width: 35%;">
                                        <div class="form-group">
                                            <input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= lang('auto') ?>" readonly="" aria-invalid="false">
                                        </div>
                                    </td>
                                    <td style="width: 15%;"><?= lang('date', 'date') ?></td>
                                    <td style="width: 35%;">
                                        <?= form_input('date', set_value('date') ? set_value('date') : date('d/m/Y H:i:s'), 'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= lang('tnh_reference_productions_orders', 'po_id') ?>
                                    </td>
                                    <td>
                                        <input type="text" name="po_id" style="width: 100%;" id="po_id" data-placeholder="<?= lang('tnh_reference_productions_orders') ?>" class="po_id" value="<?= $po_id_link ? $po_id_link : '' ?>">
                                    </td>
                                    <td><?= lang('note', 'note') ?></td>
                                    <td>
                                        <textarea name="note" id="note" class="form-control" rows="3"></textarea>
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
                <div class="" style="min-height: auto; margin-bottom: 100px;">
                    <div class="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('Nguyên phụ liệu BOM', 'items') ?>
                                    <input type="text" name="" id="items" class="items" style="width: 100%;" data-placeholder="<?= lang('Nguyên phụ liệu BOM') ?>" value="">
                                </div>
                            </div>
                        </div>
                        <div class="tb-height">
                            <table id="tb-productions-orders" class="dt-tnh tnh-table table table-bordered table-hover dataTable dont-responsive-table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <?= lang('tnh_numbers') ?>
                                        </th>
                                        <th class="text-center"><?= lang('tnh_material_code') ?></th>
                                        <th class="text-center"><?= lang('tnh_material_name') ?></th>
                                        <th class="text-center"><?= lang('unit_manufactures') ?></th>
                                        <th class="text-center"><?= lang('tnh_quantity_bu_hao') ?></th>
                                        <th class="text-center"><?= lang('tnh_quantity_use') ?></th>
                                        <!-- <th class="text-center"><?= ''//lang('tnh_height_cm') ?></th>
                                        <th class="text-center"><?= ''//lang('tnh_total_height_cm') ?></th>
                                        <th class="text-center"><?= ''//lang('tnh_number_paper_hard_material') ?></th>
                                        <th class="text-center"><?= ''//lang('tnh_landscape_print_size') ?></th>
                                        <th class="text-center"><?= ''//lang('tnh_number_children_size') ?></th>
                                        <th class="text-center"><?= ''//lang('tnh_exchange_value') ?></th>
                                        <th class="text-center"><?= ''//lang('tnh_paper_exchange_unit_paper') ?></th> -->
                                        <th class="text-center" style="width: 150px;"><?= lang('note') ?></th>
                                        <th class="text-center" style="width: 80px;" class="text-center"><?= lang('actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody class="tbody-items">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                <input type="hidden" name="add" id="" class="form-control" value="1">
                <button type="submit" class="btn btn-info only-save customer-form-submiter add">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript">
    var token = "<?= $this->security->get_csrf_token_name() ?>";
    var hash = "<?= $this->security->get_csrf_hash() ?>";
    var edit = 0;
    var counter = 0;
    var count_errors = 0;
    var arr_productions_plan_id = [];
    var lang_core = <?= json_encode(['Lot' => lang('Lot'), 'ch_date_of_manufacture' => lang('ch_date_of_manufacture'), 'ch_items_dateed' => lang('ch_items_dateed')]) ?>;
    var po_id_link = <?= $po_id_link ? $po_id_link : 0 ?>;
</script>
<script type="text/javascript" src="<?= js('manufacture_new_1.js?vs=2.7') ?>"></script>
<script>
    // $('body').on('change', '#id_production_detail', function(e) {
    //     var trTable = $('#tb-productions-orders').find('tbody').find('tr');
    //     $.each(trTable, function(index, value) {
    //         $(value).find('.remove-row').trigger('click');
    //     })
    //     ajaxSelectItemsCallBack($('#items'), 'admin/manufacture/searchProductAndGoods', 0, {
    //         id_production_detail: $('#id_production_detail').val()
    //     });
    // })
</script>