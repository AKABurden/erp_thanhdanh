<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head_noMenu(); ?>
<?php
// Status labels / colors  – giống hệt modal task.php
$status_colors = [
    1 => ['label' => _l('task_status_1'), 'class' => 'default'],
    2 => ['label' => _l('task_status_4'), 'class' => 'info'],
    3 => ['label' => _l('task_status_3'), 'class' => 'warning'],
    4 => ['label' => _l('task_status_2'), 'class' => 'primary'],
    5 => ['label' => _l('task_status_5'), 'class' => 'success'],
];
$priority_labels = [
    1 => ['label' => _l('task_priority_low'),    'color' => '#adb5bd'],
    2 => ['label' => _l('task_priority_medium'), 'color' => '#ffc107'],
    3 => ['label' => _l('task_priority_high'),   'color' => '#fd7e14'],
    4 => ['label' => _l('task_priority_urgent'), 'color' => '#dc3545'],
];
$cur_status   = $task->status ?? 1;
$cur_priority = $task->priority ?? 2;

// Relation info
$rel_type = $task->rel_type ?? '';
$rel_id   = $task->rel_id   ?? '';
$rel_name = '';
if ($rel_id && $rel_type) {
    $rel_data = get_relation_data($rel_type, $rel_id);
    $rel_val  = get_relation_values($rel_data, $rel_type);
    $rel_name = $rel_val['name'] ?? '';
}

// Tags
$tags = isset($task) ? get_tags_in($task->id, 'task') : [];

// Departments for this task
$task_departments = $this->db->get_where('tbltask_department', ['task_id' => $task->id])->result_array();
$dept_names = [];
if (!empty($task_departments)) {
    foreach ($task_departments as $td) {
        $dept = $this->db->get_where('tbldepartments', ['departmentid' => $td['department_id']])->row_array();
        if ($dept) $dept_names[] = $dept['name'];
    }
}

