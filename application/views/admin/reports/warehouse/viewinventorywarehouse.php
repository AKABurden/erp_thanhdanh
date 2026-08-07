<style type="text/css">

</style>
<div class="modal fade in" id="viewinventorywarehouse" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo ($id == 1) ? 'Thống kê nhập kho' : 'Thống kê xuất kho' ?> </span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table table-striped table-inventory_warehouses">
                            <thead>
                                <tr class="bold" style="text-align: center;font-weight: bold;">
                                    <th style="text-align: center;"><?php echo ucwords(_l('date_warehouse')); ?></th>
                                    <th style="text-align: center;"><?php echo ucwords(_l('ch_date_p')); ?></th>
                                    <th style="text-align: center;"><?php echo ucwords(_l('ch_code_p')); ?></th>
                                    <th style="text-align: center;"><?php echo ucwords(_l('Kho hàng')); ?></th>
                                    <th style="text-align: center;"><?php echo ucwords(_l('note')); ?></th>
                                    <th style="text-align: center;"><?php echo ucwords(_l('ch_items_unit')); ?></th>
                                    <?php if ($id == 1) { ?>
                                        <th style="text-align: center;"><?php echo ucwords(_l('ch_quantity_import')); ?></th>
                                    <?php } else { ?>
                                        <th style="text-align: center;"><?php echo ucwords(_l('tnh_quantity_export')); ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <!-- <td></td> -->
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function() {
            if ($.fn.DataTable.isDataTable('.table-inventory_warehouses')) {
                $('.table-inventory_warehouses').DataTable().destroy();
            }
            initDataTable('.table-inventory_warehouses', admin_url + 'reports/table_inventory_warehouses/<?= $id ?>/<?= $id_items ?>/<?= $type ?>', false, false, fnServerParams, [0, 'asc']);
        });
    </script>