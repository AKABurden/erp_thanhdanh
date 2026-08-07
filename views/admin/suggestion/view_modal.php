<style>
    #tnhModal2 {
        z-index: 10002;
    }
</style>
<div class="modal-dialog modal-lg" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('ch_view_suggestion') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="info">
                        <div class="title-modal">
                            <h3>Thông tin</h3>
                        </div>
                        <div class="body-modal">
                            <div class="row-modal">
                                <div class="row-group">
                                    <div class="row-contro">
                                        <div><?= _l('ch_code_p') ?>: </div>
                                        <div class="ml-at t-bold"><?php echo $items->code ?>
                                        </div>
                                    </div>
                                    <div class="row-contro">
                                        <div><?= _l('ch_date_p') ?>: </div>
                                        <div class="ml-at t-bold"><?php echo _d($items->date) ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div><?= _l('Loại') ?>: </div>
                                        <div class="ml-at t-bold"><?php echo $items->type ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div><?= _l('Trạng thái') ?>: </div>
                                        <div class="ml-at t-bold"><?php echo $items->status ?></div>
                                    </div>
                                </div>
                                <div class="row-group">
                                    <div class="row-contro">
                                        <div><?= _l('ch_staff_suggestion') ?>: </div>
                                        <div class="ml-at t-bold"><?php echo staff_profile_image($items->staff_create, array('staff-profile-image-small mright5 img_ch'), 'small', array(
                                                                        'data-toggle' => 'tooltip',
                                                                        'data-title' => get_staff_full_name($items->staff_create)
                                                                    )) . get_staff_full_name($items->staff_create) ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div><?= _l('ch_total_suggestion') ?>: </div>
                                        <div class="ml-at t-bold"><?php echo formatNumber($items->price_total) ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div><?= _l('ch_note_suggestion') ?>: </div>
                                        <div class="ml-at t-bold"><?php echo $items->note ?></div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>

                            </div>
                            <div class="clearfix"></div>
                            <div class="clearfix"></div>
                            <?php if (count($items->file) > 0) { ?>
                                <h4>FILE đính kèm</h4>
                            <?php } ?>
                            <div class="col-md-12">
                                <?php $j = 0; ?>
                                <?php foreach ($items->file as $key => $value) { ?>
                                    <?php if (substr($value['filetype'], 0, 5) != 'image') { ?>
                                        <div class="media lead-note file_<?= $key ?>">
                                            <div class="media-body"><i class="mime mime-file"></i>
                                                <a href="<?= base_url() ?>uploads/suggestion/<?= $items->id ?>/<?= $value['file_name'] ?>">
                                                    <?= $value['file_name'] ?>
                                                </a>
                                                <hr>
                                            </div>
                                        </div>
                                <?php $j++;
                                    }
                                }
                                ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <?php foreach ($items->file as $key => $value) { ?>
                                    <?php if (substr($value['filetype'], 0, 5) == 'image') { ?>
                                        <div class="preview_image id_images<?= $key ?>" id="images_product_view" style="width: auto;float: left;margin: 0 !important;margin-left: 10px;">
                                            <div class="display-block contract-attachment-wrapper img-1"><a href="<?= base_url() ?>uploads/suggestion/<?= $items->id ?>/<?= $value['file_name'] ?>" data-lightbox="customer-pos" class="display-block mbot5 show-images" product_id="<?= $items->id ?>">
                                                    <div><img src="<?= base_url() . 'uploads/suggestion/' . $items->id . '/' . $value['file_name'] ?>" style="width:100px;height:100px;" class="img-rounded "></div>
                                                </a> </div>
                                        </div>
                                    <?php $j++;
                                    } ?>
                                <?php }
                                ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <table id="view-enquiry" class="table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 20px;" class="text-center"><?php echo _l('STT'); ?></th>
                                    <th style="width: 100px;" class="text-center"><?php echo _l('Mã vật tư'); ?></th>
                                    <th style="width: 250px;" class="text-center"><?php echo _l('Vật tư'); ?></th>
                                    <th style="width: 100px;" class="text-center"><?php echo _l('tnh_dvt'); ?></th>
                                    <th style="width: 100px;" class="text-center"><?php echo _l('Quy cách'); ?></th>
                                    <th style="width: 100px;" class="text-center"><?php echo _l('Số lượng'); ?>
                                    <th style="width: 100px;" class="text-center"><?php echo _l('Đơn giá'); ?>
                                    <th style="width: 100px;" class="text-center"><?php echo _l('Thuế'); ?>
                                    <th style="width: 100px;" class="text-center"><?php echo _l('Thành tiền'); ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $quantity = 0;
                                $amount = 0;

                                foreach ($items->item as $key => $value) {
                                    $items_detail = get_items($value['id_items'], $value['type']);
                                    $quantity += $value['quantity'];
                                    $amount += $value['amount'];
                                ?>
                                    <tr>
                                        <td style="width:20px">
                                            <div>
                                                <?php echo ($key + 1); ?>
                                            </div>
                                        </td>
                                        <td style="width:100px">
                                            <div>
                                                <?php echo $items_detail->code; ?>
                                            </div>
                                        </td>
                                        <td style="width:250px">
                                            <div>
                                                <?php echo $items_detail->name; ?>
                                            </div>
                                        </td>
                                        <td style="width:100px">
                                            <div>
                                                <?php echo $items_detail->unit_name; ?>
                                            </div>
                                        </td>
                                        <td style="width:100px">
                                            <div>
                                                <?php echo $items_detail->mode; ?>
                                            </div>
                                        </td>
                                        <td style="width:100px" class="text-center">
                                            <div>
                                                <?php echo formatNumber($value['quantity']); ?>
                                            </div>
                                        </td>
                                        <td style="width:100px" class="text-right">
                                            <div>
                                                <?php echo formatNumber($value['price']); ?>
                                            </div>
                                        </td>
                                        <td style="width:100px" class="text-center">
                                            <?php $tax = get_table_where('tbltaxes', ['id'=>$value['tax_id']], '', 'row_array') ?>
                                            <div>
                                                <?php echo (!empty($tax['name']) ? $tax['name'] : '') ?>
                                            </div>
                                        </td>
                                        <td style="width:100px" class="text-right">
                                            <div>
                                                <?php echo formatNumber($value['amount']); ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php  } ?>
                            </tbody>
                            <footer>
                                <tr class="bold">
                                    <td colspan="5" class="text-center">Tổng</td>
                                    <td class="text-center"> <?php echo formatNumber($quantity); ?></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right"> <?php echo formatNumber($amount); ?></td>
                                </tr>
                            </footer>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
    var dtItems;
    $(document).ready(function() {
        var flagView = <?= !empty($flagView) ? 1 : 0; ?>;
        dtItems = $('#view-enquiry').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            scrollX: true,
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {}
        });
        setTimeout(function() {
            dtItems.draw('page');
        }, 150);
    });
</script>