<div class="modal fade" id="detail_service" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo _l('dt_detail_service'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6  pull-left">
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h3 class="panel-title"><?= _l('info'); ?></h3>
                            </div>
                            <div class="panel-body">
                                <div class="well well-sm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div><b><?= _l('ch_code_p') ?>: </b><?php echo $service->prefix . '' . $service->code ?></div>
                                            <div><b><?= _l('ch_staff_crate_rfq') ?>: </b><?php echo staff_profile_image($service->staff_id, array('staff-profile-image-small mright5 img_ch'), 'small', array(
                                                                                            'data-toggle' => 'tooltip',
                                                                                            'data-title' => get_staff_full_name($service->staff_id)
                                                                                        )) . get_staff_full_name($service->staff_id) ?></div>
                                            <div><b><?= _l('ch_date_p') ?>: </b><?php echo _d($service->date) ?></div>
                                            <div><b><?= _l('dt_type_service') ?>: </b><?php echo $service->name ?></div>
                                            <div><b><?= _l('ch_note_t') ?>: </b><?php echo $service->note ?></div>
                                            <p></p>
                                        </div>
                                    </div>
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
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="item_info">
                        <table class="table detail_service">
                            <thead>
                                <th><?php echo _l('STT'); ?></th>
                                <th><?php echo _l('dt_service_name'); ?></th>
                                <th><?php echo _l('dt_service_qty'); ?></th>
                                <th><?php echo _l('dt_service_price'); ?></th>
                                <th><?php echo _l('dt_service_total') ?></th>
                            </thead>
                            <tbody>
                                <?php $sum_total = 0; ?>
                                <?php foreach ($data as $key => $value) { ?>
                                    <tr>
                                        <td><?php echo $key + 1; ?></td>
                                        <td><?php echo $value->name ?></td>
                                        <td class="text-center"><?php echo $value->quanliti ?></td>
                                        <td class="text-right"><?php echo number_format($value->price); ?></td>
                                        <td class="text-right"><?php echo number_format($value->total); ?></td>
                                    </tr>
                                <?php $sum_total += $value->total;
                                } ?>
                            </tbody>
                            <tfoot class="bold">
                                <tr>
                                    <th class="text-center" style="text-transform: uppercase;" colspan=4><?= lang('tnh_grand_total') ?></th>
                                    <th class="text-right"><?php echo number_format($sum_total); ?></th>

                                </tr>
                            </tfoot>
                        </table>
                        <div class="clearfix"></div>
                        <div class="col-md-6">
                            <table class="table tnh-tb table-bordered table-hover" style="margin-top: 10px;">
                                <tbody>
                                    <tr>
                                        <td><?= _l('dt_discount_sum_total') ?></td>
                                        <td class="text-right"><?php echo $service->total_discount > 0 ? number_format($service->total_discount) : 0 ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= _l('VAT') . $service->tax_rate . '%'; ?></td>
                                        <td class="text-right"><?= number_format($service->vat); ?></td>
                                    </tr>
                                    <tr class="success" style="font-weight: 700;">
                                        <td><?= lang('tnh_grand_total', 'grand_total') ?></td>
                                        <td class="td-grand-total-all text-right"><?= formatNumber($service->subtotal); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="clearfix"></div>
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
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript">
    $(document).ready(function() {
        var flagView = <?= !empty($flagView) ? 1 : 0; ?>;
        dtItems = $('.detail_service').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            scrollY: '300px',
            // fixedColumns:   {
            //     leftColumns: 4,
            //     rightColumns: 0
            // },
            // 'searching': false,
            // 'ordering': false,
            // 'paging': false,
            // "info": false,
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            }
        });
        setTimeout(function() {
            dtItems.draw('page');
        }, 150);
    });
    $('body').on('hidden.bs.modal', '#detail_service', function() {
        $('#service_view_data').html('');
        tAPI.draw('page');
    });
</script>