
<?php if(!empty($data)){ ?>
        <div class="div_advisory panel panel-info mbot10">
            <div class="panel-heading">
                Đơn hàng
            </div>
            <div class="panel-body">
                <table class="table table-not-top-bot">
                    <thead>
                        <tr>
                            <th><?=_l('cong_t_item')?></th>
                            <th><?=_l('cong_t_price')?></th>
                            <th><?=_l('cong_quantity_short')?></th>
                            <th><?=_l('cong_discount_short')?></th>
                            <th><?=_l('cong_t_money')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $key => $value){?>
                        <tr>
                            <td colspan="5" class="panel-heading">
                                <?php
                                $this->load->model('orders_model');
                                $status = $this->orders_model->get_status_orders($value->id, $value->status);
                                ?>
                                <?= $value->prefix . $value->code .' - '. _d($value->date).' - '.$status  ?>
                            </td>
                        </tr>
                            <?php foreach($value->detail as $kDetail => $vDetail) {?>
                                <tr>
                                    <td>
                                        <p>
                                            <?=$vDetail->name?>
                                            <?php if ($vDetail->type_items == "items"): ?>
                                                <span class="label label-success"><?= lang('ch_items') ?></span>
                                            <?php elseif ($vDetail->type_items == "products"): ?>
                                                <span class="label label-warning"><?= lang('tnh_products') ?></span>
                                            <?php endif ?>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-right">
                                            <?=number_format_data($vDetail->price)?>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-center">
                                            <?=number_format_data($vDetail->quantity)?>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-right">
                                            <?=number_format_data($vDetail->money_discount)?>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-right">
                                            <?=number_format_data($vDetail->grand_total)?>
                                        </p>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
<?php } else { ?>
    <div class="div_advisory panel panel-info">
        <div class="panel-heading">
            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
            <?=_l('cong_orders')?>
        </div>

        <div class="panel-body">
            <p class="text-danger"><?=_l('cong_not_find_orders')?></p>
        </div>
    </div>
<?php } ?>