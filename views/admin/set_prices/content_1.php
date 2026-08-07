<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="col-md-12" style="margin-left: -15px">
                    <div style="z-index: 9" data-toggle="btn" class="btn-group mbot15">
                        <button style=" font-size: 11px;" type="button" id="btndata_all" data-toggle="tab" class="btn btn-info btn-search" data-value="all">
                            <?= _l('leads_all') ?>
                        </button>
                        <?php foreach ($groupss as $key => $value) { ?>
                            <button style="font-size: 11px; color: #fff; background: <?= $value['color'] ?>" type="button" data-toggle="tab" class="btn btn-info btn-search" data-value="<?= $value['id'] ?>">
                                <?= $value['name'] ?>
                            </button>
                        <?php } ?>
                    </div>
                </div>
                <input type="hidden" name="filterStatus">
                <div class="panel-body">
                    <?php render_datatable(array(
                        _l('#'),
                        _l('name_set_prices'),
                        _l('number_item'),
                        _l('date_active'),
                        _l('status'),
                        _l('range_customer'),
                        _l('customer_group'),
                        // _l('range_item'),
                        _l('apply'),
                        _l('ch_option'),
                    ),'set_prices'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function(){
    var CustomersServerParams = {
          'filterStatus' : '[name="filterStatus"]',
       };
    var tAPI = initDataTableCustom('.table-set_prices', admin_url+'set_prices/table_set_prices', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'desc'))); ?>);
    $.each(CustomersServerParams, function(filterIndex, filterItem){
        $('' + filterItem).on('change', function(){
            tAPI.draw('page');
        });
    });
    appValidateForm($('#set-prices-modal'), {name: 'required'}, manage_set_prices);
});
</script>