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
                    <?php if ($type_object == "doanh_thu_ban_hang") : ?>
                        <table id="tb-cs" class="table tnh-table dataTable">
                            <thead>
                                <tr>
                                    <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                    <th><?= lang('Bộ phận') ?></th>
                                    <th><?= lang('tnh_employees_charge') ?></th>
                                    <th class="text-center"><?= lang('Doanh thu bán hàng') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="th-grand_total"></th>
                                </tr>
                            </tfoot>
                        </table>
                    <?php elseif ($type_object == "chiet_khau_hoa_don") : ?>
                        <table id="tb-cs" class="table tnh-table dataTable">
                            <thead>
                                <tr>
                                    <th><?= lang('tnh_numbers') ?></th>
                                    <th><?= lang('tnh_employees_charge') ?></th>
                                    <th><?= lang('tnh_discount') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th class="th-grand_total"></th>
                                </tr>
                            </tfoot>
                        </table>
                        <!-- <table id="tb-cs" class="table tnh-table dataTable">
                            <thead>
                                <tr>
                                    <th><?= lang('date') ?></th>
                                    <th><?= lang('tnh_reference_orders') ?></th>
                                    <th><?= lang('tnh_employees_charge') ?></th>
                                    <th><?= lang('tnh_discount') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="th-grand_total"></th>
                                </tr>
                            </tfoot>     
                        </table> -->
                    <?php elseif ($type_object == "hang_tra_ve") : ?>
                        <table id="tb-cs" class="table tnh-table dataTable">
                            <thead>
                                <tr>
                                    <th><?= lang('tnh_numbers') ?></th>
                                    <th><?= lang('tnh_employees_charge') ?></th>
                                    <th><?= lang('tnh_grand_total') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th class="th-grand_total"></th>
                                </tr>
                            </tfoot>
                        </table>
                        <!-- <table id="tb-cs" class="table tnh-table dataTable">
                            <thead>
                                <tr>
                                    <th><?= lang('date') ?></th>
                                    <th><?= lang('Số đơn hàng trả về') ?></th>
                                    <th><?= lang('tnh_grand_total') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th class="th-grand_total"></th>
                                </tr>
                            </tfoot>
                        </table> -->
                    <?php elseif ($type_object == "gia_von_hang_ban") : ?>
                        <table id="tb-cs" class="table tnh-table dataTable">
                            <thead>
                                <tr>
                                    <th><?= lang('date') ?></th>
                                    <th><?= lang('tnh_reference_orders') ?></th>
                                    <th><?= lang('Giá vốn(TT)') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th class="th-grand_total"></th>
                                </tr>
                            </tfoot>
                        </table>
                    <?php elseif ($type_object == "chi_phi") : ?>
                        <!-- <table id="tb-cs" class="table tnh-table dataTable">
                                <thead>
                                    <tr>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('Số chi phí') ?></th>
                                        <th><?= lang('Tên chi phí') ?></th>
                                        <th><?= lang('Số tiền') ?></th>
                                        <th><?= lang('Loại chi phí') ?></th>
                                        <th><?= lang('Ghi chú') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="th-grand_total"></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>     
                            </table> -->
                        <style>
                            .reload button {
                                display: none;
                            }
                        </style>
                        <?php
                        $table_columns = array(
                            _l('STT'),
                            _l('Mã mục cha'),
                            _l('Mã chi phí'),
                            _l('Tên khoản chi phí'),
                            _l('Đã chi'),
                        );
                        render_datatable($table_columns, 'report_financial');
                        ?>

                        <input type="hidden" name="custom" id="custom" class="form-control" value="custom">
                        <input type="hidden" name="report_from" id="report_from" class="form-control" value="<?= _d($start_date) ?>">
                        <input type="hidden" name="report_to" id="report_to" class="form-control" value="<?= _d($end_date) ?>">
                        <input type="hidden" name="id_branch" id="id_branch" class="form-control" value="<?= $id_branch ?>">

                        <script>
                            var fnServerParams = {
                                'report_months': '[name="custom"]',
                                'report_from': '[name="report_from"]',
                                'report_to': '[name="report_to"]',
                                'id_branch': '[name="id_branch"]',
                            };
                            $(document).ready(function() {
                                initDataTable('.table-report_financial', admin_url + 'reports/report_charge', false, false, fnServerParams, [0, 'ASC']);
                            });
                        </script>
                    <?php elseif ($type_object == "thu_nhap_khac") : ?>
                        <table id="tb-cs" class="table tnh-table dataTable">
                            <thead>
                                <tr>
                                    <th><?= lang('date') ?></th>
                                    <th><?= lang('Số thu nhập khác') ?></th>
                                    <th><?= lang('Số tiền') ?></th>
                                    <th><?= lang('note') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th class="th-grand_total"></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    <?php elseif ($type_object == "chi_tiet_doanh_thu") : ?>
                        <table id="tb-cs-detail" class="table tnh-table dataTable">
                            <thead>
                                <tr>
                                    <th><?= lang('date') ?></th>
                                    <th><?= lang('tnh_reference_orders') ?></th>
                                    <th><?= lang('tnh_employees_charge') ?></th>
                                    <th><?= lang('tnh_grand_total') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="th-grand_total"></th>
                                </tr>
                            </tfoot>
                        </table>
                    <?php elseif ($type_object == "gtgt") : ?>
                        <table id="tb-cs" class="table tnh-table dataTable">
                            <thead>
                                <tr>
                                    <th><?= lang('date') ?></th>
                                    <th><?= lang('tnh_reference_orders') ?></th>
                                    <th><?= lang('tnh_employees_charge') ?></th>
                                    <th><?= lang('tnh_taxs') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="th-grand_total"></th>
                                </tr>
                            </tfoot>
                        </table>
                    <?php elseif ($type_object == "chi_tiet_chiet_khau_hoa_don") : ?>
                        <table id="tb-cs-detail" class="table tnh-table dataTable">
                            <thead>
                                <tr>
                                    <th><?= lang('date') ?></th>
                                    <th><?= lang('tnh_reference_orders') ?></th>
                                    <th><?= lang('tnh_employees_charge') ?></th>
                                    <th><?= lang('tnh_discount') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="th-grand_total"></th>
                                </tr>
                            </tfoot>
                        </table>
                    <?php elseif ($type_object == "chi_tiet_hang_tra_ve") : ?>
                        <table id="tb-cs-detail" class="table tnh-table dataTable">
                            <thead>
                                <tr>
                                    <th><?= lang('date') ?></th>
                                    <th><?= lang('Số đơn hàng trả về') ?></th>
                                    <th><?= lang('tnh_grand_total') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th class="th-grand_total"></th>
                                </tr>
                            </tfoot>
                        </table>
                    <?php elseif ($type_object == "chi_phi_ghi_nhan") : ?>
                        <table id="tb-cs" class="table tnh-table dataTable">
                            <thead>
                                <tr>
                                    <th><?= lang('Ngày chứng từ') ?></th>
                                    <th><?= lang('Mã chứng từ') ?></th>
                                    <th><?= lang('Tổng giá trị') ?></th>
                                    <th><?= lang('Tổng chi') ?></th>
                                    <th><?= lang('Tổng tiền') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th class="th-tonggiatri"></th>
                                    <th class="th-tongchi"></th>
                                    <th class="th-tongtien"></th>
                                </tr>
                            </tfoot>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
        <input type="hidden" name="mstart_date_search" id="mstart_date_search" class="form-control mstart_date_search" value="<?= $start_date ?>">
        <input type="hidden" name="mend_date_search" id="mend_date_search" class="form-control mend_date_search" value="<?= $end_date ?>">
        <input type="hidden" name="mid_branch" id="mid_branch" class="form-control mid_branch" value="<?= $id_branch ?>">
        <input type="hidden" name="memployee_id" id="memployee_id" class="form-control memployee_id" value="<?= $employee_id ?>">
    </div>
