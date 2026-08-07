
<!-- ĐIỀU KIỆN THỰC HIỆN CỦA HÀNH ĐỘNG ĐỐI TƯỢNG-->
<div class="well mtop20 condition_<?=$Cinit?>">

    <div class="col-md-12 text-right delete_wall"><a class="text-danger delete_well pointer">X</a></div>
    <div class="col-md-6">
        <select class="selectpicker" name="proviso[<?=$Cinit?>][where_colum]" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
            <option class="hide"></option>
            <?php
                $colums_where = GetAutomationWhereColum(1);
                if(!empty($colums_where))
                {
                    foreach($colums_where as $key => $value)
                    {
                        $selected = "";
                        if(!empty($where_colum) && $where_colum == $value['id'])
                        {
                            $selected = "selected";
                        }
                        echo "<option value='".$value['id']."' ".$selected.">".$value['name']."</option>";
                    }
                }
            ?>
        </select>
    </div>
    <div class="col-md-6">
        <select class="selectpicker" name="proviso[<?=$Cinit?>][then_colum]" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
            <option value=""></option>
            <?php
                if(!empty($then_colum))
                {
                    if($where_colum == 'datecreated')
                    {
                        echo '<option value="<" '.($then_colum == "<" ? "selected" : "").'>  '._l('lesser').' </option>';
                        echo '<option value=">" '.($then_colum == ">" ? "selected" : "").'>  '._l('bigger').' </option>';
                        echo '<option value="<=" '.($then_colum == "<=" ? "selected" : "").'>  '._l('less_than_or_equal').' </option>';
                        echo '<option value=">=" '.($then_colum == ">=" ? "selected" : "").'>  '._l('greater_than_or_equal_to').' </option>';
                        echo '<option value="=" '.($then_colum == "=" ? "selected" : "").'>  '._l('equal').' </option>';
                    }
                    else if($where_colum == 'city' || $where_colum == 'type_client')
                    {
                        echo '<option value="IN" '.($then_colum = "IN" ? "selected" : "").'>  IN </option>';
                        echo '<option value="NOT IN" '.($then_colum = "NOT IN" ? "selected" : "").'> NOT IN </option>';
                    }
                }
            ?>
        </select>
    </div>
    <div class="condition col-md-12 mtop10" id="condition<?=$Cinit?>">
        <?php
        if (!empty($then_colum)) {
            if ($where_colum == 'datecreated') {
                echo render_input('proviso['.$Cinit.'][then_data]', 'cong_num_day', $then_data);
            }
            else if($where_colum == 'city')
            {
                echo render_select('proviso['.$Cinit.'][then_data][]', $province, array('provinceid', 'name'), 'cong_city', explode(',',$then_data), array('multiple' => 'true', 'data-actions-box'=>true), [], '', '', false);
            }
            else if($where_colum == 'type_client')
            {
                echo render_select('proviso['.$Cinit.'][then_data][]', $group_customer, array('provinceid', 'name'), 'customer_group', explode(',',$then_data), array('multiple' => 'true', 'data-actions-box'=>true), [], '', '', false);
            }
        } ?>
    </div>
    <div class="clearfix"></div>
</div>

<script>
    $(function(){
        $('select[name="proviso[<?= $Cinit ?>][where_colum]"]').selectpicker('refresh');
        $('select[name="proviso[<?= $Cinit ?>][then_colum]"]').selectpicker('refresh');
    });
    $('body').on('change','select[name="proviso[<?=$Cinit?>][where_colum]"]', function(e){
        var val_proviso = $(this).val();
        if(val_proviso != "")
        {

            var selectThen = $('select[name="proviso[<?=$Cinit?>][then_colum]"]');
            if(val_proviso == 'datecreated')
            {
                selectThen.html('');
                selectThen.append('<option value="<">  <?=_l('lesser')?> </option>');
                selectThen.append('<option value=">">  <?=_l('bigger')?> </option>');
                selectThen.append('<option value="<="> <?=_l('less_than_or_equal')?> </option>');
                selectThen.append('<option value=">="> <?=_l('greater_than_or_equal_to')?> </option>');
                selectThen.append('<option value="=">  <?=_l('equal')?> </option>');
                selectThen.selectpicker('refresh');

                $('#condition<?=$Cinit?>').html('<?php echo render_input('proviso['.$Cinit.'][then_data]', 'cong_num_day'); ?>');
            }
            else if(val_proviso == 'city')
            {
                selectThen.html('<option value="IN"> IN </option>');
                selectThen.append('<option value="NOT IN"> NOT IN </option>');
                selectThen.selectpicker('refresh');
                $('#condition<?=$Cinit?>').html('<?php echo render_select('proviso['.$Cinit.'][then_data][]', $province, array('provinceid', 'name'), 'cong_city', '', array('multiple' => 'true', 'data-actions-box'=>true), [], '', '', false); ?>');
                $('select[name="proviso[<?= $Cinit ?>][then_data][]"]').selectpicker('refresh');
            }
            else if(val_proviso == 'type_client')
            {
                selectThen.html('<option value="IN"> IN </option>');
                selectThen.append('<option value="NOT IN"> NOT IN </option>');
                selectThen.selectpicker('refresh');

                $('#condition<?=$Cinit?>').html('<?php echo render_select('proviso['.$Cinit.'][then_data][]', $group_customer, array('provinceid', 'name'), 'customer_group', '', array('multiple' => 'true', 'data-actions-box'=>true), [], '', '', false); ?>');
                $('select[name="proviso[<?= $Cinit ?>][then_data][]"]').selectpicker('refresh');
            }
        }
    })
</script>