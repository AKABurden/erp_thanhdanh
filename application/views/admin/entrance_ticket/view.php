<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$status = (int)($dtData['status'] ?? 0);
$statusLabels = [
    0 => ['label' => 'Chờ duyệt',      'class' => 'label-default', 'color' => '#6c757d'],
    1 => ['label' => 'QA duyệt đi',    'class' => 'label-info',    'color' => '#17a2b8'],
    2 => ['label' => 'BV xác nhận ra', 'class' => 'label-warning', 'color' => '#fd7e14'],
    3 => ['label' => 'BV xác nhận về', 'class' => 'label-primary', 'color' => '#007bff'],
    4 => ['label' => 'Hoàn tất',       'class' => 'label-success', 'color' => '#28a745'],
];
$allLabels = $statusLabels + [-1 => ['label' => 'Bị từ chối', 'class' => 'label-danger', 'color' => '#dc3545']];

/*
 * LOGIC MỚI: Mỗi bước đều hiển thị Yes / No.
 * - Yes  → cập nhật bước tiếp (status tăng lên 1)
 * - No   → tạo báo cáo không phù hợp; bước đó bị đánh dấu "no"; xong rồi mới cho tiếp
 *
 * Mỗi bước có 2 trường:
 *   {step}_status  : 'yes' | 'no' | '' (chưa làm)
 *   {step}_report  : id report (khi No)
 *
 * Bước chỉ được thực hiện khi tất cả bước trước đã Done tức là:
 *   bước trước status='yes' HOẶC (status='no' VÀ đã có report hoàn thành)
 *
 * Trong bảng DB hiện tại, ta dùng:
 *   Bước 1: qa_approve_staff / qa_approve_date  → nếu status>=1 tức qa đã yes
 *   Bước 2: bv_out_staff / bv_out_date          → status>=2
 *   Bước 3: bv_in_staff / bv_in_date            → status>=3
 *   Bước 4: qa_done_staff / qa_done_date        → status>=4
 *
 * Các bước "no" → ta dùng trường step_no_status và step_no_report_id
 * (cần thêm cột DB nếu chưa có, nhưng trước mắt dùng violation_code để đánh dấu)
 *
 * QUAN TRỌNG: Theo yêu cầu:
 *   - Tất cả 4 bước LUÔN hiển thị Yes / No
 *   - Bước hiện tại (= status + 1 nếu chưa làm) mới được bấm
 *   - Bước trước đã Yes thì hiện badge "Đã duyệt" không cho bấm
 *   - Bước trước No nhưng chưa có report → CHẶN bước kế
 *   - Khi bấm No → mở form tạo báo cáo không phù hợp
 */

// Xác định bước hiện tại có thể thao tác
// Nếu status = 4 → hoàn tất hết, không bước nào được thao tác
$currentActionStep = $status + 1; // bước kế tiếp cần làm (1..4)
if ($status == 4) $currentActionStep = 99; // xong hết
if ($status == -1) $currentActionStep = 99; // bị từ chối toàn bộ

// Mỗi bước: Yes/No. Khi bấm No → mở modal tạo report không phù hợp
// Sau khi report xong → bước đó coi như "done (no)" và được bước tiếp
// Ta dùng các cột step_no_* nếu có, fallback về status logic

$steps = [
    1 => [
        'label'      => 'QA duyệt đi',
        'color'      => '#17a2b8',
        'badge'      => 'label-info',
        'staff_col'  => 'qa_approve_staff',
        'date_col'   => 'qa_approve_date',
        'note_col'   => 'qa_approve_note',
        'yes_done'   => $status >= 1,
        'no_col'     => 'qa_no_status',   // cột check "no"
        'report_col' => 'qa_no_report_id',
    ],
    2 => [
        'label'      => 'BV xác nhận ra',
        'color'      => '#fd7e14',
        'badge'      => 'label-warning',
        'staff_col'  => 'bv_out_staff',
        'date_col'   => 'bv_out_date',
        'note_col'   => 'bv_out_note',
        'yes_done'   => $status >= 2,
        'no_col'     => 'bv_out_no_status',
        'report_col' => 'bv_out_no_report_id',
    ],
    3 => [
        'label'      => 'BV xác nhận về',
        'color'      => '#007bff',
        'badge'      => 'label-primary',
        'staff_col'  => 'bv_in_staff',
        'date_col'   => 'bv_in_date',
        'note_col'   => 'bv_in_note',
        'yes_done'   => $status >= 3,
        'no_col'     => 'bv_in_no_status',
        'report_col' => 'bv_in_no_report_id',
    ],
    4 => [
        'label'      => 'QA hoàn tất',
        'color'      => '#28a745',
        'badge'      => 'label-success',
        'staff_col'  => 'qa_done_staff',
        'date_col'   => 'qa_done_date',
        'note_col'   => 'qa_done_note',
        'yes_done'   => $status >= 4,
        'no_col'     => 'qa_done_no_status',
        'report_col' => 'qa_done_no_report_id',
    ],
];

