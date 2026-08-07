<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .border{
        border: 1px #bfcbd9 solid;
    }
    legend{
        width:auto;
        margin-left: 10px;
    }
</style>

<div class="row border">
    <fieldset>
        <legend><?=_l('fanpage_facebook')?></legend>
        <div class="col-md-6">
            <?php echo render_input('settings[VersionAppFB]','cong_VersionAppFB',get_option('VersionAppFB')); ?>
        </div>
        <div class="col-md-6">
            <?php echo render_input('settings[IdAppFB]','cong_IdAppFB',get_option('IdAppFB')); ?>
        </div>
    </fieldset>
</div>

<hr />

