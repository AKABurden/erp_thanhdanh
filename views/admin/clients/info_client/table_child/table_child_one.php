<?php
    if(!empty($client_info_detail))
    {
        foreach($client_info_detail as $key => $value)
        {?>
            <?php
            $rule_add = "";
            if($value['type_form'] == 'select' || $value['type_form'] == 'select multiple' || $value['type_form'] == 'radio' || $value['type_form'] == 'checkbox')
            {
                $rule_add = true;
            }
            ?>
            <tr class="col-child-1 child-row-<?=$value['id_info_group']?>">
                <td></td>
                <td class="two-control <?=!empty($rule_add) ? '' : 'not-control'?>" data-loading-text="" id-data="<?=$value['id']?>">
                    <span class="treegrid-indent"></span>
                    <span class="treegrid-expander"></span>
                    <h5 style="display: inline-block;"><span class="label label-default" style="border: 1px solid <?=$value['color']?>;"><?=$value['name']?></span></h5>
                </td>
                <td></td>
                <td><?=strtoupper($value['type_form'])?></td>
                <td><?=!empty($value['is_required']) ? '<p class="mleft20"><b>X</b></p>' :''?></td>
                <td>
                    <?php
                        if(!empty($rule_add))
                        {
                            echo icon_btn('#', 'plus-circle', 'btn-info', [
                                    'onclick' => "addTwo(".$value['id_info_group'].",".$value['id']."); return false;",
                                    'data-toggle' => 'tooltip',
                                    'title' => _l('cong_add_value_from')
                            ]);
                        }
                    ?>
                    <?= icon_btn('#', 'pencil-square-o', 'btn-default', [
                            'onclick' => 'editOne('.$value['id'].'); return false;',
                            'data-toggle' => 'tooltip',
                            'title' => _l('cong_edit_from')
                    ]); ?>
                    <?= icon_btn('#', 'remove', 'btn-danger delete-remind', [
                            'onclick' => 'deleteOne('.$value['id'].'); return false;',
                            'data-toggle' => 'tooltip',
                            'title' => _l('cong_delete_from')
                    ]); ?>
                </td>
            </tr>
    <?php }
    }
?>
