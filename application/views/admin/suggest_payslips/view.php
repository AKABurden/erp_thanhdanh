<div class="modal-dialog modal-lg" style="width: 60%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mbot10">
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('tnh_date_creted') ?>: </div>
                            <div class="ml-at t-bold"><?= _dt($dtData['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Số phiếu yêu cầu') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['reference_no'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('Người lập phiếu') ?>: </div>
                            <div class="ml-at t-bold"><?= staff_profile_image($dtData['staff_id'], array('staff-profile-image-small mright5'), 'small', array(
                                                            'data-toggle' => 'tooltip',
                                                            'data-title' => get_staff_full_name($dtData['staff_id'])
                                                        )) . get_staff_full_name($dtData['staff_id']); ?>
                            </div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Nhà cung cấp') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['company']) ?></div>
                        </div>
                        <div class="row-contro">
                            <?php
                            $dtBranch = get_table_where('tblbranch', ['id' => $dtData['branch_id']], '', 'row_array');
                            ?>
                            <div><?= lang('Chi nhánh') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtBranch['name'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="wap-content mtop20">
                        <table class="table dataTable">
                            <thead>
                                <tr>
                                    <th style="width: 10%;" class="text-center">STT</th>
                                    <th class="text-center" style="width: 250px"><?= lang('Nội Dung-Diễn Giải') ?></th>
                                    <th class="text-center" style="width: 100px"><?= lang('tnh_dvt') ?></th>
                                    <th class="text-center" style="width: 100px"><?= lang('Số Lượng') ?></th>
                                    <th class="text-center" style="width: 100px"><?= lang('Đơn Giá') ?></th>
                                    <th class="text-center" style="width: 100px"><?= lang('Thành Tiền') ?></th>
                                    <th class="text-center" style="width: 100px"><?= lang('Thuế') ?></th>
                                    <th class="text-center" style="width: 100px"><?= lang('Tiền Thuế') ?></th>
                                    <th class="text-center" style="width: 100px"><?= lang('Tổng Thành Tiền') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dtItems)) { ?>
                                    <?php
                                    $total_quantity = 0;
                                    $total_amount = 0;
                                    $total_taxrate = 0;
                                    $total = 0;
                                    ?>
                                    <?php foreach ($dtItems as $key => $value) { ?>
                                        <tr>
                                            <td class="text-center"><?= (++$key) ?></td>
                                            <td><?= !empty($value['category_payslip']) ? $value['name_category_payslip'] : $value['note_item'] ?></td>
                                            <td class="text-center"><?= $value['unit'] ?></td>
                                            <td class="text-center"><?= formatNumber($value['quantity']) ?></td>
                                            <td class="text-right"><?= formatMoney($value['price']) ?></td>
                                            <td class="text-right"><?= formatMoney($value['amount']) ?></td>
                                            <td class="text-center"><?= ($value['taxrate']) ?>%</td>
                                            <td class="text-right"><?= formatMoney($value['amount'] * ($value['taxrate'] / 100)) ?></td>
                                            <td class="text-right"><?= formatMoney($value['amount'] + ($value['amount'] * ($value['taxrate'] / 100))) ?></td>
                                        </tr>
                                    <?php
                                        $total_quantity += $value['quantity'];
                                        $total_amount += $value['amount'];
                                        $total_taxrate += $value['amount'] * ($value['taxrate'] / 100);
                                        $total += $value['amount'] + ($value['amount'] * ($value['taxrate'] / 100));
                                    } ?>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr style="font-weight: 500;">
                                    <td class="text-center" colspan="3"><b>Tổng</b></td>
                                    <td class="text-center"><?= formatNumber($total_quantity) ?></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"><?= formatMoney($total_amount) ?></td>
                                    <td class="text-center"></td>
                                    <td class="text-right"><?= formatMoney($total_taxrate) ?></td>
                                    <td class="text-right"><?= formatMoney($total) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="col-md-6 pull-right mtop10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div><?= lang('tnh_created_by') ?>: <?= get_staff_full_name($dtData['created_by']) ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($dtData['date_created']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty(get_staff_full_name($dtData['updated_by']))) : ?>
                                    <div><?= lang('tnh_updated_by') ?>: <?= get_staff_full_name($dtData['updated_by']) ?></div>
                                    <div><?= lang('tnh_date_updated') ?>: <?= _dt($dtData['date_updated']) ?></div>
                                <?php endif ?>
                            </div>
                        </div>
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
</script>