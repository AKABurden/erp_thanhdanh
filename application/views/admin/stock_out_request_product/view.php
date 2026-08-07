<div class="modal-dialog modal-lg" style="width: 70%;">
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
                            <div class="ml-at t-bold"><?= _dt($value['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Số phiếu yêu cầu') ?>: </div>
                            <div class="ml-at t-bold"><?= $value['code'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('note') ?>: </div>
                            <div class="ml-at t-bold"><?= $value['note'] ?></div>
                        </div>

                    </div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">
                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <div class="table-responsive">
                                    <table id="table-items" class="table dt-tnh table-hover table-condensed table-cs-border">
                                        <thead>
                                        <tr>
                                            <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                                            <th><?= lang('Tên Brand') ?></th>
                                            <th><?= lang('Mã Thành Phẩm') ?></th>
                                            <th><?= lang('Tên Thành Phẩm') ?></th>
                                            <th><?= lang('Tên Nhóm SP') ?></th>
                                            <th><?= lang('Tên Chủng Loại SP') ?></th>
                                            <th><?= lang('Định Mức Thời Gian') ?></th>
                                            <th><?= lang('Hình Ảnh SP') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (!empty($value['items'])){ ?>
                                            <?php foreach($value['items'] as $itemRow => $itemValue) {
                                                $imageUrl = base_url('assets/images/tnh/no_image.png');
                                                if (!empty($itemValue['image'])) {
                                                    $imageUrl = base_url($itemValue['image']);
                                                } ?>
                                                <tr>
                                                    <td class="text-center"><div class="stt"><?= (++$itemRow) ?></div></td>
                                                    <td><div class="brand_item"><?= $itemValue['brand_name'] ?></div></td>
                                                    <td>
                                                        <div class="code_item">
                                                            <?= $itemValue['item_code'] ?>
                                                        </div>
                                                    </td>
                                                    <td><div class="name_item"><?= $itemValue['item_name'] ?></div></td>
                                                    <td><div class="category_item"><?= $itemValue['category_name'] ?? '' ?></div></td>
                                                    <td><div class="specie_item"><?= $itemValue['specie_name'] ?? '' ?></div></td>
                                                    <td><div class="quota_time_change_one_item text-right"><?= (!empty($itemValue['quota_time_change_one']) ? formatNumber($itemValue['quota_time_change_one']) : 0) ?></div></td>
                                                    <td><div class="td-image">
                                                        <div class="preview_image" style="width: auto;">
                                                            <div class="display-block contract-attachment-wrapper img">
                                                                <div style="width:45px;">
                                                                    <a href="<?= $imageUrl ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                        <div class="">
                                                                            <img src="<?= $imageUrl ?>" style="border-radius: 50%">
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
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
                                <div><?= lang('tnh_created_by') ?>: <?= get_staff_full_name($value['create_by']) ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($value['date_create']) ?></div>
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
                // var apiSub = this.api(),
                //     data;
                // pageQuantity = apiSub
                //     .column(5, {
                //         page: 'current'
                //     })
                //     .data()
                //     .reduce(function(a, b) {
                //         return intVal(a) + intVal(b);
                //     }, 0);

                // pageQuantityKien = apiSub
                //     .column(6, {
                //         page: 'current'
                //     })
                //     .data()
                //     .reduce(function(a, b) {
                //         return intVal(a) + intVal(b);
                //     }, 0);

                // pageQuantityKg = apiSub
                //     .column(7, {
                //         page: 'current'
                //     })
                //     .data()
                //     .reduce(function(a, b) {
                //         return intVal(a) + intVal(b);
                //     }, 0);
                // $(apiSub.column(5).footer()).html('<div class="text-center bold">' + tnhFormatNumber(pageQuantity) + '</div>');
                // $(apiSub.column(6).footer()).html('<div class="text-center bold">' + tnhFormatNumber(pageQuantityKien) + '</div>');
                // $(apiSub.column(7).footer()).html('<div class="text-center bold">' + tnhFormatNumber(pageQuantityKg) + '</div>');;
            }
        });
    });
</script>