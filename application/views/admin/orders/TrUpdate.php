<?php
$vValdateFrom = [];
if(!empty($orders->detail))
{
    foreach($orders->detail as $Korder => $Vorder){?>
        <tr class="strMain">
            <td><?=($Korder+1)?></td>
            <td>
                <div class="show-types mbot10">
                    <?php if ($Vorder->type_items == "items"): ?>
                        <span class="label label-success"><?= lang('ch_items') ?></span>
                    <?php elseif ($Vorder->type_items == "products"): ?>
                        <span class="label label-warning"><?= lang('tnh_products') ?></span>
                    <?php endif ?>
                </div>
                <input type="hidden" name="items_update[<?=$Korder?>][type_items]" id="items_update[<?=$Korder?>][type_items]" class="form-control type_items" value="<?= !empty($Vorder->type_items) ? $Vorder->type_items : 'items' ?>">
                <input type="hidden" class="c_product_items" name="items_update[<?=$Korder?>][id]" id="items_update[<?=$Korder?>][id]" value="<?=$Vorder->id?>" />
                <input type="hidden" name="items_update[<?=$Korder?>][id_product]" id="items_update[<?=$Korder?>][id_product]" value="<?=$Vorder->id_product?>" />
                <p><!-- <a target="_blank" href="<?=admin_url('invoice_items/item/'.$Vorder->id_product)?>"> --><?=$Vorder->code_items?><!-- </a> --></p>
            </td>
            <td class="text-center">
                <img src="<?=base_url($Vorder->avatar);?>" class="c_img_item">
            </td>
            <td>
                <p class="name_product mtop20">
                    <?php echo !empty($Vorder->name) ? $Vorder->name : $Vorder->name_product;?>
                </p>
            </td>
            <td>
                <input class="form-control c_quantity width100 H_input" onkeyup="C_formatNumBerKeyUp(this)" name="items_update[<?=$Korder?>][quantity]" id="items_update[<?=$Korder?>][quantity]" value="<?=number_format($Vorder->quantity);?>">
            </td>
            <td>
                <input class="form-control c_price width130 H_input" onkeyup="C_formatNumBerKeyUp(this)" name="items_update[<?=$Korder?>][price]" id="items_update[<?=$Korder?>][price]" value="<?=number_format($Vorder->price)?>">
            </td>
            <td>
                <div class="input-group">
                    <input class="form-control c_discount width100 H_input " name="items_update[<?=$Korder?>][discount]" onkeyup="C_formatNumBerKeyUp(this)" value="<?=number_format($Vorder->discount)?>">
                    <div class="input-group-addon group_addon">
                        <div class="radio pull-left">
                            <input type="radio" class="c_type_discount" name="items_update[<?=$Korder?>][type_discount]" value="1" <?= ($Vorder->type_discount == 1 ? 'checked': '') ?>>
                            <label>%</label>
                        </div>
                        <div class="radio pull-right">
                            <input type="radio" class="c_type_discount" name="items_update[<?=$Korder?>][type_discount]" value="2" <?= ($Vorder->type_discount == 2 ? 'checked': '') ?>>
                            <label><?=_l('cong_money')?></label>
                        </div>
                    </div>
                </div>
            </td>
            <td>
                <input class="form-control c_cost_trans width130 H_input" onkeyup="C_formatNumBerKeyUp(this)" name="items_update[<?=$Korder?>][cost_trans]" value="<?=number_format($Vorder->cost_trans)?>">
            </td>
            <td>
                <p class="c_total mtop25">
                    <?=number_format($Vorder->grand_total)?>
                </p>
            </td>
            <td>
                <select class="c_customer selectpicker with-ajax " data-live-search="true" name="items_update[<?=$Korder?>][id_customer]" tabindex="-98" title="<?= _l('cong_pls_selected_option') ?>">
                    <option class="bs-title-option" value=""></option>
                    <?php
                        if(!empty($Vorder->id_customer))
                        {
                            $option_client = get_table_where(db_prefix().'clients', ['userid' => $Vorder->id_customer], '', 'row');
                            if(!empty($option_client))
                            {
                                echo "<option value='".$option_client->userid."' selected> ".$option_client->company."</option>";
                            }
                        }
                    ?>
                </select>
            </td>
            <td>
                <input class="form-control c_size width100 H_input" name="items_update[<?=$Korder?>][size]"  value="<?= $Vorder->size ?>">
            </td>
            <td>
                <div class="row-shipping" InitRow="<?=!empty($Vorder->shipping) ? (count($Vorder->shipping)) : 0?>">
                    <?php if(!empty($Vorder->shipping)){?>
                        <?php foreach($Vorder->shipping as $kShipping => $vShipping){?>
                            <div class="row-0 row-index">
                                <div class="col-md-1 plef0 pright0">
                                    <p class="mtop15"><a class="DeleteInitRow text-danger pointer">x</a></p>
                                    <input type="hidden" name="items_update[<?=$Korder?>][shipping][<?=$kShipping?>][id]" value="<?= $vShipping->id ?>">
                                </div>
                                <div class="col-md-7 plef0 pright5">
                                    <input class="form-control c_date_shipping  H_input datepicker" name="items_update[<?=$Korder?>][shipping][<?=$kShipping?>][date_shipping]" id="items_update[<?=$Korder?>][shipping][<?=$kShipping?>][date_shipping]"  value="<?= _d($vShipping->date_shipping) ?>">
                                </div>
                                <div class="col-md-4 plef0 pright0">
                                    <input type="number" class="form-control c_quantity_shipping  H_input" name="items_update[<?=$Korder?>][shipping][<?=$kShipping?>][quantity_shipping]" id="items_update[<?=$Korder?>][shipping][<?=$kShipping?>][quantity_shipping]"  value="<?= $vShipping->quantity_shipping ?>">
                                </div>
                                <?php
                                    $vValdateFrom['items_update['.$Korder.'][shipping]['.$kShipping.'][date_shipping]'] = 'required';
                                    $vValdateFrom['items_update['.$Korder.'][shipping]['.$kShipping.'][quantity_shipping]'] = [
                                        'required' => true,
                                        'range' => [0, $Vorder->quantity]
                                    ];;
                                ?>
                                <div class="clearfix"></div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="col-md-12 mtop10">
                    <a class="pointer" onclick="AddRowShipping('items_update', <?=$Korder?>, this)">
                        + <?=_l('cong_add_shipping_row')?>
                    </a>
                </div>
            </td>
            <td>
                <a class="btn btn-icon btn-danger DeleteItems"><i class="fa fa-times" aria-hidden="true"></i></a>
            </td>
        </tr>
        <?php
            $vValdateFrom['items_update['.$Korder.'][id]'] = 'required';
            $vValdateFrom['items_update['.$Korder.'][id_product]'] = 'required';
            $vValdateFrom['items_update['.$Korder.'][quantity]'] = [
                'required' => true,
                'min' => [1]
            ];
            $vValdateFrom['items_update['.$Korder.'][price]'] = 'required';
        ?>
<?php }
} else {?>
    <tr class="strMain" index="0">
        <td>1</td>
        <td>
            <div class="show-types"></div>
            <input type="hidden" name="items[0][type_items]" id="items[0][type_items]" class="form-control type_items" value="">
            <select class="c_product_items selectpicker with-ajax" data-live-search="true" name="items[0][id_product]" id="items[0][id_product]" tabindex="-98" title="<?= _l('cong_pls_selected_option') ?>"></select>
        </td>
        <td class="text-center">
            <img src="<?=base_url()?>assets/images/preview-not-available.jpg" class="c_img_item">
        </td>
        <td>
            <p class="name_product mtop20"></p>
        </td>
        <td>
            <input class="form-control c_quantity width100 H_input" onkeyup="C_formatNumBerKeyUp(this)" name="items[0][quantity]" id="items[0][quantity]" value="">
        </td>
        <td>
            <input class="form-control c_price width130 H_input" onkeyup="C_formatNumBerKeyUp(this)" name="items[0][price]" id="items[0][price]" value="">
        </td>
        <td>
            <div class="input-group">
                <input class="form-control c_discount width100 H_input " name="items[0][discount]" value="">
                <div class="input-group-addon group_addon">
                    <div class="radio pull-left">
                        <input type="radio" class="c_type_discount" name="items[0][type_discount]" value="1" checked>
                        <label>%</label>
                    </div>
                    <div class="radio pull-right">
                        <input type="radio" class="c_type_discount" name="items[0][type_discount]" value="2">
                        <label><?=_l('cong_money')?></label>
                    </div>
                </div>
            </div>
        </td>
        <td>
            <input class="form-control c_cost_trans width130 H_input" onkeyup="C_formatNumBerKeyUp(this)" name="items[0][cost_trans]" value="0">
        </td>
        <td>
            <p class="c_total mtop25"></p>
        </td>
        <td>
            <select class="c_customer selectpicker with-ajax" data-live-search="true" name="items[0][id_customer]" tabindex="-98" title="<?= _l('cong_pls_selected_option') ?>">
                <option class="bs-title-option" value=""></option>
            </select>
        </td>
        <td>
            <input class="form-control c_size width100 H_input" name="items[0][size]">
        </td>
        <td>
            <div class="row-shipping" InitRow="0"></div>
            <div class="col-md-12 mtop10">
                <a class="pointer" onclick="AddRowShipping('items', 0, this)">
                    + <?=_l('cong_add_shipping_row')?>
                </a>
            </div>
        </td>
        <td>
            <a class="btn btn-icon btn-danger DeleteItems"><i class="fa fa-times" aria-hidden="true"></i></a>
        </td>
    </tr>
    <?php
        $vValdateFrom['items[0][id]'] = 'required';
        $vValdateFrom['items[0][id_product]'] = 'required';
        $vValdateFrom['items[0][quantity]'] = 'required';
        $vValdateFrom['items[0][price]'] = 'required';
    ?>
<?php } ?>
<script>
    var vValdateFrom = <?=!empty($vValdateFrom) ? json_encode($vValdateFrom) : '[]' ?>
</script>