// Assignees
$assignees = $this->db->get_where('tbltask_assigned', ['taskid' => $task->id])->result_array();
$assignee_names = [];
foreach ($assignees as $a) {
    $full = get_staff_full_name($a['staffid']);
    if ($full) $assignee_names[] = $full;
}
?>
<style>
    /* ====== MOBILE TASK DETAIL – GIỐNG HỆT MODAL, DẠNG FULL PAGE ====== */
    .mtv-wrap {
        background: #f5f6fa;
        min-height: 100vh;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        padding-bottom: 20px;
    }

    /* ---- Top bar ---- */
    .mtv-topbar {
        position: sticky;
        top: 0;
        z-index: 999;
        background: #fff;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        align-items: center;
        height: 52px;
        padding: 0 10px;
        gap: 8px;
    }

    .mtv-topbar-back {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        color: #495057;
        text-decoration: none;
        font-size: 18px;
        flex-shrink: 0;
    }

    .mtv-topbar-back:hover {
        background: #f8f9fa;
        color: #212529;
    }

    .mtv-topbar-title {
        flex: 1;
        font-size: 16px;
        font-weight: 700;
        color: #212529;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .mtv-topbar-actions {
        display: flex;
        gap: 6px;
        flex-shrink: 0;
    }

    .mtv-topbar-actions a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        font-size: 15px;
        text-decoration: none;
        color: #495057;
        border: 1px solid #dee2e6;
        background: #fff;
        transition: background .15s;
    }

    .mtv-topbar-actions a:hover {
        background: #f0f1f3;
    }

    .mtv-topbar-actions a.btn-edit {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }

    .mtv-topbar-actions a.btn-edit:hover {
        background: #0b5ed7;
    }

    /* ---- Status + Priority strip ---- */
    .mtv-strip {
        background: #fff;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        flex-wrap: wrap;
    }

    .mtv-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .mtv-badge.label-default {
        background: #e9ecef;
        color: #495057;
    }

    .mtv-badge.label-info {
        background: #cff4fc;
        color: #0c7a9e;
    }

    .mtv-badge.label-warning {
        background: #fff3cd;
        color: #856404;
    }

    .mtv-badge.label-primary {
        background: #cfe2ff;
        color: #0a428a;
    }

    .mtv-badge.label-success {
        background: #d1e7dd;
        color: #0a3622;
    }

    /* ---- Card block (giống modal-body nội dung theo từng nhóm) ---- */
    .mtv-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0;
        margin-bottom: 8px;
    }

    .mtv-card-header {
        padding: 10px 14px 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .7px;
        color: #6c757d;
        border-bottom: 1px solid #f0f1f3;
    }

    /* ---- Row giống .row > col-md-6/.col-md-12 của modal ---- */
    .mtv-row {
        display: flex;
        flex-wrap: wrap;
    }

    .mtv-col {
        width: 50%;
        padding: 10px 14px;
        border-bottom: 1px solid #f0f1f3;
        box-sizing: border-box;
    }

    .mtv-col:nth-child(odd) {
        border-right: 1px solid #f0f1f3;
    }

    .mtv-col.full {
        width: 100%;
        border-right: none;
    }

    /* Trên mobile nhỏ: 1 cột */
    @media (max-width: 460px) {
        .mtv-col {
            width: 100% !important;
            border-right: none !important;
        }
    }

    .mtv-col:last-child {
        border-bottom: none;
    }

    .mtv-field-label {
        font-size: 11px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 3px;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .mtv-field-value {
        font-size: 14px;
        color: #212529;
        line-height: 1.45;
        word-break: break-word;
    }

    .mtv-field-value.empty {
        color: #adb5bd;
        font-style: italic;
    }

    /* ---- Description ---- */
    .mtv-desc {
        padding: 12px 14px;
        font-size: 14px;
        color: #212529;
        line-height: 1.6;
    }

    .mtv-desc.empty-desc {
        color: #adb5bd;
        font-style: italic;
    }

    /* ---- Tags ---- */
    .mtv-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        padding: 10px 14px 12px;
    }

    .mtv-tag {
        background: #e9ecef;
        color: #495057;
        border-radius: 12px;
        padding: 3px 10px;
        font-size: 12px;
        font-weight: 500;
    }

    /* ---- Assignees avatar row ---- */
    .mtv-persons {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 10px 14px;
    }

    .mtv-person {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #f8f9fa;
        border-radius: 20px;
        padding: 4px 10px 4px 4px;
        font-size: 13px;
        color: #343a40;
    }

    .mtv-person-avatar {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #0d6efd;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* ---- Progress checklist ---- */
    .mtv-checklist-bar {
        padding: 10px 14px;
    }

    .mtv-checklist-bar .bar-label {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 4px;
    }

    .mtv-checklist-bar .progress {
        height: 6px;
        border-radius: 3px;
        background: #dee2e6;
        overflow: hidden;
    }

    .mtv-checklist-bar .progress-bar {
        background: #0d6efd;
        border-radius: 3px;
    }

    /* ---- Checklist item interactive ---- */
    .mtv-ci {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px 14px;
        border-top: 1px solid #f0f1f3;
        position: relative;
    }

    .mtv-ci-icon {
        font-size: 20px;
        flex-shrink: 0;
        margin-top: 1px;
        cursor: pointer;
        transition: transform .15s;
    }

    .mtv-ci-icon:active {
        transform: scale(1.25);
    }

    .mtv-ci-body {
        flex: 1;
        min-width: 0;
    }

    .mtv-ci-desc {
        font-size: 13px;
        color: #212529;
        line-height: 1.45;
        word-break: break-word;
    }

    .mtv-ci-desc.done {
        text-decoration: line-through;
        color: #6c757d;
    }

    .mtv-ci-checked-by {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 4px;
        font-size: 11px;
        color: #6c757d;
    }

    .mtv-ci-checked-by img {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        object-fit: cover;
    }

    .mtv-ci-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 5px;
    }

    .mtv-ci-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid;
        cursor: pointer;
    }

    .mtv-ci-btn.blue {
        color: #0d6efd;
        border-color: #0d6efd;
    }

    .mtv-ci-btn.red {
        color: #dc3545;
        border-color: #dc3545;
    }

    .mtv-ci-btn.orange {
        color: #ffc107;
        border-color: #ffc107;
    }

    /* ---- Alert billed ---- */
    .mtv-alert-billed {
        margin: 10px 14px;
        padding: 10px 14px;
        background: #d1e7dd;
        color: #0a3622;
        border-radius: 6px;
        font-size: 13px;
        border: 1px solid #a3cfbb;
    }