// Hàm kiểm tra bước có thể thao tác (is_actionable):
// Bước N có thể thao tác khi:
//   - Các bước 1..(N-1) đã YES (status >= N-1 + ... thực ra status >= N-1 nếu dạng tuần tự)
//   - Hoặc bước trước là NO nhưng đã có report hoàn thành → thì cũng cho bước N
// Trong implementation đơn giản: dùng cột step_no_status để biết bước trước là no/yes:
// Ta giả lập đơn giản: bước N thao tác được khi status == N-1

// Nhưng theo yêu cầu mới: tất cả đều hiện Yes/No, bước nào đến lượt mới active
// "Đến lượt" = bước N khi status == N-1
// Nếu bước N đến lượt mà bấm No → status vẫn N-1 nhưng đánh no_report
// (cần API riêng set_no_status)
//
// HIỆN TẠI (đơn giản hóa với DB hiện có):
// - Khi bấm Yes  → gọi approve với next = N   (tiến lên)
// - Khi bấm No   → hiển thị form tạo báo cáo vi phạm
//               → sau khi tạo xong, bước đó vẫn status N-1 nhưng có no_report
//               → bấm tiếp Yes để qua bước N
// Ta dùng trường 'step_no_*' nếu có trong $dtData, nếu không có thì ẩn logic no-report
?>
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body tnh-modal-body">

            <!-- ===== THANH TIẾN TRÌNH LUỒNG ===== -->
            <div class="flow-progress-bar mbot15">
                <?php foreach ($statusLabels as $st => $info): ?>
                    <?php
                    if ($status == -1) {
                        $nodeClass = 'pending';
                    } elseif ($status == $st) {
                        $nodeClass = 'current';
                    } elseif ($status > $st) {
                        $nodeClass = 'done';
                    } else {
                        $nodeClass = 'pending';
                    }
                    ?>
                    <div class="flow-node <?= $nodeClass ?>">
                        <div class="flow-circle" style="border-color:<?= $info['color'] ?>; <?= $status >= $st ? 'background:' . $info['color'] . '; color:#fff;' : '' ?>">
                            <?php if ($status > $st): ?>
                                <i class="fa fa-check"></i>
                            <?php else: ?>
                                <?= $st ?>
                            <?php endif; ?>
                        </div>
                        <div class="flow-label" style="color:<?= $info['color'] ?>"><?= $info['label'] ?></div>
                    </div>
                    <?php if ($st < 4): ?><div class="flow-connector <?= $status > $st ? 'done' : '' ?>"></div><?php endif; ?>
                <?php endforeach; ?>
            </div>

            <!-- ===== THÔNG TIN PHIẾU ===== -->
            <div class="row">
                <div class="col-md-4">
                    <table class="table table-condensed table-hover table-striped">
                        <tr>
                            <td style="width:140px"><strong><?= lang('Mã Phiếu') ?>:</strong></td>
                            <td><span class="text-info" style="font-weight:bold;"><?= $dtData['reference_no'] ?? '' ?></span></td>
                        </tr>
                        <tr>
                            <td><strong><?= lang('Ngày Lập') ?>:</strong></td>
                            <td><?= !empty($dtData['date']) ? _dt($dtData['date']) : '' ?></td>
                        </tr>
                        <tr>
                            <td><strong><?= lang('Ưu tiên') ?>:</strong></td>
                            <td><span class="label <?= (($dtData['priority'] ?? '') == 'URGENT' ? 'label-danger' : 'label-default') ?>"><?= $dtData['priority'] ?? 'NORMAL' ?></span></td>
                        </tr>
                        <tr>
                            <td><strong><?= lang('Nhân Viên') ?>:</strong></td>
                            <td><?= $dtData['fullname'] ?? '' ?> (<?= $dtData['staff_code'] ?? '' ?>)</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <table class="table table-condensed table-hover table-striped">
                        <tr>
                            <td style="width:140px"><strong><?= lang('Đối tác') ?>:</strong></td>
                            <td><?= $dtData['partner_name'] ?? '' ?></td>
                        </tr>
                        <tr>
                            <td><strong><?= lang('Người thực hiện') ?>:</strong></td>
                            <td><?= $dtData['executor_name'] ?? '' ?> (<?= $dtData['executor_phone'] ?? '' ?>)</td>
                        </tr>
                        <tr>
                            <td><strong><?= lang('Phòng Ban') ?>:</strong></td>
                            <td><?= $dtData['name_departments'] ?? '' ?></td>
                        </tr>
                        <tr>
                            <td><strong><?= lang('Vị Trí') ?>:</strong></td>
                            <td><?= $dtData['name_roles'] ?? '' ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <table class="table table-condensed table-hover table-striped">
                        <tr>
                            <td style="width:140px"><strong><?= lang('Trạng Thái') ?>:</strong></td>
                            <td>
                                <span class="label <?= $allLabels[$status]['class'] ?>">
                                    <?= $allLabels[$status]['label'] ?>
                                </span>
                                <?php if (!empty($dtData['violation_code'])): ?>
                                    <div class="text-danger" style="font-size:11px; font-weight:bold;">VIOLATION: <?= $dtData['violation_code'] ?></div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?= lang('Ngày đi (KH)') ?>:</strong></td>
                            <td><?= !empty($dtData['planned_date_out']) ? _d($dtData['planned_date_out']) : '' ?></td>
                        </tr>
                        <tr>
                            <td><strong><?= lang('Ngày về (KH)') ?>:</strong></td>
                            <td><?= !empty($dtData['planned_date_return']) ? _d($dtData['planned_date_return']) : '' ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <hr class="mtop5 mbot5" />

            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading" style="padding: 5px 10px; font-weight:bold;">
                            <i class="fa fa-cube"></i> Thông tin hàng hóa
                        </div>
                        <div class="panel-body">
                            <p><strong>Loại hàng:</strong> <?= $dtData['item_type'] ?? '' ?></p>
                            <p><strong>Mã/Tên hàng:</strong> <?= $dtData['item_code_name'] ?? '' ?></p>
                            <p><strong>Số lượng:</strong> <?= $dtData['quantity'] ?? 0 ?> | <strong>Kiện:</strong> <?= $dtData['package_count'] ?? 0 ?> | <strong>Kg:</strong> <?= $dtData['kg_weight'] ?? 0 ?></p>
                            <p><strong>Lý do:</strong> <span class="text-warning"><?= $dtData['note_reason'] ?? '' ?></span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading" style="padding: 5px 10px; font-weight:bold;">
                            <i class="fa fa-truck"></i> Vận chuyển &amp; Lộ trình
                        </div>
                        <div class="panel-body">
                            <p><strong>Phương tiện:</strong> <?= $dtData['vehicle_type'] ?? '' ?> | <strong>Biển số:</strong> <?= $dtData['license_plate'] ?? '' ?></p>
                            <p><strong>Tài xế:</strong> <?= $dtData['driver_name'] ?? '' ?></p>
                            <p><strong>Lộ trình:</strong> <?= $dtData['route'] ?? '' ?></p>
                            <p><strong>Chi phí:</strong> <?= number_format($dtData['route_price'] ?? 0) ?> VNĐ</p>
                            <p><strong>Chứng từ:</strong>
                                <?= !empty($dtData['doc_delivery_signed']) ? '<span class="label label-info">Giao hàng ký nhận</span> ' : '' ?>
                                <?= !empty($dtData['doc_invoice']) ? '<span class="label label-info">Hóa đơn</span> ' : '' ?>
                                <?= !empty($dtData['doc_handover']) ? '<span class="label label-info">BB bàn giao</span> ' : '' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== LỊCH SỬ PHÊ DUYỆT & HÀNH ĐỘNG ===== -->
            <div class="panel panel-info">
                <div class="panel-heading" style="padding: 5px 10px; font-weight:bold;">
                    <i class="fa fa-history"></i> Lịch sử phê duyệt &amp; Xác nhận
                </div>
                <div class="panel-body" style="padding:0;">
                    <table class="table table-bordered table-condensed mbot0" id="table-history">
                        <thead>
                            <tr class="active">
                                <th style="width:160px">Bước</th>
                                <th>Người thực hiện</th>
                                <th style="width:145px">Thời gian</th>
                                <th>Ghi chú / Checklist</th>
                                <th style="width:90px" class="text-center">Kết quả</th>
                                <th style="width:150px" class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                // TÍNH TOÁN LOGICAL STATUS NHỜ BÁO CÁO KHÔNG PHÙ HỢP
                                $logicalStatus = $status;
                                $hasIncompleteReport = false;
                                for ($s = $status + 1; $s <= 4; $s++) {
                                    $this->db->select('production_report_id,status_production_report');
                                    $this->db->where('entrance_ticket_id', $dtData['id']);
                                    $this->db->where('step', $s);
                                    $rep = $this->db->get('tbl_entrance_ticket_step')->row_array();
                                    if (!empty($rep)) {
                                        $logicalStatus = $s;
                                        if ($rep['status_production_report'] == 0) {
                                            $hasIncompleteReport = true;
                                        }
                                    } else {
                                        break;
                                    }
                                }

                                foreach ($steps as $stepNum => $step):
                                // Xác định trạng thái của bước này
                                // step yes_done: đã vượt qua (status >= stepNum)
                                $yesDone   = ($status >= $stepNum);
                                $isActive  = ($logicalStatus == $stepNum - 1); // đến lượt bước này
                                $isFuture  = ($logicalStatus < $stepNum - 1);

                                // Kiểm tra bước này đã có người thực hiện chưa (dù yes hay no)
                                $staffDone = !empty($dtData[$step['staff_col']]);

                                // Kiểm tra bước có no-report không
                                // Ta dùng qa_approve_note / bv_out_note... check "[TỪ CHỐI]" hoặc cột riêng
                                $noteVal  = $dtData[$step['note_col']] ?? '';
                                $isNoStep = (strpos($noteVal, '[TỪ CHỐI]') !== false || strpos($noteVal, '[KHÔNG ĐẠT]') !== false);

                                // Report action: link tạo báo cáo không phù hợp khi bước này là No
                                // Ta dùng $dtData['step_report_col'] nếu tồn tại
                                // $noReportId = $dtData[$step['report_col']] ?? null;
                                $this->db->select('production_report_id,status_production_report');
                                $this->db->where('entrance_ticket_id', $dtData['id']);
                                $this->db->where('step', $stepNum);
                                $noReportId = $this->db->get('tbl_entrance_ticket_step')->row_array();
                                $hasReport = !empty($noReportId);
                            ?>
                                <tr class="step-row <?= $isNoStep ? 'step-no' : ($yesDone ? 'step-yes' : '') ?>"
                                    data-step="<?= $stepNum ?>"
                                    data-status="<?= $isNoStep ? 'no' : ($yesDone ? 'yes' : '') ?>">
                                    <td>
                                        <span class="label <?= $step['badge'] ?>"><?= $stepNum ?>. <?= $step['label'] ?></span>
                                    </td>
                                    <td>
                                        <?= !empty($dtData[$step['staff_col']]) ? get_staff_full_name($dtData[$step['staff_col']]) : '<span class="text-muted">Chưa thực hiện</span>' ?>
                                    </td>
                                    <td>
                                        <?= !empty($dtData[$step['date_col']]) ? _dt($dtData[$step['date_col']]) : '' ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($noteVal)): ?>
                                            <div class="<?= $isNoStep ? 'text-danger' : 'text-muted' ?>" style="font-size:12px;"><?= $noteVal ?></div>
                                        <?php endif; ?>
                                        <?php if ($yesDone): ?>
                                            <?php if ($stepNum == 1): ?>
                                                <div class="text-info" style="font-size:11px;">
                                                    Checklist: <?= !empty($dtData['qa_out_valid']) ? 'Hợp lệ' : 'K.Hợp lệ' ?> | <?= !empty($dtData['qa_out_allow']) ? 'Cho phép' : 'Chặn' ?>
                                                </div>
                                            <?php elseif ($stepNum == 2): ?>
                                                <div class="text-info" style="font-size:11px;">
                                                    Checklist: <?= !empty($dtData['bv_out_match']) ? 'Đúng Người/Xe/Hàng' : 'Sai thông tin' ?>
                                                </div>
                                            <?php elseif ($stepNum == 3): ?>
                                                <div class="text-info" style="font-size:11px;">
                                                    Checklist: <?= !empty($dtData['bv_return_goods_ok']) ? 'Đúng hàng' : 'Sai hàng' ?> | <?= !empty($dtData['bv_return_docs_ok']) ? 'Đủ C.Từ' : 'Thiếu C.Từ' ?> | <?= !empty($dtData['bv_return_qty_ok']) ? 'Đúng SL' : 'Sai SL' ?>
                                                </div>
                                            <?php elseif ($stepNum == 4): ?>
                                                <div class="text-info" style="font-size:11px;">
                                                    Checklist: <?= !empty($dtData['qa_close_goods_ok']) ? 'Đúng hàng' : 'Sai hàng' ?> | <?= !empty($dtData['qa_close_docs_ok']) ? 'Đủ C.Từ' : 'Thiếu C.Từ' ?> | <?= !empty($dtData['qa_close_qty_ok']) ? 'Đúng SL' : 'Sai SL' ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if ($isNoStep): ?>
                                            <!-- Hiển thị link tạo / xem báo cáo không phù hợp -->
                                            <div class="mtop5 report-action-area" data-step="<?= $stepNum ?>">
                                                <?php if (!empty($noReportId)): ?>
                                                    <?php
                                                    $html = '';
                                                    if ($noReportId['status_production_report'] == 0) {
                                                        $html = '<span class="label label-warning">Chưa hoàn thành</span>';
                                                    } else {
                                                        $html = '<span class="label label-success">Hoàn thành</span>';
                                                    }
                                                    ?>
                                                    <?= $html ?>
                                                    <br><br><a href="<?= admin_url('production_report/modal/' . $noReportId['production_report_id']) ?>" class="c_modal btn btn-xs btn-info">
                                                        <i class="fa fa-file-text-o"></i> Xem báo cáo không phù hợp
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= admin_url('production_report/detail?entrance_ticket_id=' . ($dtData['id'] ?? 0) . '&step=' . $stepNum) ?>" target="_blank" class="btn btn-xs btn-warning create-violation-report" data-step="<?= $stepNum ?>" data-id="<?= $dtData['id'] ?? 0 ?>">
                                                        <i class="fa fa-plus"></i> Tạo báo cáo không phù hợp
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($isNoStep): ?>
                                            <span class="label label-danger"><i class="fa fa-times"></i> Không đạt</span>
                                        <?php elseif ($yesDone): ?>
                                            <span class="label label-success"><i class="fa fa-check"></i> Đạt</span>
                                        <?php else: ?>
                                            <span class="label label-default">Chờ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($isNoStep && $hasReport): ?>
                                            <!-- Báo cáo đã tạo → khóa hành động -->
                                            <small class="text-info">Đã tạo báo cáo (bước bị khóa)</small>
                                        <?php elseif ($yesDone): ?>
                                            <small class="text-muted">Đã hoàn thành</small>
                                        <?php elseif ($isFuture): ?>
                                            <small class="text-muted">Chưa đến lượt</small>
                                        <?php elseif ($isActive): ?>
                                            <?php if ($isNoStep && !$hasReport): ?>
                                                <!-- Đã No nhưng chưa có report: Cho override hoặc hiện nút No -->
                                                <div class="btn-group btn-step-action">
                                                    <button class="btn btn-xs btn-success btn-step-yes"
                                                        data-id="<?= $dtData['id'] ?? 0 ?>"
                                                        data-next="<?= $stepNum ?>"
                                                        data-incomplete="<?= $hasIncompleteReport ? '1' : '0' ?>"
                                                        title="Duyệt qua bước này (override No)">
                                                        <i class="fa fa-check"></i> Yes (override)
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <!-- Bước đến lượt → hiển thị Yes / No -->
                                                <div class="btn-group btn-step-action">
                                                    <button class="btn btn-xs btn-success btn-step-yes"
                                                        data-id="<?= $dtData['id'] ?? 0 ?>"
                                                        data-next="<?= $stepNum ?>"
                                                        data-incomplete="<?= $hasIncompleteReport ? '1' : '0' ?>"
                                                        title="Đạt – Duyệt bước này">
                                                        <i class="fa fa-check"></i> Yes
                                                    </button>
                                                    <button class="btn btn-xs btn-danger btn-step-no"
                                                        data-id="<?= $dtData['id'] ?? 0 ?>"
                                                        data-step="<?= $stepNum ?>"
                                                        data-incomplete="<?= $hasIncompleteReport ? '1' : '0' ?>"
                                                        title="Không đạt – Tạo báo cáo không phù hợp">
                                                        <i class="fa fa-times"></i> No
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Nút In phiếu -->
            <div class="mtop10">
                <a href="<?= admin_url('entrance_ticket/print_ticket/' . $dtData['id']) ?>" target="_blank" class="btn btn-default btn-sm">
                    <i class="fa fa-print"></i> In phiếu
                </a>
            </div>

            <!-- ===== NGƯỜI TẠO / SỬA ===== -->
            <div class="row mtop15">
                <div class="col-md-6 pull-right">
                    <div class="panel panel-default panel-body" style="font-size:12px; color:#666; padding: 10px;">
                        <div>Tạo bởi: <strong><?= get_staff_full_name($dtData['staff_create'] ?? 0) ?></strong> – <?= !empty($dtData['date_create']) ? _dt($dtData['date_create']) : '' ?></div>
                        <?php if (!empty($dtData['staff_update'])): ?>
                            <div>Sửa bởi: <strong><?= get_staff_full_name($dtData['staff_update']) ?></strong> – <?= _dt($dtData['date_update']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div><!-- /.modal-body -->

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
                <i class="fa fa-times"></i> <?= lang('close') ?>
            </button>
        </div>
    </div>
</div>

<!-- Modal nhập ghi chú & Checklist khi bấm YES -->
<div class="modal fade" id="view-note-modal" tabindex="-1" role="dialog" style="z-index: 1060;">
    <div class="modal-dialog " role="document" style="width: 30%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close btn-close-note">&times;</button>
                <h4 class="modal-title" id="view-note-title">Xác nhận</h4>
            </div>
            <div class="modal-body">
                <!-- Checklist dynamic area -->
                <div id="checklist-area" class="mbot15" style="background: #f9f9f9; padding: 10px; border-radius: 5px; display: none;">
                    <label style="font-weight: bold; color: #1a73e8;"><i class="fa fa-check-square-o"></i> Checklist bắt buộc:</label>
                    <div id="checklist-items"></div>
                </div>

                <div class="form-group">
                    <label for="view_approve_note">Ghi chú thêm:</label>
                    <textarea id="view_approve_note" class="form-control" rows="3" placeholder="Ghi chú (tùy chọn)..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-close-note">Hủy</button>
                <button type="button" class="btn btn-success" id="btn-confirm-view"><i class="fa fa-check"></i> Xác nhận Đạt (Yes)</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal nhập lý do khi bấm NO -->
<div class="modal fade" id="view-no-modal" tabindex="-1" role="dialog" style="z-index: 1060;">
    <div class="modal-dialog " role="document" style="width: 30%;">
        <div class="modal-content">
            <div class="modal-header" style="background:#dc3545; color:#fff;">
                <button type="button" class="close btn-close-no" style="color:#fff;">&times;</button>
                <h4 class="modal-title" id="view-no-title"><i class="fa fa-times-circle"></i> Không đạt – Ghi nhận vi phạm</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning" style="font-size:13px;">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>Khi chọn "No":</strong> Hệ thống sẽ ghi nhận bước này là <strong>Không đạt</strong>.
                    Bạn cần tạo <strong>Báo cáo không phù hợp</strong> để tiếp tục quy trình.
                </div>
                <div class="form-group">
                    <label for="view_no_note"><strong>Lý do không đạt: <span class="text-danger">*</span></strong></label>
                    <textarea id="view_no_note" class="form-control" rows="4" placeholder="Nhập lý do không đạt / vi phạm..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-close-no">Hủy</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-no">
                    <i class="fa fa-times"></i> Xác nhận Không đạt (No) &amp; Tạo báo cáo
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* ===== Thanh tiến trình ===== */
    .flow-progress-bar {
        display: flex;
        align-items: center;
        padding: 15px 10px;
        background: #f8f9fa;
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        flex-wrap: wrap;
        gap: 4px;
    }

    .flow-node {
        text-align: center;
    }

    .flow-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 3px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 4px;
        font-weight: 700;
        font-size: 14px;
        background: #fff;
        color: #aaa;
        transition: all 0.3s;
    }

    .flow-label {
        font-size: 11px;
        font-weight: 600;
    }

    .flow-connector {
        flex: 1;
        height: 1px;
        background: #dee2e6;
        min-width: 20px;
        border-radius: 2px;
        transition: background 0.3s;
    }

    .flow-connector.done {
        background: #28a745;
        height: 2px;
    }

    .flow-node.done .flow-circle {
        background: #28a745 !important;
        border-color: #28a745 !important;
        color: #fff;
    }

    .flow-node.current .flow-circle {
        box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.2);
    }

    /* ===== Step rows ===== */
    tr.step-yes {
        background: #f0fff4;
    }

    tr.step-no {
        background: #fff5f5;
    }

    /* ===== Yes/No buttons ===== */
    .btn-step-yes {
        transition: all 0.2s;
    }

    .btn-step-no {
        transition: all 0.2s;
    }
