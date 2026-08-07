<?php
    if(!empty($client_info_detail_value))
    {
        foreach($client_info_detail_value as $key => $value)
        {?>
            <tr class="col-child-2 child-row-two-<?=$value['id_info_detail']?>">
                <td></td>
                <td>
                    <span class="treegrid-indent"></span>
                    <span class="treegrid-expander"></span>
                    <span class="treegrid-indent"></span>
                    <span class="treegrid-expander"></span>
                    <h5 style="display: inline-block;"><?=$value['name']?></h5>
                </td>
                <td></td>
                <td><?=_l('cong_value')?></td>

                <td></td>
                <td>
                    <?= icon_btn('#', 'pencil-square-o', 'btn-default', [
                            'onclick' => 'editTwo('.$value['id'].'); return false;',
                            'data-toggle' => 'tooltip',
                            'title' => _l('cong_edit_from_value')
                    ]); ?>
                    <?= icon_btn('#', 'remove', 'btn-danger delete-remind', [
                            'onclick' => 'deleteTwo('.$value['id'].'); return false;',
                            'data-toggle' => 'tooltip',
                            'title' => _l('cong_delete_from_value')
                    ]); ?>
                </td>
            </tr>
    <?php }
    }
?>
