<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<style>
    #tnhModal2 {
        z-index: 10002;
    }
</style>
<div class="modal-dialog modal-lg" style="width: 90%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_view_order') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $edit = $this->perEditOrders ? '<a href="' . base_url('admin/orders/edit/' . $order['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('tnh_order') . '</a>' : '';
                  
                    $print = $this->perPrintOrders ? '<a href="' . base_url('admin/orders/print_orders/' . $order['id']) . '" target="_blank"><i class="fa fa-print"></i> ' . lang('print') . ' ' . lang('tnh_order') . '</a>' : '';

                    $convertDelivery = $this->perAddOrders ? '<a data-tnh="modal" class="tnh-modal tnh-convert-delivery ' . ($order['status'] != 'approved' ? 'tnh-disabled' : '') . '" href="' . base_url('admin/orders/convert_delivery/' . $order['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-truck"></i> ' . lang('convert_delivery') . '</a>' : '';

                    $convert_contract = $this->perAddOrders ? '<a  data-tnh="modal" class="tnh-modal cvc ' . (($order['status'] != 'approved' || $order['contract_id'] != 0)  ? 'tnh-disabled' : '') . '" href="' . base_url('admin/quotes/convert_contract/' . $order['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-exchange"></i> ' . lang('tnh_convert_contract') . '</a>' : '';

                    $addPayment = $this->perAddOrders ? '<a data-tnh="modal" class="tnh-modal tnh-add-payment ' . ($order['status'] != 'approved' ? 'tnh-disabled' : '') . '" href="' . base_url('admin/orders/add_payment/' . $order['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-money"></i> ' . lang('tnh_payment') . '</a>' : '';

                    $actions = '
                            <div class="dropdown pull-right mbot5">
                                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                                ' . lang('actions') . '
                                <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 200px;">
                                    <li>' . $edit . '</li>
                                    <li>' . $print . '<li>
                                    <li>' . $convertDelivery . '<li>
                                    <li>' . $convert_contract . '<li>
                                    <li>' . $addPayment . '<li>
                                </ul>
                            </div>';
                    echo $actions;
                    ?>
                    <div class="status_ch">
                        <?php
                        if ($order['status'] == "approved") {
                            $user_status = '<div class="mtop10">' . lang('tnh_user_agree') . '</div>';
                        } else {
                            $user_status = '';
                        }
                        if ($order['status'] == "un_approved") {
                            $str = '<div  style="margin-right: 10px;margin-top: 5px;" class="pull-right mbot5  text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a id=\'agree\' order_id=\'' . $order['id'] . '\' value=\'approved\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('tnh_un_approved') . '</span></div>' . $user_status;
                        } else if ($order['status'] == "approved") {
                            $str =  '<div style="margin-right: 10px;margin-top: -3px;" class="pull-right mbot5 text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a id=\'agree\' order_id=\'' . $order['id'] . '\' value=\'un_approved\' class=\'btn btn-danger\'>' . lang('tnh_un_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('tnh_approved') . '</span></div>' . $user_status;
                        }

                        echo $str;
                        ?>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="lead-view" id="leadViewWrapper">

                        <div class="row-contro">
                            <div><?= lang('date') ?>: </div>
                            <div class="ml-at t-bold"><?= _dt($order['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_reference_orders') ?>: </div>
                            <div class="ml-at t-bold"><?= ($order['reference_no']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('customers') ?>: </div>
                            <div class="ml-at t-bold"><?= $company['company_short'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_person_contact') ?>: </div>
                            <div class="ml-at t-bold"><?= $person_contact['firstname'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_address_delivery') ?>: </div>
                            <div class="ml-at t-bold">
                                <?= !empty($address_delivery) ? $address_delivery['address'] : '' ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_employees_charge') ?>: </div>
                            <div class="ml-at t-bold"><?= !empty($employee) ? $employee : '' ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('h_branch') ?>: </div>
                            <div class="ml-at t-bold"><?= !empty($name_branch) ? $name_branch['name'] : '' ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_transporters') ?>: </div>
                            <div class="ml-at t-bold"><?= $transport['company'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('note') ?>: </div>
                            <div class="ml-at t-bold"><?= $order['note'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_agree') ?>: </div>
                            <div class="ml-at t-bold"><?= lang($order['status']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('table_set_prices') ?>: </div>
                            <div class="ml-at t-bold">
                                <?php
                                    $dtPrice = get_table_where('tblgroup_price', ['id' => $order['table_price_id']], '', 'row_array');
                                    if (!empty($dtPrice)) {
                                        echo $dtPrice['name_price'];
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('tnh_currencies') ?>: </div>
                            <div class="ml-at t-bold"><?= !empty($currencies) ? $currencies['name'] : '' ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('amount_to_vnd') ?>: </div>
                            <div class="ml-at t-bold"><?= formatMoney($order['amount_to_vnd']) ?></div>
                        </div>
                        <?php
                            $dtCustomer = $this->site_model->rowCustomer($order['customer_id']);
                            $is_separate_guest = $dtCustomer['is_separate_guest'];
                            $styleGuest = '';
                            if (empty($is_separate_guest)) {
                                $styleGuest = 'style="display: none;"';
                            }
                        ?>
                        <div class="row-contro" <?= $styleGuest ?>>
                            <div><?= lang('tnh_so') ?>: </div>
                            <div class="ml-at t-bold"><?= $order['so'] ?></div>
                        </div>
                        <div class="row-contro" <?= $styleGuest ?>>
                            <div><?= lang('tnh_pi') ?>: </div>
                            <div class="ml-at t-bold"><?= $order['pi'] ?></div>
                        </div>
                        <div class="row-contro" <?= $styleGuest ?>>
                            <div><?= lang('tnh_po_style') ?>: </div>
                            <div class="ml-at t-bold"><?= $order['po_style'] ?></div>
                        </div>
                        <div class="row-contro" <?= $styleGuest ?>>
                            <div><?= lang('tnh_item_code_tem') ?>: </div>
                            <div class="ml-at t-bold"><?= $order['item_code'] ?></div>
                        </div>
                        <?php if($order['is_cancel']): ?>
                            <div class="row-contro">
                                <div><?= lang('tnh_cancel_order') ?>: </div>
                                <div class="ml-at t-bold"><?= lang('yes') ?></div>
                            </div>
                            <div class="row-contro">
                                <div><?= lang('Ngày hủy') ?>: </div>
                                <div class="ml-at t-bold"><?= _d($order['date']) ?></div>
                            </div>
                            <div class="row-contro">
                                <div><?= lang('Người hủy') ?>: </div>
                                <div class="ml-at t-bold"><?= get_staff_full_name($order['user_cancel']) ?></div>
                            </div>
                            <div class="row-contro">
                                <div><?= lang('tnh_note_cancel') ?>: </div>
                                <div class="ml-at t-bold"><?= $order['note_cancel'] ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">
                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                        <label for="tab1"><?= lang('tnh_items') ?></label>

                        <input type="radio" name="tabset" id="tab0" aria-controls="tab_feedback">
                        <label for="tab0">
                            <i class="icon-foso fa fa-comments-o"></i>
                            <?= _l('FeedBack') ?>
                            <span class="badge menu-badge bg-warning"><?= !empty($feedback) ? count($feedback) : '' ?></span>
                        </label>
                        <!-- Tab 8 -->
                        <input type="radio" name="tabset" id="tab8" aria-controls="view-delivery">
                        <label for="tab8"><?= lang('tnh_history_delivery') ?></label>
                        <!-- Tab 3 -->
                        <input type="radio" name="tabset" id="tab3" aria-controls="view-attachments">
                        <label for="tab3"><?= lang('attachments_file') ?></label>
                        <!-- Tab 4 -->
                        <input type="radio" name="tabset" id="tab4" aria-controls="view-returned-goods">
                        <label for="tab4"><?= lang('returned_goods') ?></label>
                        <!-- Tab 6 -->
                        <input type="radio" name="tabset" id="tab6" aria-controls="view-complain">
                        <label for="tab6"><?= lang('complain') ?></label>
                        <!-- Tab 7 -->
                        <input type="radio" name="tabset" id="tab7" aria-controls="view-activity-log">
                        <label for="tab7"><?= lang('activity_log_puchases') ?></label>

                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <div class="table-responsive">
                                    <table id="table-items" class="table table-hover dont-responsive-table" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="width: 30px;" class="text-center"><?= lang('tnh_numbers') ?></th>
                                                <th style="width: 80px" class="text-center"><?= lang('tnh_images') ?></th>
                                                <th style="width: 120px;" class="text-center"><?= lang('tnh_product_code') ?></th>
                                                <th style="width: 120px;" class="text-center"><?= lang('tnh_product_name_customer') ?></th>
                                                <th style="width: 50px;" class="text-center"><?= lang('tnh_mode_product') ?></th>
                                                <th style="width: 50px;" class="text-center"><?= lang('tnh_dvt') ?></th>
                                                <th style="width: 70px;" class="text-center"><?= lang('tnh_total_quantity_put') ?></th>
                                                <th style="width: 70px;" class="text-center"><?= lang('tnh_sample_quantity') ?></th>
                                                <th style="width: 70px;" class="text-center"><?= lang('tnh_total_quantity') ?></th>
                                                <th style="width: 70px;" class="text-center"><?= lang('tnh_unit_price') ?></th>
                                                <th style="width: 70px;" class="text-center"><?= lang('tnh_discount_percent') ?></th>
                                                <th style="width: 70px;" class="text-center"><?= lang('tnh_total_amount') ?></th>
                                                <th style="width: 100px;" class="text-center"><?= lang('cong_shipment_date') ?></th>
                                                <th style="width: 120px;" class="text-center"><?= lang('Chi tiết giao hàng') ?></th>
                                                <th style="width: 100px;" class="text-center"><?= lang('note') ?></th>
                                                <th class="hide" style="width: 100px;"><?= lang('note') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?= $bodyItems ?>
                                        </tbody>
                                        <tfoot class="bold">
                                            <tr>
                                                <td class="text-center" style="text-transform: uppercase;" colspan="4">
                                                    <?= lang('tnh_grand_total') ?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="hide"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                            </section>
                            <section id="tab_feedback" class="tab-panel">
                                <div class="col-md-12 mtop5">
                                    <?php include_once(APPPATH . 'views/admin/feedback/orders/feedback.php'); ?>
                                </div>
                                <div class="clearfix"></div>
                            </section>
                            <section id="view-delivery" class="tab-panel">
                                <table id="table-delivery" class="table dt-tnh tnh-table table-hover table-bordered table-condensed">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;"><?= lang('tnh_numbers') ?></th>
                                            <th><?= lang('date') ?></th>
                                            <th style="width: 140px;"><?= lang('tnh_reference_deliveries') ?></th>
                                            <th><?= lang('tnh_address_delivery') ?></th>
                                            <th style="width: 140px;"><?= lang('tnh_employees_charge') ?></th>
                                            <th style="width: 120px;"><?= lang('tnh_total_quantity') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_created_by') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_date_creted') ?></th>
                                            <th style="width: 100px;"><?= lang('note') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($deliveries)) : ?>
                                            <?php foreach ($deliveries as $key => $value) : ?>
                                                <tr>
                                                    <td class="text-center"><?= ++$key ?></td>
                                                    <td><?= _d($value['date']) ?></td>
                                                    <td><?= $value['reference_no'] ?></td>
                                                    <td><?= $value['address_delivery'] ?></td>
                                                    <td><?= $value['name_employee'] ?></td>
                                                    <td><?= $value['total_quantity'] ?></td>
                                                    <td><?= $value['created_by'] ?></td>
                                                    <td><?= _d($value['date_created']) ?></td>
                                                    <td><?= $value['note'] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </tbody>
                                </table>
                            </section>
                            <section id="view-attachments" class="tab-panel">
                                <table class="tnh-table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;" class="text-center"><?= lang('tnh_numbers') ?></th>
                                            <th class=""><?= lang('tnh_name_attachment') ?></th>
                                            <!-- <th style="width: 80px;" class="text-center"><?= lang('actions') ?></th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($order['attachments'])) : ?>
                                            <?php $attachments = explode('||', $order['attachments']); ?>
                                            <?php foreach ($attachments as $key => $value) : ?>
                                                <tr>
                                                    <td class="text-center"><?= (++$key) ?></td>
                                                    <td>
                                                        <a href="<?= base_url('uploads/orders/' . $value) ?>" target="_blank"><?= $value ?></a>
                                                    </td>
                                                    <!-- <td class="text-center"></td> -->
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </tbody>
                                </table>
                            </section>
                            <section id="view-returned-goods" class="tab-panel">
                                <table id="tb-returned_goods" class="table dt-tnh tnh-table table-hover table-bordered table-condensed dataTable">
                                    <thead>
                                        <tr>
                                            <th><?= lang('tnh_numbers') ?></th>
                                            <th><?= lang('date') ?></th>
                                            <th><?= lang('tnh_reference_no_returned_goods') ?></th>
                                            <th><?= lang('tnh_product_code') ?></th>
                                            <th><?= lang('tnh_product_name') ?></th>
                                            <th><?= lang('quantity') ?></th>
                                            <th><?= lang('price') ?></th>
                                            <th><?= lang('tnh_total_amount') ?></th>
                                            <th><?= lang('note') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($returned_goods)) : ?>
                                            <?php foreach ($returned_goods as $key => $value) : ?>
                                                <tr>
                                                    <td class="text-center"><?= ++$key ?></td>
                                                    <td><?= _d($value['date']) ?></td>
                                                    <td><?= $value['reference_no'] ?></td>
                                                    <td><?= $value['item_code'] ?></td>
                                                    <td><?= $value['item_name'] ?></td>
                                                    <td class="text-center"><?= formatNumber($value['quantity']) ?></td>
                                                    <td class="text-right"><?= formatMoney($value['price']) ?></td>
                                                    <td class="text-right"><?= formatMoney($value['amount']) ?></td>
                                                    <td><?= $value['note_item'] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </tbody>
                                    <tfoot class="bold">
                                        <tr>
                                            <th class="text-center" style="text-transform: uppercase;" colspan="4">
                                                <?= lang('tnh_grand_total') ?></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </section>
                            <section id="view-complain" class="tab-panel">
                                <table id="table-complain" class="table dt-tnh tnh-table table-hover table-bordered table-condensed dataTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;"><?= lang('tnh_numbers') ?></th>
                                            <th><?= lang('tnh_topic') ?></th>
                                            <th><?= lang('tags') ?></th>
                                            <th><?= lang('departments') ?></th>
                                            <th><?= lang('tnh_status') ?></th>
                                            <th><?= lang('ticket_settings_priority') ?></th>
                                            <th><?= lang('ticket_dt_last_reply') ?></th>
                                            <th><?= lang('ticket_date_created') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($complains)) : ?>
                                            <?php foreach ($complains as $key => $value) : ?>
                                                <tr>
                                                    <td class="text-center"><?= ++$key ?></td>
                                                    <td><?= $value['subject'] ?></td>
                                                    <td><?= render_tags($value['tags']) ?></td>
                                                    <td><?= $value['name_deparment'] ?></td>
                                                    <td><?= ticket_status_translate($value['status']) ?></td>
                                                    <td><?= ticket_priority_translate($value['priority']) ?></td>
                                                    <td><?= _d($value['lastreply']) ?></td>
                                                    <td><?= _d($value['date']) ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </tbody>
                                </table>
                            </section>
                            <section id="view-activity-log" class="tab-panel">
                                <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                    <?php
                                    $history = getActivityLogByObjId($order['id'], 'orders');
                                    ?>
                                    <?php if (!empty($history)) : ?>
                                        <?php foreach ($history as $key => $value) : ?>
                                            <?php
                                            echo '<div class="feed-item">
                                                    <div class="activity-text">
                                                        ' . staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small') . '' . $value['staff_name'] . '
                                                    </div>
                                                    <div class="activity-time">
                                                        ' . time_ago($value['date']) . '<span class="activity-module">' . _l($value['type_parent_obj']) . '</span>
                                                    </div>
                                                    <div>
                                                        ' . $value['content'] . '
                                                    </div>
                                                </div>';
                                            ?>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <table class="table tnh-tb table-bordered table-hover" style="margin-top: 10px;">
                        <tbody>
                            <tr>
                                <td style="width: 40%;"><?= lang('tax', 'tax') ?></td>
                                <td class="text-right"><?= $order['tax_name'] ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('tnh_cost_delivery', 'cost_delivery') ?></td>
                                <td class="text-right">
                                    <span class="label <?= $order['charge_party'] == "customer" ? "btn-success" : "btn-primary" ?>"><?= lang('tnh_' . $order['charge_party']) ?></span>
                                    <?= formatMoney($order['cost_delivery']) ?>
                                </td>
                            </tr>
                            <tr class="success" style="font-weight: 700;">
                                <td><?= lang('tnh_grand_total', 'grand_total') ?></td>
                                <td class="td-grand-total-all text-right"><?= formatMoney($order['grand_total']) ?></td>
                            </tr>
                            <tr class="success" style="font-weight: 700;">
                                <td><?= lang('Tổng cộng(VND)', 'grand_total') ?></td>
                                <td class="td-grand-total-all text-right"><?= formatMoney($order['grand_total'] * $order['amount_to_vnd']) ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('tnh_htlgtcn', 'htlgtcn') ?></td>
                                <td class="text-right">
                                    <?= formatMoney($total_returns['total_return']) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table tnh-tb table-bordered table-hover" style="margin-top: 10px;">
                        <tbody>
                            <tr>
                                <td style="width: 40%;"><?= lang('tnh_payment_money', 'tnh_payment_money') ?></td>
                                <td class="text-right"><?= formatMoney($order['total_payment']) ?></td>
                            </tr>
                            <tr class="hide">
                                <td><?= lang('staff_coupon', 'staff_coupon') ?></td>
                                <td class="text-right"><?= $staff_coupon ?></td>
                            </tr>
                            <tr class="hide">
                                <td><?= lang('acs_sales_payment_modes_submenu', 'acs_sales_payment_modes_submenu') ?>
                                </td>
                                <td class="text-right"><?= $payment_mode['name'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6 pull-right mtop10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div><?= lang('tnh_created_by') ?>: <?= $created_by ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($order['date_created']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($updated_by)) : ?>
                                    <div><?= lang('tnh_updated_by') ?>: <?= $updated_by ?></div>
                                    <div><?= lang('tnh_date_updated') ?>: <?= _dt($order['date_updated']) ?></div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="view_order_id" id="view_order_id" class="form-control" value="<?= $id ?>">
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var flagView = <?= !empty($flagView) ? 1 : 0; ?>;
        var dtItems = $('#table-items').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": dataTableLengthMenu(),
            // scrollY: '300px',
            // scrollX: true,
            // fixedColumns: {
            //     leftColumns: 0,
            //     rightColumns: 0
            // },
            // 'searching': false,
            // 'ordering': false,
            // 'paging': false,
            // "info": false,
            "responsive": true,
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;

                pageTotalQ = api
                    .column(6, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                pageTotalQSamp = api
                    .column(7, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                pageTotalQuantity = api
                    .column(8, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                pageTotalAmount = api
                    .column(11, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(6).footer()).html('<div class="text-center">' + tnhFormatNumber(
                    pageTotalQ) + '</div>');
                $(api.column(7).footer()).html('<div class="text-center">' + tnhFormatNumber(
                    pageTotalQSamp) + '</div>');
                $(api.column(8).footer()).html('<div class="text-center">' + tnhFormatNumber(
                    pageTotalQuantity) + '</div>');
                $(api.column(11).footer()).html('<div class="text-right">' + tnhFormatMoney(
                    pageTotalAmount) + '</div>');
            }
        });

        function format(d) {
            return d[15];
        }

        $('#table-items').DataTable().rows().every(function() {
            var tr = $(this.node());
            var row = dtItems.row(tr);

            if (row.child.isShown()) {} else {
                row.child(format(row.data())).show();
                tr.addClass('shown');
            }
        });
        setTimeout(function() {
            dtItems.draw()
        }, 500);

        $('#tab1').click(function(event) {
            dtItems.draw();
        });

        var dtPurchase = $('#table-purchase-items').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": dataTableLengthMenu(),
            // scrollX: true,
            // scrollY: true,
            // fixedColumns:   {
            //     leftColumns: 3,
            //     rightColumns: 0
            // },
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;
                pageTotalQuantity = api
                    .column(7, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);


                $(api.column(7).footer()).html('<div class="text-center">' + tnhFormatNumber(
                    pageTotalQuantity) + '</div>');
            }
        });

        $('#tab5').click(function(event) {
            dtPurchase.draw();
        });

        $('#tb-returned_goods').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": dataTableLengthMenu(),
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;
                pageTotalQuantity = api
                    .column(5, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                pageTotalAmount = api
                    .column(7, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                $(api.column(5).footer()).html('<div class="text-center">' + tnhFormatNumber(
                    pageTotalQuantity) + '</div>');
                $(api.column(7).footer()).html('<div class="text-right">' + tnhFormatMoney(
                    pageTotalAmount) + '</div>');
            }
        });

        $('#table-complain').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": dataTableLengthMenu(),
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {}
        });

        $('#table-delivery').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": dataTableLengthMenu(),
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {}
        });

        if (flagView == 1) {
            oTable.draw('page');
        }
    });
