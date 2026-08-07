<div class="modal-dialog modal-lg" style="width: 90%;">
    <style>
        #table-items th {
            white-space: nowrap;
        }
    </style>
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
                        <div class="row-contro">
                        </div>
                        <div class="row-contro">
                            <?php
                            $htmlObjectType = '';
                            if ($dtData['object_type'] == 'po') {
                                $this->db->select('GROUP_CONCAT(tbl_productions_orders.reference_no) as reference_no');
                                $this->db->from('tbl_productions_orders');
                                $this->db->where('tbl_productions_orders.id IN (' . $dtData['object_id'] . ')');
                                $dtObject = $this->db->get()->row_array();
                                $htmlObjectType = '<div class="label label-success">Lệnh sản xuất</div>';
                            } else {
                                $this->db->select('GROUP_CONCAT(tbl_orders.reference_no) as reference_no');
                                $this->db->from('tbl_orders');
                                $this->db->where('tbl_orders.id IN (' . $dtData['object_id'] . ')');
                                $dtObject = $this->db->get()->row_array();
                                $htmlObjectType = '<div class="label label-info">Đơn hàng</div>';
                            }
                            ?>
                            <div><?= lang('Loại') ?>: </div>
                            <div class="ml-at t-bold"><?= $htmlObjectType ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Đơn hàng /Lệnh sản xuất') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtObject['reference_no'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <?php
                            $dtBranch = get_table_where('tblbranch', ['id' => $dtData['branch_id']], '', 'row_array');
                            ?>
                            <div><?= lang('Chi nhánh') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtBranch['name'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Người lập kế hoạch') ?>: </div>
                            <div class="ml-at t-bold"><?= get_staff_full_name($dtData['staff_plan']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('note') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['note'] ?></div>
                        </div>

                    </div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">

                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                        <label for="tab1"><i class="icon-foso fal fa-info-circle"></i><?= lang('tnh_items') ?></label>
                        <!-- Tab 5 -->
                        <input type="radio" name="tabset" id="tab5" aria-controls="view-activity-log">
                        <label for="tab5"><i class="icon-foso fal fa-history"></i><?= lang('activity_log_puchases') ?></label>


                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <div class="table-responsive">
                                    <table id="table-items" class="table dt-tnh table-hover table-condensed table-cs-border">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                                                <th style="width: 150px;"><?= lang('Đơn Vị Gia Công(NCC)') ?></th>
                                                <th style="width: 100px;"><?= lang('Mã thành phẩm') ?></th>
                                                <th style="width: 150px;"><?= lang('Tên thành phẩm') ?></th>

                                                <th style="width: 150px;">Quy Cách</th><!--mode-->
                                                <th style="width: 150px;">Đơn Vị Tính</th><!--unit-->
                                                <th style="width: 150px;">Chi Tiết Gia Công</th>
                                                <th style="width: 150px;">Đơn Vị Vận chuyển Gia Công</th><!--shipping_unit_outsource-->
                                                <th style="width: 150px;">Phương tiện vận chuyển gia công</th><!--transport_outsource-->
                                                <th style="width: 150px;">Chi Phí Vận Chuyển</th><!--price_transport  amount_transport-->
                                                <th style="width: 150px;">Đơn Giá Gia Công</th><!--pricee-->
                                                <th style="width: 150px;">Số lượng gia công</th><!--pricee-->
                                                <th style="width: 150px;">Thành tiền</th><!--pricee-->
                                                <th style="width: 150px;">VAT (%)</th><!--radio_vat-->
                                                <th style="width: 150px;">Tổng Sau VAT</th><!--total_vat-->


                                                <th style="width: 150px;"><?= lang('Số lượng tờ in') ?></th>
                                                <th style="width: 100px;"><?= lang('NVL in') ?></th>
                                                <th style="width: 150px;"><?= lang('Số lượng bù hao') ?></th>
                                                <th style="width: 100px;"><?= lang('Số lượng bù hao xuất thêm (tờ in)') ?></th>
                                                <th style="width: 100px;"><?= lang('Khổ in(cm)') ?></th>
                                                <th style="width: 100px;"><?= lang('Hình ảnh') ?></th>
                                                <th style="width: 100px;"><?= lang('Loại hình phủ') ?></th>
                                                <th style="width: 100px;"><?= lang('Cách in') ?></th>
                                                <th style="width: 100px;"><?= lang('Số mặt in') ?></th>
                                                <th style="width: 100px;"><?= lang('Số màu - Mặt A') ?></th>
                                                <th style="width: 100px;"><?= lang('Số màu - Mặt B') ?></th>
                                                <th style="width: 100px;"><?= lang('Số kẽm- Mặt A') ?></th>
                                                <th style="width: 100px;"><?= lang('Số kẽm- Mặt B') ?></th>
                                                <th style="width: 100px;"><?= lang('Nhíp kẽm') ?></th>
                                                <th style="width: 100px;"><?= lang('Hình ảnh mực in') ?></th>
                                                <th style="width: 100px;"><?= lang('Hình ảnh bóng phủ') ?></th>
                                                <th style="width: 100px;"><?= lang('Ghi Chú') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($dtDataItems)) { ?>
                                                <?php foreach ($dtDataItems as $key => $value) {?>
                                                    <?php
                                                        $item_id = $value['item_id'];
                                                        $type_item = $value['type_item'];
                                                        $info = null;
                                                        $images = base_url('assets/images/tnh/no_image.png');
                                                        if ($type_item == "products") {
                                                            $info = $this->products_model->rowProduct($item_id);
                                                            $unit = $this->unit_model->rowUnit($info['unit_id']);
                                                            if ($info['images']) {
                                                                $images = base_url('uploads/products/' . $info['images']);
                                                            }
                                                        }
                                                        if ($value['object_type'] == 'po') {
                                                            $dtObject = get_table_where('tbl_productions_orders', ['id' => $value['order_id']], '', 'row_array');
                                                        } else {
                                                            $dtObject = get_table_where('tbl_orders', ['id' => $value['order_id']], '', 'row_array');
                                                        }
                                                    ?>
                                                    <tr>
                                                        <td class="text-center"><?= (++$key) ?></td>
                                                        <td class="text-left"><?= ($value['company']) ?></td>
                                                        <td>
                                                            <div class="code_item">
                                                                <?= $info['code'] ?>
                                                            </div>
                                                            <div style="color: green"><?= $dtObject['reference_no'] ?></div>
                                                        </td>
                                                        <td>
                                                            <div><?= $info['name'] ?></div>
                                                        </td>

                                                        <td><div class="mode" style="width: 100px"><?= $info['mode'] ?? '' ?></div></td>
                                                        <td><?=$unit['unit'] ?? ''?></td>
                                                        <td><?=$value['note_detail'] ?? ''?></td>
                                                        <td><?=$value['shipping_unit_outsource'] ?? ''?></td>
                                                        <td><?=$value['transport_outsource'] ?? ''?></td>
                                                        <td><?=number_format_data($value['price_transport']) ?? 0?></td>
                                                        <td><?=number_format_data($value['price']) ?? 0?></td>
                                                        <td><?=number_format_data($value['quantity']) ?? 0?></td>
                                                        <td><?=number_format_data($value['amount']) ?? 0?></td>
                                                        <td><?=$value['name_tax'] ?? ''?></td>
                                                        <td><?=number_format_data($value['grand_total']) ?? 0?></td>
                                                        <td>
                                                            <div><?= formatNumber($value['sltin']) ?></div>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $type_material = $value['type_material'];
                                                            if ($type_material == "materials") {
                                                                $info = $this->items_model->rowMaterial($value['material']);
                                                            } else if ($type_material == "tools_supplies") {
                                                                $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                                                            } else {
                                                                $info = $this->products_model->rowProduct($item_id);
                                                            }
                                                            ?>
                                                            <div><?= ($info['name']) ?></div>
                                                        </td>
                                                        <td>
                                                            <div><?= formatNumber($value['quantity_compensation']) ?></div>
                                                        </td>
                                                        <td>
                                                            <div><?= formatNumber($value['quantity_compensation_more']) ?></div>
                                                        </td>
                                                        <td>
                                                            <div><?= ($value['landscape_print_size']) ?></div>
                                                        </td>
                                                        <td>
                                                            <div class="td-image" style="display: flex;justify-content: center;">
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
                                                        <td>
                                                            <div><?= $value['name_stage'] ?></div>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $print_text = '';
                                                            $print = [
                                                                [
                                                                    'id' => 1,
                                                                    'name' => 'In A-B',
                                                                ],
                                                                [
                                                                    'id' => 2,
                                                                    'name' => 'In Trở',
                                                                ],
                                                                [
                                                                    'id' => 3,
                                                                    'name' => 'In 1 mặt',
                                                                ]
                                                            ];
                                                            foreach ($print as $kk => $vv) {
                                                                if ($vv['id'] == $value['print']) {
                                                                    $print_text = $vv['name'];
                                                                    break;
                                                                }
                                                            }

                                                            ?>
                                                            <div><?= $print_text ?></div>
                                                        </td>
                                                        <td>
                                                            <div><?= formatNumber($value['number_of_printed_sides']) ?></div>
                                                        </td>
                                                        <td>
                                                            <div><?= formatNumber($value['color_number_a']) ?></div>
                                                        </td>
                                                        <td>
                                                            <div><?= formatNumber($value['color_number_b']) ?></div>
                                                        </td>
                                                        <td>
                                                            <div><?= formatNumber($value['zinc_number_a']) ?></div>
                                                        </td>
                                                        <td>
                                                            <div><?= formatNumber($value['zinc_number_b']) ?></div>
                                                        </td>
                                                        <td>
                                                            <div><?= formatNumber($value['grape']) ?></div>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($value['image_mucin'])) { ?>
                                                                <div class="td-image" style="display: flex;justify-content: center;">
                                                                    <div class="preview_image" style="width: auto;">
                                                                        <div class="display-block contract-attachment-wrapper img">
                                                                            <div style="width:45px;">
                                                                                <a href="<?= base_url($value['image_mucin']) ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                                    <div class="">
                                                                                        <img src="<?= base_url($value['image_mucin']) ?>">
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($value['image_bongmo'])) { ?>
                                                                <div class="td-image" style="display: flex;justify-content: center;">
                                                                    <div class="preview_image" style="width: auto;">
                                                                        <div class="display-block contract-attachment-wrapper img">
                                                                            <div style="width:45px;">
                                                                                <a href="<?= base_url($value['image_bongmo']) ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                                    <div class="">
                                                                                        <img src="<?= base_url($value['image_bongmo']) ?>">
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <div><?= ($value['note']) ?></div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        </tbody>
                                        <!-- <tfoot>
                                            <tr>
                                                <td colspan="2" class="uppercase bold">Tổng cộng</td>
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
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tfoot> -->
                                    </table>
                                </div>
                            </section>
                            <section id="view-activity-log" class="tab-panel">
                                <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                    <?php
                                    $history = getActivityLogByObjId($dtData['id'], 'suggest_plan_outsource');
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
        <a data-tnh="modal" class="tnh-modal hide click1" href=" <?= base_url() ?>admin/suggest_plan_overtime/view/<?= $dtData['id'] ?>" data-toggle="modal" data-target="#myModal"></a>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var dtItems = $('#table-items').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            // scrollY: true,
            // scrollX: true,
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {
                var apiSub = this.api(),
                    data;
                pageQuantity = apiSub
                    .column(9, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                pageAmount = apiSub
                    .column(12, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(apiSub.column(9).footer()).html('<div class="text-center bold">' + tnhFormatNumber(pageQuantity) + '</div>');
                $(apiSub.column(12).footer()).html('<div class="text-right bold">' + tnhFormatMoney(pageAmount) + '</div>');
            }
        });
    });
</script>