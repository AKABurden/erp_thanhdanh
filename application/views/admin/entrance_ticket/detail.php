<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg" style="min-width: 85%;">
    <?php echo form_open(admin_url('entrance_ticket/detail/' . $id), ['id' => 'form-entrance-ticket', 'enctype' => 'multipart/form-data']); ?>
    <div class="modal-content">
        <div class="modal-header" style="background: linear-gradient(135deg,#1a73e8,#0d47a1); color:#fff;">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:1;">
                <span>&times;</span>
            </button>
            <h4 class="modal-title"><i class="fa fa-sign-out"></i> <?= $title ?></h4>
        </div>
        <div class="modal-body">
            <!-- ===== THÔNG TIN CHUNG ===== -->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Mã Phiếu (Request ID)', 'reference_no') ?>
                        <input type="text" name="reference_no" class="form-control" id="reference_no"
                            value="<?= !empty($dtData) ? ($dtData['reference_no'] ?? '') : (lang('auto') ?? '') ?>" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Ngày lập phiếu', 'date') ?>
                        <?= form_input(
                            'date',
                            !empty($dtData['date']) ? _dt($dtData['date']) : date('d/m/Y H:i'),
                            'class="form-control datetimepicker" required'
                        ) ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Ưu tiên (Priority)', 'priority') ?>
                        <select name="priority" id="priority" class="form-control">
                            <option value="NORMAL" <?= (!empty($dtData) && ($dtData['priority'] ?? '') == 'NORMAL') ? 'selected' : '' ?>>NORMAL (Thường)</option>
                            <option value="URGENT" <?= (!empty($dtData) && ($dtData['priority'] ?? '') == 'URGENT') ? 'selected' : '' ?>>URGENT (Gấp)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group" style="width: 100%;">
                        <?= lang('Nhân viên phụ trách', 'id_staff') ?>
                        <select name="id_staff" id="id_staff" onchange="loadStaffInfo()" data-live-search="true" style="width: 100%;">
                            <option value=""></option>
                            <?php foreach ($employees as $emp): ?>
                                <option <?= (!empty($dtData) && ($dtData['id_staff'] ?? '') == $emp['staffid']) ? 'selected' : (get_staff_user_id() == $emp['staffid'] ? 'selected' : '') ?>
                                    value="<?= $emp['staffid'] ?>"><?= $emp['fullname'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Vị Trí</label>
                        <div class="cell-name-roles text-info" style="padding: 7px 0; font-weight: bold;"><?= !empty($dtData) ? ($dtData['name_roles'] ?? '') : '' ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Phòng Ban</label>
                        <div class="cell-name-departments text-info" style="padding: 7px 0; font-weight: bold;"><?= !empty($dtData) ? ($dtData['name_departments'] ?? '') : '' ?></div>
                    </div>
                </div>
            </div>

            <hr class="mtop5 mbot5" />
            <h4 class="mbot10 text-primary" style="font-size: 16px; font-weight: bold; border-left: 4px solid #1a73e8; padding-left: 10px;">THÔNG TIN ĐỐI TÁC & NGƯỜI THỰC HIỆN</h4>

            <?php
            $partnerType = !empty($dtData) ? (int)($dtData['partner_type'] ?? 3) : 3;
            $partnerId   = !empty($dtData) ? (int)($dtData['partner_id'] ?? 0) : 0;
            $partnerName = !empty($dtData) ? ($dtData['partner_name'] ?? '') : '';
            ?>

            <!-- Hidden fields gửi khi submit -->
            <input type="hidden" name="partner_type" id="partner_type" value="<?= $partnerType ?>">
            <input type="hidden" name="partner_id" id="partner_id" value="<?= $partnerId ?>">
            <input type="hidden" name="partner_name" id="partner_name_hidden" value="<?= htmlspecialchars($partnerName) ?>">

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Loại đối tác</label><br>
                        <label class="radio-inline">
                            <input type="radio" name="partner_type_radio" value="1" <?= $partnerType == 1 ? 'checked' : '' ?>> Khách hàng
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="partner_type_radio" value="2" <?= $partnerType == 2 ? 'checked' : '' ?>> Nhà cung cấp
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="partner_type_radio" value="3" <?= $partnerType == 3 ? 'checked' : '' ?>> Khác
                        </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Tên đối tác / KH / NCC</label>

                        <!-- Combobox Khách hàng (type=1) - Dùng input hidden cho Select2 cũ -->
                        <div id="wrap-partner-client" style="display:<?= $partnerType == 1 ? 'block' : 'none' ?>;">
                            <input type="hidden" id="partner_select_client" class="modal-select2" data-placeholder="<?= lang('Chọn khách hàng') ?>" style="width:100%;">
                        </div>

                        <!-- Combobox Nhà cung cấp (type=2) - Dùng input hidden cho Select2 cũ -->
                        <div id="wrap-partner-supplier" style="display:<?= $partnerType == 2 ? 'block' : 'none' ?>;">
                            <input type="hidden" id="partner_select_supplier" class="modal-select2" data-placeholder="<?= lang('Chọn nhà cung cấp') ?>" style="width:100%;">
                        </div>

                        <!-- Text nhập tay (type=3) -->
                        <div id="wrap-partner-other" style="display:<?= $partnerType == 3 ? 'block' : 'none' ?>;">
                            <input type="text" id="partner_name_text" class="form-control"
                                value="<?= ($partnerType == 3) ? htmlspecialchars($partnerName) : '' ?>"
                                placeholder="Tên đối tác liên quan…">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Người trực tiếp thực hiện', 'executor_name') ?>
                        <input type="text" name="executor_name" class="form-control" value="<?= $dtData['executor_name'] ?? '' ?>" placeholder="Họ tên người giao nhận…">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('SĐT người thực hiện', 'executor_phone') ?>
                        <input type="text" name="executor_phone" class="form-control" value="<?= $dtData['executor_phone'] ?? '' ?>">
                    </div>
                </div>
            </div>

            <hr class="mtop5 mbot5" />
            <h4 class="mbot10 text-primary" style="font-size: 16px; font-weight: bold; border-left: 4px solid #1a73e8; padding-left: 10px;">THÔNG TIN HÀNG HÓA TÀI SẢN</h4>

            <?php
            $itemProductId = !empty($dtData) ? (int)($dtData['item_product_id'] ?? 0) : 0;
            $itemCodeName  = !empty($dtData) ? ($dtData['item_code_name'] ?? '') : '';
            ?>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('Phân loại hàng', 'item_type') ?>
                        <input type="text" name="item_type" class="form-control" value="<?= $dtData['item_type'] ?? '' ?>" placeholder="Ví dụ: Công cụ, Phế liệu…">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <?= lang('Mã / Tên hàng chi tiết', 'item_code_name') ?>
                        <!-- Hidden lưu id và name sản phẩm -->
                        <input type="hidden" name="item_product_id" id="item_product_id" value="<?= $itemProductId ?>">
                        <input type="hidden" name="item_code_name" id="item_code_name_hidden" value="<?= htmlspecialchars($itemCodeName) ?>">

                        <!-- Select2 ajax tìm sản phẩm - Dùng hidden cho Select2 cũ -->
                        <input type="hidden" id="product_select" class="modal-select2" data-placeholder="<?= lang('Chọn sản phẩm') ?>" style="width:100%;">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('SL', 'quantity') ?>
                                <input type="number" name="quantity" class="form-control text-center" value="<?= $dtData['quantity'] ?? 0 ?>" step="any">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Số kiện', 'package_count') ?>
                                <input type="number" name="package_count" class="form-control text-center" value="<?= $dtData['package_count'] ?? 0 ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Số Kg', 'kg_weight') ?>
                                <input type="number" name="kg_weight" class="form-control text-center" value="<?= $dtData['kg_weight'] ?? 0 ?>" step="any">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?= lang('Lý do mang hàng ra', 'note_reason') ?>
                <textarea name="note_reason" class="form-control" rows="2"><?= $dtData['note_reason'] ?? '' ?></textarea>
            </div>

            <hr class="mtop5 mbot5" />
            <h4 class="mbot10 text-primary" style="font-size: 16px; font-weight: bold; border-left: 4px solid #1a73e8; padding-left: 10px;">VẬN CHUYỂN & LỘ TRÌNH</h4>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Phương tiện', 'vehicle_type') ?>
                        <select name="vehicle_type" id="vehicle_type" class="form-control selectpicker" data-live-search="true">
                            <option value="">-- Chọn phương tiện --</option>
                            <?php foreach ($transportation_vehicles as $tv): ?>
                                <option value="<?= htmlspecialchars($tv['name']) ?>"
                                    <?= (!empty($dtData) && ($dtData['vehicle_type'] ?? '') == $tv['name']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tv['code'] . ' - ' . $tv['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Biển số xe', 'license_plate') ?>
                        <input type="text" name="license_plate" class="form-control" value="<?= $dtData['license_plate'] ?? '' ?>" placeholder="Biển số…">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Tên tài xế', 'driver_name') ?>
                        <input type="text" name="driver_name" class="form-control" value="<?= $dtData['driver_name'] ?? '' ?>" placeholder="Họ tên tài xế (nếu có)…">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('Lộ trình giao nhận', 'route') ?>
                        <select name="route" id="route" class="form-control selectpicker" data-live-search="true">
                            <option value="">-- Chọn lộ trình --</option>
                            <?php foreach ($list_vehicles as $lv): ?>
                                <?php
                                $dep = !empty($lv['departure_point']) ? trim($lv['departure_point']) : '';
                                $des = !empty($lv['destination']) ? trim($lv['destination']) : '';
                                $routeName = '';
                                if ($dep && $des) {
                                    $routeName = $dep . ' - ' . $des;
                                } elseif ($dep) {
                                    $routeName = $dep;
                                } elseif ($des) {
                                    $routeName = $des;
                                } else {
                                    $routeName = $lv['code_vehicle'];
                                }

                                $displayRoute = $routeName . ' (' . $lv['transporters'] . ' | ' . $lv['type_vehicle'] . ')';
                                ?>
                                <option value="<?= htmlspecialchars($routeName) ?>" data-price="<?= $lv['price'] ?? 0 ?>" data-destination="<?= htmlspecialchars($lv['destination'] ?? '') ?>"
                                    <?= (!empty($dtData) && ($dtData['route'] ?? '') == $routeName) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($displayRoute) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('Chi phí vận chuyển', 'route_price') ?>
                        <input type="text" id="route_price" name="route_price" class="form-control text-right" onkeyup="formatNumBerKeyUp(this)" value="<?= !empty($dtData['route_price']) ? number_format($dtData['route_price'], 0, '.', ',') : 0 ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('Điểm đến cuối', 'destination') ?>
                        <textarea id="destination" name="destination" class="form-control" rows="5"><?= $dtData['destination'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>

            <hr class="mtop5 mbot5" />
            <div class="row">
                <div class="col-md-6">
                    <h4 class="mbot10 text-primary" style="font-size: 16px; font-weight: bold; border-left: 4px solid #1a73e8; padding-left: 10px;">LỊCH TRÌNH DỰ KIẾN</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= lang('Ngày đi dự kiến', 'planned_date_out') ?>
                                <input type="text" name="planned_date_out" class="form-control datepicker" value="<?= !empty($dtData['planned_date_out']) ? _d($dtData['planned_date_out']) : '' ?>" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= lang('Ngày về dự kiến', 'planned_date_return') ?>
                                <input type="text" name="planned_date_return" class="form-control datepicker" value="<?= !empty($dtData['planned_date_return']) ? _d($dtData['planned_date_return']) : '' ?>" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Loại phiếu:</label><br />
                        <label class="radio-inline"><input type="radio" name="type" value="out" <?= (!empty($dtData) && ($dtData['type'] ?? '') == 'out') ? 'checked' : '' ?>> Một chiều (Không về)</label>
                        <label class="radio-inline"><input type="radio" name="type" value="in_out" <?= (empty($dtData) || (!empty($dtData) && ($dtData['type'] ?? '') == 'in_out')) ? 'checked' : '' ?>> Hai chiều (Có về)</label>
                    </div>
                </div>
                <div class="col-md-6" style="border-left: 1px solid #eee;">
                    <h4 class="mbot10 text-primary" style="font-size: 16px; font-weight: bold; border-left: 4px solid #1a73e8; padding-left: 10px;">CHỨNG TỪ ĐI KÈM</h4>
                    <div class="checkbox checkbox-primary mtop15">
                        <input type="checkbox" name="doc_delivery_signed" id="doc_delivery_signed" value="1" <?= (!empty($dtData['doc_delivery_signed']) ? 'checked' : '') ?>>
                        <label for="doc_delivery_signed"><strong>Phiếu giao hàng có ký nhận KH</strong></label>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" name="doc_invoice" id="doc_invoice" value="1" <?= (!empty($dtData['doc_invoice']) ? 'checked' : '') ?>>
                        <label for="doc_invoice"><strong>Hóa đơn (HĐĐ)</strong></label>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" name="doc_handover" id="doc_handover" value="1" <?= (!empty($dtData) && ($dtData['doc_handover'] == 1) ? 'checked' : '') ?>>
                        <label for="doc_handover"><strong>Biên bản bàn giao / gia công</strong></label>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-body -->

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-submit">
                <i class="fa fa-save"></i> <?= lang('submit') ?>
            </button>
            <button type="button" class="btn btn-default" data-dismiss="modal">
                <i class="fa fa-times"></i> <?= lang('close') ?>
            </button>
        </div>
    </div><!-- /.modal-content -->
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    /* =========================================================
     *  Biến dùng chung
     * ======================================================= */
    var initPartnerId = <?= (int)($dtData['partner_id'] ?? 0) ?>;
    var initPartnerType = <?= (int)($dtData['partner_type'] ?? 3) ?>;
    var initPartnerName = <?= json_encode($dtData['partner_name'] ?? '') ?>;

    var initProductId = <?= (int)($dtData['item_product_id'] ?? 0) ?>;
    var initItemName = <?= json_encode($dtData['item_code_name'] ?? '') ?>;

    /* =========================================================
     *  Hàm tải thông tin nhân viên
     * ======================================================= */
    function loadStaffInfo() {
        var staffId = $('#id_staff').val();
        if (!staffId) {
            $('.cell-name-roles, .cell-name-departments').html('');
            return;
        }
        $.ajax({
            url: '<?= admin_url('entrance_ticket/getStaff/') ?>' + staffId,
            type: 'GET',
            dataType: 'JSON',
            success: function(data) {
                if (data) {
                    $('.cell-name-roles').html(data.name_roles || '');
                    $('.cell-name-departments').html(data.name_departments || '');
                }
            }
        });
    }

    /* =========================================================
     *  Toggle hiện/ẩn vùng nhập đối tác theo loại
     * ======================================================= */
    function switchPartnerType(type) {
        $('#partner_type').val(type);
        $('#partner_id').val(0);
        $('#partner_name_hidden').val('');

        $('#wrap-partner-client').hide();
        $('#wrap-partner-supplier').hide();
        $('#wrap-partner-other').hide();

        if (type == 1) {
            $('#wrap-partner-client').show();
        } else if (type == 2) {
            $('#wrap-partner-supplier').show();
        } else {
            $('#wrap-partner-other').show();
        }
    }

    /* =========================================================
     *  Khởi tạo select2 cho Khách hàng
     * ======================================================= */
    function initClientSelect2(selectedId, selectedText) {
        ajaxSelectParamsCallback(
            '#partner_select_client',
            'admin/entrance_ticket/searchClients',
            selectedId,
            false, true,
            selectedId > 0 ? {
                id: selectedId,
                text: selectedText
            } : false
        );
        $('#partner_select_client').on('change', function() {
            var d = $(this).select2('data');
            $('#partner_id').val(d ? d.id : 0);
            $('#partner_name_hidden').val(d ? d.text : '');
        });
    }

    /* =========================================================
     *  Khởi tạo select2 cho Nhà cung cấp
     * ======================================================= */
    function initSupplierSelect2(selectedId, selectedText) {
        ajaxSelectParamsCallback(
            '#partner_select_supplier',
            'admin/entrance_ticket/searchSuppliers',
            selectedId,
            false, true,
            selectedId > 0 ? {
                id: selectedId,
                text: selectedText
            } : false
        );
        $('#partner_select_supplier').on('change', function() {
            var d = $(this).select2('data');
            $('#partner_id').val(d ? d.id : 0);
            $('#partner_name_hidden').val(d ? d.text : '');
        });
    }

    /* =========================================================
     *  Khởi tạo select2 cho Sản phẩm
     * ======================================================= */
    function initProductSelect2(selectedId, selectedText) {
        ajaxSelectParamsCallback(
            '#product_select',
            'admin/entrance_ticket/searchProducts',
            selectedId,
            false, true,
            selectedId > 0 ? {
                id: selectedId,
                text: selectedText
            } : false
        );
        $('#product_select').on('change', function() {
            var d = $(this).select2('data');
            if (d && d.id) {
                $('#item_product_id').val(d.id);
                $('#item_code_name_hidden').val(d.text);
            } else {
                $('#item_product_id').val(0);
                $('#item_code_name_hidden').val('');
            }
        });
    }

    /* =========================================================
     *  DOM Ready
     * ======================================================= */
    $(function() {
        init_datepicker();
        init_selectpicker('refresh');
        $('#id_staff').select2();

        <?php if (!empty($dtData['id_staff'])): ?>
            loadStaffInfo();
        <?php endif; ?>

        /* --- Khởi tạo select2 đối tác --- */
        initClientSelect2(
            initPartnerType == 1 ? initPartnerId : 0,
            initPartnerType == 1 ? initPartnerName : ''
        );
        initSupplierSelect2(
            initPartnerType == 2 ? initPartnerId : 0,
            initPartnerType == 2 ? initPartnerName : ''
        );

        /* --- Khởi tạo select2 sản phẩm --- */
        initProductSelect2(initProductId, initItemName);

        /* --- Lắng nghe thay đổi loại đối tác --- */
        $('input[name="partner_type_radio"]').on('change', function() {
            switchPartnerType(parseInt($(this).val()));
        });

        /* --- Đồng bộ text nhập tay cho Khác --- */
        $('#partner_name_text').on('input', function() {
            $('#partner_name_hidden').val($(this).val());
        });

        /* --- Tự động chọn giá & Điểm đến cho Lộ trình --- */
        $('#route').on('change', function() {
            var selectedOpt = $(this).find('option:selected');
            var price = selectedOpt.data('price');
            var dest = selectedOpt.data('destination');

            if (price !== undefined) {
                // Định dạng tiền bằng dấu phẩy
                var formattedPrice = Number(price).toLocaleString('en-US');
                $('#route_price').val(formattedPrice);
            } else {
                $('#route_price').val(0);
            }

            // Gắn vào field điểm đến cuối
            if (dest !== undefined && dest !== '') {
                $('#destination').val(dest);
            }
        });

        /* --- Validate & Submit --- */
        appValidateForm($('#form-entrance-ticket'), {
            date: 'required',
            id_staff: 'required',
            executor_name: 'required',
            item_type: 'required',
        }, submitForm);
    });

    function submitForm(form) {
        $('.btn-submit').attr('disabled', 'disabled');
        var formData = $(form).serializeArray();

        // Đảm bảo checkbox không chọn vẫn gửi giá trị 0
        ['doc_delivery_signed', 'doc_invoice', 'doc_handover'].forEach(function(name) {
            if (!formData.some(function(i) {
                    return i.name === name;
                })) {
                formData.push({
                    name: name,
                    value: 0
                });
            }
        });

        $.ajax({
            url: form.action,
            type: 'POST',
            data: formData,
            dataType: 'JSON',
            success: function(data) {
                if (data.result) {
                    alert_float('success', data.message);
                    if (typeof oTable !== 'undefined') oTable.draw();
                    $('.modal-dialog .close').trigger('click');
                } else {
                    alert_float('danger', data.message);
                    $('.btn-submit').removeAttr('disabled');
                }
            },
            error: function() {
                alert_float('danger', 'Lỗi hệ thống!');
                $('.btn-submit').removeAttr('disabled');
            }
        });
        return false;
    }
</script>