</script>

<script>
    function viewEdit(_this) {
        var row_update = $(_this).parents('.row_update');
        row_update.find('.view_data').addClass('hide');
        row_update.find('.input_data').removeClass('hide');


        var date =  row_update.find('.date_ship').attr('data-value');
        row_update.find('.date_ship').val(date);

        var quantity =  row_update.find('.quantity_ship').attr('data-value');
        row_update.find('.quantity_ship').val(quantity);

        init_datepicker();
    }
    function hideEdit(_this) {
        var row_update = $(_this).parents('.row_update');
        row_update.find('.view_data').removeClass('hide');
        row_update.find('.input_data').addClass('hide');

        var date =  row_update.find('.date_ship').attr('data-value');
        row_update.find('.view_data').find('.date').html(date);


        var quantity =  row_update.find('.quantity_ship').attr('data-value');
        row_update.find('.view_data').find('.quantity').html(quantity);
    }

    function submitFrom(_this) {
        var row_update = $(_this).parents('.row_update');

        var date_ship = $(row_update).find('.date_ship').val();
        var id = $(row_update).find('.id').val();
        var quantity_ship = $(row_update).find('.quantity_ship').val();

        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['date_ship'] = date_ship;
        data['quantity_ship'] = quantity_ship;
        $.post(admin_url + 'orders/update_ship/' + id, data, function (result) {
            result = JSON.parse(result);
            if(result.success) {
                row_update.find('.view_data').removeClass('hide');
                row_update.find('.input_data').addClass('hide');

                row_update.find('.view_data').find('.date').html(result.date);
                row_update.find('.date_ship').attr('data-value', result.date);

                row_update.find('.view_data').find('.quantity').html(result.quantity);
                row_update.find('.quantity_ship').attr('data-value', result.quantity);
            }
            alert_float(result.alert_type, result.message);
        })
    }
</script>