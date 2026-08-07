<?php if(!empty($info_group) && !empty($lead)){?>
    <?php $is_required_client = [];?>
    <?php foreach($info_group as $key => $value){?>
                  <div class="lead-info-heading padding0 mar-pad-lef15">
                      <h4 class="no-margin font-medium-xs bold backgroundBlue padding10 colorFFF uppercase">
                          <?=$value['name']?>
                      </h4>
                  </div>
                <?php
                    if(!empty($value['detail']))
                    {
                        foreach($value['detail'] as $KeyDetail => $ValueDetail)
                        {
                            $firt = 'firt';
                            if($KeyDetail%2 == 0)
                            {
                            $firt = 'second';    
                            }
                            if($ValueDetail['type_form'] == 'input' || $ValueDetail['type_form'] == 'password')
                            {
                                echo "<div class='wap-content ".$firt."'><span class='text-muted lead-field-heading no-mtop'>".$ValueDetail['name'].": </span>";
                                echo "<span class='bold font-medium-xs'>".(!empty($ValueDetail['value']) ? $ValueDetail['value'] : '')."</span></div>";
                            }
                            else if($ValueDetail['type_form'] == 'checkbox' )
                            {
                                echo "<span class='text-muted lead-field-heading no-mtop'>".$ValueDetail['name'].": </span>";
                                $_explodevalueData = !empty($ValueDetail['value']) ? explode(',', $ValueDetail['value']) : '';
                                $_valueData = "";
                                foreach($ValueDetail['detail'] as $kVal => $vVal)
                                {
                                    $checked = "";
                                    if(!empty($_explodevalueData))
                                    {
                                        foreach($_explodevalueData as $Kv => $Vv)
                                        {
                                            if($vVal['id'] == $Vv){
                                                $_valueData .=$vVal['name'];
                                            }
                                        }
                                    }
                                }
                            }
                            else if($ValueDetail['type_form'] == 'select' || $ValueDetail['type_form'] == 'radio' )
                            {
                                $_valueData = '-';
                                echo "<div class='wap-content ".$firt."'><span class='text-muted lead-field-heading no-mtop'>".$ValueDetail['name'].": </span>";
                                foreach($ValueDetail['detail'] as $kVal => $vVal)
                                {
                                    if($vVal['id'] == $ValueDetail['value'])
                                    {
                                        $_valueData = $vVal['name'];
                                        break;
                                    }
                                }
                                echo "<span class='bold font-medium-xs'>".$_valueData."</span></div>";
                            }
                            else if($ValueDetail['type_form'] == 'select multiple')
                            {
                                echo "<div class='wap-content ".$firt."'><span class='text-muted lead-field-heading no-mtop'>".$ValueDetail['name'].": </span>";
                                $_explodevalueData = !empty($ValueDetail['value']) ? explode(',', $ValueDetail['value']) : '';
                                $_valueData = "";
                                foreach($ValueDetail['detail'] as $kVal => $vVal)
                                {
                                    $checked = "";
                                    if(!empty($_explodevalueData))
                                    {
                                        foreach($_explodevalueData as $Kv => $Vv)
                                        {
                                            if($vVal['id'] == $Vv){
                                                $_valueData .=$vVal['name'].', ';
                                            }
                                        }
                                    }
                                }
                                echo "<span class='bold font-medium-xs'>".trim($_valueData, ', ')."</span></div>";
                            }
                            else if($ValueDetail['type_form'] == 'date')
                            {
                                echo "<div class='wap-content ".$firt."'><span class='text-muted lead-field-heading no-mtop'>".$ValueDetail['name'].": </span>";
                                $_valueData = !empty($ValueDetail['value']) ? $ValueDetail['value'] : '-';
                                echo "<span class='bold font-medium-xs'>"._d($_valueData)."</span></div>";
                            }
                            else if($ValueDetail['type_form'] == 'datetime')
                            {
                                echo "<div class='wap-content ".$firt."'><span class='text-muted lead-field-heading no-mtop'>".$ValueDetail['name'].": </span>";
                                $_valueData = !empty($ValueDetail['value']) ? $ValueDetail['value'] : '-';
                                echo "<span class='bold font-medium-xs'>"._dt($_valueData)."</span></div>";
                            }
                        }
                    }
                ?>
    <?php } ?>
<?php } ?>
<script>
    var is_required_client = <?=!empty($is_required_client) ? json_encode($is_required_client) : "[]"?>;
</script>