</style>

<div class="mtv-wrap">

    <!-- ====== TOP BAR ====== -->
    <div class="mtv-topbar">
        <a href="<?php echo admin_url('tasks'); ?>" class="mtv-topbar-back" title="Quay lại">
            <i class="fa fa-angle-left"></i>
        </a>
        <div class="mtv-topbar-title"><?php echo $task->name; ?></div>
        <div class="mtv-topbar-actions">
        </div>
    </div>

    <!-- ====== STATUS + PRIORITY STRIP ====== -->
    <div class="mtv-strip">
        <?php if (isset($task->billed) && $task->billed == 1): ?>
            <div class="mtv-alert-billed">
                <?php echo _l('task_is_billed', '<a href="' . admin_url('invoices/list_invoices/' . $task->invoice_id) . '" target="_blank">' . format_invoice_number($task->invoice_id)) . '</a>'; ?>
            </div>
        <?php endif; ?>

        <?php
        $sl = $status_colors[$cur_status] ?? $status_colors[1];
        ?>
        <span class="mtv-badge label-<?= $sl['class'] ?>">
            <i class="fa fa-circle"></i> <?= $sl['label'] ?>
        </span>

        <?php if (isset($priority_labels[$cur_priority])): ?>
            <span class="mtv-badge" style="background:<?= $priority_labels[$cur_priority]['color'] ?>22;color:<?= $priority_labels[$cur_priority]['color'] ?>">
                <i class="fa fa-flag"></i> <?= $priority_labels[$cur_priority]['label'] ?>
            </span>
        <?php endif; ?>

        <?php if (!empty($task->is_public)): ?>
            <span class="mtv-badge label-default"><i class="fa fa-globe"></i> <?= _l('task_public') ?></span>
        <?php endif; ?>
    </div>

    <!-- ====== THÔNG TIN CƠ BẢN (giống row col-md-6 của modal) ====== -->
    <div class="mtv-card" style="margin-top:8px;">
        <div class="mtv-card-header">Thông tin công việc</div>

        <!-- Tiêu đề - full width -->
        <div class="mtv-row">
            <div class="mtv-col full" style="border-bottom:1px solid #f0f1f3;">
                <div class="mtv-field-label"><?= _l('task_add_edit_subject') ?></div>
                <div class="mtv-field-value" style="font-size:15px;font-weight:600;"><?= htmlspecialchars($task->name) ?></div>
            </div>
        </div>

        <!-- Mã công việc + Phòng ban -->
        <div class="mtv-row">
            <div class="mtv-col">
                <div class="mtv-field-label">Mã công việc</div>
                <div class="mtv-field-value">
                    <?php
                    if (!empty($task->category_tasks)) {
                        $cat = $this->db->get_where('tblcategory_tasks', ['id' => $task->category_tasks])->row_array();
                        echo $cat ? htmlspecialchars($cat['code']) : '<span class="empty">—</span>';
                    } else {
                        echo '<span class="empty">—</span>';
                    }
                    ?>
                </div>
            </div>
            <div class="mtv-col">
                <div class="mtv-field-label">Phòng ban phụ trách</div>
                <div class="mtv-field-value">
                    <?php if (!empty($dept_names)):
                        echo implode(', ', array_map('htmlspecialchars', $dept_names));
                    else: ?><span class="empty">—</span><?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Ngày bắt đầu + Ngày hết hạn -->
        <div class="mtv-row">
            <div class="mtv-col">
                <div class="mtv-field-label"><?= _l('task_add_edit_start_date') ?></div>
                <div class="mtv-field-value">
                    <?= !empty($task->startdate) ? _dt($task->startdate) : '<span class="empty">—</span>' ?>
                </div>
            </div>
            <div class="mtv-col">
                <div class="mtv-field-label"><?= _l('task_add_edit_due_date') ?></div>
                <div class="mtv-field-value">
                    <?php if (!empty($task->duedate)): ?>
                        <?php
                        $due_ts  = strtotime($task->duedate);
                        $now_ts  = time();
                        $overdue = ($cur_status != 5 && $due_ts < $now_ts);
                        ?>
                        <span <?= $overdue ? 'style="color:#dc3545;font-weight:600;"' : '' ?>>
                            <?= _dt($task->duedate) ?>
                            <?= $overdue ? ' <i class="fa fa-exclamation-circle"></i>' : '' ?>
                        </span>
                    <?php else: ?><span class="empty">—</span><?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Chi nhánh + Loại phiếu -->
        <div class="mtv-row">
            <div class="mtv-col">
                <div class="mtv-field-label">Chi nhánh</div>
                <div class="mtv-field-value">
                    <?php
                    if (!empty($task->id_branch)) {
                        $br = $this->db->get_where('tblbranch', ['id' => $task->id_branch])->row_array();
                        echo $br ? htmlspecialchars($br['name']) : '<span class="empty">—</span>';
                    } else {
                        echo '<span class="empty">—</span>';
                    }
                    ?>
                </div>
            </div>
            <div class="mtv-col">
                <div class="mtv-field-label">Loại phiếu yêu cầu</div>
                <div class="mtv-field-value">
                    <?php
                    if (!empty($task->category_recommended_id)) {
                        $cr = $this->db->get_where('tbl_category_recommended', ['id' => $task->category_recommended_id])->row_array();
                        echo $cr ? htmlspecialchars($cr['name']) : '<span class="empty">—</span>';
                    } else {
                        echo '<span class="empty">—</span>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Nhóm công việc + Công đoạn -->
        <div class="mtv-row">
            <div class="mtv-col">
                <div class="mtv-field-label">Nhóm công việc</div>
                <div class="mtv-field-value">
                    <?php
                    if (!empty($task->tasks_group)) {
                        $tg = $this->db->get_where('tbl_task_group', ['id' => $task->tasks_group])->row_array();
                        echo $tg ? htmlspecialchars($tg['name']) : '<span class="empty">—</span>';
                    } else {
                        echo '<span class="empty">—</span>';
                    }
                    ?>
                </div>
            </div>
            <div class="mtv-col">
                <div class="mtv-field-label">Công đoạn</div>
                <div class="mtv-field-value">
                    <?php
                    if (!empty($task->stage_id)) {
                        $stg = $this->db->get_where('tbltask_stage', ['id' => $task->stage_id])->row_array();
                        echo $stg ? htmlspecialchars($stg['name']) : '<span class="empty">—</span>';
                    } else {
                        echo '<span class="empty">—</span>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ====== LIÊN QUAN ====== -->
    <?php
    $has_related = $rel_name || !empty($task->order_id) || !empty($task->po_id) || !empty($task->purchase_order_id) || !empty($task->import_id) || !empty($task->suggest_id);
    if ($has_related):
    ?>
        <div class="mtv-card">
            <div class="mtv-card-header">Liên quan đến</div>
            <div class="mtv-row">
                <?php if ($rel_name): ?>
                    <div class="mtv-col">
                        <div class="mtv-field-label"><?= _l('task_related_to') ?></div>
                        <div class="mtv-field-value"><?= htmlspecialchars($rel_name) ?></div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($task->order_id)): ?>
                    <div class="mtv-col">
                        <div class="mtv-field-label">Đơn đặt hàng</div>
                        <div class="mtv-field-value"><?= htmlspecialchars($task->order_id) ?></div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($task->po_id)): ?>
                    <div class="mtv-col">
                        <div class="mtv-field-label">Lệnh sản xuất</div>
                        <div class="mtv-field-value"><?= htmlspecialchars($task->po_id) ?></div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($task->purchase_order_id)): ?>
                    <div class="mtv-col">
                        <div class="mtv-field-label">Đơn đặt hàng mua</div>
                        <div class="mtv-field-value"><?= htmlspecialchars($task->purchase_order_id) ?></div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($task->import_id)): ?>
                    <div class="mtv-col">
                        <div class="mtv-field-label">Nhập kho</div>
                        <div class="mtv-field-value"><?= htmlspecialchars($task->import_id) ?></div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    <?php endif; ?>

    <!-- ====== NGƯỜI PHỤ TRÁCH ====== -->
    <?php if (!empty($assignee_names)): ?>
        <div class="mtv-card">
            <div class="mtv-card-header"><?= _l('task_single_assignees') ?></div>
            <div class="mtv-persons">
                <?php foreach ($assignee_names as $n): ?>
                    <div class="mtv-person">
                        <div class="mtv-person-avatar"><?= strtoupper(mb_substr($n, 0, 1)) ?></div>
                        <?= htmlspecialchars($n) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- ====== LẶP LẠI ====== -->
    <?php
    $repeat_label = '';
    if (!empty($task->recurring)) {
        if ($task->custom_recurring == 1) {
            $repeat_label = $task->repeat_every . ' ' . _l('task_recurring_' . $task->recurring_type . 's');
        } elseif ($task->custom_recurring == 2) {
            $custom_days = $this->db->get_where('tbltasks_repeat_day', ['taskid' => $task->id])->result_array();
            $days = array_column($custom_days, 'day');
            $repeat_label = 'Ngày ' . implode(', ', $days) . ' hàng tháng';
        } else {
            $rmap = [
                '1-week' => '1 tuần',
                '2-week' => '2 tuần',
                '1-month' => '1 tháng',
                '2-month' => '2 tháng',
                '3-month' => '3 tháng',
                '6-month' => '6 tháng',
                '1-year' => '1 năm',
            ];
            $key = $task->repeat_every . '-' . $task->recurring_type;
            $repeat_label = $rmap[$key] ?? ($task->repeat_every . ' ' . $task->recurring_type);
        }
    }
    if ($repeat_label):
    ?>
        <div class="mtv-card">
            <div class="mtv-card-header">Lặp lại</div>
            <div class="mtv-row">
                <div class="mtv-col">
                    <div class="mtv-field-label"><?= _l('task_repeat_every') ?></div>
                    <div class="mtv-field-value"><?= htmlspecialchars($repeat_label) ?></div>
                </div>
                <?php if (!empty($task->cycles) && $task->cycles > 0): ?>
                    <div class="mtv-col">
                        <div class="mtv-field-label"><?= _l('recurring_total_cycles') ?></div>
                        <div class="mtv-field-value">
                            <?= $task->cycles ?>
                            <?php if (!empty($task->total_cycles)): ?>
                                <small style="color:#6c757d;">(<?= _l('cycles_passed', $task->total_cycles) ?>)</small>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($cur_status == 5 && !empty($task->datefinished)): ?>
                    <div class="mtv-col">
                        <div class="mtv-field-label"><?= _l('task_finished') ?></div>
                        <div class="mtv-field-value"><?= _dt($task->datefinished) ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    <!-- ====== MÔ TẢ (giống phần description trong modal-body) ====== -->
    <div class="mtv-card">
        <div class="mtv-card-header"><?= _l('task_add_edit_description') ?></div>
        <?php if (!empty($task->description)): ?>
            <div class="mtv-desc"><?= $task->description ?></div>
        <?php else: ?>
            <div class="mtv-desc empty-desc"><?= _l('task_add_description') ?></div>
        <?php endif; ?>
    </div>
    <!-- ====== CHECKLIST ITEMS ====== -->
    <?php
    // Load checklist with inspection criteria progress (same query as desktop tasks.php)
    $this->db->select('
        tbltask_checklist_items.*,
        COUNT(p.id) as count_process,
        COUNT(CASE WHEN p.isCheck IS NULL AND p.isCheckNot IS NULL THEN 1 END) as count_not_process
    ');
    $this->db->from('tbltask_checklist_items');
    $this->db->join(
        'tbl_tasks_inspection_criteria_process as p',
        'p.process_id = tbltask_checklist_items.process_id AND p.tasks = ' . $this->db->escape($task->id),
        'left'
    );
    $this->db->where('tbltask_checklist_items.taskid', $task->id);
    $this->db->group_by('tbltask_checklist_items.id');
    $checklist_items = $this->db->get()->result_array();

    if (!empty($checklist_items)):
        $total_items    = count($checklist_items);
        $finished_items = count(array_filter($checklist_items, function ($c) {
            return $c['finished'] == 1;
        }));
        $pct = $total_items > 0 ? round($finished_items / $total_items * 100) : 0;
    ?>
        <div class="mtv-card">
            <div class="mtv-card-header"><?= _l('task_checklist_items') ?></div>
            <div class="mtv-checklist-bar">
                <div class="bar-label"><?= $finished_items ?>/<?= $total_items ?> (<?= $pct ?>%)</div>
                <div class="progress">
                    <div class="progress-bar" style="width:<?= $pct ?>%"></div>
                </div>
            </div>

            <?php foreach ($checklist_items as $ci):
                $is_done  = !empty($ci['finished']);
                $ci_color = '';
                if (!empty($task->duedate) && $task->duedate < date('Y-m-d 00:00:00')) {
                    $ci_color = 'color:red;';
                }

                // Label thêm tiến độ tiêu chí nếu có
                $ci_label = htmlspecialchars($ci['description']);
                if (!empty($ci['count_process'])) {
                    $done_cnt = $ci['count_process'] - $ci['count_not_process'];
                    $ci_label .= ' <small style="color:#6c757d;">(' . $done_cnt . '/' . $ci['count_process'] . ')</small>';
                }
            ?>
                <div class="mtv-ci" style="<?= $ci_color ?>">

                    <?php if ($is_done): ?>
                        <!-- Đã check: icon xanh, click để uncheck -->
                        <i class="fa fa-check-square-o mtv-ci-icon"
                            style="color:#198754;"
                            onclick="status_checklist(<?= $ci['id'] ?>, 0, 1)"
                            title="Bỏ hoàn thành"></i>
                    <?php elseif (!empty($ci['stages'])): ?>
                        <!-- Cần bàn giao: mở modal hand_over -->
                        <a href="javascript:void(0)" class="mtv-ci-icon" style="color:#adb5bd;"
                            onclick="mtvModalOpen('<?= admin_url('tasks/hand_over/' . $ci['id']) ?>'); return false;"
                            title="Duyệt bàn giao">
                            <i class="fa fa-check-circle-o" style="<?= $ci_color ?>"></i>
                        </a>
                    <?php else: ?>
                        <!-- Chưa check: mở modal tiêu chí -->
                        <a href="javascript:void(0)" class="mtv-ci-icon" style="color:#adb5bd;"
                            onclick="mtvModalOpen('<?= admin_url('tasks/inspection_criteria/' . $task->id . '/' . $ci['id'] . '/' . (empty($ci['process_id']) ? 1 : $ci['process_id'])) ?>'); return false;"
                            title="Duyệt tiêu chí">
                            <i class="fa fa-check-circle-o" style="<?= $ci_color ?>"></i>
                        </a>
                    <?php endif; ?>

                    <div class="mtv-ci-body">
                        <div class="mtv-ci-desc <?= $is_done ? 'done' : '' ?>"><?= $ci_label ?></div>

                        <?php if ($is_done && !empty($ci['finished_from'])): ?>
                            <div class="mtv-ci-checked-by">
                                <?= staff_profile_image($ci['finished_from'], ['staff-profile-image-small'], 'small') ?>
                                <span><?= get_staff_full_name($ci['finished_from']) ?></span>
                                <?php if (!empty($ci['date_finished'])): ?>
                                    <span style="color:#adb5bd;">· <?= _d($ci['date_finished']) ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="mtv-ci-actions">
                            <?php if ($is_done): ?>
                                <!-- Xem tiêu chí đã duyệt -->
                                <a href="javascript:void(0)" class="mtv-ci-btn blue"
                                    onclick="mtvModalOpen('<?= admin_url('tasks/inspection_criteria/' . $task->id . '/' . $ci['id'] . '/' . (empty($ci['process_id']) ? 1 : $ci['process_id']) . '/' . $ci['finished']) ?>'); return false;">
                                    <i class="fa fa-eye"></i> Xem tiêu chí
                                </a>
                                <!-- Nút xoá check -->
                                <a class="mtv-ci-btn red"
                                    href="javascript:void(0)"
                                    onclick="status_checklist(<?= $ci['id'] ?>, 0, 1); return false;">
                                    <i class="fa fa-times"></i> Bỏ
                                </a>
                                <?php
                                // Bàn giao nếu có stages
                                if (!empty($ci['stages'])) {
                                    $dro = get_table_where('tbl_delivery_records', ['id_create' => $task->id, 'type_create' => 'tasks'], '', 'row_array');
                                    if (!empty($dro)) {
                                        echo '<a class="mtv-ci-btn blue" href="javascript:void(0)" onclick="mtvModalOpen(\'' . admin_url('hand_over/view/' . $dro['id']) . '\'); return false;"><i class="fa fa-handshake-o"></i> ' . $dro['reference_no'] . '</a>';
                                    }
                                }
                                ?>
                            <?php else: ?>
                                <!-- Chưa done: nút duyệt tiêu chí -->
                                <a href="javascript:void(0)" class="mtv-ci-btn blue"
                                    onclick="mtvModalOpen('<?= admin_url('tasks/inspection_criteria/' . $task->id . '/' . $ci['id'] . '/' . (empty($ci['process_id']) ? 1 : $ci['process_id'])) ?>'); return false;">
                                    <i class="fa fa-check-circle-o"></i> Duyệt tiêu chí
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- ====== TAGS ====== -->
    <?php if (!empty($tags)): ?>
        <div class="mtv-card">
            <div class="mtv-card-header"><i class="fa fa-tag"></i> <?= _l('tags') ?></div>
            <div class="mtv-tags">
                <?php foreach ($tags as $tag): ?>
                    <span class="mtv-tag"><?= htmlspecialchars($tag['name']) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>



    <!-- ====== CUSTOM FIELDS ====== -->
    <?php
    $_cf_html = render_custom_fields('tasks', $task->id);
    if (!empty(trim(strip_tags($_cf_html)))):
    ?>
        <div class="mtv-card">
            <div class="mtv-card-header">Trường mở rộng</div>
            <div style="padding:10px 14px;">
                <?php echo $_cf_html; ?>
            </div>
        </div>
    <?php endif; ?>

</div><!-- /mtv-wrap -->

<!-- Modal loading indicator (hiển thị khi đang tải) -->
<div id="mtv-loading-overlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:8px;padding:30px 40px;text-align:center;">
        <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
        <p style="margin-top:10px;color:#666;">Đang tải…</p>
    </div>
</div>
<script>
    /*
     * mtvModalOpen: Load AJAX → inject #view_modal gốc vào DOM → show Bootstrap modal
     * Y hệt cách desktop làm — giữ nguyên style/layout/script gốc.
     */
    window.mtvModalOpen = function(url) {
        var jQ = window.jQuery;
        if (!jQ) {
            window.open(url, '_blank');
            return;
        }

        // Hiện loading
        var $loader = jQ('#mtv-loading-overlay').css('display', 'flex');

        // Xoá modal cũ nếu còn
        jQ('#view_modal').remove();
        jQ('.modal-backdrop').remove();
        jQ('body').removeClass('modal-open').css('padding-right', '');

        jQ.get(url, function(html) {
            $loader.hide();

            // parseHTML với keepScripts=true để giữ script tags
            var $parsed = jQ('<div>').append(jQ.parseHTML(html, document, true));
            var $viewModal = $parsed.find('#view_modal');

            if (!$viewModal.length) {
                window.open(url, '_blank');
                return;
            }

            // ① Inject <style> từ response vào <head> (CSS responsive mobile)
            $parsed.find('style').each(function() {
                jQ('head').append('<style>' + jQ(this).text() + '</style>');
            });

            // ② Inject modal vào body
            jQ('body').append($viewModal);

            // ③ Chạy scripts nhưng bỏ qua dòng modal('show') — ta sẽ show sau
            $parsed.find('script').each(function() {
                var code = jQ(this).html() || '';

                code = code.replace(/\$?\(\s*['"]#view_modal['"]\s*\)\s*\.modal\s*\(\s*['"]show['"]\s*\)\s*;?/g, '');
                code = code.replace(/(['"])#view_modal(['"])/g, '$1#mtv-ajax-modal$2');

                try {
                    jQ.globalEval(code);
                } catch (e) {
                    console.warn('MTV modal script error:', e);
                }
            });

            // ④ Show modal bằng Bootstrap
            jQ('#view_modal').modal({
                backdrop: true,
                keyboard: true,
                show: true
            });

            // ⑤ Khi đóng → dọn sạch + reload
            jQ('#view_modal').off('hidden.bs.modal.mtv').on('hidden.bs.modal.mtv', function() {
                jQ(this).remove();
                jQ('.modal-backdrop').remove();
                jQ('body').removeClass('modal-open').css('padding-right', '');
                window.location.reload();
            });

        }).fail(function() {
            $loader.hide();
            alert('Không tải được nội dung. Vui lòng thử lại.');
        });
    };
</script>
<?php init_tail(); ?>

</html>