<?php if(!empty($info_group)){?>
    <?php foreach($info_group as $key => $value) { ?>
            <div class="col-md-6 col-xs-12 lead-information-col mbot10">
                <div class="padding0">
                    <h4 class="no-margin font-medium-xs bold backgroundBlue padding10 colorFFF uppercase">
                        <?=$value['name']?>
                    </h4>
                </div>
                <?php
                    if(!empty($value['detail']))
                    {
                        foreach($value['detail'] as $KeyDetail => $ValueDetail)
                        {
                            if($KeyDetail % 2 == 0) {
                                $FoS = 'firt';
                            }
                            else {
                                $FoS = 'second';
                            }

                            if($ValueDetail['type_form'] == 'input' || $ValueDetail['type_form'] == 'password')
                            {
                                $_valueData = !empty($ValueDetail['value']) ? $ValueDetail['value'] : '';
                                echo '<div class="wap-content '.$FoS.'">
                                        <span class="text-muted lead-field-heading">'.$ValueDetail['name'].': </span>
                                        <span class="bold font-medium-xs">'.$_valueData.'</span>
                                    </div>';
                            }
                            else if($ValueDetail['type_form'] == 'radio')
                            {
                                $_valueData = !empty($ValueDetail['value']) ? $ValueDetail['value'] : '';
                                foreach($ValueDetail['detail'] as $kVal => $vVal)
                                {
                                    if($_valueData == $vVal['id']) {
                                        echo '<div class="wap-content '.$FoS.'">
                                                <span class="text-muted lead-field-heading">'.$ValueDetail['name'].': </span>
                                                <span class="bold font-medium-xs">'.$vVal['name'].'</span>
                                            </div>';
                                    }
                                }
                            }
                            else if($ValueDetail['type_form'] == 'checkbox')
                            {
                                $_valueData = !empty($ValueDetail['value']) ? explode(',', $ValueDetail['value']) : '';
                                foreach($ValueDetail['detail'] as $kVal => $vVal)
                                {
                                    $checked = "";
                                    if(!empty($_valueData))
                                    {
                                        foreach($_valueData as $Kv => $Vv)
                                        {
                                            if($vVal['id'] == $Vv){
                                                echo '<div class="wap-content '.$FoS.'">
                                                        <span class="text-muted lead-field-heading">'.$ValueDetail['name'].': </span>
                                                        <span class="bold font-medium-xs">'.$vVal['name'].'</span>
                                                    </div>';
                                            }
                                        }
                                    }
                                }
                            }
                            else if($ValueDetail['type_form'] == 'select')
                            {
                                $_valueData = !empty($ValueDetail['value']) ? $ValueDetail['value'] : '';
                                $dataSelect = get_table_where('tblclient_info_detail_value',array('id'=>$_valueData),'','row');
                                echo '<div class="wap-content '.$FoS.'">
                                        <span class="text-muted lead-field-heading">'.$ValueDetail['name'].': </span>
                                        <span class="bold font-medium-xs">'.(!empty($dataSelect->name) ? $dataSelect->name : '').'</span>
                                    </div>';
                            }
                            else if($ValueDetail['type_form'] == 'select multiple')
                            {
                                $_valueData = !empty($ValueDetail['value']) ? explode(',', $ValueDetail['value']) : '';
                                echo '<div class="wap-content '.$FoS.'">
                                        <span class="text-muted lead-field-heading">'.$ValueDetail['name'].': </span>';
                                if(!empty($_valueData))
                                {
                                    foreach ($_valueData as $key_valueData => $value_valueData) {
                                        $dataSelect = get_table_where('tblclient_info_detail_value',array('id'=>$value_valueData),'','row');
                                        echo '<span class="bold font-medium-xs">'.$dataSelect->name.', </span>';
                                    }
                                }
                                echo '</div>';
                            }
                            else if($ValueDetail['type_form'] == 'date')
                            {
                                $_valueData = !empty($ValueDetail['value']) ? _d($ValueDetail['value']) : '';
                                echo '<div class="wap-content '.$FoS.'">
                                        <span class="text-muted lead-field-heading">'.$ValueDetail['name'].': </span>
                                        <span class="bold font-medium-xs">'.$_valueData.', </span>
                                    </div>';
                            }
                            else if($ValueDetail['type_form'] == 'datetime')
                            {
                                $_valueData = !empty($ValueDetail['value']) ? _dt($ValueDetail['value']) : '';
                                echo '<div class="wap-content '.$FoS.'">
                                        <span class="text-muted lead-field-heading">'.$ValueDetail['name'].': </span>
                                        <span class="bold font-medium-xs">'.$_valueData.', </span>
                                    </div>';
                            }
                        }
                    }
                ?>
            </div>
    <?php } ?>
<?php } ?>
