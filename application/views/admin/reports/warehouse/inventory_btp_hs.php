<div id="inventory_btp_hs-report" class="hide">
<div class="row">
        <div class="col-md-3">
            <div class="form-group" style="width:100%;">
                <label for="semi_products_id_hs"><?php echo _l('Bán thành phẩm'); ?></label>
                <input style="width:100%;" data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" name="semi_products_id_hs" class="semi_products_id_hs" id="semi_products_id_hs" name="semi_products_id_hs" style="width: 100%">
            </div>
        </div>
    </div>
    <br>
    <table class="table table table-striped table-inventory_btp_hs-report">
        <thead>
            <tr>
                <th class="text-center"><?= ucwords(_l('STT')); ?></th>
                <th class="text-center"><?= ucwords(_l('Mã TP')); ?></th>
                <th class="text-center"><?= ucwords(_l('Tên TP')); ?></th>
                <th class="text-center"><?= ucwords(_l('Vị trí')); ?></th>
                <th class="text-center"><?= ucwords(_l('Số lượng tồn')); ?></th>
                <th class="text-center"><?= ucwords(_l('Đơn vị tính')); ?></th>
                <th class="text-center"><?= ucwords(_l('Thành tiền')); ?></th>
                <th class="text-center"><?= ucwords(_l('LOT nhập')); ?></th>
                <th class="text-center"><?= ucwords(_l('Thời gian lưu kho')); ?></th>
                <th class="text-center"><?= ucwords(_l('Ngày lưu kho')); ?></th>
                <th class="text-center"><?= ucwords(_l('Hạn sử dụng')); ?></th>
                <th class="text-center"><?= ucwords(_l('Chủng loại')); ?></th>

            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>