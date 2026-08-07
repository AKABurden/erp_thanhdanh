<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<div class="row">
    <div class="col-md-12">
        <table class="tnh-table-settings">
            <tr>
                <td class="text-primary bg-primary bold"><?= lang('Thuế') ?></td>
            </tr>
            <tr>
                <td><?= lang('Thuế đầu vào') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[vat_dauvao]" id="settings[vat_dauvao]" class="money-format form-control" value="<?= formatMoney(get_option('vat_dauvao')) ?>" placeholder="<?= lang('Thuế đầu vào') ?>">
                </td>
            </tr>
        </table>
    </div>
</div>