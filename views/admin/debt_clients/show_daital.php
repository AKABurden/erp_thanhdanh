                        <div class="tab-panels">
                                <table id="table-items" class="table table-bordered table-hover dont-responsive-table" style="max-height: 400px !important;">
                                    <thead>
                                        <tr>
                                            <th style="width: 30px;"><?= lang('tnh_numbers') ?></th>
                                            <!-- <th style="width: 50px"><?= lang('tnh_images') ?></th> -->
                                            <th style="width: 90px;"><?= lang('code') ?></th>
                                            <th style="width: 210px;"><?= lang('tnh_product_name') ?></th>
                                            <!-- <th style="width: 50px;"><?= lang('tnh_dvt') ?></th> -->
                                            <th style="width: 60px;"><?= lang('quantity') ?></th>
                                            <th style="width: 70px;"><?= lang('tnh_unit_price') ?></th>
                                            <!-- <th style="width: 100px;"><?= lang('tnh_total_amount') ?></th> -->
                                            <!-- <th style="width: 100px;"><?= lang('tnh_tax_items') ?></th> -->
                                            <th style="width: 100px;"><?= lang('tnh_discount_percent') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_discount_direct') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_grand_total') ?></th>
                                            <!-- <th style="width: 100px;"><?= lang('note') ?></th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?= $bodyItems ?>
                                    </tbody>
                                </table>
                        </div>
<script type="text/javascript">
    $(document).ready(function() {
        var flagView = <?= !empty($flagView) ? 1 : 0; ?>;
        var dtItems = $('#table-items').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            // scrollY: '300px',
            scrollX: true,
            fixedColumns:   {
                leftColumns: 4,
                rightColumns: 0
            },
            // 'searching': false,
            // 'ordering': false,
            // 'paging': false,
            // "info": false,
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            }
        });

        setTimeout(function(){ dtItems.draw(); }, 1000);
        $('#tab1').click(function(event) {
            dtItems.draw();
        });

        if (flagView == 1) {
            oTable.draw('page');
        }
    });
</script>