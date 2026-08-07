<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<div class="row">
    <div class="col-md-12">
        <table class="tnh-table-settings">
            <tr>
                <td colspan="2" class="text-primary bg-primary bold"><?= lang('Thiết lập ngày dashboard srceen') ?></td>
            </tr>
            <tr>
                <td><?= lang('Ngày lấy data') ?></td>
                <td><?= lang('Số phút chuyển mỗi trang báo cáo') ?></td>
            </tr>
            <tr>
                <td>
                    <?php $date_dashboard_srceen_sales = _d(get_option('date_dashboard_srceen_sales')); ?>
                    <?php echo render_date_input('settings[date_dashboard_srceen_sales]', '', $date_dashboard_srceen_sales); ?>
                </td>
                <td>
                    <?php $time_dashboard_srceen = (get_option('time_dashboard_srceen')); ?>
                    <?php echo render_input('settings[time_dashboard_srceen]', '', $time_dashboard_srceen, 'number'); ?>
                </td>
            </tr>
            <!-- 
            <tr>
                <td><?= lang('Ngày lấy data xuất kho') ?></td>
                <td><?= lang('Ngày lấy data mua hàng') ?></td>
            </tr>
            <tr>
                <td>
                    <?php $date_dashboard_srceen_accounting_dxnb = _d(get_option('date_dashboard_srceen_accounting_dxnb')); ?>
                    <?php echo render_date_input('settings[date_dashboard_srceen_accounting_dxnb]', '', $date_dashboard_srceen_accounting_dxnb); ?>
                </td>
                
            </tr> -->
            <!-- <tr>
                <td style="width: 50%;"><?= lang('Ngày lấy data BỘ PHẬN SẢN XUẤT') ?></td>
                <td style="width: 50%;"><?= lang('Ngày lấy data XUẤT KHO GIAO HÀNG') ?></td>
            </tr> -->
            <!-- <tr>
                <td>
                    <?php $date_dashboard_srceen_production = _d(get_option('date_dashboard_srceen_production')); ?>
                    <?php echo render_date_input('settings[date_dashboard_srceen_production]', '', $date_dashboard_srceen_production); ?>
                </td>
                <td>
                    <?php $date_dashboard_srceen_export = _d(get_option('date_dashboard_srceen_export')); ?>
                    <?php echo render_date_input('settings[date_dashboard_srceen_export]', '', $date_dashboard_srceen_export); ?>
                </td>
            </tr> -->
            <!-- <tr>
                <td><?= lang('Ngày lấy data PHÒNG KINH DOANH') ?></td>
                <td><?= lang('Ngày lấy data PHÒNG KẾ HOẠCH') ?></td>

            </tr>
            <tr>
                <td>
                    <?php $date_dashboard_srceen_sales = _d(get_option('date_dashboard_srceen_sales')); ?>
                    <?php echo render_date_input('settings[date_dashboard_srceen_sales]', '', $date_dashboard_srceen_sales); ?>
                </td>
                <td>
                    <?php $date_dashboard_srceen_planning = _d(get_option('date_dashboard_srceen_planning')); ?>
                    <?php echo render_date_input('settings[date_dashboard_srceen_planning]', '', $date_dashboard_srceen_planning); ?>
                </td>
            </tr>
            <tr>
                <td><?= lang('Ngày lấy data PHÒNG KẾ TOÁN - TÀI CHÍNH [Đề xuất nội bộ]') ?></td>
                <td><?= lang('Ngày lấy data PHÒNG KẾ TOÁN - TÀI CHÍNH [Đơn chưa khai hải quan]') ?></td>
            </tr>
            <tr>
                <td>
                    <?php $date_dashboard_srceen_accounting_dxnb = _d(get_option('date_dashboard_srceen_accounting_dxnb')); ?>
                    <?php echo render_date_input('settings[date_dashboard_srceen_accounting_dxnb]', '', $date_dashboard_srceen_accounting_dxnb); ?>
                </td>
                <td>
                    <?php $date_dashboard_srceen_accounting_ckhq = _d(get_option('date_dashboard_srceen_accounting_ckhq')); ?>
                    <?php echo render_date_input('settings[date_dashboard_srceen_accounting_ckhq]', '', $date_dashboard_srceen_accounting_ckhq); ?>
                </td>
            </tr>
            <tr>
                <td><?= lang('Ngày lấy data PHÒNG KẾ TOÁN - TÀI CHÍNH [Hóa đơn mua chưa kê khai]') ?></td>
                <td><?= lang('Ngày lấy data PHÒNG KẾ TOÁN - TÀI CHÍNH [Phiếu yêu cầu chi chưa xử lý]') ?></td>
            </tr>
            <tr>
                <td>
                    <?php $date_dashboard_srceen_accounting_hdmckk = _d(get_option('date_dashboard_srceen_accounting_hdmckk')); ?>
                    <?php echo render_date_input('settings[date_dashboard_srceen_accounting_hdmckk]', '', $date_dashboard_srceen_accounting_hdmckk); ?>
                </td>
                <td>
                    <?php $date_dashboard_srceen_accounting_ycc = _d(get_option('date_dashboard_srceen_accounting_ycc')); ?>
                    <?php echo render_date_input('settings[date_dashboard_srceen_accounting_ycc]', '', $date_dashboard_srceen_accounting_ycc); ?>
                </td>

            </tr>
            <tr>
                <td><?= lang('Ngày lấy data PHÒNG KẾ TOÁN - TÀI CHÍNH [Hóa đơn bán chưa xuất]') ?></td>
                <td><?= lang('Ngày lấy data PHÒNG KẾ TOÁN - TÀI CHÍNH [Phiếu đề xuất tài chính chưa xử lý]') ?></td>
            </tr>
            <tr>
                <td>
                    <?php $date_dashboard_srceen_accounting_hdbs = _d(get_option('date_dashboard_srceen_accounting_hdbs')); ?>
                    <?php echo render_date_input('settings[date_dashboard_srceen_accounting_hdbs]', '', $date_dashboard_srceen_accounting_hdbs); ?>
                </td>
                <td>
                    <?php $date_dashboard_srceen_accounting_dxtc = _d(get_option('date_dashboard_srceen_accounting_dxtc')); ?>
                    <?php echo render_date_input('settings[date_dashboard_srceen_accounting_dxtc]', '', $date_dashboard_srceen_accounting_dxtc); ?>
                </td>
            </tr>
            <tr>
                <td><?= lang('Ngày lấy data PHÒNG KẾ TOÁN - TÀI CHÍNH [Hóa đơn mua theo DXNB]') ?></td>
                <td><?= lang('Ngày lấy data PHÒNG MUA HÀNG - KHO HÀNG [Danh sách PO nhập hàng]') ?></td>
            </tr>
            <tr>
                <td>
                    <?php $date_dashboard_srceen_purchases_internal = _d(get_option('date_dashboard_srceen_purchases_internal')); ?>
                    <?php echo render_date_input('settings[date_dashboard_srceen_purchases_internal]', '', $date_dashboard_srceen_purchases_internal); ?>
                </td>
                <td>
                    <?php $date_dashboard_srceen_warehouse_import = _d(get_option('date_dashboard_srceen_warehouse_import')); ?>
                    <?php echo render_date_input('settings[date_dashboard_srceen_warehouse_import]', '', $date_dashboard_srceen_warehouse_import); ?>
                </td>
            </tr> -->
        </table>
        <table class="tnh-table-settings">
            <tr>
                <td colspan="2" class="text-primary bg-primary bold"><?= lang('Thiết lập thời gian KPI') ?></td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="from-group">
                        <div class="radio radio-inline radio-primary">
                            <input type="radio" name="settings[type_search_kpi]" id="type_search_kpi-1" value="1" <?= get_option('type_search_kpi') == 1 ? "checked" : '' ?>>
                            <label for="type_search_kpi-1"><?= lang('Tháng') ?></label>
                        </div>
                        <div class="radio radio-inline radio-primary">
                            <input type="radio" name="settings[type_search_kpi]" id="type_search_kpi-2" value="2" <?= get_option('type_search_kpi') == 2 ? "checked" : '' ?>>
                            <label for="type_search_kpi-2"><?= lang('Quý') ?></label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="wrap_month <?= get_option('type_search_kpi') == 1 ? '' : 'hide' ?>">
                        <?= lang('month', 'month_kpi_setting') ?>
                        <select name="settings[month_kpi_setting]" id="month_kpi_setting" class="" data-placeholder="<?= lang('month') ?>"
                                style="width: 100%;">
                            <?php if (!empty(getMonth())) : ?>
                                <?php foreach (getMonth() as $key => $value) : ?>
                                    <option <?= get_option('month_kpi_setting') == $key ? 'selected' : '' ?>
                                            value="<?= $key ?>"><?= $value ?>
                                    </option>
                                <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </div>
                    <div class="wrap_precious <?= get_option('type_search_kpi') == 2 ? '' : 'hide' ?>">
                        <?= lang('Quý', 'precious_kpi_setting') ?>
                        <select name="settings[precious_kpi_setting]" id="precious_kpi_setting" class="" data-placeholder="<?= lang('Quý') ?>"
                                style="width: 100%;" style="width: 100%;">
                            <?php if (!empty(getPrecious())) : ?>
                                <?php foreach (getPrecious() as $key => $value) : ?>
                                    <option <?= get_option('precious_kpi_setting') == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?>
                                    </option>
                                <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </div>
                </td>
                <td>
                    <?= lang('year', 'year') ?>
                    <select name="settings[year_kpi_setting]" id="year_kpi_setting" class="" data-placeholder="<?= lang('year') ?>"
                            style="width: 100%;" style="width: 100%;">
                        <?php if (!empty(getYear())) : ?>
                            <?php foreach (getYear() as $key => $value) : ?>
                                <option <?= get_option('year_kpi_setting') == $key ? 'selected' : '' ?>
                                        value="<?= $key ?>"><?= $value ?>
                                </option>
                            <?php endforeach ?>
                        <?php endif ?>
                    </select>
                </td>
            </tr>
        </table>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("select#month_kpi_setting").select2();
        $("select#precious_kpi_setting").select2();
        $("select#year_kpi_setting").select2();
    })
    $('input[name="settings[type_search_kpi]"]').on('change', function() {
        var type = $(this).val();
        if (type == 1) {
            $('.wrap_month').removeClass('hide');
            $('.wrap_precious').addClass('hide');
        } else {
            $('.wrap_month').addClass('hide');
            $('.wrap_precious').removeClass('hide');
        }
    })
</script>