<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<div class="row">
    <div class="col-md-12">
        <table class="tnh-table-settings">
            <tr>
                <td class="text-primary bg-primary bold"><?= lang('tnh_salary') ?></td>
            </tr>
            <tr>
                <td><?= lang('Lương tối thiểu') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[salary_minimum_new]" id="salary_minimum_new"
                           class="form-control salary_minimum_new money-format"
                           value="<?= !empty(get_option('salary_minimum_new')) ? formatMoney(get_option('salary_minimum_new')) : '' ?>"
                           title="">
                </td>
            </tr>
            <tr>
                <td><?= lang('Mức tiền trừ thuế') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[money_vat]" id="money_vat"
                           class="form-control money_vat money-format"
                           value="<?= !empty(get_option('money_vat')) ? formatMoney(get_option('money_vat')) : '' ?>"
                           title="">
                </td>
            </tr>
            <tr>
                <td><?= lang('Mức tiền số người giảm trừ thuế') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[money_reduce]" id="money_vat"
                           class="form-control money_reduce money-format"
                           value="<?= !empty(get_option('money_reduce')) ? formatMoney(get_option('money_reduce')) : '' ?>"
                           title="">
                </td>
            </tr>
            <tr>
                <td><?= lang('Tiền cơm') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[rice_money]" id="rice_money"
                           class="form-control rice_money money-format"
                           value="<?= formatMoney(get_option('rice_money')) ?>" title="">
                </td>
            </tr>
            <tr>
            <tr>
                <td><?= lang('Tiền cơm audit') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[rice_money_audit]" id="rice_money_audit"
                           class="form-control rice_money_audit money-format"
                           value="<?= formatMoney(get_option('rice_money_audit')) ?>" title="">
                </td>
            </tr>
            <tr>
                <td><?= lang('Hệ số tăng ca bình thường') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[coefficient]" id="coefficient"
                           class="form-control coefficient money-format" value="<?= (get_option('coefficient')) ?>"
                           title="">
                </td>
            </tr>
            <tr>
                <td><?= lang('Hệ số tăng ca chủ nhật') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[coefficient_sunday]" id="coefficient_sunday"
                           class="form-control coefficient_sunday money-format"
                           value="<?= (get_option('coefficient_sunday')) ?>" title="">
                </td>
            </tr>
            <tr>
                <td><?= lang('Hệ số tăng ca lễ') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[coefficient_holiday]" id="coefficient_holiday"
                           class="form-control coefficient_holiday money-format"
                           value="<?= (get_option('coefficient_holiday')) ?>" title="">
                </td>
            </tr>
            <tr>
                <td><?= lang('Ngày tự động tạo tạm ứng lương') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[date_auto_advance]" id="date_auto_advance"
                           class="form-control date_auto_advance number-format"
                           value="<?= (get_option('date_auto_advance')) ?>" title="">
                </td>
            </tr>
        </table>
        <div class=""><a style="color: white !important;"
                         href="<?= base_url('admin/allowance_reduce/add_allowance_reduce') ?>"
                         class="tnh-modal btn btn-primary" data-tnh="modal" data-toggle="modal" data-target="#myModal">
                <?php echo _l('Thêm tiêu chí'); ?>
            </a></div>
        <div class="row" style="display: flex;align-items: center">
            <div class="col-md-5">Phụ cấp</div>
            <div class="col-md-5">
                <button type="button" class="btn btn-primary" onclick="apply_allowance_staff(this)">Áp dụng vào nhân
                    viên
                </button>
            </div>
        </div>
        <table class="table-bordered table" id="table_phucap">
            <thead>
            <tr>
                <th style="width: 50px;text-align: center">
                    <button type="button" class="btn btn-info btn-icon" onclick="add_phucap()"><i class="fa fa-plus"
                                                                                                  aria-hidden="true"></i>
                    </button>
                </th>
                <th>Tiêu chí</th>
                <th>Số tiền</th>
            </tr>
            </thead>
            <tbody>
            <?php $countPhuCap = 0; ?>
            <?php if (!empty($salary_allowance)) { ?>
                <?php foreach ($salary_allowance as $key => $value) { ?>
                    <tr>
                        <td><a class="btn btn-danger btn-icon" onclick="removePC(this)"><i class="fa fa-remove"></i></a>
                        </td>
                        <td>
                            <input type="text" data-placeholder="Tiêu chí" style="width: 100%"
                                   name="title[<?= $countPhuCap ?>]" id="title_<?= $countPhuCap ?>"
                                   value="<?= $value['category_id'] ?>"
                                   class="title">
                            <input type="hidden" name="countPhuCap[]" value="<?= $countPhuCap ?>" class="form-control">
                            <input type="hidden" name="id_pc[<?= $countPhuCap ?>]" value="<?= $value['id'] ?>"
                                   class="form-control">
                        </td>
                        <td>
                            <input type="text" name="amount[<?= $countPhuCap ?>]"
                                   value="<?= formatMoney($value['amount']) ?>" class="form-control number-format">
                        </td>
                    </tr>
                    <?php $countPhuCap++; ?>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
        <div class="row" style="display: flex;align-items: center">
            <div class="col-md-5">Khoản giảm trừ</div>
            <div class="col-md-5">
                <button type="button" class="btn btn-primary" onclick="apply_reduce_staff(this)">Áp dụng vào nhân viên
                </button>
            </div>
        </div>
        <table class="table-bordered table" id="table_giamtru">
            <thead>
            <tr>
                <th style="width: 50px;text-align: center">
                    <button type="button" class="btn btn-info btn-icon" onclick="add_giamtru()"><i class="fa fa-plus"
                                                                                                   aria-hidden="true"></i>
                    </button>
                </th>
                <th>Tiêu chí</th>
                <th>Số tiền</th>
            </tr>
            </thead>
            <tbody>
            <?php $countGiamTru = 0; ?>
            <?php if (!empty($salary_reduce)) { ?>
                <?php foreach ($salary_reduce as $key => $value) { ?>
                    <tr>
                        <td><a class="btn btn-danger btn-icon" onclick="removeGT(this)"><i class="fa fa-remove"></i></a>
                        </td>
                        <td>
                            <input type="text" data-placeholder="Tiêu chí" style="width: 100%"
                                   name="title_gt[<?= $countGiamTru ?>]" id="title1_<?= $countGiamTru ?>"
                                   value="<?= $value['category_id'] ?>"
                                   class="title_gt">
                            <input type="hidden" name="countGiamTru[]" value="<?= $countGiamTru ?>"
                                   class="form-control">
                            <input type="hidden" name="id_gt[<?= $countGiamTru ?>]" value="<?= $value['id'] ?>"
                                   class="form-control">
                        </td>
                        <td>
                            <input type="text" name="amount_gt[<?= $countGiamTru ?>]"
                                   value="<?= formatMoney($value['amount']) ?>" class="form-control number-format">
                        </td>
                    </tr>
                    <?php $countGiamTru++; ?>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('Chức vụ') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
                <td><?= lang('Định mức') ?></td>
                <td><?= lang('Công thức') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[role_criteria]" id="role_criteria"
                           class="form-control role_criteria"
                           value="<?= !empty(get_option('role_criteria')) ? (get_option('role_criteria')) : '' ?>"
                           title="">
                </td>
                <td>
                    <input type="text" name="settings[role_quota]" id="role_quota"
                           class="form-control role_quota"
                           value="<?= !empty(get_option('role_quota')) ? (get_option('role_quota')) : '' ?>"
                           title="">
                </td>
                <td>
                    <input type="text" name="settings[role_recipe]" id="role_recipe"
                           class="form-control role_recipe"
                           value="<?= !empty(get_option('role_recipe')) ? (get_option('role_recipe')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('Trách nhiệm') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
                <td><?= lang('Định mức') ?></td>
                <td><?= lang('Công thức') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[responsibility_criteria]" id="responsibility_criteria"
                           class="form-control responsibility_criteria"
                           value="<?= !empty(get_option('responsibility_criteria')) ? (get_option('responsibility_criteria')) : '' ?>"
                           title="">
                </td>
                <td>
                    <input type="text" name="settings[responsibility_quota]" id="responsibility_quota"
                           class="form-control responsibility_quota"
                           value="<?= !empty(get_option('responsibility_quota')) ? (get_option('responsibility_quota')) : '' ?>"
                           title="">
                </td>
                <td>
                    <input type="text" name="settings[responsibility_recipe]" id="responsibility_recipe"
                           class="form-control responsibility_recipe"
                           value="<?= !empty(get_option('responsibility_recipe')) ? (get_option('responsibility_recipe')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('Lễ tết') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[le_tet]" id="le_tet"
                           class="form-control le_tet"
                           value="<?= !empty(get_option('le_tet')) ? (get_option('le_tet')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="4" class="text-primary bg-primary bold"><?= lang('Nuôi con nhỏ') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
                <td><?= lang('Định mức') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[raising_children_criteria]" id="raising_children_criteria"
                           class="form-control raising_children_criteria"
                           value="<?= !empty(get_option('raising_children_criteria')) ? (get_option('raising_children_criteria')) : '' ?>"
                           title="">
                </td>
                <td>
                    <input type="text" name="settings[raising_children_quota]" id="raising_children_quota"
                           class="form-control raising_children_quota"
                           value="<?= !empty(get_option('raising_children_quota')) ? (get_option('raising_children_quota')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('Thưởng lễ') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[bonus_holiday]" id="bonus_holiday"
                           class="form-control bonus_holiday"
                           value="<?= !empty(get_option('bonus_holiday')) ? (get_option('bonus_holiday')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('Tăng ca thường 1.5') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
                <td><?= lang('công thức') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[overtime_thuong_criteria]" id="overtime_thuong_criteria"
                           class="form-control overtime_thuong_criteria"
                           value="<?= !empty(get_option('overtime_thuong_criteria')) ? (get_option('overtime_thuong_criteria')) : '' ?>"
                           title="">
                </td>
                <td>
                    <input type="text" name="settings[overtime_thuong_recipe]" id="overtime_thuong_recipe"
                           class="form-control overtime_thuong_recipe"
                           value="<?= !empty(get_option('overtime_thuong_recipe')) ? (get_option('overtime_thuong_recipe')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('Tăng ca chủ nhật 2.0') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
                <td><?= lang('công thức') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[overtime_cn_criteria]" id="overtime_cn_criteria"
                           class="form-control overtime_cn_criteria"
                           value="<?= !empty(get_option('overtime_cn_criteria')) ? (get_option('overtime_cn_criteria')) : '' ?>"
                           title="">
                </td>
                <td>
                    <input type="text" name="settings[overtime_cn_recipe]" id="overtime_cn_recipe"
                           class="form-control overtime_cn_recipe"
                           value="<?= !empty(get_option('overtime_cn_recipe')) ? (get_option('overtime_cn_recipe')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('Tăng ca lễ') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
                <td><?= lang('công thức') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[overtime_holiday_criteria]" id="overtime_holiday_criteria"
                           class="form-control overtime_holiday_criteria"
                           value="<?= !empty(get_option('overtime_holiday_criteria')) ? (get_option('overtime_holiday_criteria')) : '' ?>"
                           title="">
                </td>
                <td>
                    <input type="text" name="settings[overtime_holiday_recipe]" id="overtime_holiday_recipe"
                           class="form-control overtime_holiday_recipe"
                           value="<?= !empty(get_option('overtime_holiday_recipe')) ? (get_option('overtime_holiday_recipe')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('Tăng ca đêm thường') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
                <td><?= lang('công thức') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[overtime_thuong_night_criteria]" id="overtime_thuong_night_criteria"
                           class="form-control overtime_thuong_night_criteria"
                           value="<?= !empty(get_option('overtime_thuong_night_criteria')) ? (get_option('overtime_thuong_night_criteria')) : '' ?>"
                           title="">
                </td>
                <td>
                    <input type="text" name="settings[overtime_thuong_night_recipe]" id="overtime_thuong_night_recipe"
                           class="form-control overtime_thuong_night_recipe"
                           value="<?= !empty(get_option('overtime_thuong_night_recipe')) ? (get_option('overtime_thuong_night_recipe')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('Tăng ca đêm chủ nhật') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
                <td><?= lang('công thức') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[overtime_holiday_night_criteria]" id="overtime_holiday_night_criteria"
                           class="form-control overtime_holiday_night_criteria"
                           value="<?= !empty(get_option('overtime_holiday_night_criteria')) ? (get_option('overtime_holiday_night_criteria')) : '' ?>"
                           title="">
                </td>
                <td>
                    <input type="text" name="settings[overtime_holiday_night_recipe]" id="overtime_holiday_night_recipe"
                           class="form-control overtime_holiday_night_recipe"
                           value="<?= !empty(get_option('overtime_holiday_night_recipe')) ? (get_option('overtime_holiday_night_recipe')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('8% BHXH') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
                <td><?= lang('công thức') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[bhxh_criteria]" id="bhxh_criteria"
                           class="form-control bhxh_criteria"
                           value="<?= !empty(get_option('bhxh_criteria')) ? (get_option('bhxh_criteria')) : '' ?>"
                           title="">
                </td>
                <td>
                    <input type="text" name="settings[bhxh_recipe]" id="bhxh_recipe"
                           class="form-control bhxh_recipe"
                           value="<?= !empty(get_option('bhxh_recipe')) ? (get_option('bhxh_recipe')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('1,5% BHYT') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
                <td><?= lang('công thức') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[bhyt_criteria]" id="bhyt_criteria"
                           class="form-control bhyt_criteria"
                           value="<?= !empty(get_option('bhyt_criteria')) ? (get_option('bhyt_criteria')) : '' ?>"
                           title="">
                </td>
                <td>
                    <input type="text" name="settings[bhyt_recipe]" id="bhyt_recipe"
                           class="form-control bhyt_recipe"
                           value="<?= !empty(get_option('bhyt_recipe')) ? (get_option('bhyt_recipe')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('1% BHTN') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
                <td><?= lang('công thức') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[bhtn_criteria]" id="bhtn_criteria"
                           class="form-control bhtn_criteria"
                           value="<?= !empty(get_option('bhtn_criteria')) ? (get_option('bhtn_criteria')) : '' ?>"
                           title="">
                </td>
                <td>
                    <input type="text" name="settings[bhtn_recipe]" id="bhtn_recipe"
                           class="form-control bhtn_recipe"
                           value="<?= !empty(get_option('bhtn_recipe')) ? (get_option('bhtn_recipe')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('1% Đoàn phí') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
                <td><?= lang('công thức') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[union_criteria]" id="union_criteria"
                           class="form-control union_criteria"
                           value="<?= !empty(get_option('union_criteria')) ? (get_option('union_criteria')) : '' ?>"
                           title="">
                </td>
                <td>
                    <input type="text" name="settings[union_recipe]" id="union_recipe"
                           class="form-control union_recipe"
                           value="<?= !empty(get_option('union_recipe')) ? (get_option('union_recipe')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('Số giờ công ') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[hour_criteria]" id="hour_criteria"
                           class="form-control hour_criteria"
                           value="<?= !empty(get_option('hour_criteria')) ? (get_option('hour_criteria')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('Số ngày công ') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[day_criteria]" id="day_criteria"
                           class="form-control day_criteria"
                           value="<?= !empty(get_option('day_criteria')) ? (get_option('day_criteria')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('Phép năm') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
                <td><?= lang('Định mức') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[paid_holiday_criteria]" id="paid_holiday_criteria"
                           class="form-control paid_holiday_criteria"
                           value="<?= !empty(get_option('paid_holiday_criteria')) ? (get_option('paid_holiday_criteria')) : '' ?>"
                           title="">
                </td>
                <td>
                    <input type="text" name="settings[paid_holiday_quota]" id="paid_holiday_quota"
                           class="form-control paid_holiday_quota"
                           value="<?= !empty(get_option('paid_holiday_quota')) ? (get_option('paid_holiday_quota')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('Thâm niên') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
                <td><?= lang('Định mức') ?></td>
                <td><?= lang('Công thức') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[seniority_criteria]" id="seniority_criteria"
                           class="form-control seniority_criteria"
                           value="<?= !empty(get_option('seniority_criteria')) ? (get_option('seniority_criteria')) : '' ?>"
                           title="">
                </td>
                <td>
                    <input type="text" name="settings[seniority_quota]" id="seniority_quota"
                           class="form-control seniority_quota"
                           value="<?= !empty(get_option('seniority_quota')) ? (get_option('seniority_quota')) : '' ?>"
                           title="">
                </td>
                <td>
                    <input type="text" name="settings[seniority_recipe]" id="seniority_recipe"
                           class="form-control seniority_recipe"
                           value="<?= !empty(get_option('seniority_recipe')) ? (get_option('seniority_recipe')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('Ban Iso/FSC') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[fsc_criteria]" id="fsc_criteria"
                           class="form-control fsc_criteria"
                           value="<?= !empty(get_option('fsc_criteria')) ? (get_option('fsc_criteria')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('Chuyên cần') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[diligence_criteria]" id="diligence_criteria"
                           class="form-control diligence_criteria"
                           value="<?= !empty(get_option('diligence_criteria')) ? (get_option('diligence_criteria')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('PCCC') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[pccc_criteria]" id="pccc_criteria"
                           class="form-control pccc_criteria"
                           value="<?= !empty(get_option('pccc_criteria')) ? (get_option('pccc_criteria')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

        <table class="tnh-table-settings">
            <tr>
                <td colspan="6" class="text-primary bg-primary bold"><?= lang('Độc hại') ?></td>
            </tr>
            <tr>
                <td><?= lang('Tiêu chí') ?></td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="settings[toxic_criteria]" id="toxic_criteria"
                           class="form-control toxic_criteria"
                           value="<?= !empty(get_option('toxic_criteria')) ? (get_option('toxic_criteria')) : '' ?>"
                           title="">
                </td>
            </tr>
        </table>

    </div>
</div>
<script type="text/javascript">
    var arrIdPc = [];
    var arrIdGt = [];
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    $("#date_auto_advance").change(function () {
        value = $(this).val();
        if (value < 1 || value > 28) {
            alert_float('danger', 'Vui lòng chọn ngày hợp lệ !');
            $(this).val('');
        }
    })

    function totalPc() {
        tb = '#table_phucap tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        arrIdPc = [];
        count_errors = 0;
        for (ii = 0; ii < n; ii++) {
            element = $(tb)[ii];
            category_id = $(element).find('input.title').val();
            if (category_id) {
                index = jQuery.inArray(category_id, arrIdPc);
                if (index !== -1) {
                } else {
                    arrIdPc.push(category_id);
                }
            }
        }
    }

    function totalGt() {
        tb = '#table_giamtru tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        arrIdGt = [];
        count_errors = 0;
        for (ii = 0; ii < n; ii++) {
            element = $(tb)[ii];
            category_id = $(element).find('input.title_gt').val();
            if (category_id) {
                index = jQuery.inArray(category_id, arrIdGt);
                if (index !== -1) {
                } else {
                    arrIdGt.push(category_id);
                }
            }
        }
    }

    totalPc();
    totalGt();

    var countPhuCap = <?=!empty($countPhuCap) ? $countPhuCap : "0"?>;
    var countGiamTru = <?=!empty($countGiamTru) ? $countGiamTru : "0"?>;
    $(document).ready(function () {
        for (i = 0; i < countPhuCap; i++) {
            ajaxSelectCallBack($('#title_' + i + ''), 'admin/allowance_reduce/searchAllowanceReduce', $('#title_' + i + '').val(), 1);
        }
        for (i = 0; i < countGiamTru; i++) {
            ajaxSelectCallBack($('#title1_' + i + ''), 'admin/allowance_reduce/searchAllowanceReduce', $('#title1_' + i + '').val(), 2);
        }
        $(document).on('change', '.title', function (
            event) {
            event.preventDefault();
            category_id = $(this).val();
            var tr = $(this).parents('tr');

            if (category_id) {
                if (jQuery.inArray(category_id, arrIdPc) !== -1) {
                    alert_float('danger', 'Tiêu chí này đã tồn tại');
                    totalPc();
                    tr.remove();
                    add_phucap();
                    return;
                }
            }
            add_phucap();
        });

        $(document).on('change', '.title_gt', function (
            event) {
            event.preventDefault();
            category_id = $(this).val();
            var tr = $(this).parents('tr');

            if (category_id) {
                if (jQuery.inArray(category_id, arrIdGt) !== -1) {
                    alert_float('danger', 'Tiêu chí này đã tồn tại');
                    totalGt();
                    tr.remove();
                    add_giamtru();
                    return;
                }
            }
            add_giamtru();
        });
    });


    function add_phucap() {
        var trPC = $('<tr></tr>');
        var td_delete = $('<td class="text-center"></td>');
        var td_title = $('<td></td>');
        var td_amount = $('<td></td>');
        td_delete.append('<a class="btn btn-danger btn-icon"  onclick="removePC(this)"><i class="fa fa-remove"></i></a>');
        td_title.append('<input type="text" name="title[' + countPhuCap + ']" id="title_' + countPhuCap + '" value="" style="width: 100%;" class="title"><input type="hidden" name="countPhuCap[]" value="' + countPhuCap + '">');
        td_amount.append('<input type="text" name="amount[' + countPhuCap + ']" value="" class="form-control number-format">');

        trPC.append(td_delete);
        trPC.append(td_title);
        trPC.append(td_amount);
        $('#table_phucap tbody').append(trPC);
        ajaxSelectCallBack($('#title_' + countPhuCap + ''), 'admin/allowance_reduce/searchAllowanceReduce', 0, 1);
        countPhuCap++;
        totalPc();
    }

    function removePC(_this) {
        var tr = $(_this).parents('tr');
        tr.remove();
        totalPc();
    }


    function add_giamtru() {
        var trGT = $('<tr></tr>');
        var td_delete = $('<td class="text-center"></td>');
        var td_title = $('<td></td>');
        var td_amount = $('<td></td>');
        td_delete.append('<a class="btn btn-danger btn-icon"  onclick="removePC(this)"><i class="fa fa-remove"></i></a>');
        td_title.append('<input type="text" name="title_gt[' + countGiamTru + ']"  id="title1_' + countGiamTru + '" value="" style="width: 100%;" class="title_gt"><input type="hidden" name="countGiamTru[]" value="' + countGiamTru + '">');
        td_amount.append('<input type="text" name="amount_gt[' + countGiamTru + ']" value="" class="form-control number-format">');

        trGT.append(td_delete);
        trGT.append(td_title);
        trGT.append(td_amount);
        $('#table_giamtru tbody').append(trGT);
        ajaxSelectCallBack($('#title1_' + countGiamTru + ''), 'admin/allowance_reduce/searchAllowanceReduce', 0, 2);
        countGiamTru++;
        totalGt();
    }

    function removeGT(_this) {
        var tr = $(_this).parents('tr');
        tr.remove();
        totalGt();
    }

    function apply_allowance_staff(_this) {
        var r = confirm(
            "<?php echo _l('Bạn có chắc muốn thực hiện thao tác này!');?>");
        if (r == false) {
            return false;
        } else {
            $.ajax({
                url: "<?= base_url() ?>" + 'admin/allowance_reduce/apply_allowance_staff',
                type: 'POST',
                dataType: 'json',
                data: {
                    csrf_token_name: hash
                },
            })
                .done(function (data) {
                    if (data.result) {
                        alert_float('success', data.message);
                    } else {
                        alert_float('danger', data.message);
                    }
                })
                .fail(function () {
                    console.log("error");
                });
        }
    }

    function apply_reduce_staff(_this) {
        var r = confirm(
            "<?php echo _l('Bạn có chắc muốn thực hiện thao tác này!');?>");
        if (r == false) {
            return false;
        } else {
            $.ajax({
                url: "<?= base_url() ?>" + 'admin/allowance_reduce/apply_reduce_staff',
                type: 'POST',
                dataType: 'json',
                data: {
                    csrf_token_name: hash
                },
            })
                .done(function (data) {
                    if (data.result) {
                        alert_float('success', data.message);
                    } else {
                        alert_float('danger', data.message);
                    }
                })
                .fail(function () {
                    console.log("error");
                });
        }
    }

    function ajaxSelectCallBack(element, url, id, types = '') {
        if (id != 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                //allowClear: true,
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: "<?= base_url() ?>" + url + '/' + $(element).val(),
                        dataType: "json",
                        success: function (data) {
                            callback(data.row);
                        }
                    });
                },
                ajax: {
                    url: "<?= base_url() ?>" + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                //allowClear: true,
                ajax: {
                    url: "<?= base_url() ?>" + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                }
            });
        }
    }
</script>