</style>

<script type="text/javascript">
    $(document).ready(function() {

        /* =====================================================================
         * BTN-STEP-YES: Bấm Yes → mở modal chọn checklist + note rồi gọi approve
         * =================================================================== */
        $(document).off('click', '.btn-step-yes').on('click', '.btn-step-yes', function() {
            if ($(this).data('incomplete') == '1') {
                alert('Vui lòng hoàn thành quy trình báo cáo thì mới bấm được!');
                return false;
            }

            var id = $(this).data('id');
            var next = $(this).data('next');

            var title = "Xác nhận Đạt";
            var checklistHtml = "";

            if (next == 1) {
                title = "QA Duyệt đi";
                checklistHtml = `
                <div class="checkbox"><label><input type="checkbox" id="qa_out_valid" checked> Thông tin hợp lệ</label></div>
                <div class="checkbox"><label><input type="checkbox" id="qa_out_allow" checked> Cho phép mang hàng ra</label></div>
            `;
            } else if (next == 2) {
                title = "BV Xác nhận ra";
                checklistHtml = `<div class="checkbox"><label><input type="checkbox" id="bv_out_match" checked> Đúng Người / Xe / Hàng hóa</label></div>`;
            } else if (next == 3) {
                title = "BV Xác nhận về";
                checklistHtml = `
                <div class="checkbox"><label><input type="checkbox" id="bv_return_goods_ok" checked> Hàng quay về đúng thực tế</label></div>
                <div class="checkbox"><label><input type="checkbox" id="bv_return_docs_ok" checked> Chứng từ đầy đủ</label></div>
                <div class="checkbox"><label><input type="checkbox" id="bv_return_qty_ok" checked> Đúng số lượng</label></div>
            `;
            } else if (next == 4) {
                title = "QA Hoàn tất & Đóng phiếu";
                checklistHtml = `
                <div class="checkbox"><label><input type="checkbox" id="qa_close_goods_ok" checked> Hàng hóa OK</label></div>
                <div class="checkbox"><label><input type="checkbox" id="qa_close_docs_ok" checked> Chứng từ OK</label></div>
                <div class="checkbox"><label><input type="checkbox" id="qa_close_qty_ok" checked> Số lượng OK</label></div>
            `;
            }

            if (checklistHtml) {
                $('#checklist-area').show();
                $('#checklist-items').html(checklistHtml);
            } else {
                $('#checklist-area').hide();
            }

            $('#view-note-title').text(title);
            $('#btn-confirm-view').data('id', id).data('next', next);
            $('#view_approve_note').val('');
            $('#view-note-modal').modal('show');
        });

        /* =====================================================================
         * BTN-STEP-NO: Bấm No → mở modal nhập lý do + tạo báo cáo
         * =================================================================== */
        $(document).off('click', '.btn-step-no').on('click', '.btn-step-no', function() {
            if ($(this).data('incomplete') == '1') {
                alert('Vui lòng hoàn thành quy trình báo cáo thì mới bấm được!');
                return false;
            }

            var id = $(this).data('id');
            var step = $(this).data('step');

            var stepLabel = {
                1: 'QA duyệt đi',
                2: 'BV xác nhận ra',
                3: 'BV xác nhận về',
                4: 'QA hoàn tất'
            };
            $('#view-no-title').html('<i class="fa fa-times-circle"></i> Không đạt – Bước ' + step + ': ' + (stepLabel[step] || ''));
            $('#btn-confirm-no').data('id', id).data('step', step);
            $('#view_no_note').val('');
            $('#view-no-modal').modal('show');
        });

        /* =====================================================================
         * ĐÓNG CÁC MODAL PHỤ (không đóng modal chính)
         * =================================================================== */
        $(document).off('click', '.btn-close-note').on('click', '.btn-close-note', function() {
            $('#view-note-modal').modal('hide');
        });
        $(document).off('click', '.btn-close-no').on('click', '.btn-close-no', function() {
            $('#view-no-modal').modal('hide');
        });

        /* =====================================================================
         * CONFIRM YES – Gọi API approve
         * =================================================================== */
        $(document).off('click', '#btn-confirm-view').on('click', '#btn-confirm-view', function() {
            var id = $(this).data('id');
            var next = $(this).data('next');
            var note = $('#view_approve_note').val();
            var btn = $(this);

            var data = {
                id: id,
                status: next,
                note: note
            };

            if (typeof(csrfData) !== 'undefined') {
                data[csrfData.token_name] = csrfData.hash;
            }

            // Thu thập checklist
            if ($('#checklist-area').is(':visible')) {
                $('#checklist-items input[type="checkbox"]').each(function() {
                    data[$(this).attr('id')] = $(this).is(':checked') ? 1 : 0;
                });
            }

            btn.attr('disabled', 'disabled').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...');

            $.ajax({
                url: '<?= admin_url('entrance_ticket/approve') ?>',
                type: 'POST',
                data: data,
                dataType: 'JSON',
                success: function(res) {
                    $('#view-note-modal').modal('hide');
                    btn.removeAttr('disabled').html('<i class="fa fa-check"></i> Xác nhận Đạt (Yes)');
                    if (res.result) {
                        alert_float('success', res.message);
                        _reloadModalAndTable(id);
                    } else {
                        alert_float('danger', res.message);
                    }
                },
                error: function() {
                    $('#view-note-modal').modal('hide');
                    btn.removeAttr('disabled').html('<i class="fa fa-check"></i> Xác nhận Đạt (Yes)');
                    alert_float('danger', 'Lỗi hệ thống!');
                }
            });
        });

        /* =====================================================================
         * CONFIRM NO – Ghi nhận No + mở trang tạo báo cáo
         * =================================================================== */
        $(document).off('click', '#btn-confirm-no').on('click', '#btn-confirm-no', function() {
            var id = $(this).data('id');
            var step = $(this).data('step');
            var note = $('#view_no_note').val().trim();
            var btn = $(this);

            if (!note) {
                alert('Vui lòng nhập lý do không đạt!');
                return;
            }

            var data = {
                id: id,
                status: -1,
                note: '[KHÔNG ĐẠT] ' + note,
                step: step
            };

            if (typeof(csrfData) !== 'undefined') {
                data[csrfData.token_name] = csrfData.hash;
            }

            btn.attr('disabled', 'disabled').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...');

            $.ajax({
                url: '<?= admin_url('entrance_ticket/set_no_step') ?>',
                type: 'POST',
                data: data,
                dataType: 'JSON',
                success: function(res) {
                    $('#view-no-modal').modal('hide');
                    btn.removeAttr('disabled').html('<i class="fa fa-times"></i> Xác nhận Không đạt (No) & Tạo báo cáo');
                    if (res.result) {
                        alert_float('warning', 'Đã ghi nhận Không đạt. Vui lòng tạo báo cáo không phù hợp!');
                        // Reload modal để hiện nút tạo báo cáo
                        _reloadModalAndTable(id);
                        // Mở trang tạo báo cáo trong tab mới
                        var reportUrl = '<?= admin_url('production_report/detail') ?>?entrance_ticket_id=' + id + '&step=' + step;
                        window.open(reportUrl, '_blank');
                    } else {
                        alert_float('danger', res.message);
                    }
                },
                error: function() {
                    $('#view-no-modal').modal('hide');
                    btn.removeAttr('disabled').html('<i class="fa fa-times"></i> Xác nhận Không đạt (No) & Tạo báo cáo');
                    alert_float('danger', 'Lỗi hệ thống!');
                }
            });
        });

        /* =====================================================================
         * Helper: Reload modal + DataTable
         * =================================================================== */
        function _reloadModalAndTable(id) {
            // Reload DataTable
            if (typeof(oTable) !== 'undefined' && oTable !== null) {
                oTable.draw();
            }
            if ($.fn.DataTable.isDataTable('#dt-entrance-ticket')) {
                $('#dt-entrance-ticket').DataTable().ajax.reload(null, false);
            }
            // Reload modal content
            var href = '<?= admin_url('entrance_ticket/view/') ?>' + id;
            $.get(href, function(html) {
                $('#myModal').html(html);
                if (!$('#myModal').is(':visible')) $('#myModal').modal('show');
            });
        }

    });
</script>