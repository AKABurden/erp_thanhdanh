<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/stage_control_galvanize/detail/' . ($id ?? '') . '', array('id' => 'submit_form')); ?>
<div>
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
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
                                    <?= lang('Mã Số Phiếu', 'code') ?>
                                </td>
                                <td style="width: 35%;">
                                    <div class="form-group">
                                        <input type="text" name="code" class="form-control" id="code" value="<?= $value['code'] ?? 'Tự động hệ thống' ?>" readonly="" aria-invalid="false">
                                    </div>
                                </td>
                                <td style="width: 15%;">
                                    <?= lang('Ngày lập phiếu', 'date') ?>
                                </td>
                                <td style="width: 35%;">
                                    <?= form_input('date', set_value('date') ?? (!empty($value['date']) ? _dt($value['date']) : date('d/m/Y H:i')), 'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('Mã Phiếu Yêu Cầu Ghi Kẽm', 'reference_request ') ?></td>
                                <td colspan="1">
                                    <?= form_input('reference_request', ($value['reference_request'] ?? ''), 'id="reference_request" class="form-control" placeholder="' . lang('Mã Phiếu Yêu Cầu Ghi Kẽm') . '" required ') ?>
                                </td>
                                <td><?= lang('Thời Gian Dự Kiến', 'anticipated_time') ?></td>
                                <td colspan="1">
                                    <?= form_input('anticipated_time', (!empty($value['anticipated_time']) ? _dt($value['anticipated_time']) : ''), 'id="anticipated_time" class="form-control datetimepicker" placeholder="' . lang('Thời Gian Dự Kiến') . '" required ') ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('Thời Gian Bắt Đầu', 'start_time') ?></td>
                                <td>
                                    <?= form_input('start_time', (!empty($value['start_time']) ? _dt($value['start_time']) : ''), 'id="start_time" class="form-control datetimepicker" placeholder="' . lang('Thời Gian Bắt Đầu') . '" required ') ?>
                                </td>
                                <td><?= lang('Thời Gian Hoàn Thành', 'finish_time') ?></td>
                                <td>
                                    <?= form_input('finish_time', (!empty($value['finish_time']) ? _dt($value['finish_time']) : ''), 'id="finish_time" class="form-control datetimepicker" placeholder="' . lang('Thời Gian Hoàn Thành') . '" required ') ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('Mã LSX', 'start_time') ?></td>
                                <td>
                                    <input type="text" name="production_order_id" id="production_order_id" class="production_order_id" data-placeholder="<?= lang('Mã LSX') ?>" style="width: 100%;" value="<?= $value['production_order_id'] ?? '' ?>" title="">
                                </td>
                                <td><?= lang('Sản phẩm', 'product_id') ?></td>
                                <td>
                                    <input type="text" name="product_id" id="product_id" class="product_id" data-placeholder="<?= lang('Sản phẩm') ?>" style="width: 100%;" value="<?= $value['product_id'] ?? '' ?>" title="">
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('note', 'note') ?></td>
                                <td colspan="3">
                                    <textarea name="note" id="note" class="form-control note" rows="3"><?= !empty($dtSuggestPurchase) ? $dtSuggestPurchase['note'] : '' ?></textarea>
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
                <div>
                    <label for="items_search"><?= lang('Mặt hàng') ?></label>
                    <input type="text" name="items_search" id="items_search" class="items_search" style="width: 100%;"
                           data-placeholder="<?= lang('Mặt hàng') ?>" value="">
                </div>
                <div class="table-responsive">
                    <table id="tb-purchases" class="dt-tnh table table-hover" style="width: 100%;">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                            <th style="width: 100px;"><?= lang('tnh_product_code') ?></th>
                            <th style="width: 50px"><?= lang('tnh_images') ?></th>
                            <th style="width: 100px;"><?= lang('tnh_product_name') ?></th>
                            <th style="width: 50px;"><?= lang('ĐVT') ?></th>
                            <th style="width: 100px;"><?= lang('quantity') ?></th>
                            <th style="width: 100px;"><?= lang('Số kiện') ?></th>
                            <th style="width: 100px;"><?= lang('Số Kg') ?></th>
                            <th style="width: 100px;"><?= lang('Tiêu chuẩn/ quy định') ?></th>
                            <th style="width: 50px;"><?= lang('actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 0; if (!empty($dtSuggestPurchaseItems)){ ?>
                            <?php foreach ($dtSuggestPurchaseItems as $key => $value){ ?>
                                <?php
                                    $item_id = $value['item_id'];
                                    $type_item = $value['type_item'];
                                    $info = null;
                                    if ($type_item == "products") {
                                        $info = $this->products_model->rowProduct($item_id);
                                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                                        if (!empty($info['images'])) {
                                            $images = base_url('uploads/products/' . $info['images']);
                                        }
                                    }
                                    if (empty($images)) {
                                        $images = base_url('assets/images/tnh/no_image.png');
                                    }

                                ?>
                                <tr>
                                    <td  class="text-center"><?= (++$key) ?></td>
                                    <td><div class="code_item">
                                        <input type="hidden" name="counter[]" class="counter" value="<?= $counter ?>">
                                        <input type="hidden" name="item_id[<?= $counter ?>]" class="item_id" value="<?= $value['item_id'].'__'.$value['type_item'] ?>">
                                        <input type="hidden" name="suggest_purchase_product_item_id[<?= $counter ?>]" class="suggest_purchase_product_item_id" value="<?= $value['id'] ?>">
                                            <?= $info['code'] ?>
                                    </div>
                                    </td>
                                    <td><div class="td-image">
                                        <div class="preview_image" style="width: auto;">
                                            <div class="display-block contract-attachment-wrapper img">
                                                <div style="width:45px;">
                                                    <a href="<?= $images ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                        <div class="">
                                                            <img src="<?= $images ?>" style="border-radius: 50%">
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </td>
                                    <td><div class="name_item"><?= $info['name'] ?></div></td>
                                    <td><div class="unit_item"><?= $unit['unit'] ?></div></td>
                                    <td><div class="td-quantity"><input type="text" name="quantity[<?= $counter ?>]" class="quantity form-control number-format" value="<?= formatNumber($value['quantity']) ?>"></div></td>
                                    <td><div class="td-quantity-kien"><input type="text" name="quantity_kien[<?= $counter ?>]" class="quantity_kien number-format form-control" value="<?= formatNumber($value['quantity_kien']) ?>"></div></td>
                                    <td><div class="td-quantity-kg"><input type="text" name="quantity_kg[<?= $counter ?>]" class="quantity_kg form-control number-format" value="<?= formatNumber($value['quantity_kg']) ?>"></div></td>
                                    <td><div class="standard_item"><input type="text" name="standard[<?= $counter ?>]" class="standard form-control" value="<?= $value['standard'] ?>"></div></td>
                                    <td class="text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a></td>
                                </tr>
                            <?php $counter++;} ?>
                        <?php } ?>
                        </tbody>
                    </table>
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
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript">
    var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
    var hash = "<?= $this->security->get_csrf_hash() ?>";
    $(document).ready(function() {
        var filter_production_order_id = $('#production_order_id').val() ? $('#production_order_id').val() : -1;

        ajaxSelectParamsCallback('#production_order_id', 'admin/stage_control_galvanize/searchProductionOrder/', $('#production_order_id').val(), false, true);
        ajaxSelectParamsCallback('#product_id', 'admin/stage_control_galvanize/searchProduct', $('#product_id').val(), {production_order_id: filter_production_order_id}, true);
    });

    $('#production_order_id').change(function(data) {
        $('#product_id').val('');
        ajaxSelectParamsCallback('#product_id', 'admin/stage_control_galvanize/searchProduct', $('#product_id').val(), {production_order_id: $('#production_order_id').val()}, true);
    });
</script>
<?php //$this->load->view('admin/stage_control_galvanize/script_js.php') ?>
