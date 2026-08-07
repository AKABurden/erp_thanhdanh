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
<div class="modal fade in" id="view_other_payslips_view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?= ($items->is_advance == 1) ? _l('ch_advance') : _l('ch_orher_payslip_t') ?> </span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12  pull-left">
                        <div class="panel panel-success">
                            <?php
                            $type = '';
                            if (!isset($items))
                                $type = 'warning';
                            elseif ($items->status == 0)
                                $type = 'warning';
                            elseif ($items->status == 2)
                                $type = 'danger';
                            elseif ($items->status == 1)
                                $type = 'info';
                            ?>
                            <div style="right: 10px;" class="ribbon <?= $type ?>" project-status-ribbon-2="">
                                <?php
                                if (isset($items)) {
                                    $status = format_status_pay_slip_s($items->status, '', false);
                                }
                                ?>
                                <span><?= $status ?></span>
                            </div>
                            <div class="panel-heading">
                                <h3 class="panel-title"><?= _l('ch_information_t') ?></h3>
                            </div>
                            <div class="panel-body">
                                <div class="well well-sm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div>
                                                <b><?= _l('ch_code_p') ?>: </b><?php echo $items->prefix . '-' . $items->code ?>
                                            </div>
                                            <div><b><?= _l('ch_staff_crate_rfq') ?>: </b><?php echo staff_profile_image($items->staff_id, array('staff-profile-image-small mright5 img_ch'), 'small', array(
                                                                                                'data-toggle' => 'tooltip',
                                                                                                'data-title' => get_staff_full_name($items->staff_id)
                                                                                            )) . get_staff_full_name($items->staff_id) ?></div>
                                            <div><b><?= _l('ch_date_p') ?>: </b><?php echo _d($items->date) ?></div>
                                            <?php if ($items->objects == 2) {
                                                $supplier = get_table_where('tblsuppliers', array('id' => $items->objects_id), '', 'row');
                                                echo '
                                                  <div><b style="">' . _l('ch_units_in') . ': </b><span style="">' . $supplier->company . '</span><br/></div>';
                                            }
                                            if ($items->objects == 1) {
                                                $client = get_table_where('tblclients', array('userid' => $items->objects_id), '', 'row');
                                                echo '
                                                  <div><b style="">' . _l('clients') . ': </b><span style="">' . $client->company . '</span><br/></div>';
                                            }
                                            if ($items->objects == 3) {
                                                $_data = get_staff_full_name($items->objects_id);
                                                echo '
                                                  <div><b style="">' . _l('ch_units_in') . ': </b><span style="">' . $_data . '</span><br/></div>';
                                            }
                                            if ($items->objects == 4) {
                                                echo '
                                                  <div><b style="">' . _l('ch_units_in') . ': </b><span style="">' . $items->objects_text . '</span><br/></div>';
                                            } ?>
                                            <?php if ($items->type_vouchers == 1) {
                                                echo '
                                                  <div><b style="">' . _l('ch_type_of_document') . ': </b><span style="">Đơn đặt hàng mua</span><br/></div>';
                                                $order = get_table_where('tblpurchase_order', array('id' => $items->vouchers_id), '', 'row');
                                                if (!empty($order)) {
                                                    echo '
                                                  <div><b style="">' . _l('ch_code_p') . ': </b><span style="">' . $order->prefix . '-' . $order->code . '</span><br/></div>';
                                                }
                                            }
                                            if ($items->type_vouchers == 2) {
                                                echo '
                                                  <div><b style="">' . _l('ch_type_of_document') . ': </b><span style="">Xuất kho khác</span><br/></div>';
                                                $order = get_table_where('tblexport_different', array('id' => $items->vouchers_id), '', 'row');
                                                if (!empty($order)) {
                                                    echo '
                                                  <div><b style="">' . _l('ch_code_p') . ': </b><span style="">' . $order->prefix . '-' . $order->code . '</span><br/></div>';
                                                }
                                            }
                                            if ($items->type_vouchers == 5) {
                                                $_data = get_staff_full_name($items->objects_id);
                                                echo '
                                                  <div><b style="">' . _l('ch_type_of_document') . ': </b><span style="">Đơn đặt hàng bán</span><br/></div>';
                                            }
                                            if ($items->type_vouchers == 8) {
                                                echo '
                                                  <div><b style="">' . _l('ch_type_of_document') . ': </b><span style="">Trả hàng</span><br/></div>';
                                                $order = get_table_where('tblreturn_suppliers', array('id' => $items->vouchers_id), '', 'row');
                                                if (!empty($order)) {
                                                    echo '
                                                  <div><b style="">' . _l('ch_code_p') . ': </b><span style="">' . $order->prefix . $order->code . '</span><br/></div>';
                                                }
                                            }
                                            if ($items->type_vouchers == 12) {
                                                echo '
                                                  <div><b style="">' . _l('ch_type_of_document') . ': </b><span style="">' . _l('ch_suggestion') . '</span><br/></div>';
                                                $order = get_table_where('tblsuggestion', array('id' => $items->vouchers_id), '', 'row');
                                                if (!empty($order)) {
                                                    echo '
                                                  <div><b style="">' . _l('ch_code_p') . ': </b><span style="">' .  $order->code . '</span><br/></div>';
                                                }
                                            } ?>

                                            <p></p>
                                        </div>
                                        <div class="col-md-6">
                                            <?php
                                            $history_status = explode('|', $items->history_status);
                                            foreach ($history_status as $key => $value) {
                                                $data = explode(',', $value);
                                                if (is_numeric($data[0])) {
                                            ?>
                                                    <div><b><?= _l('ch_status_import') ?>: <?php echo staff_profile_image($data[0], array('staff-profile-image-small mright5 img_ch'), 'small', array(
                                                                                                'data-toggle' => 'tooltip',
                                                                                                'data-title' => ' Vào lúc: ' . _dt($data[1])
                                                                                            )) . get_staff_full_name($data[0]) ?>
                                                    </div>
                                            <?php
                                                }
                                            }
                                            ?>
                                            <div><b><?= _l('ch_note_pay_slip') ?>: </b><?php echo $items->note ?></div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#item_info" aria-controls="item_info" role="tab" data-toggle="tab"><?= _l('ch_information') ?></a>
                    </li>
                    <li role="presentation">
                        <a href="#item_activity" aria-controls="item_activity" role="tab" data-toggle="tab"><?= _l('activity_log_puchases') ?></a>
                    </li>
                </ul>
                <div role="tabpanel" class="tab-pane active" id="item_info">
                    <div id="bottom-total" class="well well-sm" style="margin-bottom: 5px;">
                        <table class="table table-bordered table-condensed totals" style="margin-bottom:0;margin-top:0;">
                            <thead>
                                <tr class="success">
                                    <th><?= _l('ch_status_pays_slip') ?>:<span class="pull-right"><?= number_format($items->total) ?></span></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="item_activity">
                    <div class="activity-container">
                        <?php foreach ($dataLog as $key => $value) { ?>
                            <div class="feed-item">
                                <div class="activity-text">
                                    <?= staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small'); ?> <?= get_staff_full_name($value['staff_id']); ?>
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
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?= _l('close') ?></button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.tip').tooltip();
        });
    </script>