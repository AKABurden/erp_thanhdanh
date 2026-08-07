<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<div class="modal-dialog modal-lg <?= $typeDetail ?>">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-danger"><?= $note ?></div>
                </div>
                <div class="col-md-12 mtop20">
                    <?php if ($type_object == "so_luong_ht") : ?>
                        <table id="tb-cs" class="table tnh-table dataTable">
                            <thead>
                                <tr>
                                    <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                    <th><?= lang('Ngày') ?></th>
                                    <th><?= lang('Số nhập kho') ?></th>
                                    <th class="text-center"><?= lang('quantity') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="th-grand-quantity"></th>
                                </tr>
                            </tfoot>
                        </table>
                    <?php elseif ($type_object == "chi_phi_nvl_mh") : ?>
                        <table id="tb-cs" class="table tnh-table dataTable">
                            <thead>
                                <tr>
                                    <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                    <th><?= lang('Ngày') ?></th>
                                    <th><?= lang('Số xuất kho/Thu hồi phế liệu') ?></th>
                                    <th class="text-center"><?= lang('Tiền xuất kho') ?></th>
                                    <th class="text-center"><?= lang('Tiền phế liệu') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="th-grandtotal"></th>
                                    <th class="th-grandTotalPheLieu"></th>
                                </tr>
                            </tfoot>
                        </table>
                    <?php elseif ($type_object == "chi_phi_nhan_cong_truc_tiep") : ?>
                        <table id="tb-cs" class="table tnh-table dataTable">
                            <thead>
                                <tr>
                                    <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                    <th><?= lang('Ngày chứng từ') ?></th>
                                    <th><?= lang('Mã phiếu') ?></th>
                                    <th><?= lang('Loại chi phí') ?></th>
                                    <th class="text-center"><?= lang('Số tiền') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="th-grandtotal"></th>
                                </tr>
                            </tfoot>
                        </table>
                    <?php elseif ($type_object == "chi_phi_san_xuat_chung") : ?>
                        <table id="tb-cs" class="table tnh-table dataTable">
                            <thead>
                                <tr>
                                    <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                    <th><?= lang('Ngày chứng từ') ?></th>
                                    <th><?= lang('Mã phiếu') ?></th>
                                    <th><?= lang('Loại chi phí') ?></th>
                                    <th class="text-center"><?= lang('Số tiền') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="th-grandtotal"></th>
                                </tr>
                            </tfoot>
                        </table>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
        <input type="hidden" name="mstart_date_search" id="mstart_date_search" class="form-control mstart_date_search" value="<?= $start_date ?>">
        <input type="hidden" name="mend_date_search" id="mend_date_search" class="form-control mend_date_search" value="<?= $end_date ?>">
        <input type="hidden" name="mitem_id" id="mitem_id" class="form-control mitem_id" value="<?= $item_id ?>">
        <input type="hidden" name="mtype_item" id="mtype_item" class="form-control mtype_item" value="<?= $type_item ?>">
    </div>
</div>

<script>
    var oTableModal = '';
    var paramsModal = {
        'start_date': '#mstart_date_search',
        'end_date': '#mend_date_search',
        'item_id': '#mitem_id',
        'type_item': '#mtype_item',
    };

    $(document).ready(function() {
        oTableModal = tnhDatatable(
            '#tb-cs', {
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                "processing": true,
                'searching': false,
                'ordering': false,
                'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
                buttons: [{
                    text: 'Excel',
                    title: '<?= $title ?>',
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':visible'
                    },
                    footer: true
                }, ],
                "serverSide": true,
                'sAjaxSource': '<?= $link ?>',
                'fnServerData': function(sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsModal) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsModal[key]).val()
                        });
                    }
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        // 'success': fnCallback
                        success: function(response) {
                            fnCallback(response);
                            if (response) {
                                $('.th-grand-quantity').html('<div class="text-center bold">' + response.grandTotal + '</div>');
                                $('.th-grandtotal').html('<div class="text-right bold">' + response.grandTotal + '</div>');
                                if (typeof response.grandTotalPheLieu !== "undefined" && response.grandTotalPheLieu) {
                                    $('.th-grandTotalPheLieu').html('<div class="text-right bold">' + response.grandTotalPheLieu + '</div>');
                                }
                            }
                        }
                    });
                },
                "drawCallback": function(aoData, settings) {
                    $('.group-orders').closest('tr').css('background', '#ffff0094');
                    $('.group-orders').closest('tr').addClass('par-group-orders');
                },
                'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "footerCallback": function(tfoot, data, start, end, display) {},
                "columnDefs": [{
                    "targets": 0,
                    'width': '80px'
                }, ],
                "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {}
            }
        );
    });
</script>