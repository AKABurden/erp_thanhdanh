<style type="text/css">
    .w-container {
        min-width: 230px;
    }
    .w-content {
        padding: 10px;
        border-bottom: 1px solid #ececec;
    }
    .w-content-icon {
        margin-top: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        float: left;
        width: 20px;
        font-size: 14px;
        color: #256faa;
        transition: 0.3s all;
        transform: rotate(0deg);
    }
    .w-content-action {
        float: left;
        width: calc(100% - 25px);
        margin-left: 5px;
    }
    .w-content-a {
        color: #252525;
        text-transform: uppercase;
        font-size: 14px;
        font-weight: 500;
    }
    .w-content:hover {
        background: #f7f7f7;
    }
    .w-content:hover .w-content-icon {
        transform: rotate(90deg);
    }
</style>
<div class="w-container">
    <?php if (has_permission('customers', '', 'create')) { ?>
        <a href="<?= admin_url('clients/client') ?>">
            <div class="w-content">
                <div class="w-content-icon">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="w-content-action">
                    <span class="w-content-a"><?= _l('addon_client') ?></span>
                </div>
                <div class="clearfix"></div>
            </div>
        </a>
    <?php } ?>
    <?php if (has_permission('suppliers', '', 'create')) { ?>
        <a href="<?= admin_url('suppliers?modal=true') ?>">
            <div class="w-content">
                <div class="w-content-icon">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="w-content-action">
                    <span class="w-content-a"><?= _l('addon_suppliers') ?></span>
                </div>
                <div class="clearfix"></div>
            </div>
        </a>
    <?php } ?>
    <?php if (has_permission('purchases', '', 'create')) { ?>
        <a href="<?= admin_url('purchases/detail') ?>">
            <div class="w-content">
                <div class="w-content-icon">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="w-content-action">
                    <span class="w-content-a"><?= _l('addon_purchases') ?></span>
                </div>
                <div class="clearfix"></div>
            </div>
        </a>
    <?php } ?>
    <?php if (has_permission('purchase_order', '', 'create')) { ?>
        <a href="<?= admin_url('purchase_order/detail') ?>">
            <div class="w-content">
                <div class="w-content-icon">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="w-content-action">
                    <span class="w-content-a"><?= _l('addon_purchase_order') ?></span>
                </div>
                <div class="clearfix"></div>
            </div>
        </a>
    <?php } ?>
    <?php if (has_permission('quotes', '', 'create')) { ?>
        <a href="<?= admin_url('quotes/add') ?>">
            <div class="w-content">
                <div class="w-content-icon">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="w-content-action">
                    <span class="w-content-a"><?= _l('addon_quotes') ?></span>
                </div>
                <div class="clearfix"></div>
            </div>
        </a>
    <?php } ?>
    <?php if (has_permission('orders', '', 'create')) { ?>
        <a href="<?= admin_url('orders/add') ?>">
            <div class="w-content">
                <div class="w-content-icon">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="w-content-action">
                    <span class="w-content-a"><?= _l('addon_orders') ?></span>
                </div>
                <div class="clearfix"></div>
            </div>
        </a>
    <?php } ?>
    <?php if (has_permission('manufactures_productions_plan', '', 'create')) { ?>
        <a href="<?= admin_url('manufactures/add_productions_plan') ?>">
            <div class="w-content">
                <div class="w-content-icon">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="w-content-action">
                    <span class="w-content-a"><?= _l('addon_manufactures_productions_plan') ?></span>
                </div>
                <div class="clearfix"></div>
            </div>
        </a>
    <?php } ?>
    <?php if (has_permission('manufactures_productions_orders', '', 'create')) { ?>
        <a href="<?= admin_url('manufactures/add_productions_orders') ?>">
            <div class="w-content">
                <div class="w-content-icon">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="w-content-action">
                    <span class="w-content-a"><?= _l('addon_manufactures_productions_orders') ?></span>
                </div>
                <div class="clearfix"></div>
            </div>
        </a>
    <?php } ?>
    <!-- <?php if (has_permission('invoice_items', '', 'create')) { ?>
        <a href="<?= admin_url('invoice_items/item') ?>">
            <div class="w-content">
                <div class="w-content-icon">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="w-content-action">
                    <span class="w-content-a"><?= _l('addon_invoice_items') ?></span>
                </div>
                <div class="clearfix"></div>
            </div>
        </a>
    <?php } ?>
    <?php if (has_permission('items', '', 'create')) { ?>
        <a href="<?= admin_url('items?modal=true') ?>">
            <div class="w-content">
                <div class="w-content-icon">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="w-content-action">
                    <span class="w-content-a"><?= _l('addon_items') ?></span>
                </div>
                <div class="clearfix"></div>
            </div>
        </a>
    <?php } ?>
    <?php if (has_permission('products', '', 'create')) { ?>
        <a href="<?= admin_url('products?modal=true') ?>">
            <div class="w-content">
                <div class="w-content-icon">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="w-content-action">
                    <span class="w-content-a"><?= _l('addon_products') ?></span>
                </div>
                <div class="clearfix"></div>
            </div>
        </a>
    <?php } ?> -->
    <?php if (has_permission('vouchers_coupon', '', 'create')) { ?>
        <a href="<?= admin_url('vouchers_coupon?modal=true') ?>">
            <div class="w-content">
                <div class="w-content-icon">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="w-content-action">
                    <span class="w-content-a"><?= _l('addon_vouchers_coupon') ?></span>
                </div>
                <div class="clearfix"></div>
            </div>
        </a>
    <?php } ?>
</div>