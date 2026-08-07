<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .wap-container {
        border: 1px solid #d8d8d8;
        margin-bottom: 10px;
    }
    .wap-header {
        background: linear-gradient(to right, #226faa 0%, #2989d8 37%, #72c0d3 100%);
        color: #fff;
        text-align: center;
        padding: 10px 10px;
    }
    .wap-left {
        float: left;
        width: 50px;
    }
    .wap-right {
        float: left;
        width: calc(100% - 50px);
    }
    .wap-body {
        height: 250px;
        overflow: auto;
        padding: 10px;
    }
    .wap-title-staff {
        color: #a5a5a5;
    }
    .wap-body hr:last-child {
        display: none;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php foreach ($dataLog as $key => $value) { ?>
                    <div class="col-md-4">
                        <div class="wap-container">
                            <div class="wap-header">
                                <?=$value['name_obj']?>
                            </div>
                            <div class="wap-body">
                            <?php $group = get_table_where('tblactivity_log_v2',array('table_obj'=>$value['table_obj'],'id_obj'=>$value['id_obj'])); ?>
                            <?php foreach ($group as $key_group => $value_group) { ?>
                                <div>
                                    <div class="wap-left">
                                        <?= staff_profile_image($value_group['staff_id'], array('staff-profile-image-small'), 'small', array());?>
                                    </div>
                                    <div class="wap-right">
                                        <div class="wap-title-staff">
                                            <?= get_staff_full_name($value_group['staff_id']); ?> - <?= time_ago($value_group['date']) ?>
                                        </div>
                                        <div><?=$value_group['content']?></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <hr \>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
</script>
</body>
</html>
