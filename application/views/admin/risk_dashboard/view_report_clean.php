<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <link href="<?= base_url('assets/plugins/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/plugins/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet">
    <script src="<?= base_url('assets/plugins/jquery/jquery.min.js') ?>"></script>
    <style>
        body {
            background: #f1f5f9;
            font-family: "Inter", sans-serif;
            padding: 25px;
            color: #334155;
        }

        .report-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 1000px;
            margin: 0 auto;
            border: 1px solid #e2e8f0;
        }

        .header {
            background: #1e293b;
            color: #fff;
            padding: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
        }

        .header-meta {
            margin-top: 12px;
            display: flex;
            gap: 25px;
            font-size: 13px;
            opacity: 0.85;
            flex-wrap: wrap;
        }

        .main-content {
            padding: 35px;
        }

        .grid-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-bottom: 40px;
        }

        .info-box {
            border-left: 3px solid #e2e8f0;
            padding-left: 15px;
        }

        .info-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 15px;
            color: #1e293b;
            font-weight: 600;
        }

        .detail-section {
            margin-bottom: 35px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .detail-title {
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-text {
            font-size: 14px;
            line-height: 1.7;
            color: #334155;
        }

        /* Read-only list */
        .procedure-section {
            background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        }

        .procedure-title {
            color: #0f766e;
        }

        .procedure-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .procedure-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 12px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
        }

        .procedure-index {
            flex: 0 0 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 700;
        }

        .fix-index {
            background: #d1fae5;
            color: #047857;
        }

        .procedure-label {
            flex: 1;
            font-size: 13px;
            line-height: 1.6;
            color: #334155;
            word-break: break-word;
        }

        /* Inline edit checkboxes */
        .edit-section-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            padding: 8px 10px;
            background: #fef9c3;
            border: 1px dashed #ca8a04;
            border-radius: 8px;
            font-size: 12px;
            color: #854d0e;
            font-weight: 600;
        }

        .cause-group-title {
            font-size: 13px;
            font-weight: 700;
            margin-top: 12px;
            margin-bottom: 6px;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
        }

        .checkbox-edit-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 8px;
            background: #fff;
            border: 1px solid #e2e8f0;
            margin-bottom: 5px;
            cursor: pointer;
        }

        .checkbox-edit-item:hover {
            border-color: #93c5fd;
            background: #eff6ff;
        }

        .checkbox-edit-item input[type=checkbox] {
            cursor: pointer;
            width: 15px;
            height: 15px;
        }

        .checkbox-edit-item label {
            margin: 0;
            font-size: 13px;
            cursor: pointer;
            color: #334155;
        }

        .checkbox-edit-item input:checked+label {
            color: #1d4ed8;
            font-weight: 600;
        }

        .reason-text {
            font-size: 12px;
            color: #dc2626;
            margin-bottom: 4px;
            font-style: italic;
        }

        .procedure-empty {
            font-size: 13px;
            color: #94a3b8;
            padding: 4px 0 8px;
        }

        .fix-section {
            background: linear-gradient(180deg, #f9fffb 0%, #ffffff 100%);
        }

        .fix-title {
            color: #047857;
        }

        .note-fix-box {
            margin-top: 14px;
            padding: 12px 14px;
            border-radius: 10px;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #d1fae5;
            font-size: 13px;
            line-height: 1.6;
        }

        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .img-item {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            height: 150px;
        }

        .img-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
            cursor: pointer;
        }

        .img-item img:hover {
            transform: scale(1.05);
        }

        .cost-tag {
            color: #dc2626;
            font-weight: 800;
            font-size: 16px;
        }

        .badge-violate {
            background: #fee2e2;
            color: #991b1b;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
        }

        /* Toast */
        .toast-msg {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 13px;
            color: #fff;
            z-index: 9999;
            display: none;
        }
    </style>
</head>

