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
                <input type="hidden" name="items[<?=$Korder?>][type_items]" id="items[<?=$Korder?>][type_items]" class="form-control items" value="<?= !empty($Vorder->type_items) ? $Vorder->type_items : 'items' ?>">
                <input type="hidden" name="items[<?=$Korder?>][id_product]" id="items[<?=$Korder?>][id_product]" value="<?=$Vorder->id_product?>" />
                <p class="code_product mtop20">
                    <?php echo $Vorder->code_items;?>
                </p>
            </td>
            <td class="text-center">
                <img src="<?=base_url($Vorder->avatar);?>" class="c_img_item">
            </td>
            <td>
                <p class="name_product mtop20">
                    <?php echo $Vorder->name;?>
                </p>
            </td>
            <td>
                <input class="form-control c_quantity width100 H_input" onkeyup="C_formatNumBerKeyUp(this)" name="items[<?=$Korder?>][quantity]" id="items[<?=$Korder?>][quantity]" value="<?=number_format($Vorder->quantity);?>">
            </td>
            <td>
                <input class="form-control c_price width130 H_input" onkeyup="C_formatNumBerKeyUp(this)" name="items[<?=$Korder?>][price]" id="items[<?=$Korder?>][price]" value="<?=number_format($Vorder->price)?>">
            </td>
            <td>
                <div class="input-group">
                    <input class="form-control c_discount width100 H_input " name="items[<?=$Korder?>][discount]" onkeyup="C_formatNumBerKeyUp(this)" value="<?=number_format($Vorder->discount)?>">
                    <div class="input-group-addon group_addon">
                        <div class="radio pull-left">
                            <input type="radio" class="c_type_discount" name="items[<?=$Korder?>][type_discount]" value="1" <?= ($Vorder->type_discount == 1 ? 'checked': '') ?>>
                            <label>%</label>
                        </div>
                        <div class="radio pull-right">
                            <input type="radio" class="c_type_discount" name="items[<?=$Korder?>][type_discount]" value="2" <?= ($Vorder->type_discount == 2 ? 'checked': '') ?>>
                            <label><?=_l('cong_money')?></label>
                        </div>
                    </div>
                </div>
            </td>
            <td>
                <input class="form-control c_cost_trans width130 H_input" onkeyup="C_formatNumBerKeyUp(this)" name="items[<?=$Korder?>][cost_trans]" value="<?=number_format($Vorder->cost_trans)?>">
            </td>
            <td>
                <p class="c_total mtop25">
                    <?=number_format($Vorder->grand_total)?>
                </p>
            </td>
            <td>
                <select class="c_customer selectpicker with-ajax " data-live-search="true" name="items[<?=$Korder?>][id_customer]" tabindex="-98" title="<?= _l('cong_pls_selected_option') ?>">
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
                <input class="form-control c_size width100 H_input" name="items[<?=$Korder?>][size]"  value="<?= $Vorder->size ?>">
            </td>
            <td>
                <div class="row-shipping" InitRow="<?=!empty($Vorder->shipping) ? (count($Vorder->shipping)) : 0?>">
                    <?php if(!empty($Vorder->shipping)){?>
                        <?php foreach($Vorder->shipping as $kShipping => $vShipping){?>
                            <div class="row-0 row-index">
                                <div class="col-md-1 plef0 pright0">
                                    <p class="mtop15"><a class="DeleteInitRow text-danger pointer">x</a></p>
                                    <input type="hidden" name="items[<?=$Korder?>][shipping][<?=$kShipping?>][id]" value="<?= $vShipping->id ?>">
                                </div>
                                <div class="col-md-7 plef0 pright5">
                                    <input class="form-control c_date_shipping  H_input datepicker" name="items[<?=$Korder?>][shipping][<?=$kShipping?>][date_shipping]" id="items[<?=$Korder?>][shipping][<?=$kShipping?>][date_shipping]"  value="<?= _d($vShipping->date_shipping) ?>">
                                </div>
                                <div class="col-md-4 plef0 pright0">
                                    <input type="number" class="form-control c_quantity_shipping  H_input" name="items[<?=$Korder?>][shipping][<?=$kShipping?>][quantity_shipping]" id="items_update[<?=$Korder?>][shipping][<?=$kShipping?>][quantity_shipping]"  value="<?= $vShipping->quantity_shipping ?>">
                                </div>
                                <?php
                                    $vValdateFrom['items['.$Korder.'][shipping]['.$kShipping.'][date_shipping]'] = 'required';
                                    $vValdateFrom['items['.$Korder.'][shipping]['.$kShipping.'][quantity_shipping]'] = [
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
                    <a class="pointer" onclick="AddRowShipping('items', <?=$Korder?>, this)">
                        + <?=_l('cong_add_shipping_row')?>
                    </a>
                </div>
            </td>
            <td>
                <a class="btn btn-icon btn-danger DeleteItems"><i class="fa fa-times" aria-hidden="true"></i></a>
            </td>
        </tr>
        <?php
            $vValdateFrom['items['.$Korder.'][id]'] = 'required';
            $vValdateFrom['items['.$Korder.'][id_product]'] = 'required';
            $vValdateFrom['items['.$Korder.'][quantity]'] = [
                'required' => true,
                'min' => [1]
            ];
            $vValdateFrom['items['.$Korder.'][price]'] = 'required';
        ?>
<?php }
    $numDetail = count($orders->detail);
}?>
<script>
    var vValdateFrom = <?=!empty($vValdateFrom) ? json_encode($vValdateFrom) : '[]' ?>
</script>
