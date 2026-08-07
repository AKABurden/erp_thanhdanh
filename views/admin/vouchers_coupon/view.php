<style>
    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }
</style>
<div class="modal fade" id="view_vouchers_coupon" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('detail_coupon'); ?></span>
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
                            else if ($items->status == 0)
                                $type = 'warning';
                            else if ($items->status == 2)
                                $type = 'danger';
                            else if ($items->status == 1)
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
                                                <b><?= _l('ch_code_p') ?>: </b><?php echo $items->code_vouchers ?>
                                            </div>
                                            <div><b><?= _l('ch_staff_crate_rfq') ?>: </b><?php echo staff_profile_image($items->staff_create, array('staff-profile-image-small mright5'), 'small', array(
                                                                                            'data-toggle' => 'tooltip',
                                                                                            'data-title' => get_staff_full_name($items->staff_create)
                                                                                        )) . get_staff_full_name($items->staff_create) ?></div>
                                            <div><b><?= _l('ch_date_p') ?>: </b><?php echo _d($items->date_vouchers) ?></div>
                                            <div><b><?= _l('staff_coupon') ?>: </b><?php echo staff_profile_image($items->staff, array('staff-profile-image-small mright5'), 'small', array(
                                                                                        'data-toggle' => 'tooltip',
                                                                                        'data-title' => get_staff_full_name($items->staff)
                                                                                    )) . get_staff_full_name($items->staff) ?></div>
                                            <!-- yct start -->
                                            <div><b><?= _l('collect_categories') ?>: </b><?php echo $items->colcat_name ?></div>
                                            <!-- yct end -->
                                            <div><b><?= _l('note') ?>: </b><?php echo $items->note ?></div>
                                            <p></p>
                                        </div>
                                        <div class="col-md-6">
                                            <?php
                                            $data = explode('|', $items->history_status);
                                            if (is_numeric($data[0])) { ?>
                                                <div><b><?= _l('ch_status_import') ?>: <?php echo staff_profile_image($data[0], array('staff-profile-image-small mright5'), 'small', array(
                                                                                            'data-toggle' => 'tooltip',
                                                                                            'data-title' => ' Vào lúc: ' . _dt($data[1])
                                                                                        )) . get_staff_full_name($data[0]) ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="bottom-total" class="well well-sm" style="margin-bottom: 5px;">
                    <table class="table table-bordered table-condensed totals" style="margin-bottom:0;margin-top:0;">
                        <thead>
                            <tr class="success">
                                <th><?= _l('ch_all_total') ?>:<span class="pull-right"><?= number_format($items->total) ?></span></th>
                                <th><?= _l('coupon_status_do') ?>:<span class="pull-right"><?= number_format($items->payment) ?></span></th>
                            </tr>
                        </thead>
                    </table>
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
                    <input type="hidden" id="view" name="view" value="," />
                    <div class="clearfix mtop20"></div>
                    <?php $table_data = array(
                        _l('#'),
                        _l('ch_code_orders'),
                        _l('tnh_grand_total'),
                        _l('payment_dt'),
                        _l('payment_collected'),
                    );
                    render_datatable($table_data, 'view_vouchers_coupon');
                    ?>
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
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('.tip').tooltip();
    });
    var tAPI;
    $(function() {
        var CustomersServerParams = {
            'view': '[name="view"]',
        };
        if ($.fn.DataTable.isDataTable('.table-view_vouchers_coupon')) {
            $('.table-view_vouchers_coupon').DataTable().destroy();
        }
        tAPI = initDataTable('.table-view_vouchers_coupon', admin_url + 'vouchers_coupon/view_vouchers_coupon/' + <?= $items->id ?>, [0], [0], CustomersServerParams, [0, 'desc']);
        // tAPI.columns(2).visible(false, false);
        // tAPI.columns(3).visible(false, false);
        // tAPI.columns(4).visible(false, false);
        // tAPI.columns(5).visible(false, false);
        $.each(CustomersServerParams, function(filterIndex, filterItem) {
            $('' + filterItem).on('change', function() {
                tAPI.ajax.reload();
            });
        });
    });

    function view(id) {
        var view = $('[name="view"]').val();
        view = view + id + ',';
        $('[name="view"]').val(view);
        // tAPI.columns(2).visible(true, true);
        // tAPI.columns(3).visible(true, true);
        // tAPI.columns(4).visible(true, true);
        // tAPI.columns(5).visible(true, true);
        $('.table-view_vouchers_coupon').DataTable().ajax.reload();
    }

    function no_view(id) {
        var view = $('[name="view"]').val();
        view = view.replace(',' + id + ',', ',');
        $('[name="view"]').val(view);
        if (view == ',') {
            // tAPI.columns(2).visible(false, false);
            // tAPI.columns(3).visible(false, false);
            // tAPI.columns(4).visible(false, false);
            // tAPI.columns(5).visible(false, false);
        }
        $('.table-view_vouchers_coupon').DataTable().ajax.reload();
    }
</script>