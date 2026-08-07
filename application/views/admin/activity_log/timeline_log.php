<?php
    $staff = $this->site_model->getStaff();
?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?= lang('activity_log_puchases') ?></h3>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label for="date_history"><?=_l('cong_automations_time')?></label>
            <div class="input-group" style="width: 100%;">
                <input type="text" name="date_history" id="date_history" class="form-control dateranger-custom" data-module=''>
                <div class="input-group-addon">
                    <i class="fa fa-calendar calendar-icon"></i>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="staff_history"><?=_l('utility_activity_log_dt_staff')?></label>
            <select name="staff_history" id="staff_history" data-none-selected-text="<?= lang('utility_activity_log_dt_staff') ?>" class="form-control selectpicker" data-live-search="true">
                <option value=""></option>
                <?php foreach ($staff as $key => $value): ?>
                    <option value="<?= $value['staffid'] ?>"><?= $value['firstname'] ?> <?= $value['lastname'] ?></option>
                <?php endforeach ?>
            </select>
            <input type="hidden" name="module-history" id="module_history" class="form-control module-history" value="<?= !empty($module) ? $module : '' ?>">
        </div>
        <hr />
        <div class="activity-container tnh-activity-log" style="max-height: 1000px;">
        </div>
        <div class="text-center">
            <a class="btn btn-info more-activity-log" onclick="loadMoreActivityLog()"><?=_l('load_more')?></a>
        </div>
    </div>
</div>
<script type="text/javascript">
    var moreHistory = 0;
    function getTNHActiviLog(activeOption)
    {
        date_history = $('#date_history').val();
        staff_history = $('#staff_history').val();
        module_history = $('#module_history').val();

        $.ajax({
            url: site.base_url+'admin/activity_log_puchases/getActivityLog',
            type: 'POST',
            dataType: 'html',
            data: {
                date_history: date_history,
                staff_history: staff_history,
                module_history: module_history,
                moreHistory: moreHistory,
                '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>'
            },
        })
        .done(function(data) {
            if (activeOption == 'html') {
                $('.tnh-activity-log').html(data);
                if (!data) {
                    $('.tnh-activity-log').html('<?= lang('not_data') ?>');
                }
                moreHistory = 0;
            } else if (activeOption == 'append') {
                if (data) {
                    $('.tnh-activity-log').append(data);
                } else {
                    $('.more-activity-log').hide();
                }
            }
        })
        .fail(function() {
            console.log("error");
        });
    }

    function loadMoreActivityLog()
    {
        moreHistory++;
        getTNHActiviLog('append')
    }

    $(document).ready(function() {
        getTNHActiviLog('html');
        $('#date_history, #staff_history').change(function(event) {
            getTNHActiviLog('html');
        });
    });
</script>