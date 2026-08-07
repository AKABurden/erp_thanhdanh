<style type="text/css">
    .primary-table {
    background: #e8fbff !important;
    color: white !important;
}
</style>
<div style="padding-left: 10px;">
                           <table id="sub-products-<?= $id ?>" class="table table-bordered table-hover dont-responsive-table" style="max-height: 400px !important;">
                                    <thead>
                                        <tr class="primary-table">
                                            <th style="width: 30px;"><?= lang('tnh_numbers') ?></th>
                                            <th style="width: 80px"><?= lang('tnh_images') ?></th>
                                            <th style="width: 100px;"><?= lang('code') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_product_name') ?></th>
                                            <th style="width: 50px;"><?= lang('tnh_unit') ?></th>
                                            <th style="width: 100px;"><?= lang('quantity') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_unit_price') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_total_amount') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_tax_items') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_discount_percent') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_discount_direct') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_grand_total') ?></th>
                                            <th style="width: 100px;"><?= lang('note') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?= $bodyItems ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
</div>
<script type="text/javascript">
    var intVal = function ( i ) {
    rs = typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
    // if (isNaN(rs)) {
    //     return 0;
    // }
    return rs;
    };
	$(document).ready(function() {
        // $('#sub-products-<?= $id ?>').DataTable({
        //     "language": app.lang.datatables,
        //     "bLengthChange" : false,
        //     "pageLength": app.options.tables_pagination_limit,
        //     "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
        //     "initComplete": function(settings, json) {
        //         var t = this;
        //         t.parents('.table-loading').removeClass('table-loading');
        //         t.removeClass('dt-table-loading');
        //         // mainWrapperHeightFix();
        //     },
        // });
		$('#sub-products-<?= $id ?>').DataTable({
			"bLengthChange" : false,
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            scrollX: true,
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
                pageTotalQuantity = api
                    .column( 5, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                pageTotalAmount = api
                    .column( 7, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                pageGrandAmount = api
                    .column( 11, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );


                $( api.column( 5 ).footer() ).html('<div class="text-center">'+formatNumber(pageTotalQuantity)+'</div>');
                $( api.column( 7 ).footer() ).html('<div class="text-right">'+formatNumber(pageTotalAmount)+'</div>');
                $( api.column( 11 ).footer() ).html('<div class="text-right">'+formatNumber(pageTotalAmount)+'</div>');
            }
		});
        $('#sub-products-<?= $id ?>_wrapper').find('div .dataTables_info').css({"float": "left"});
        $('#sub-products-<?= $id ?>_wrapper').find('div #sub-products-<?= $id ?>_paginate').find('.pagination').css({"margin-top": "-40px"});
	});
    function formatNumber(nStr, decSeperate=".", groupSeperate=",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        x2=x2.substr(0,2);
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    };

</script>