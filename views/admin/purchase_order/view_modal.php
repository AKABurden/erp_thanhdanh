<style type="text/css">
    .img_ch {
        height: 20px;
        width: 20px;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }
</style>
<?php
$view_price = '';
if (!has_permission('purchase_order', '', 'view_price')) {
    $view_price = 'hide';
} ?>
<div class="modal fade in" id="view_purchase_order" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('ch_po_t'); ?> </span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="info">
                            <?php
                            $type = '';
                            if (!isset($items))
                                $type = 'warning';
                            elseif ($items->status == 1)
                                $type = 'warning';
                            elseif ($items->status == 2)
                                $type = 'danger';
                            elseif ($items->status == 3)
                                $type = 'success';

                            ?>
                            <div style="right: 10px;" class="ribbon <?= $type ?>" project-status-ribbon-2="">
                                <?php
                                if (isset($items)) {
                                    $status = format_purchase_status($items->status, '', false);
                                } else {
                                    $status = format_purchase_status(-1, '', false);
                                }
                                ?>
                                <span><?= $status ?></span>
                            </div>
                            <div class="title-modal">
                                <h3>Thông tin</h3>
                            </div>
                            <div class="body-modal">
                                <div class="row-modal">
                                    <div class="row-group">
                                        <?php if (format_purchase_order_father_all($items->id, '', true, '12px')) { ?>
                                            <div class="row-contro">
                                                <?= format_purchase_order_father_all($items->id, '', true, '12px') ?></div>
                                        <?php } ?>
                                        <div class="row-contro">
                                            <div><?= _l('ch_code_p') ?>: </div>
                                            <div class="ml-at t-bold"><?php echo $items->prefix . '-' . $items->code ?>
                                            </div>
                                        </div>

                                        <div class="row-contro">
                                            <div><?= _l('ch_date_p') ?>: </div>
                                            <div class="ml-at t-bold"><?php echo _d($items->date) ?></div>
                                        </div>
                                        <div class="row-contro">
                                            <div><?= _l('Nhà cung cấp') ?>: </div>
                                            <?php $supplier = get_table_where('tblsuppliers', ['id' => $items->suppliers_id], '', 'row_array') ?>
                                            <div class="ml-at t-bold"><?= $supplier['company'] ?></div>
                                        </div>

                                    </div>
                                    <div class="row-group">
                                        <div class="row-contro" style="color:red;">
                                            <div><?= _l('ch_delivery_date') ?>: </div>
                                            <div class="ml-at t-bold" style="color:red;">
                                                <?php echo _d($items->delivery_date) ?></div>
                                        </div>
                                        <div class="row-contro">
                                            <div><?= _l('ch_staff_crate_rfq') ?>: </div>
                                            <div class="ml-at t-bold">
                                                <?php echo staff_profile_image($items->staff_create, array('staff-profile-image-small mright5 img_ch'), 'small', array(
                                                    'data-toggle' => 'tooltip',
                                                    'data-title' => get_staff_full_name($items->staff_create)
                                                )) . get_staff_full_name($items->staff_create) ?>
                                            </div>
                                        </div>
                                        <div class="row-contro">
                                            <div><?= _l('ch_note_t') ?>: </div>
                                            <div class="ml-at t-bold"><?php echo $items->note ?></div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $customer_custom_fields = false;
                    if (total_rows(db_prefix() . 'customfields', array('fieldto' => 'purchase_order', 'active' => 1)) > 0) {
                        $customer_custom_fields = true;
                    }
                    ?>
                    <?php if ($customer_custom_fields) { ?>
                        <div class="col-md-6  pull-left">
                            <div class="panel panel-info">

                                <div class="panel-heading">
                                    <h3 class="panel-title"><?php echo _l('custom_fields'); ?></h3>
                                </div>
                                <div class="panel-body">
                                    <div class="well well-sm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php $custom_fields = get_table_custom_fields('purchase_order'); ?>
                                                <?php
                                                $custom_fields = get_custom_fields('purchase_order', array('show_on_table' => 1));
                                                foreach ($custom_fields as $field) {
                                                ?>
                                                    <div class="form-group border_ch">
                                                        <label class="form-label control-label ng-binding"><?php echo $field['name']; ?>:</label>
                                                        <span>
                                                            <?php $value = get_custom_field_value((isset($items) && isset($items->id) ? $items->id : ''), $field['id'], 'purchase_order'); ?>
                                                            <strong class="ng-binding"><?php echo (isset($items) && $value != '' ? $value : '-') ?></strong>
                                                        </span>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#item_info" aria-controls="item_info" role="tab" data-toggle="tab"><i class="icon-foso fal fa-info-circle"></i><?= _l('Tổng hợp đặt hàng') ?></a>
                    </li>
                    <?php if ($items->type_plan == 1) { ?>
                        <li role="presentation">
                            <a href="#item_info_detail" aria-controls="item_info_detail" role="tab" data-toggle="tab"><i class="icon-foso fal fa-info-circle"></i><?= _l('Chi tiết đặt hàng') ?></a>
                        </li>
                    <?php } ?>
                    <li role="presentation">
                        <a href="#tab_feedback" aria-controls="tab_feedback" role="tab" data-toggle="tab">
                            <i class="icon-foso fa fa-comments-o"></i>
                            <?= _l('FeedBack') ?><span class="badge menu-badge bg-warning"><?= !empty($feedback) ? count($feedback) : '' ?></span>
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#item_activity" aria-controls="item_activity" role="tab" data-toggle="tab"><i class="icon-foso fal fa-history"></i><?= _l('activity_log_puchases') ?></a>
                    </li>
                </ul>
                <div role="tabpanel" class="tab-pane active" id="item_info">
                    <?php
                    $subtotal = 0;
                    if (isset($items->items) && (count($items->items) > 0)) { ?>
                        <div class="table-responsive">
                            <table id="view-enquiry" class="table table-view-enquiry" style="width: 100%; max-height: 400px !important;">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="center"><?= _l('image') ?></th>
                                        <th style="width: 250px;" class="text-left"><?php echo _l('Mã hàng'); ?>
                                        <th style="width: 250px;" class="text-left"><?php echo _l('ch_items_name_t'); ?>
                                        </th>
                                        <th style="width: 100px;" class="text-center"><?php echo _l('quantili_unit_standard'); ?>
                                        <th style="width: 100px;" class="text-center"><?php echo _l('quantili_unit_stock'); ?>
                                        <th style="width: 100px;" class="text-center"><?php echo _l('quantili_unit_payment'); ?>
                                        </th>
                                        <!-- <th style="width: 100px;" class="text-left"><?= _l('item_unit'); ?></th> -->
                                        <?php if (has_permission('purchase_order', '', 'view_price')) {  ?>
                                            <th style="width: 100px;" class="text-right <?= $view_price ?>">
                                                <?php echo _l('ch_price_ncc'); ?></th>
                                            <th style="width: 100px;" class="text-right <?= $view_price ?>">
                                                <?php echo _l('promotion_suppliers'); ?></th>
                                            <th style="width: 100px;" class="text-center <?= $view_price ?>"><?= _l('tax'); ?>
                                            </th>
                                            <th style="width: 100px;" class="text-right <?= $view_price ?>">
                                                <?php echo _l('amount_suppliers_vnd'); ?></th>
                                        <?php } ?>
                                        <th style="width: 200px;" class="text-left"><?= _l('note'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $items_array = [];
                                    // if ($items->type_plan == 1) {
                                    //     $items_array = $items_plan;
                                    // } else {
                                        $items_array = $items->items;
                                    // }
                                    ?>
                                    <?php foreach ($items_array as $key => $value) { ?>

                                        <tr>
                                            <?php if ($value['avatar'] == '') {
                                                $value['avatar'] = 'uploads/no-img.jpg';
                                            }
                                            ?>
                                            <td style="width:80px" class="center">
                                                <div class="preview_image text-center" style="width: 100px;margin-bottom:0;margin-top:0">
                                                    <div class="display-block contract-attachment-wrapper img-<?= $value['id'] ?>">
                                                        <div>
                                                            <a href="<?= $value['avatar'] ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                <img class="mbot5" style="border-radius: 50%;width: 2em;height: 2em;" src="<?= $value['avatar'] ?>">
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <?= format_item_purchases($value['type']) ?> -->
                                            </td>
                                            <td style="width:100px">
                                                <?php echo $value['code_item']; ?>
                                            </td>
                                            <td style="width:250px">
                                                <?php echo $value['name_item'] ?><?= GetQuycach($value['product_id'],$value['type']) ?><br><?= format_item_color($value['product_id'], $value['type']) ?>
                                            </td>
                                            <td style="width:80px" class="center">
                                                <?php echo formatNumber($value['quantity_unit']); ?>/<?= $value['unit'] ?>
                                            </td>
                                            <td style="width:80px" class="center">
                                                <?php echo formatNumber($value['quantity_stock']); ?>/<?= $value['unit_name_stock'] ?>
                                            </td>
                                            <td style="width:80px" class="center">
                                                <?php echo formatNumber($value['quantity_payment']); ?>/<?= $value['unit_name_payment'] ?>
                                            </td>
                                            <!-- <td style="width:100px" class="text-center"><?= $value['unit'] ?></td> -->
                                            <?php if (has_permission('purchase_order', '', 'view_price')) {  ?>
                                                <td class="text-right <?= $view_price ?>" style="width:100px">
                                                    <?php if (get_staff_user_id() == 67) {  ?>
                                                        <div class="type_v1"><?= ch_EditColumSelectInput_po(formatNumber($value['price_suppliers']), $value['id'], '', '<a class="pointer" id="price_suppliers_text_v2_' . $value['id'] . '" target="_blank" >' . number_format($value['price_suppliers']) . '</a>', '', admin_url('purchase_order/price_suppliers/' . $value['id'] . '/' . $items->id), 'class="formUpdateDataTable"') ?></div>
                                                        <div class="type_v2 hide" data-id="<?= $value['id'] ?>" class="price_suppliers_input"><input onkeyup="formatNumBerKeyUp(this)" type="text" name="price_suppliers" id="price_suppliers" class="height_auto  price_suppliers H_input align_right" value="<?= number_format($value['price_suppliers']) ?>"></div>
                                                        <!-- <?php echo formatNumber($value['price_suppliers']); ?> -->

                                                    <?php } else { ?>
                                                        <?php echo formatNumber($value['price_suppliers']); ?>
                                                    <?php } ?>
                                                </td>
                                                <td style="width:100px" class="align_right <?= $view_price ?>">
                                                    <?php echo formatNumber($value['promotion_expected']); ?>
                                                </td>
                                                <td style="width:80px" class="center <?= $view_price ?>">
                                                    <?= (formatNumber($value['tax_rate'])) ?> %
                                                </td>
                                                <td style="width:100px" class="align_right <?= $view_price ?> total_suppliers">
                                                    <?php echo formatNumber($value['total_suppliers']); ?>
                                                </td>
                                            <?php } ?>
                                            <td style="width:100px">
                                                <?php echo $value['note']; ?>
                                            </td>
                                        </tr>
                                    <?php $subtotal += $value['total_suppliers'];
                                    } ?>
                                </tbody>
                                <?php if (has_permission('purchase_order', '', 'view_price')) {  ?>
                                    <tfoot class="bold <?= $view_price ?>">
                                        <tr>
                                            <th class="text-center" style="text-transform: uppercase;" colspan="9">
                                                <?= lang('tnh_grand_total') ?></th>
                                            <th  class="tfoot_grand_total align_right"></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                <?php } ?>
                            </table>
                        </div>
                    <?php } ?>
                    <?php if (has_permission('purchase_order', '', 'view_price')) {  ?>
                        <div class="col-md-6 <?= $view_price ?>">
                            <table class="table tnh-tb table-bordered table-hover" style="margin-top: 10px;">
                                <tbody>
                                    <tr>
                                        <td><?= _l('estimate_discount') ?></td>
                                        <?php $total = (($items->valtype_check_suppliers == 1) ? (($items->discount_percent_suppliers / 100) * $subtotal) : $items->discount_percent_suppliers); ?>
                                        <td class="text-right"><?= formatNumber($total) ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= _l('ch_delivery_cost') ?></td>
                                        <td class="text-right"><?= formatNumber($items->delivery_cost) ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= _l('ch_reduce_cost') ?></td>
                                        <td class="text-right"><?= formatNumber($items->reduce_cost) ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= _l('total_c_exchange') ?></td>
                                        <td class="text-right"><?= formatNumber($items->total_cqd) ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= _l('currency_lowercase') ?></td>
                                        <td class="text-right"><?= $amount_to_vnd->name ?> <?= formatNumber($items->amount_to_vnd) ?><?= $amount_to_vnd->symbol ?></td>
                                    </tr>
                                    <tr class="success" style="font-weight: 700;">
                                        <td><?= lang('tnh_grand_total', 'grand_total') ?></td>
                                        <td class="td-grand-total-all text-right">
                                            <?= formatNumber($items->total_dqd) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                    <div class="clearfix"></div>
                </div>
                <div role="tabpanel" class="tab-pane" id="item_info_detail">
                    <?php
                    $subtotal = 0;
                    if (isset($items->items) && (count($items->items) > 0)) {  ?>
                        <div class="table-responsive">
                            <table id="view-enquiry-detail" class="table" style="width: 100%; max-height: 400px !important;">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="center"><?= _l('image') ?></th>
                                        <th style="width: 250px;" class="text-left"><?php echo _l('ch_items_name_t'); ?>
                                        </th>
                                        <th style="width: 100px;" class="text-center"><?php echo _l('item_quantity'); ?>
                                        </th>
                                        <th style="width: 100px;" class="text-left"><?= _l('item_unit'); ?></th>
                                        <?php if (has_permission('purchase_order', '', 'view_price')) {  ?>
                                            <th style="width: 100px;" class="text-right <?= $view_price ?>">
                                                <?php echo _l('ch_price_ncc'); ?></th>
                                            <th style="width: 100px;" class="text-right <?= $view_price ?>">
                                                <?php echo _l('promotion_suppliers'); ?></th>
                                            <th style="width: 100px;" class="text-center <?= $view_price ?>"><?= _l('tax'); ?>
                                            </th>
                                            <th style="width: 100px;" class="text-right <?= $view_price ?>">
                                                <?php echo _l('amount_suppliers_vnd'); ?></th>
                                        <?php } ?>
                                        <th style="width: 100px;" class="text-left"><?= _l('note'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items->items as $key => $value) { ?>
                                        <tr>
                                            <?php if ($value['avatar'] == '') {
                                                $value['avatar'] = 'uploads/no-img.jpg';
                                            }
                                            $code_plan = '';
                                            $plan = get_table_where('tbl_productions_plan', ['id' => $value['plan_id']], '', 'row_array');
                                            if (!empty($plan)) {
                                                $code_plan = $plan['reference_no'];
                                            }
                                            ?>
                                            <td style="width:80px" class="center">
                                                <div class="preview_image text-center" style="width: 100px;margin-bottom:0;margin-top:0">
                                                    <div class="display-block contract-attachment-wrapper img-<?= $value['id'] ?>">
                                                        <div>
                                                            <a href="<?= $value['avatar'] ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                <img class="mbot5" style="border-radius: 50%;width: 2em;height: 2em;" src="<?= $value['avatar'] ?>">
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <?= format_item_purchases($value['type']) ?> -->
                                            </td>
                                            <td style="width:250px">
                                                <?php echo $value['name_item'] . ' (' . $value['code_item'] . ')'; ?><br><?= format_item_color($value['product_id'], $value['type']) ?>
                                                <div class="label label-success"><?= $code_plan ?></div>
                                            </td>
                                            <td style="width:80px" class="center">
                                                <?php echo formatNumber($value['quantity_suppliers']); ?>
                                            </td>
                                            <td style="width:100px" class="text-center"><?= $value['unit'] ?></td>
                                            <?php if (has_permission('purchase_order', '', 'view_price')) {  ?>
                                                <td class="align_right <?= $view_price ?>">
                                                    <?php echo formatNumber($value['price_suppliers']); ?>
                                                </td>
                                                <td style="width:100px" class="align_right <?= $view_price ?>">
                                                    <?php echo formatNumber($value['promotion_expected']); ?>
                                                </td>
                                                <td style="width:80px" class="center <?= $view_price ?>">
                                                    <?= (formatNumber($value['tax_rate'])) ?> %
                                                </td>
                                                <td style="width:100px" class="align_right <?= $view_price ?>">
                                                    <?php echo formatNumber($value['total_suppliers']); ?>
                                                </td>
                                            <?php } ?>
                                            <td style="width:100px">
                                                <?php echo $value['note']; ?>
                                            </td>
                                        </tr>
                                    <?php $subtotal += $value['total_suppliers'];
                                    } ?>
                                </tbody>
                                <?php if (has_permission('purchase_order', '', 'view_price')) {  ?>
                                    <tfoot class="bold <?= $view_price ?>">
                                        <tr>
                                            <th class="text-center" style="text-transform: uppercase;" colspan="7">
                                                <?= lang('tnh_grand_total') ?></th>
                                            <th></th>
                                            <th></th>

                                        </tr>
                                    </tfoot>
                                <?php } ?>
                            </table>
                        </div>
                    <?php } ?>
                    <?php if (has_permission('purchase_order', '', 'view_price')) {  ?>
                        <div class="col-md-6 <?= $view_price ?>">
                            <table class="table tnh-tb table-bordered table-hover" style="margin-top: 10px;">
                                <tbody>
                                    <tr>
                                        <td><?= _l('estimate_discount') ?></td>
                                        <?php $total = (($items->valtype_check_suppliers == 1) ? (($items->discount_percent_suppliers / 100) * $subtotal) : $items->discount_percent_suppliers); ?>
                                        <td class="text-right"><?= formatNumber($total) ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= _l('ch_delivery_cost') ?></td>
                                        <td class="text-right"><?= formatNumber($items->delivery_cost) ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= _l('ch_reduce_cost') ?></td>
                                        <td class="text-right"><?= formatNumber($items->reduce_cost) ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= _l('total_c_exchange') ?></td>
                                        <td class="text-right"><?= formatNumber($items->total_cqd) ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= _l('currency_lowercase') ?></td>
                                        <td class="text-right"><?= $amount_to_vnd->name ?> <?= formatNumber($items->amount_to_vnd) ?><?= $amount_to_vnd->symbol ?></td>
                                    </tr>
                                    <tr class="success" style="font-weight: 700;">
                                        <td><?= lang('tnh_grand_total', 'grand_total') ?></td>
                                        <td class="td-grand-total-all text-right">
                                            <?= formatNumber($items->total_dqd) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                    <div class="clearfix"></div>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_feedback">
                    <div class="col-md-12 mtop5">
                        <?php include_once(APPPATH . 'views/admin/feedback/purchase_order/feedback.php'); ?>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="item_activity">
                    <div class="activity-container">
                        <?php foreach ($dataLog as $key => $value) { ?>
                            <div class="feed-item">
                                <div class="activity-text">
                                    <?= staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small'); ?>
                                    <?= get_staff_full_name($value['staff_id']); ?>
                                </div>
                                <div class="activity-time">
                                    <?= time_ago($value['date']) ?> <span class="activity-module"><?= _l($value['table_obj']) ?></span>
                                </div>
                                <div>
                                    <?= $value['content'] ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $('body').on('click', '.editDataTable_ch', function(e) {
            var type = $(this).attr('data-type');
            var client = $(this).attr('data-client');
            var _td = $(this).parents('td');
            _td.find('.lableScript').addClass('hide');
            _td.find('.inputScript').removeClass('hide');
            appValidateForm($('.formUpdateDataTable'), {}, manage_Udpdatecolum);
        })
        $('body').on('click', '.closeEditData', function(e) {
            var type = $(this).attr('data-type');
            var client = $(this).attr('data-client');
            var _td = $(this).parents('td');
            _td.find('.lableScript').removeClass('hide');
            _td.find('.inputScript').addClass('hide');
            var id = _td.find('.inputScript').find('input#id_ch').val();
            _td.find('.inputScript').find('input.ChangeDataTable').val($('#price_suppliers_text_v2_' + id).text());
            appValidateForm($('.formUpdateDataTable'), {}, manage_Udpdatecolum);
        })

        function manage_Udpdatecolum(form) {
            var data = $(form).serialize();
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            action = form.action;
            return $.post(action, data).done(function(form) {
                form = JSON.parse(form)
                $('#price_suppliers_text_v2_' + form.id).text(form.total);
                var _td = $('#price_suppliers_text_v2_' + form.id).parent().parent();
                _td.find('.lableScript').removeClass('hide');
                _td.find('.inputScript').addClass('hide');
                var _tdd = $('#price_suppliers_text_v2_' + form.id).parent().parent().parent();
                _tdd.find('.type_v2').find('input.price_suppliers').val(form.total);
                var _tddd = $('#price_suppliers_text_v2_' + form.id).parents('tr');
                _tddd.find('.total_suppliers').html(form.subtotal);
                alert_float(form.success, form.messeger);
                getTotalPrice();
            }), !1
        }

        function getTotalPrice() {
            var items = $('table.table-view-enquiry tbody').find('tr');
            var total_quantity_expected = 0;
            $.each(items, (index, value) => {
                total_quantity_expected += parseFloat($(value).find('td.total_suppliers').text().replace(/\,/g, ''));
            });
            $('.dataTables_scrollFoot').find('.tfoot_grand_total').html(tnhFormatNumber(total_quantity_expected));
        }
        $(document).ready(function() {
            $('.tip').tooltip();
        });
        $('body').on('hidden.bs.modal', '#view_purchase_order', function() {
            $('#purchase_order_data').html('');
            tAPI.draw('page');
        });
        var dtItems;
        var dtItemsDetail;

        function unformat_number(number) {
            var _number = 0;
            if (number) {
                _number = number.replace(/[^\-\d\.]/g, '');
            }
            return _number;
        };

        function formatNumber(nStr, decSeperate = ".", groupSeperate = ",") {
            nStr += '';
            x = nStr.split(decSeperate);
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            x2 = x2.substr(0, 2);
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
            }
            return x1 + x2;
        };
        $(document).ready(function() {
            var flagView = <?= !empty($flagView) ? 1 : 0; ?>;
            dtItems = $('#view-enquiry').DataTable({
                "language": app.lang.datatables,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                // scrollY: '300px',
                scrollX: true,
                // fixedColumns:   {
                //     leftColumns: 4,
                //     rightColumns: 0
                // },
                // 'searching': false,
                'ordering': false,
                // 'paging': false,
                // "info": false,
                'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                },
                "footerCallback": function(row, data, start, end, display) {
                    var api = this.api(),
                        data;
                    var api = this.api(),
                        data;
                    pageGrandAmount = api
                        .column(9, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + Number(intVal(b));
                        }, 0);

                    $(api.column(9).footer()).html('<div class="text-right">' + formatNumber(
                        pageGrandAmount) + '</div>');
                }
            });
            setTimeout(function() {
                dtItems.draw('page');
            }, 150);

        });
        $(document).ready(function() {
            dtItemsDetail = $('#view-enquiry-detail').DataTable({
                "language": app.lang.datatables,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "<?= lang('all') ?>"]
                ],

                'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                },
                "footerCallback": function(row, data, start, end, display) {
                    var api = this.api(),
                        data;
                    var api = this.api(),
                        data;
                    pageGrandAmount = api
                        .column(7, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + Number(intVal(b));
                        }, 0);

                    $(api.column(7).footer()).html('<div class="text-right">' + formatNumber(
                        pageGrandAmount) + '</div>');
                }
            });
            setTimeout(function() {
                dtItemsDetail.draw('page');
            }, 150);
        });
    </script>

</div>