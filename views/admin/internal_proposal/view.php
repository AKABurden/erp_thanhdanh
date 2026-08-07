<div class="modal fade" id="view_modal" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo !empty($title) ? $title : ''; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="title-modal">
                            <h3>Thông tin</h3>
                        </div>
                        <div class="body-modal">
                            <div class="row-modal">
                                <div class="row-group">
                                    <div class="row-contro">
                                        <div>Mã phiếu đề xuất:</div>
                                        <div class="ml-at t-bold"><?= $internal_proposal->code ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Ngày chứng từ:</div>
                                        <div class="ml-at t-bold"><?= _dC($internal_proposal->date) ?></div>
                                    </div>

                                    <div class="row-contro">
                                        <div>Người đề xuất:</div>
                                        <div class="ml-at t-bold">
                                            <?= staff_profile_image($internal_proposal->staff, array('staff-profile-image-small mright5'), 'small', array(
                                                'data-toggle' => 'tooltip',
                                                'data-title' => get_staff_full_name($internal_proposal->staff)
                                            )) . get_staff_full_name($internal_proposal->staff) ?>
                                        </div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Số tiền đề xuất:</div>
                                        <div class="ml-at t-bold"><?= number_format_data($internal_proposal->money) ?></div>
                                    </div>


                                </div>
                                <div class="row-group">
                                    <div class="row-contro">
                                        <div>Mã công việc:</div>
                                        <div class="ml-at t-bold"><?= ($internal_proposal->code_category) ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Nội dung công việc:</div>
                                        <div class="ml-at t-bold"><?= ($internal_proposal->content_category) ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div>Người duyệt:</div>
                                        <div class="ml-at t-bold">
                                            <?php
                                            if (!empty($internal_proposal->assigned)) {
                                                foreach ($internal_proposal->assigned as $key => $value) {
                                                    echo staff_profile_image($value['id_staff'], array('staff-profile-image-small mright5'), 'small', array(
                                                        'data-toggle' => 'tooltip',
                                                        'data-title' => get_staff_full_name($value['id_staff'])
                                                    ));
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($internal_proposal->code_purchase)) { ?>
                                        <div class="row-contro">
                                            <div>Phiếu yêu cầu mua hàng:</div>
                                            <div class="ml-at t-bold"><?= (!empty($internal_proposal->code_purchase) ? $internal_proposal->code_purchase : '') ?></div>
                                        </div>
                                    <?php } ?>
                                    <?php if (($internal_proposal->id_purchases == -1)) { ?>
                                        <div class="row-contro">
                                            <div>Phiếu yêu cầu mua hàng:</div>
                                            <?php
                                            $text = '';
                                            $check_purchase =  get_table_where('tblinternal_proposal_purchase', array('id_internal_proposal' => $internal_proposal->id));
                                            foreach ($check_purchase as $kk => $vv) {
                                                $check_purchase_detail = get_table_where('tblpurchases', array('id' => $vv['id_purchases']), '', 'row');
                                                $text .= $check_purchase_detail->prefix . $check_purchase_detail->code . ', ';
                                            }
                                            ?>
                                            <div class="ml-at t-bold"><?= trim($text, ', ') ?></div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($internal_proposal->code_purchase_order)) { ?>
                                        <div class="row-contro">
                                            <div>Phiếu mua hàng (PO):</div>
                                            <div class="ml-at t-bold"><?= (!empty($internal_proposal->code_purchase_order) ? $internal_proposal->code_purchase_order : '') ?></div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($internal_proposal->code_services)) { ?>
                                        <div class="row-contro">
                                            <div>Phiếu dịch vụ:</div>
                                            <div class="ml-at t-bold"><?= (!empty($internal_proposal->code_services) ? $internal_proposal->code_services : '') ?></div>
                                        </div>
                                    <?php } ?>

                                    <div class="row-contro">
                                        <div>Chi nhánh:</div>
                                        <div class="ml-at t-bold"><?= (!empty($internal_proposal->id_branch) ? get_table_where('tblbranch', ['id' => $internal_proposal->id_branch], '', 'row')->name : '') ?></div>
                                    </div>
                                </div>
                                <div class="row-contro">
                                    <div>Nội dung đề xuất:</div>
                                    <div class="ml-at t-bold"><?= ($internal_proposal->content) ?></div>
                                </div>

                                <div class="clearfix"></div>

                                <div class="clearfix"></div>
                                <?php if (!empty($files)) { ?>
                                    <h4 class="mtop30">Tập tin đính kèm</h4>
                                    <div class="clearfix"></div>
                                    <div class="fild-content mtop10">
                                        <?php foreach ($files as $keyFile => $valFile) { ?>
                                            <?php if (explode('/', $valFile->filetype)[0] == 'image') { ?>
                                                <div class="mtop5 mbot5 rowData">
                                                    <div class="preview_image" style="width: auto;">
                                                        <div class="display-block contract-attachment-wrapper img">
                                                            <a class="pull-right text-danger" onclick="removeFile(<?= $valFile->id ?>, this)"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                            <div style="width:150px;">
                                                                <a href="<?= base_url('uploads/internal_proposal/' . $internal_proposal->id . '/' . $valFile->file_name) ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                    <div class="">
                                                                        <img src="<?= base_url('uploads/internal_proposal/' . $internal_proposal->id . '/' . $valFile->file_name) ?>" style="max-height: 100px">
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            <?php } else { ?>
                                                <div class="mtop5 mbot5 rowData">
                                                    <a target="_blank" href="<?= base_url('uploads/internal_proposal/' . $internal_proposal->id . '/' . $valFile->file_name) ?>"><i class="fa fa-file-archive-o"></i> <?= $valFile->file_name ?></a>
                                                    <a class="pull-right text-danger" onclick="removeFile(<?= $valFile->id ?>, this)"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                </div>
                                            <?php } ?>
                                        <?php }
                                        ?>
                                    </div>
                                    <div class="clearfix"></div>
                                <?php } ?>
                            </div>
                            <?php if (!empty($internal_proposal->code_purchase) || ($internal_proposal->id_purchases == -1)) { ?>
                                <div class="">
                                    <div>Chi tiết yêu cầu:</div>
                                    <table id="tb-items-internal" class="dt-tnh table item-purchases table-bordered table-hover" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="200" class="text-left"></i> <?php echo _l('ch_items_name_t'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Số lượng đề xuất'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Số lượng PO ĐV chuẩn'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Số lượng PO ĐV kho'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Số lượng PO ĐV thanh toán'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Đơn giá'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Thuế'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Thành tiền'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="150" class="text-center"><?php echo _l('Nhà cung cấp'); ?></th>
                                                <th style="border-top: 1px solid #b4b9bf!important" width="100" class="text-center"><?php echo _l('Ghi chú đề xuất'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="table_purchase">
                                            <?php $i = 0;
                                            $tbody = '';
                                            $total = 0;
                                            foreach ($items as $key => $value) {
                                                $purchase_items = get_table_where('tblpurchases_items', array('id' => $value['id_purchases_items']), '', 'row');
                                                $purchase = get_table_where('tblpurchases', array('id' => $value['id_purchases']), '', 'row');
                                                $text = '';
                                                if (!empty($purchase)) {
                                                    $text = '<br><span class="label label-danger pull-left mtop5 text-center">' . $purchase->prefix . $purchase->code . '</span>';
                                                }
                                                $supplier = get_table_where('tblsuppliers', array('id' => $value['suppliers_id']), '', 'row');
                                                $unit_name = $value['unit_name'];
                                                if ($value['unit_name'] == null) {
                                                    $unit_name = '';
                                                }
                                                $unit_name_payment = $value['unit_name_payment'];
                                                if ($value['unit_name_payment'] == null) {
                                                    $unit_name_payment = '';
                                                }
                                                $unit_name_stock = $value['unit_name_stock'];
                                                if ($value['unit_name_stock'] == null) {
                                                    $unit_name_stock = '';
                                                }
                                                $tbody .= '<tr>';
                                                $tbody .= '<td>
                                                ' . $value['name_item'] . ' (' . $value['code_item'] . ')' . $text . '
                                                </td>';
                                                $tbody .= '<td class="text-center sldx">' . formatNumber($purchase_items->quantity_net) . '</td>';
                                                $tbody .= '<td><span class="text_mainquantity_stock text-center">' . formatNumber($value['quantity']) . '</span><span class="unit_name">/' . $unit_name . '</span></td>';
                                                $tbody .= '<td class="text-center"><span class="text_mainquantity_stock text-center">' . formatNumber($value['quantity_stock']) . '</span><span class="unit_name_stock">/' . $unit_name_stock . '</span></td>';
                                                $tbody .= '<td class="text-center"><span class="text_mainquantity_payment">' . formatNumber($value['quantity_payment']) . '</span><span class="unit_name_payment">/' . $unit_name_payment . '</span></td>';
                                                $tbody .= '<td class="text-right">' . formatNumber($value['price']) . '</td>';
                                                $tbody .= '<td class="text-center">' . ($value['tax_rate']) . '%</td>';
                                                $tbody .= '<td class="text-right">' . number_format_data(($value['price'] * $value['quantity_payment']) * (1 + $value['tax_rate']/100)) . '</td>';
                                                $tbody .= '<td style="width:150px">' . $supplier->company . '</td>';
                                                $tbody .= '<td>' . $purchase_items->note . '</td>';
                                                $tbody .= '</tr>';
                                                $i++;
                                                $total += ($value['price'] * $value['quantity_payment']) * (1 + $value['tax_rate']/100);
                                            } ?>
                                            <?php echo $tbody ?>
                                        </tbody>
                                        <tfoot>
                                            <td>Tổng</td>
                                            <td class="tfood_sldx text-center"></td>
                                            <td class="tfood_slc text-center"></td>
                                            <td class="tfood_slk text-center"></td>
                                            <td class="tfood_slp text-center"></td>
                                            <td></td>
                                            <td></td>
                                            <td class="tfood_total text-right"><?//= formatNumber($total) ?></td>
                                            <td></td>
                                            <td></td>
                                        </tfoot>
                                    </table>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#view_modal').modal('show');

    function removeFile(id, _this) {
        if (confirm('Bạn có chắc muốn xóa file?')) {
            $.get(admin_url + 'internal_proposal/removeFile/' + id, function(result) {
                result = JSON.parse(result);
                if (result.success) {
                    $(_this).parents('.rowData').remove();
                }
            })
        }
    }

    $(document).ready(function () {
        var dtItemsInternal = $('#tb-items-internal').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": dataTableLengthMenu(),
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                $('#tb-items-internal_wrapper').find('.btn-dt-reload').hide();
            },
            dom: "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row pull-left'<'col-md-4'i>><'row pull-right'<'#colvis'><'.dt-page-jump'>p>",
            buttons: get_datatable_buttons($('#tb-items-internal')),
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;
                pageTotalAmount = api
                    .column(7, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(7).footer()).html('<div class="text-right">' + tnhFormatMoney(pageTotalAmount) + '</div>');
            }
        });
    });
</script>