</div>

<script>
    <?php if ($type_object != "chi_tiet_doanh_thu" && $type_object != "chi_tiet_chiet_khau_hoa_don" && $type_object != "chi_tiet_hang_tra_ve") : ?>
        var oTableModal = '';
    <?php else : ?>
        var oTableModalDetail = '';
    <?php endif; ?>
    var paramsModal = {
        'start_date': '#mstart_date_search',
        'end_date': '#mend_date_search',
        'id_branch': '#mid_branch',
        'employee_id': '.chi_tiet #memployee_id',
    };

    $(document).ready(function() {
        <?php if ($type_object != "chi_tiet_doanh_thu" && $type_object != "chi_tiet_chiet_khau_hoa_don" && $type_object != "chi_tiet_hang_tra_ve") : ?>
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
                                    $('.th-grand_total').html('<div class="text-right bold">' + tnhFormatMoney(response.grandTotal) + '</div>');
                                    if (typeof response.tonggiatri !== "undefined") {
                                        $('.th-tonggiatri').html('<div class="text-right bold">' + tnhFormatMoney(response.tonggiatri) + '</div>');
                                    }
                                    if (typeof response.tongchi !== "undefined") {
                                        $('.th-tongchi').html('<div class="text-right bold">' + tnhFormatMoney(response.tongchi) + '</div>');
                                    }
                                    if (typeof response.tongtien !== "undefined") {
                                        $('.th-tongtien').html('<div class="text-right bold">' + tnhFormatMoney(response.tongtien) + '</div>');
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
        <?php else : ?>
            oTableModalDetail = tnhDatatable(
                '#tb-cs-detail', {
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
                                    $('.chi_tiet .th-grand_total').html('<div class="text-right bold">' + tnhFormatMoney(response.grandTotal) + '</div>');
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
        <?php endif; ?>
    });
</script>