<body>
    <div class="report-card">
        <div class="header">
            <h1><i class="fa fa-file-text"></i> <?= $report->code ?> | <?= $report->title ?></h1>
            <div class="header-meta">
                <span><i class="fa fa-calendar"></i> Ngày ghi nhận: <b><?= _d($report->date_report) ?></b></span>
                <span><i class="fa fa-user"></i> Người liên quan: <b><?= !empty($report->staff_name) ? $report->staff_name : 'Chưa cập nhật' ?></b></span>
                <?php if ($report->violate == 1): ?>
                    <span class="badge-violate"><i class="fa fa-warning"></i> Có Vi Phạm</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="main-content">

            <!-- NGUYÊN NHÂN + CHI PHÍ -->
            <div class="grid-info">
                <div class="info-box">
                    <span class="info-label">Nguyên nhân</span>
                    <?php
                    $causeGroups = [
                        'material'    => 'Nguyên phụ liệu (Material)',
                        'man'         => 'Nhân lực (Man)',
                        'machine'     => 'Máy móc (Machine)',
                        'method'      => 'Phương pháp (Method)',
                        'environment' => 'Môi trường (Environment)',
                    ];
                    $dtReason   = $report->dtReason ?? [];
                    $causeFound = false;
                    foreach ($causeGroups as $groupKey => $groupLabel) {
                        if (!empty($report->$groupKey) && is_array($report->$groupKey)) {
                            foreach ($report->$groupKey as $step) {
                                if (!empty($step['ischeck']) || !empty($step['is_check'])) {
                                    $causeFound = true;
                                    break 2;
                                }
                            }
                        }
                    }
                    ?>

                    <?php if ($causeFound): ?>
                        <!-- VIEW: chỉ hiện các item đã check -->
                        <?php foreach ($causeGroups as $groupKey => $groupLabel): ?>
                            <?php if (!empty($report->$groupKey) && is_array($report->$groupKey)): ?>
                                <?php
                                $hasCheck = false;
                                foreach ($report->$groupKey as $step) {
                                    if (!empty($step['ischeck']) || !empty($step['is_check'])) {
                                        $hasCheck = true;
                                        break;
                                    }
                                }
                                ?>
                                <?php if ($hasCheck): ?>
                                    <div class="cause-group-title"><?= $groupLabel ?></div>
                                    <?php
                                    $reasonText = $dtReason[$groupKey]['reason'] ?? '';
                                    if (!$reasonText && $groupKey === 'environment') {
                                        $reasonText = $dtReason['method']['environment'] ?? '';
                                    }
                                    ?>
                                    <?php if ($reasonText): ?>
                                        <div class="reason-text"><?= htmlspecialchars($reasonText) ?></div>
                                    <?php endif; ?>
                                    <ol class="procedure-list" style="margin-top: 6px;">
                                        <?php foreach ($report->$groupKey as $idx => $step): ?>
                                            <?php if (!empty($step['ischeck']) || !empty($step['is_check'])): ?>
                                                <li class="procedure-item">
                                                    <span class="procedure-index"><i class="fa fa-check" style="font-size:11px;"></i></span>
                                                    <span class="procedure-label"><?= htmlspecialchars($step['name'] ?? '-') ?></span>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ol>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <!-- INLINE EDIT: chưa có → hiện tất cả để check -->
                        <div class="edit-section-header">
                            <i class="fa fa-pencil-square-o"></i> Chưa có nguyên nhân — tick chọn để cập nhật ngay
                        </div>
                        <?php
                        $hasAnyItem = true;
                            // foreach ($causeGroups as $groupKey => $groupLabel) {
                            //     if (!empty($report->$groupKey) && is_array($report->$groupKey) && count($report->$groupKey) > 0) {
                            //         $hasAnyItem = true;
                            //         break;
                            //     }
                            // }
                        ;
                        $causeGroups = [
                            'material'    => 'Nguyên phụ liệu (Material)',
                            'man'         => 'Nhân lực (Man)',
                            'machine'     => 'Máy móc (Machine)',
                            'method'      => 'Phương pháp (Method)',
                            'environment' => 'Môi trường (Environment)',
                        ];
                        ?>
                        <?php if ($hasAnyItem): ?>
                            <?php foreach ($causeGroups as $groupKey => $groupLabel): ?>
                                <div class="cause-group-title">
                                    <?= $groupLabel ?>
                                    <a href="javascript:void(0)" onclick="$(this).parent().next('.add-item-box').toggle();" class="text-success mleft10" style="font-size: 11px;"><i class="fa fa-plus"></i> Thêm</a>
                                </div>
                                <div class="add-item-box" style="display: none; align-items: center; gap: 5px; margin-bottom: 8px;">
                                    <input type="text" class="form-control input-sm" id="new_item_<?= $groupKey ?>" placeholder="Nhập <?= $groupLabel ?> mới...">
                                    <button type="button" class="btn btn-sm btn-success" onclick="addCustomItemBox(<?= $report->id ?>, '<?= $groupKey ?>')">Lưu</button>
                                </div>

                                <?php
                                $reasonText = $dtReason[$groupKey]['reason'] ?? '';
                                if (!$reasonText && $groupKey === 'environment') {
                                    $reasonText = $dtReason['method']['environment'] ?? '';
                                }
                                ?>
                                <?php if ($reasonText): ?>
                                    <div class="reason-text text-danger" style="margin-bottom: 4px;">
                                        <?= htmlspecialchars($reasonText) ?>
                                    </div>
                                <?php endif; ?>

                                <div id="wrapper_items_<?= $groupKey ?>">
                                    <?php if (!empty($report->$groupKey) && is_array($report->$groupKey)): ?>
                                        <?php foreach ($report->$groupKey as $key => $value): ?>
                                            <div class="checkbox-edit-item">
                                                <input type="checkbox" id="cause_<?= $groupKey ?>_<?= $key ?>"
                                                    onclick="changeIscheck(<?= (int)$value['id'] ?>, this)"
                                                    value="1" <?= (!empty($value['ischeck']) || !empty($value['is_check'])) ? 'checked' : '' ?>>
                                                <label for="cause_<?= $groupKey ?>_<?= $key ?>"><?= htmlspecialchars($value['name'] ?? '-') ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            <script>
                                function addCustomItemBox(id, type) {
                                    var name = $('#new_item_' + type).val();
                                    if (name.trim() == '') {
                                        alert_float('danger', 'Vui lòng nhập nội dung');
                                        return;
                                    }
                                    $.ajax({
                                        url: admin_url + 'production_report/addCustomItem',
                                        type: 'GET',
                                        dataType: 'json',
                                        data: {
                                            id: id,
                                            type: type,
                                            name: name
                                        },
                                        success: function(response) {
                                            if (response.success) {
                                                if (typeof window.parent.alert_float === 'function') {
                                                    window.parent.alert_float(response.alert_type || 'success', response.message);
                                                } else if (typeof alert_float === 'function') {
                                                    alert_float(response.alert_type || 'success', response.message);
                                                }

                                                var html = '<div class="checkbox-edit-item">';
                                                html += '<input type="checkbox" id="cause_' + type + '_' + response.new_id + '" onclick="changeIscheck(' + response.new_id + ', this)" value="1" checked>';
                                                html += '<label for="cause_' + type + '_' + response.new_id + '">' + name + '</label>';
                                                html += '</div>';

                                                $('#empty_' + type).remove();
                                                $('#wrapper_items_' + type).append(html);
                                                $('#new_item_' + type).val('');
                                                $('#new_item_' + type).parent().hide();
                                            } else {
                                                if (typeof window.parent.alert_float === 'function') {
                                                    window.parent.alert_float('danger', response.message);
                                                } else if (typeof alert_float === 'function') {
                                                    alert_float('danger', response.message);
                                                } else {
                                                    alert(response.message);
                                                }
                                            }
                                        }
                                    });
                                }
                            </script>
                        <?php else: ?>
                            <div class="procedure-empty">Chưa có dữ liệu nguyên nhân trong hệ thống</div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="info-box" style="border-color: #ef4444;">
                    <span class="info-label">Chi phí thiệt hại</span>
                    <span class="info-value cost-tag"><?= !empty($report->damage_cost) ? number_format($report->damage_cost) . ' VNĐ' : '0 VNĐ' ?></span>
                </div>
            </div>

            <!-- MÔ TẢ -->
            <div class="detail-section">
                <div class="detail-title"><i class="fa fa-search"></i> Mô tả chi tiết vi phạm</div>
                <div class="detail-text"><?= !empty($report->detail_tasks) ? $report->detail_tasks : 'Không có mô tả' ?></div>
            </div>

            <!-- QUY TRÌNH XỬ LÝ -->
            <!-- QUY TRÌNH XỬ LÝ -->
            <div class="detail-section procedure-section">
                <div class="detail-title procedure-title"><i class="fa fa-list"></i> Quy trình xử lý</div>
                <div class="detail-text">
                    <?php if (!empty($report->procedure)): ?>
                        <!-- VIEW -->
                        <ol class="procedure-list">
                            <?php foreach ($report->procedure as $idx => $step): ?>
                                <li class="procedure-item">
                                    <span class="procedure-index"><?= $idx + 1 ?></span>
                                    <span class="procedure-label"><?= htmlspecialchars($step['name'] ?? '-') ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php else: ?>
                        <!-- INLINE EDIT -->
                        <?php
                        // Load all procedure items (not just checked)
                        $allProcedure = $this->db->get_where('tblproduction_report_items', [
                            'id_production_report' => $report->id,
                            'type' => 'procedure'
                        ])->result_array();
                        ?>
                        <?php if (!empty($allProcedure)): ?>
                            <div class="edit-section-header">
                                <i class="fa fa-pencil-square-o"></i> Chưa có quy trình — tick chọn để cập nhật ngay
                            </div>
                            <?php foreach ($allProcedure as $key => $value): ?>
                                <div class="checkbox-edit-item">
                                    <input type="checkbox" id="procedure_<?= $key ?>"
                                        onclick="changeIscheck(<?= (int)$value['id'] ?>, this)"
                                        value="1" <?= !empty($value['ischeck']) ? 'checked' : '' ?>>
                                    <label for="procedure_<?= $key ?>"><?= htmlspecialchars($value['name'] ?? '-') ?></label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="procedure-empty">Chưa có quy trình xử lý</div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- QUY TRÌNH KHẮC PHỤC -->
            <div class="detail-section fix-section">
                <div class="detail-title fix-title"><i class="fa fa-shield"></i> Quy trình khắc phục, phòng ngừa</div>
                <div class="detail-text">
                    <?php if (!empty($report->fix)): ?>
                        <ol class="procedure-list">
                            <?php foreach ($report->fix as $idx => $step): ?>
                                <li class="procedure-item">
                                    <span class="procedure-index fix-index"><?= $idx + 1 ?></span>
                                    <span class="procedure-label"><?= htmlspecialchars($step['name'] ?? '-') ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php else: ?>
                        <div class="procedure-empty">Chưa có quy trình khắc phục</div>
                    <?php endif; ?>
                    <?php if (!empty($report->note_fix)): ?>
                        <div class="note-fix-box"><?= htmlspecialchars($report->note_fix) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- HÌNH ẢNH -->
            <?php if (!empty($files)): ?>
                <div class="detail-section">
                    <span class="info-label"><i class="fa fa-file-image-o"></i> Hình ảnh minh chứng (<?= count($files) ?>)</span>
                    <div class="image-gallery">
                        <?php foreach ($files as $file):
                            $img_path = base_url($file['file_name']);
                        ?>
                            <div class="img-item">
                                <img src="<?= $img_path ?>" alt="Minh chứng" onclick="window.open(this.src)">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <div class="toast-msg" id="toastMsg"></div>

    <script>
        var admin_url = '<?= admin_url() ?>';

        function changeIscheck(id, el) {
            var status = $(el).prop('checked') ? 1 : 0;
            $(el).prop('disabled', true);
            $.get(admin_url + 'production_report/changeIscheck/' + id + '/' + status, function(result) {
                try {
                    result = JSON.parse(result);
                } catch (e) {}
                showToast(result.message || 'Đã cập nhật', result.alert_type);
                $(el).prop('disabled', false);
            }).fail(function() {
                showToast('Lỗi kết nối', 'error');
                $(el).prop('checked', !$(el).prop('checked')).prop('disabled', false);
            });
        }

        function showToast(msg, type) {
            var el = $('#toastMsg');
            el.text(msg).css('background', type === 'success' ? '#16a34a' : '#dc2626');
            el.fadeIn(200).delay(2000).fadeOut(400);
        }
    </script>
</body>

</html>