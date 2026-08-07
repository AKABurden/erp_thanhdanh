<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<div class="row">
    <div class="col-md-12">
        <table class="tnh-table-settings">
            <tr>
                <td class="text-primary bg-primary bold"><?= lang('Thiết lập dashboard') ?></td>
            </tr>
            <tr>
                <td><?= lang('tnh_prefix_orders') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[prefix_orders]" id="settings[prefix_orders]" class="form-control" value="<?= get_option('prefix_orders') ?>" placeholder="<?= lang('tnh_prefix_orders') ?>">
                </td>
            </tr>
            <tr>
                <td><?= lang('tnh_default_staff_orders') ?></td>
            </tr>
        </table>
    </div>
</div>