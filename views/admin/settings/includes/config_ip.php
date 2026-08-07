<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div role="tabpanel" class="tab-pane" id="config_ip">
    <h4 class="text-danger">
        <?php echo _l('setup_ip_to_login'); ?>
    </h4>
    <hr />
	<?php render_yes_no_option('phone_login_active','Sử dụng kiểm tra ip khi đăng nhập'); ?>
    <hr />
    <?php echo render_input('settings[phone_login_ip]', 'phone_login_ip', get_option('phone_login_ip')); ?>
    <hr/>
    <?php echo render_input('settings[day_login_ip]', 'day_login_ip', get_option('day_login_ip')); ?>
    <hr/>
    <table class="table-bordered table" id="table_ip">
        <thead>
        <tr>
            <th width="10%"><button type="button" class="btn btn-info btn-icon" onclick="add_ip()"><i class="fa fa-plus" aria-hidden="true"></i></button></th>
            <th>IP</th>
        </tr>
        </thead>
        <tbody>
        <?php $countIP = 0;?>
            <?php if(!empty($config_ip)) {?>
                <?php foreach($config_ip as $key => $value) {?>
                    <tr>
                        <td><a class="btn btn-danger btn-icon" onclick="removeIp(this)"><i class="fa fa-remove"></i></a></td>
                        <td>
                            <input type="text" name="config_ip[<?=$countIP?>]" value="<?=$value['ip']?>" class="form-control">
                        </td>
                    </tr>
                    <?php ++$countIP;?>
                <?php }?>
            <?php }?>
        </tbody>
    </table>
</div>
<script>
    var countIP = <?=!empty($countIP) ? $countIP : "0"?>;
    function add_ip() {
        var trIp = $('<tr></tr>');
        var td_delete = $('<td></td>');
        var td_ip = $('<td></td>');
        td_delete.append('<a class="btn btn-danger btn-icon"  onclick="removeIp(this)"><i class="fa fa-remove"></i></a>');
        td_ip.append('<input type="text" name="config_ip['+countIP+']" value="" class="form-control">');

        trIp.append(td_delete);
        trIp.append(td_ip);
        $('#table_ip tbody').append(trIp);
    }
    function removeIp(_this) {
        var tr = $(_this).parents('tr');
        tr.remove();
    }
</script>
