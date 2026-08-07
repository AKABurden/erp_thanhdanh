<?php if(!empty($info_group)){?>
    <?php $is_required_client = [];?>
    <?php $dem_temp = 4; //4 là số trường cố định + 1 ?>
    <?php foreach($info_group as $key => $value)
    {?>
            <div class="fieldset" role-fieldset="<?=$dem_temp?>">
                <div class="col-md-12">
                    <div class="align_right">
                        <a type="button" name="previous" class="previous action-button" <?=(!$check_custom_fields && $dem_temp == 4) ? 'data-stt="2"' : ''?>>Previous</a>
                        <?php if(count($info_group) > $key+1) { ?>
                            <a type="button" name="next" class="next action-button">Next</a>
                        <?php } ?>
                    </div>
                </div>
                <?php
                    if(!empty($value['detail']))
                    {
                        foreach($value['detail'] as $KeyDetail => $ValueDetail)
                        {
                            echo '<div class="col-md-6 col-xs-12">';
                            if(!empty($ValueDetail['is_required']))
                            {
                                $is_required_client[] =  'info_detail['.$ValueDetail['id'].']';
                            }
                            if($ValueDetail['type_form'] == 'input' || $ValueDetail['type_form'] == 'password')
                            {
                                $_valueData = !empty($ValueDetail['value']) ? $ValueDetail['value'] : '';
                                echo render_input('info_detail['.$ValueDetail['id'].']', $ValueDetail['name'], $_valueData, $ValueDetail['type_form']);
                            }
                            else if($ValueDetail['type_form'] == 'radio')
                            {
                                $_valueData = !empty($ValueDetail['value']) ? $ValueDetail['value'] : '';
                                echo '<label class="control-label">'.$ValueDetail['name'].'</label>';
                                echo '<div class="clearfix"></div>';
                                foreach($ValueDetail['detail'] as $kVal => $vVal)
                                {
                                    echo "<div class='col-md-6'>";
                                    echo '    <div class="radio">';
                                    echo '        <input type="radio" id="info_detail['.$ValueDetail['id'].']['.$kVal.']" name="info_detail['.$ValueDetail['id'].']" value="'.$vVal['id'].'" '.(($_valueData == $vVal['id']) ? "checked" : "").'>';
                                    echo '        <label for="info_detail['.$ValueDetail['id'].']['.$kVal.']">'.$vVal['name'].'</label>';
                                    echo '    </div>';
                                    echo "</div>";
                                }
                            }
                            else if($ValueDetail['type_form'] == 'checkbox')
                            {
                                $_valueData = !empty($ValueDetail['value']) ? explode(',', $ValueDetail['value']) : '';
                                echo '<label class="control-label">'.$ValueDetail['name'].'</label>';
                                echo '<div class="clearfix"></div>';
                                foreach($ValueDetail['detail'] as $kVal => $vVal)
                                {
                                    $checked = "";
                                    if(!empty($_valueData))
                                    {
                                        foreach($_valueData as $Kv => $Vv)
                                        {
                                            if($vVal['id'] == $Vv){
                                                $checked = "checked";
                                            }
                                        }
                                    }
                                    echo "<div class='col-md-6'>";
                                    echo '    <div class="checkbox">';
                                    echo '        <input type="checkbox" id="info_detail['.$ValueDetail['id'].']['.$kVal.']" name="info_detail['.$ValueDetail['id'].'][]" value="'.$vVal['id'].'" '.$checked.'>';
                                    echo '        <label for="info_detail['.$ValueDetail['id'].']['.$kVal.']">'.$vVal['name'].'</label>';
                                    echo '    </div>';
                                    echo "</div>";
                                }
                            }
                            else if($ValueDetail['type_form'] == 'select')
                            {
                                $_valueData = !empty($ValueDetail['value']) ? $ValueDetail['value'] : '';
                                echo render_select('info_detail['.$ValueDetail['id'].']', $ValueDetail['detail'], array('id', 'name'), $ValueDetail['name'], $_valueData);
                            }
                            else if($ValueDetail['type_form'] == 'select multiple')
                            {
                                $_valueData = !empty($ValueDetail['value']) ? explode(',', $ValueDetail['value']) : '';
                                echo render_select('info_detail['.$ValueDetail['id'].'][]', $ValueDetail['detail'], array('id', 'name'), $ValueDetail['name'], $_valueData, array('multiple' => true));
                            }
                            else if($ValueDetail['type_form'] == 'date')
                            {
                                $_valueData = !empty($ValueDetail['value']) ? _d($ValueDetail['value']) : '';
                                echo render_date_input('info_detail['.$ValueDetail['id'].'][date]', $ValueDetail['name'], '');
                            }
                            else if($ValueDetail['type_form'] == 'datetime')
                            {
                                $_valueData = !empty($ValueDetail['value']) ? _dt($ValueDetail['value']) : '';
                                echo render_datetime_input('info_detail['.$ValueDetail['id'].'][datetime]', $ValueDetail['name'], '');
                            }
                            echo '</div>';
                        }
                    }
                ?>
            </div>
    <?php $dem_temp++; ?>
    <?php } ?>
<?php } ?>
<script>
    var is_required_client = <?=!empty($is_required_client) ? json_encode($is_required_client) : "[]"?>;
</script>
