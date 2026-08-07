<style type="text/css">
    .dataTables_filter {
        display: none;
    }
</style>
<div id="report_all_of_stock_product" class="hide">
    <div class="row">
        <div class="col-md-3">
            <?= lang('category', 'category_search_products') ?>
            <select name="category_search_products" id="category_search_products" data-placeholder="<?= lang('tnh_category_product') ?>" class="modal-select2" style="width: 100%;">
                <option value=""></option>
                <?= recursiveCategoryProducts() ?>
            </select>
        </div>
        <div class="col-md-3">
            <?= lang('products', 'products_search') ?>
            <input type="text" name="products_search" id="products_search" style="width: 100%;" data-placeholder="<?= lang('products') ?>" value="">
        </div>
        <input type="hidden" name="search_type_product" id="search_type_product" value="products" />
    </div>
    <br>
    <div class="horizontal-scrollable-tabs">
        <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
        <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
        <div class="horizontal-tabs">
            <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                <!-- <li class="active">
                    <a class="H_filter_v2 " data-id="0">
                        <?= _l('Tất cả') ?>
                    </a>
                </li> -->
                <li class="active">
                    <a class="H_filter_v2 " data-id="1">
                        <?= _l('Tồn sẵn') ?>
                    </a>
                </li>
                <li>
                    <a class="H_filter_v2" data-id="2">
                        <?= _l('Tồn chờ giao') ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <input type="hidden" id="filterStatus_v2" name="filterStatus_v2" value="1" />
    <br>
    <table class="table table table-striped table-warehouse-all-report-product">
        <thead>
            <tr class="bold" style="text-align: center;font-weight: bold;">
                <th style="text-align: center;"><?php echo ucwords(_l('code_item')); ?></th>
                <th style="text-align: center;"><?php echo ucwords(_l('name_item')); ?></th>
                <th style="text-align: center;"><?php echo ucwords(_l('tnh_dvt')); ?></th>
                <th style="text-align: center;"><?php echo ucwords(_l('Thông tin')); ?></th>
                <th style="text-align: center;"><?php echo ucwords(_l('Vị trí')); ?></th>
                <?php foreach ($warehouse as $key => $value) { ?>
                    <th style="text-align: center;"><?= $value['name']; ?></th>
                <?php } ?>
                <th style="text-align: center;"><?php echo ucwords(_l('dt_sum_stock')); ?></th>
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
                <?php foreach ($warehouse as $key => $value) {
                    echo "<td></td>";
                } ?>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
<!-- <div id="return_suppliers_data"></div>
<div id="view_adjusted_data"></div>
<div id="export_different_data"></div>
<div id="view_transfer_data"></div> -->