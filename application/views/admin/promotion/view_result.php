<div class="modal fade" id="view_result_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="add-title"><?=$title?></span>
                </h4>
            </div>
            <div class="modal-body">
                <?php if($typeResult == 'discount') { ?>
                    <?php if(!empty($dataResult)) { ?>
                        <div class="panel panel-default">
                            <div class="panel-heading text-center">
                                <div class="bold uppercase">Danh sách khách hàng được tặng</div>
                            </div>
                            <div class="panel-body">
                                <table class="table table-bordered dont-responsive-table table-sales" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 10%;">
                                                <?php echo _l('day'); ?>
                                            </th>
                                            <th class="text-center" style="width: 20%;">
                                                <?php echo _l('tnh_reference_orders'); ?>
                                            </th>
                                            <th class="text-center" style="width: 40%;">
                                                <?php echo _l('item_result'); ?>
                                            </th>
                                            <th class="text-center" style="width: 20%;">
                                                <?php echo _l('cong_total'); ?>
                                            </th>
                                            <th class="text-center" style="width: 10%;">
                                                <?php echo _l('promotion_limit_discount'); ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dataResult as $key => $value) { ?>
                                            <tr class="wap-customer" data-parent="<?=$key?>">
                                                <td class="text-left">
                                                    <span class="bold js-show wap-show">+</span>
                                                    <span><?=$value['customer_name']?></span>
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-center"><?=$value['discount']?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="panel panel-default">
                            <div class="panel-body text-center text-danger">Chưa có đơn hàng đáp ứng điều kiện</div>
                        </div>
                    <?php } ?>
                <?php } else if($typeResult == 'sales') { ?>
                    <?php if(!empty($item_gift)) { ?>
                        <div class="panel panel-default">
                            <div class="panel-heading text-center">
                                <div class="bold uppercase">Danh sách sản phẩm tặng kèm</div>
                            </div>
                            <div class="panel-body">
                                <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 80%;">
                                                <?php echo _l('promotion_item_gift'); ?>
                                            </th>
                                            <th class="text-center" style="width: 20%;">
                                                <?php echo _l('promotion_number_gift'); ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($item_gift as $k => $v) { ?>
                                            <tr>
                                                <td>
                                                    <span class="inline-block label label-warning"><?=$v['type']?></span>
                                                    <?=$v['name']?>
                                                </td>
                                                <td class="text-center">
                                                    <?=$v['quantity']?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if(!empty($dataResult)) { ?>
                        <div class="panel panel-default">
                            <div class="panel-heading text-center">
                                <div class="bold uppercase">Danh sách khách hàng được tặng</div>
                            </div>
                            <div class="panel-body">
                                <table class="table table-bordered dont-responsive-table table-sales" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 10%;">
                                                <?php echo _l('day'); ?>
                                            </th>
                                            <th class="text-center" style="width: 20%;">
                                                <?php echo _l('tnh_reference_orders'); ?>
                                            </th>
                                            <th class="text-center" style="width: 50%;">
                                                <?php echo _l('item_result'); ?>
                                            </th>
                                            <th class="text-center" style="width: 20%;">
                                                <?php echo _l('cong_total'); ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dataResult as $key => $value) { ?>
                                            <tr class="wap-customer" data-parent="<?=$key?>">
                                                <td class="text-left">
                                                    <span class="bold js-show wap-show">+</span>
                                                    <span><?=$value['customer_name']?></span>
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="panel panel-default">
                            <div class="panel-body text-center text-danger">Chưa có khách hàng đáp ứng điều kiện</div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- bổ xung cho lấy dữ liệu với js -->
<div class="hide">
    <?php if($typeResult == 'discount') { ?>
        <?php if(!empty($dataResult)) { ?>
            <?php foreach ($dataResult as $key => $value) { ?>
                <?php $total = 0; ?>
                <?php foreach ($value['order'] as $k => $v) { ?>
                    <div class="detail-child" data-child="<?=$key?>">
                        <span class="date_order"><?=_dt($v['date_order'])?></span>
                        <span class="number_order"><?=$v['number_order']?></span>
                        <span class="list_items"><?=$v['list_items']?></span>
                        <span class="total"><?=number_format($v['total'])?></span>
                    </div>
                    <?php $total += $v['total']; ?>
                <?php } ?>
                <div class="total-child" data-child="<?=$key?>">
                    <span class="all_total"><?=number_format($total)?></span>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } else if($typeResult == 'sales') { ?>
        <?php if(!empty($dataResult)) { ?>
            <?php foreach ($dataResult as $key => $value) { ?>
                <?php $total = 0; ?>
                <?php foreach ($value['order'] as $k => $v) { ?>
                    <div class="detail-child" data-child="<?=$key?>">
                        <span class="date_order"><?=_dt($v['date_order'])?></span>
                        <span class="number_order"><?=$v['number_order']?></span>
                        <span class="list_items"><?=$v['list_items']?></span>
                        <span class="total"><?=number_format($v['total'])?></span>
                    </div>
                    <?php $total += $v['total']; ?>
                <?php } ?>
                <div class="total-child" data-child="<?=$key?>">
                    <span class="all_total"><?=number_format($total)?></span>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } ?>
</div>
<!-- end -->
<script>
    $(function(){
        $('.table-sales').DataTable({
            "bLengthChange" : true,
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            scrollX: false,
            "order": [],
            "columnDefs": [{
              "targets"  : [0,1,2,3],
              "orderable": false,
            }],
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function ( row, data, start, end, display ) {
            }
        });
    });
    $('.js-show').click(function(e) {
        var current = $(e.currentTarget);
        var parent = current.parents('.wap-customer').attr('data-parent');
        var html = '';
        if(current.hasClass('wap-show')) {
            current.removeClass('wap-show');
            current.text('-');
            current.addClass('wap-hide');

            var div_child = $('div[class="detail-child"][data-child='+parent+']');
            $.each(div_child, (i, v) => {
                html += '<tr data-row="'+parent+'">\
                            <td class="text-center">\
                                '+$(v).find('.date_order').text()+'\
                            </td>\
                            <td class="text-center">\
                                '+$(v).find('.number_order').text()+'\
                            </td>\
                            <td class="text-center">\
                                '+$(v).find('.list_items').text()+'\
                            </td>\
                            <td class="text-center">\
                                '+$(v).find('.total').text()+'\
                            </td>';
                <?php if($typeResult == 'discount') { ?>
                    html += '<td></td>';
                <?php } ?>
                html += '</tr>';
            });
            html += '<tr class="wap-total" data-row="'+parent+'">\
                        <td class="text-left">\
                            <?=_l('tnh_grand_total')?>
                        </td>\
                        <td></td>\
                        <td></td>\
                        <td class="text-center">\
                            '+$('div[class="total-child"][data-child='+parent+']').find('.all_total').text()+'\
                        </td>';
            <?php if($typeResult == 'discount') { ?>
                html += '<td></td>';
            <?php } ?>
            html += '</tr>';
        }
        else if(current.hasClass('wap-hide')) {
            current.removeClass('wap-hide');
            current.text('+');
            current.addClass('wap-show');

            var div_row = $('tr[data-row='+parent+']');
            $.each(div_row, (i, v) => {
                $(v).remove();
            });
        }
        var tr = current.parents('tr');
        $(html).insertAfter(tr);
    });

    $('.table-sales').on('draw.dt', function() {
        $('.js-show').removeClass('wap-hide');
        $('.js-show').text('+');
        $('.js-show').addClass('wap-show');
    });
</script>