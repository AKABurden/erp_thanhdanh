<!-- Gửi thông báo cho khách hàng qua EMAIL-->
<div id="infomation_<?=$unit?>" class="tab-pane border-padding fade <?=!empty($active) ? 'in active' : ''?>">
    <h3><?= _l('cong_send_email_staff_client')?></h3>
    <div class="row">
        <div class="col-md-3">
            <div class="radio radio-primary">
                <input type="radio" id="type_send" class="is_primary" name="type_send" checked value="1">
                <label for="type_send" data-toggle="tooltip" data-original-title="" title=""><?= _l('cong_client')?></label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="radio radio-primary">
                <input type="radio" id="type_send" class="is_primary" name="type_send" value="2">
                <label for="type_send" data-toggle="tooltip" data-original-title="" title=""><?= _l('cong_staff')?></label>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="col-md-12">
        <div class="form-group" app-field-wrapper="day">
            <label for="day" class="control-label"><?=_l('cong_day_of_month');?></label>
            <select id="receiveEmail[<?=$unit?>][]" name="receiveEmail[<?=$unit?>][]" class="selectpicker receiveEmail" multiple="true" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                <?php
                    foreach($client as $key => $value)
                    {
                        echo "<option value='".$value['userid']."'>".$value['company']."</option>";
                    }
                ?>
            </select>
        </div>

    </div>
    <div class="clearfix"></div>
</div>
