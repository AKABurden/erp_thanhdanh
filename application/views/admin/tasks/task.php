<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo form_open_multipart(admin_url('tasks/task/' . $id), array('id' => 'task-form')); ?>

<!-- Giao diện Mobile: Tối ưu Modal thành dạng Full-screen Native cho màn hình nhỏ -->
<style>
@media (max-width: 768px) {
    /* Đưa modal thành full màn hình, tạo trải nghiệm như app mượt mà trên mobile */
    #_task_modal .modal-dialog {
        width: 100% !important;
        height: 100vh !important;
        margin: 0 !important;
        padding: 0 !important;
        max-width: 100% !important;
    }

    #_task_modal .modal-content {
        height: 100vh !important;
        border: none !important;
        border-radius: 0 !important;
        display: flex !important;
        flex-direction: column !important;
    }

    #_task_modal .modal-header {
        background-color: #fff !important;
        border-bottom: 1px solid #e5e7eb !important;
        padding: 15px !important;
        position: sticky !important;
        top: 0 !important;
        z-index: 1050 !important;
        display: flex !important;
        align-items: center;
    }

    #_task_modal .modal-title {
        font-size: 18px !important;
        font-weight: 600 !important;
        margin: 0 !important;
        flex: 1;
    }

    /* QUY TRÌNH: XẾP DỌC (1 CỘT) TRÊN MOBILE - Override Bootstrap grid */
    #_task_modal .modal-body {
        flex: unset !important;
        display: block !important;
        overflow-y: auto !important;
        padding: 15px !important;
        padding-bottom: 90px !important;
        -webkit-overflow-scrolling: touch;
        height: 100vh !important;
    }

    #_task_modal .modal-body .row {
        display: block !important;
        width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        clear: both !important;
    }

    #_task_modal .modal-body .row::after {
        content: '' !important;
        display: block !important;
        clear: both !important;
    }

    #_task_modal .modal-body .row > .col-md-6,
    #_task_modal .modal-body .row > .col-md-12 {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        float: none !important;
        flex: unset !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        clear: both !important;
    }

    /* Đảm bảo form-group xếp dọc */
    #_task_modal .form-group {
        width: 100% !important;
        margin-bottom: 15px !important;
    }

    /* Bỏ flex của Bootstrap columns */
    #_task_modal .col-md-6,
    #_task_modal .col-md-12 {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        flex: unset !important;
        float: none !important;
    }

    #_task_modal .close {
        padding: 10px !important;
        margin: -10px -10px -10px auto !important;
        font-size: 28px !important;
        opacity: 0.8 !important;
    }

    /* Fixed footer dưới cùng chứa 2 nút (Close - Submit) ngang hàng */
    #_task_modal .modal-footer {
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        background-color: #fff !important;
        border-top: 1px solid #e5e7eb !important;
        padding: 12px 15px !important;
        z-index: 1050 !important;
        display: flex !important;
        justify-content: space-between !important;
        box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.03);
    }

    #_task_modal .modal-footer .btn {
        width: 48% !important;
        margin: 0 !important;
        padding: 12px 0 !important;
        font-size: 15px !important;
        font-weight: 500 !important;
        border-radius: 8px !important;
    }

    /* Tối ưu các input, select cho dễ chạm, chống bị zoom màn hình trên iOS */
    #_task_modal .form-control {
        height: 44px !important;
        border-radius: 8px !important;
        font-size: 16px !important;
    }

    #_task_modal .bootstrap-select .dropdown-toggle {
        height: 44px !important;
        border-radius: 8px !important;
        padding: 10px 15px !important;
        font-size: 16px !important;
    }

    #_task_modal label {
        font-weight: 600 !important;
        margin-bottom: 8px !important;
        color: #374151 !important;
        font-size: 14px !important;
    }

    #_task_modal .form-group {
        margin-bottom: 20px !important;
    }

    /* Tối ưu tickbox/radio to hơn để dễ chạm */
    #_task_modal .checkbox label::before,
    #_task_modal .checkbox label::after,
    #_task_modal .radio label::before,
    #_task_modal .radio label::after {
        width: 24px !important;
        height: 24px !important;
    }

    #_task_modal .checkbox input[type="checkbox"],
    #_task_modal .radio input[type="radio"] {
        width: 24px !important;
        height: 24px !important;
        margin-top: 0 !important;
    }

    #_task_modal .checkbox label,
    #_task_modal .radio label {
        padding-left: 32px !important;
        font-size: 15px !important;
        line-height: 24px !important;
    }

    /* Bỏ bớt lề thừa trong container */
    #_task_modal hr {
        margin: 15px 0 !important;
    }
}
</style>
<div class="modal fade<?php if (isset($task)) {
                            echo ' edit';
                        } ?>" id="_task_modal" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel" <?php if ($this->input->get('opened_from_lead_id')) {
                                        echo 'data-lead-id=' . $this->input->get('opened_from_lead_id');
                                    } ?>>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo $title; ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        $rel_type = '';
                        $rel_id = '';
                        if (isset($task) || ($this->input->get('rel_id') && $this->input->get('rel_type'))) {
                            $rel_id = isset($task) ? $task->rel_id : $this->input->get('rel_id');
                            $rel_type = isset($task) ? $task->rel_type : $this->input->get('rel_type');
                        }
                        if (isset($task) && $task->billed == 1) {
                            echo '<div class="alert alert-success text-center no-margin">' . _l('task_is_billed', '<a href="' . admin_url('invoices/list_invoices/' . $task->invoice_id) . '" target="_blank">' . format_invoice_number($task->invoice_id)) . '</a></div><br />';
                        }
                        ?>
                        <?php if (isset($task)) { ?>
                            <div class="pull-right mbot10 task-single-menu task-menu-options">
                                <div class="content-menu hide">
                                    <ul>
                                        <?php if (has_permission('tasks', '', 'create')) { ?>
                                            <?php
                                            $copy_template = "";
                                            if (total_rows(db_prefix() . 'task_assigned', array('taskid' => $task->id)) > 0) {
                                                $copy_template .= "<div class='checkbox checkbox-primary'><input type='checkbox' name='copy_task_assignees' id='copy_task_assignees' checked><label for='copy_task_assignees'>" . _l('task_single_assignees') . "</label></div>";
                                            }
                                            if (total_rows(db_prefix() . 'task_followers', array('taskid' => $task->id)) > 0) {
                                                $copy_template .= "<div class='checkbox checkbox-primary'><input type='checkbox' name='copy_task_followers' id='copy_task_followers' checked><label for='copy_task_followers'>" . _l('task_single_followers') . "</label></div>";
                                            }
                                            if (total_rows(db_prefix() . 'task_checklist_items', array('taskid' => $task->id)) > 0) {
                                                $copy_template .= "<div class='checkbox checkbox-primary'><input type='checkbox' name='copy_task_checklist_items' id='copy_task_checklist_items' checked><label for='copy_task_checklist_items'>" . _l('task_checklist_items') . "</label></div>";
                                            }
                                            if (total_rows(db_prefix() . 'files', array('rel_id' => $task->id, 'rel_type' => 'task')) > 0) {
                                                $copy_template .= "<div class='checkbox checkbox-primary'><input type='checkbox' name='copy_task_attachments' id='copy_task_attachments'><label for='copy_task_attachments'>" . _l('task_view_attachments') . "</label></div>";
                                            }
                                            $copy_template .= "<p>" . _l('task_status') . "</p>";
                                            $task_copy_statuses = hooks()->apply_filters('task_copy_statuses', $task_statuses);
                                            foreach ($task_copy_statuses as $copy_status) {
                                                $copy_template .= "<div class='radio radio-primary'><input type='radio' value='" . $copy_status['id'] . "' name='copy_task_status' id='copy_task_status_" . $copy_status['id'] . "'" . ($copy_status['id'] == hooks()->apply_filters('copy_task_default_status', 1) ? ' checked' : '') . "><label for='copy_task_status_" . $copy_status['id'] . "'>" . $copy_status['name'] . "</label></div>";
                                            }
                                            $copy_template .= "<div class='text-center'>";
                                            $copy_template .= "<button type='button' data-task-copy-from='" . $task->id . "' class='btn btn-success copy_task_action'>" . _l('copy_task_confirm') . "</button>";
                                            $copy_template .= "</div>";
                                            ?>
                                            <li><a href="#" onclick="return false;" data-placement="bottom"
                                                    data-toggle="popover"
                                                    data-content="<?php echo htmlspecialchars($copy_template); ?>"
                                                    data-html="true"><?php echo _l('task_copy'); ?></span></a>
                                            </li>
                                        <?php } ?>
                                        <?php if (has_permission('tasks', '', 'delete')) { ?>
                                            <li>
                                                <a href="<?php echo admin_url('tasks/delete_task/' . $task->id); ?>"
                                                    class="_delete task-delete">
                                                    <?php echo _l('task_single_delete'); ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <?php if (has_permission('tasks', '', 'delete') || has_permission('tasks', '', 'create')) { ?>
                                    <a href="#" onclick="return false;" class="trigger manual-popover mright5">
                                        <i class="fa fa-circle-thin" aria-hidden="true"></i>
                                        <i class="fa fa-circle-thin" aria-hidden="true"></i>
                                        <i class="fa fa-circle-thin" aria-hidden="true"></i>
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <div class="checkbox checkbox-primary no-mtop checkbox-inline task-add-edit-public hide">
                            <input type="checkbox" id="task_is_public" name="is_public" <?php if (isset($task)) {
                                                                                            if ($task->is_public == 1) {
                                                                                                echo 'checked';
                                                                                            }
                                                                                        }; ?>>
                            <label for="task_is_public" data-toggle="tooltip" data-placement="bottom"
                                title="<?php echo _l('task_public_help'); ?>"><?php echo _l('task_public'); ?></label>
                        </div>
                        <div class="checkbox checkbox-primary checkbox-inline task-add-edit-billable hide">
                            <input type="checkbox" id="task_is_billable"
                                name="billable" <?php if ((isset($task) && $task->billable == 1) || (!isset($task) && get_option('task_biillable_checked_on_creation') == 1)) {
                                                    echo ' checked';
                                                } ?>>
                            <label for="task_is_billable"><?php echo _l('task_billable'); ?></label>
                        </div>
                        <div class="task-visible-to-customer checkbox checkbox-inline checkbox-primary<?php if ((isset($task) && $task->rel_type != 'project') || !isset($task) || (isset($task) && $task->rel_type == 'project' && total_rows(db_prefix() . 'project_settings', array('project_id' => $task->rel_id, 'name' => 'view_tasks', 'value' => 0)) > 0)) {
                                                                                                            echo ' hide';
                                                                                                        } ?>">
                            <input type="checkbox" id="task_visible_to_client"
                                name="visible_to_client" <?php if (isset($task)) {
                                                                if ($task->visible_to_client == 1) {
                                                                    echo 'checked';
                                                                }
                                                            } ?>>
                            <label for="task_visible_to_client"><?php echo _l('task_visible_to_client'); ?></label>
                        </div>
                        <?php if (!isset($task)) { ?>
                            <a href="#" class="pull-right"
                                onclick="slideToggle('#new-task-attachments'); return false;">
                                <?php echo _l('attach_files'); ?>
                            </a>
                            <div id="new-task-attachments" class="hide">
                                <hr />
                                <div class="row attachments">
                                    <div class="attachment">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="attachment"
                                                    class="control-label"><?php echo _l('add_task_attachments'); ?></label>
                                                <div class="input-group">
                                                    <input type="file"
                                                        extension="<?php echo str_replace('.', '', get_option('allowed_files')); ?>"
                                                        filesize="<?php echo file_upload_max_size(); ?>"
                                                        class="form-control" name="attachments[0]">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-success add_more_attachments p8"
                                                            type="button"><i class="fa fa-plus"></i></button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                            if ($this->input->get('ticket_to_task')) {
                                echo form_hidden('ticket_to_task');
                            }
                        } ?>
                        <hr />
                        <div class="form-group">
                            <?= lang('Nhóm Công Việc Phòng Ban', 'tasks_group') ?>
                            <select id="tasks_group" class="form-control selectpicker tasks_group" data-width="100%" name="tasks_group" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                <option></option>
                                <?php
                                $dtTaskGroup = get_table_where('tbl_task_group');
                                ?>
                                <?php if (!empty($dtTaskGroup)) {
                                    foreach ($dtTaskGroup as $key => $value) { ?>
                                        <option <?= !empty($task) && ($task->tasks_group == $value['id']) ? 'selected' : '' ?> data-subtext="<?= $value['code'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                        <?php
                        $value = (isset($task) ? $task->name : '');
                        if (!empty($_order_id)) {
                            $value = 'Tạo đơn hàng bán';
                        } else if (!empty($_po_id)) {
                            $value = 'Tạo lệnh sản xuất';
                        } else if (!empty($_import)) {
                            $value = 'Tạo nhập hàng';
                        } else if (!empty($_purchase_order)) {
                            $value = 'Tạo đơn hàng mua';
                        }
                        ?>
                        <?php echo render_input('name', 'task_add_edit_subject', $value); ?>
                        <div style="display: none;" class="hide task-hours<?php if (isset($task) && $task->rel_type == 'project' && total_rows(db_prefix() . 'projects', array('id' => $task->rel_id, 'billing_type' => 3)) == 0) {
                                                                                echo ' hide';
                                                                            } ?>">
                            <?php $value = (isset($task) ? $task->hourly_rate : 0); ?>
                            <?php echo render_input('hourly_rate', 'task_hourly_rate', $value); ?>
                        </div>
                        <div class="project-details<?php if ($rel_type != 'project') {
                                                        echo ' hide';
                                                    } ?>">
                            <div class="form-group">
                                <label for="milestone"><?php echo _l('task_milestone'); ?></label>
                                <select name="milestone" id="milestone" class="selectpicker" data-width="100%"
                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""></option>
                                    <?php foreach ($milestones as $milestone) { ?>
                                        <option value="<?php echo $milestone['id']; ?>" <?php if (isset($task) && $task->milestone == $milestone['id']) {
                                                                                            echo 'selected';
                                                                                        } ?>><?php echo $milestone['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?php $value = (!empty($task->department_id) ? $task->department_id : ((!empty($dtSuggest) && !empty($category_recommended_id) && $category_recommended_id == 28) ? [$dtSuggest[0]['department_id']] : (!empty($id_room) ? [$id_room] : []))); ?>
                                <?php echo render_select('department_id[]', (!empty($departments) ? $departments : []), ['id', 'name'], 'Phòng ban phụ trách', $value, ['multiple' => true], [], '', '', false) ?>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" app-field-wrapper="category_tasks">
                                    <label for="category_tasks" class="control-label">Mã công việc</label>
                                    <?php
                                    $value = (isset($task) ? $task->category_tasks : ((!empty($dtSuggest) && !empty($category_recommended_id) && $category_recommended_id == 28) ? $dtSuggest[0]['category_tasks'] : ''));
                                    if (!empty($_order)) {
                                        $value = MA_CONG_VIEC_DON_HANG; //Mở lệnh sản xuất
                                    } else if (!empty($_po_id)) {
                                        $value = MA_CONG_VIEC_LSX;
                                    } else if (!empty($_import)) {
                                        $value = MA_CONG_VIEC_NHAP_KHO;
                                    } else if (!empty($_purchase_order)) {
                                        $value = MA_CONG_VIEC_MUA_HANG;
                                    }
                                    ?>
                                    <select id="category_tasks" name="category_tasks" class="selectpicker" data-id="<?= (isset($task) ? $task->category_tasks : '') ?>" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                        <option></option>
                                        <?php if (!empty($category_tasks)) {
                                            foreach ($category_tasks as $key => $v) { ?>
                                                <?php $selected = $value == $v['id'] ? 'selected' : '' ?>
                                                <option value="<?= $v['id'] ?>" data-subtext="<?= $v['content'] ?>" data-departments="<?= $v['departments'] ?>" <?= $selected ?>><?= $v['code'] ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                </div>
                                <!--								--><?php //echo render_select('category_tasks', (!empty($category_tasks) ? $category_tasks : []), ['id', 'code', 'content'], 'Mã công việc', $value); 
                                                                        ?>
                            </div>

                            <div class="col-md-6">
                                <?php if (isset($task)) {
                                    $value = _d($task->startdate);
                                } else if (isset($start_date)) {
                                    $value = $start_date;
                                } else {
                                    $value = _d(date('Y-m-d H:i'));
                                }
                                $date_attrs = array();
                                if (isset($task) && $task->recurring > 0 && $task->last_recurring_date != null) {
                                    $date_attrs['disabled'] = true;
                                }
                                ?>
                                <?php echo render_datetime_input('startdate', 'task_add_edit_start_date', $value, $date_attrs); ?>
                            </div>
                            <div class="col-md-6">
                                <?php $value = (isset($task) ? _d($task->duedate) : ''); ?>
                                <?php echo render_datetime_input('duedate', 'task_add_edit_due_date', $value, $project_end_date_attrs); ?>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority"
                                        class="control-label"><?php echo _l('task_add_edit_priority'); ?></label>
                                    <select name="priority" class="selectpicker" id="priority" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <?php foreach (get_tasks_priorities() as $priority) { ?>
                                            <option value="<?php echo $priority['id']; ?>" <?php if (isset($task) && $task->priority == $priority['id'] || !isset($task) && get_option('default_task_priority') == $priority['id']) {
                                                                                                echo ' selected';
                                                                                            } ?>><?php echo $priority['name']; ?></option>
                                        <?php } ?>
                                        <?php hooks()->do_action('task_priorities_select', (isset($task) ? $task : 0)); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php
                                if (empty($branch)) {
                                    $branch = get_table_where('tblbranch');
                                }
                                ?>
                                <?php
                                $value = (!empty($task->id_branch) ? $task->id_branch : ((!empty($dtSuggest) && !empty($category_recommended_id) && $category_recommended_id == 28) ? $dtSuggest[0]['branch_id'] : 0));
                                if (!empty($_order)) {
                                    $value = $_order['id_branch'];
                                } else if (!empty($_po_id)) {
                                    $value = $_po['location_id'];
                                }
                                ?>
                                <?php echo render_select('id_branch', !empty($branch) ? $branch : [], ['id', 'name'], 'id_branch', $value) ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="repeat_every"
                                        class="control-label"><?php echo _l('task_repeat_every'); ?></label>
                                    <select name="repeat_every" id="repeat_every" class="selectpicker" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value=""></option>
                                        <option value="1-week" <?php if (isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'week') {
                                                                    echo 'selected';
                                                                } ?>><?php echo _l('week'); ?></option>
                                        <option value="2-week" <?php if (isset($task) && $task->repeat_every == 2 && $task->recurring_type == 'week') {
                                                                    echo 'selected';
                                                                } ?>>2 <?php echo _l('weeks'); ?></option>
                                        <option value="1-month" <?php if (isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'month') {
                                                                    echo 'selected';
                                                                } ?>>1 <?php echo _l('month'); ?></option>
                                        <option value="2-month" <?php if (isset($task) && $task->repeat_every == 2 && $task->recurring_type == 'month') {
                                                                    echo 'selected';
                                                                } ?>>2 <?php echo _l('months'); ?></option>
                                        <option value="3-month" <?php if (isset($task) && $task->repeat_every == 3 && $task->recurring_type == 'month') {
                                                                    echo 'selected';
                                                                } ?>>3 <?php echo _l('months'); ?></option>
                                        <option value="6-month" <?php if (isset($task) && $task->repeat_every == 6 && $task->recurring_type == 'month') {
                                                                    echo 'selected';
                                                                } ?>>6 <?php echo _l('months'); ?></option>
                                        <option value="1-year" <?php if (isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'year') {
                                                                    echo 'selected';
                                                                } ?>>1 <?php echo _l('year'); ?></option>
                                        <option value="custom" <?php if (isset($task) && $task->custom_recurring == 1) {
                                                                    echo 'selected';
                                                                } ?>><?php echo _l('Ngày'); ?></option>
                                        <option value="custom_day" <?php if (isset($task) && $task->custom_recurring == 2) {
                                                                        echo 'selected';
                                                                    } ?>><?php echo _l('recurring_day_to_month'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?= lang('Loại phiếu yêu cầu', 'category_recommended_id') ?>
                                <select name="category_recommended_id" data-none-selected-text="Loại phiếu yêu cầu" data-live-search="true" id="category_recommended_id" class="form-control selectpicker category_recommended_id">
                                    <option value=""></option>
                                    <?php if (!empty($categoryRecommended)) : ?>
                                        <?php foreach ($categoryRecommended as $key => $value) : ?>
                                            <option <?= !empty($task->category_recommended_id) && $task->category_recommended_id == $value['id'] ? 'selected' : !empty($category_recommended_id) && $category_recommended_id == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('Lệnh sản xuất', 'po_id') ?>
                                    <?php
                                    $value = (isset($task) ? $task->po_id : 0);
                                    if (!empty($_po_id)) {
                                        $value = $_po_id;
                                    }
                                    ?>
                                    <input type="text" onchange="changePo(this)" name="po_id" class="po_id" id="po_id" data-placeholder="<?= lang('Lệnh sản xuất') ?>" style="width: 100%;" value="<?= $value ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_service" class="control-label">
                                        <?php echo _l('Phiếu yêu cầu'); ?>
                                    </label>
                                    <select class="form-control suggest_id selectpicker" name="suggest_id" id="suggest_id" data-live-search="true" data-none-selected-text="<?= _l('dropdown_non_selected_tex') ?>">
                                        <option></option>
                                        <?php $valueSelected = !empty($task->suggest_id) ? $task->suggest_id : (!empty($suggest_id) ? $suggest_id : '') ?>
                                        <?php if (!empty($dtSuggest)) { ?>
                                            <?php foreach ($dtSuggest as $key => $value) { ?>
                                                <option data-subtext="<?= $value['staff_suggest_name'] ?>" <?= ($value['id'] == $valueSelected) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['reference_no'] ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stage_id" class="control-label">
                                        <?php echo _l('Công Đoạn'); ?>
                                    </label>
                                    <select class="form-control stage_id selectpicker" name="stage_id" id="stage_id" data-live-search="true" data-none-selected-text="<?= _l('dropdown_non_selected_tex') ?>">
                                        <option></option>
                                        <?php if (!empty($dtStage)) { ?>
                                            <?php foreach ($dtStage as $key => $value) { ?>
                                                <option <?= !empty($task) && $task->stage_id == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php
                                    $value = (isset($task) ? $task->order_id : '');
                                    if (!empty($_order)) {
                                        $value = $_order['id'];
                                    }
                                    ?>
                                    <?= lang('Đơn đặt hàng', 'order_id') ?>
                                    <input type="text" name="order_id" class="order_id modal-select2" id="order_id" data-placeholder="<?= lang('Đơn đặt hàng') ?>" style="width: 100%;" value="<?= $value ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php
                                    $value = (isset($task) ? $task->purchase_order_id : '');
                                    if (!empty($_purchase_order)) {
                                        $value = $_purchase_order['id'];
                                    }
                                    ?>
                                    <?= lang('Đơn đặt hàng mua', 'purchase_order_id') ?>
                                    <input type="text" name="purchase_order_id" class="purchase_order_id modal-select2" id="purchase_order_id" data-placeholder="<?= lang('Đơn đặt hàng mua') ?>" style="width: 100%;" value="<?= $value ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php
                                    $value = (isset($task) ? $task->import_id : '');
                                    if (!empty($_import)) {
                                        $value = $_import['id'];
                                    }
                                    ?>
                                    <?= lang('Nhập kho', 'import_id') ?>
                                    <input type="text" name="import_id" class="import_id modal-select2" id="import_id" data-placeholder="<?= lang('Nhập kho') ?>" style="width: 100%;" value="<?= $value ?>">
                                </div>
                            </div>
                        </div>
                        <div class="custom_day_to_month mbot20 <?= (empty($task) || (isset($task) && $task->custom_recurring != 2)) ? 'hide' : '' ?>">
                            <div class="form-group">
                                <label for="custom_day" class="control-label">Ngày lặp</label>
                                <?php $list_custom_day = [] ?>
                                <?php if (!empty($task)) {
                                    $get_day_custom = get_table_where('tbltasks_repeat_day', ['taskid' => $task->id]);
                                    foreach ($get_day_custom as $key => $value) {
                                        $list_custom_day[$value['day']] = true;
                                    }
                                } ?>
                                <select name="custom_day[]" id="custom_day" data-live-search="true" data-actions-box="true" class="selectpicker" multiple data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php for ($j = 1; $j <= 31; $j++) { ?>
                                        <option value="<?= $j ?>" <?= !empty($list_custom_day[$j]) ? 'selected' : '' ?> <?php if (isset($task) && $task->repeat_every == 'custom_day' && $task->recurring_type == $j) {
                                                                                                                            echo 'selected';
                                                                                                                        } ?>><?= $j ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="recurring_custom <?php if ((isset($task) && $task->custom_recurring != 1) || (!isset($task))) {
                                                            echo 'hide';
                                                        } ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php $value = (isset($task) && $task->custom_recurring == 1 ? $task->repeat_every : 1); ?>
                                    <?php echo render_input('repeat_every_custom', '', $value, 'number', array('min' => 1)); ?>
                                </div>
                                <div class="col-md-6">
                                    <select name="repeat_type_custom" id="repeat_type_custom" class="selectpicker"
                                        data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value="day" <?php if (isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'day') {
                                                                echo 'selected';
                                                            } ?>><?php echo _l('task_recurring_days'); ?></option>
                                        <option value="week" <?php if (isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'week') {
                                                                    echo 'selected';
                                                                } ?>><?php echo _l('task_recurring_weeks'); ?></option>
                                        <option value="month" <?php if (isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'month') {
                                                                    echo 'selected';
                                                                } ?>><?php echo _l('task_recurring_months'); ?></option>
                                        <option value="year" <?php if (isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'year') {
                                                                    echo 'selected';
                                                                } ?>><?php echo _l('task_recurring_years'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="cycles_wrapper"
                            class="<?php if (!isset($task) || (isset($task) && $task->recurring == 0)) {
                                        echo ' hide';
                                    } ?>">
                            <?php $value = (isset($task) ? $task->cycles : 0); ?>
                            <div class="form-group recurring-cycles">
                                <label for="cycles"><?php echo _l('recurring_total_cycles'); ?>
                                    <?php if (isset($task) && $task->total_cycles > 0) {
                                        echo '<small>' . _l('cycles_passed', $task->total_cycles) . '</small>';
                                    }
                                    ?>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" <?php if ($value == 0) {
                                                                                    echo ' disabled';
                                                                                } ?> name="cycles" id="cycles"
                                        value="<?php echo $value; ?>" <?php if (isset($task) && $task->total_cycles > 0) {
                                                                            echo 'min="' . ($task->total_cycles) . '"';
                                                                        } ?>>
                                    <div class="input-group-addon">
                                        <div class="checkbox">
                                            <input type="checkbox" <?php if ($value == 0) {
                                                                        echo ' checked';
                                                                    } ?> id="unlimited_cycles">
                                            <label for="unlimited_cycles"><?php echo _l('cycles_infinity'); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <!--								--><?php //$value = (isset($task) ? $task->id_list_object : 0); 
                                                                        ?>
                                <!--                                --><?php //echo render_select('id_list_object', $departments_tasks, ['id', 'name'], 'Liên quan đến', $value)
                                                                        ?>
                                <table class="table table-department" style="margin-top: 5px;">
                                    <thead>
                                        <tr>
                                            <td><b>Liên quan đến</b></td>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="col-md-6 hide">
                                <div class="form-group">
                                    <label for="rel_type"
                                        class="control-label"><?php echo _l('task_related_to'); ?></label>
                                    <select name="rel_type" class="selectpicker" id="rel_type" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value=""></option>
                                        <!--                                        <option value="project" --><?php //if (isset($task) || $this->input->get('rel_type')) {
                                                                                                                //											if ($rel_type == 'project') {
                                                                                                                //												echo 'selected';
                                                                                                                //											}
                                                                                                                //										} 
                                                                                                                ?><!--<?php //echo _l('project'); 
                                                                                                                        ?>
                                        </option>-->
                                        <!--                                        <option value="invoice" --><?php //if (isset($task) || $this->input->get('rel_type')) {
                                                                                                                //											if ($rel_type == 'invoice') {
                                                                                                                //												echo 'selected';
                                                                                                                //											}
                                                                                                                //										} 
                                                                                                                ?><!--
											--><?php //echo _l('invoice'); 
                                                ?>
                                        <!--                                        </option>-->
                                        <option value="customer" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                        if ($rel_type == 'customer') {
                                                                            echo 'selected';
                                                                        }
                                                                    } ?>>
                                            <?php echo _l('client'); ?>
                                        </option>
                                        <!--                                        <option value="estimate" --><?php //if (isset($task) || $this->input->get('rel_type')) {
                                                                                                                //											if ($rel_type == 'estimate') {
                                                                                                                //												echo 'selected';
                                                                                                                //											}
                                                                                                                //										} 
                                                                                                                ?><!--
											--><?php //echo _l('estimate'); 
                                                ?>
                                        <!--                                        </option>-->
                                        <!--                                        <option value="contract" --><?php //if (isset($task) || $this->input->get('rel_type')) {
                                                                                                                //											if ($rel_type == 'contract') {
                                                                                                                //												echo 'selected';
                                                                                                                //											}
                                                                                                                //										} 
                                                                                                                ?><!--
											--><?php //echo _l('contract'); 
                                                ?>
                                        <!--                                        </option>-->
                                        <!--                                        <option value="ticket" --><?php //if (isset($task) || $this->input->get('rel_type')) {
                                                                                                                //											if ($rel_type == 'ticket') {
                                                                                                                //												echo 'selected';
                                                                                                                //											}
                                                                                                                //										} 
                                                                                                                ?><!--
											--><?php //echo _l('ticket'); 
                                                ?>
                                        <!--                                        </option>-->
                                        <!--                                        <option value="expense" --><?php //if (isset($task) || $this->input->get('rel_type')) {
                                                                                                                //											if ($rel_type == 'expense') {
                                                                                                                //												echo 'selected';
                                                                                                                //											}
                                                                                                                //										} 
                                                                                                                ?><!--
											--><?php //echo _l('expense'); 
                                                ?>
                                        <!--                                        </option>-->
                                        <!--                                        <option value="lead" --><?php //if (isset($task) || $this->input->get('rel_type')) {
                                                                                                            //											if ($rel_type == 'lead') {
                                                                                                            //												echo 'selected';
                                                                                                            //											}
                                                                                                            //										} 
                                                                                                            ?><!--
											--><?php //echo _l('lead'); 
                                                ?>
                                        <!--                                        </option>-->
                                        <!--                                        <option value="proposal" --><?php //if (isset($task) || $this->input->get('rel_type')) {
                                                                                                                //											if ($rel_type == 'proposal') {
                                                                                                                //												echo 'selected';
                                                                                                                //											}
                                                                                                                //										} 
                                                                                                                ?><!--
											--><?php //echo _l('proposal'); 
                                                ?>
                                        <!--                                        </option>-->
                                        <option value="supplier" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                        if ($rel_type == 'supplier') {
                                                                            echo 'selected';
                                                                        }
                                                                    } ?>>
                                            <?php echo _l('Nhà cung cấp'); ?>
                                        </option>
                                        <option value="products" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                        if ($rel_type == 'products') {
                                                                            echo 'selected';
                                                                        }
                                                                    } ?>>
                                            <?php echo _l('Thành phẩm'); ?>
                                        </option>
                                        <option value="materials" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                        if ($rel_type == 'materials') {
                                                                            echo 'selected';
                                                                        }
                                                                    } ?>>
                                            <?php echo _l('Nguyên vật liệu'); ?>
                                        </option>
                                        <option value="quotes" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                    if ($rel_type == 'quotes') {
                                                                        echo 'selected';
                                                                    }
                                                                } ?>>
                                            <?php echo _l('Báo giá'); ?>
                                        </option>
                                        <option value="orders" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                    if ($rel_type == 'orders') {
                                                                        echo 'selected';
                                                                    }
                                                                } ?>>
                                            <?php echo _l('Đơn đặt hàng bán'); ?>
                                        </option>
                                        <option value="import" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                    if ($rel_type == 'import') {
                                                                        echo 'selected';
                                                                    }
                                                                } ?>>
                                            <?php echo _l('Nhập kho'); ?>
                                        </option>
                                        <option value="order_production_details" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                                        if ($rel_type == 'order_production_details') {
                                                                                            echo 'selected';
                                                                                        }
                                                                                    } ?> <?= ($typePOD == "pod" ? 'selected' : '') ?>>
                                            <?php echo _l('order_production_details') ?>
                                        </option>
                                        <option value="production_report" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                                if ($rel_type == 'production_report') {
                                                                                    echo 'selected';
                                                                                }
                                                                            } ?>>
                                            <?php echo _l('Phiếu báo cáo'); ?>
                                        </option>
                                        <option value="maintenance_ticket" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                                if ($rel_type == 'maintenance_ticket') {
                                                                                    echo 'selected';
                                                                                }
                                                                            } ?>>
                                            <?php echo _l('Phiếu bảo trì'); ?>
                                        </option>
                                        <option value="template" data-class="hide" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                                        if ($rel_type == 'template') {
                                                                                            echo 'selected';
                                                                                        }
                                                                                    } ?>>
                                            <?php echo _l('Mẫu'); ?>
                                        </option>
                                        <option value="khun" data-class="hide" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                                    if ($rel_type == 'khun') {
                                                                                        echo 'selected';
                                                                                    }
                                                                                } ?>>
                                            <?php echo _l('Khuân'); ?>
                                        </option>
                                        <option value="KHTH" data-class="hide" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                                    if ($rel_type == 'KHTH') {
                                                                                        echo 'selected';
                                                                                    }
                                                                                } ?>>
                                            <?php echo _l('KHTH'); ?>
                                        </option>
                                        <option value="warehouse" data-class="hide" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                                        if ($rel_type == 'warehouse') {
                                                                                            echo 'selected';
                                                                                        }
                                                                                    } ?>>
                                            <?php echo _l('Kho'); ?>
                                        </option>
                                        <option value="releases" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                        if ($rel_type == 'releases') {
                                                                            echo 'selected';
                                                                        }
                                                                    } ?>>
                                            <?php echo _l('Giao hàng'); ?>
                                        </option>
                                        <option value="QA" data-class="hide" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                                    if ($rel_type == 'QA') {
                                                                                        echo 'selected';
                                                                                    }
                                                                                } ?>>
                                            <?php echo _l('QA'); ?>
                                        </option>
                                        <option value="HCNS" data-class="hide" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                                    if ($rel_type == 'HCNS') {
                                                                                        echo 'selected';
                                                                                    }
                                                                                } ?>>
                                            <?php echo _l('HCNS'); ?>
                                        </option>
                                        <option value="TCKT" data-class="hide" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                                    if ($rel_type == 'TCKT') {
                                                                                        echo 'selected';
                                                                                    }
                                                                                } ?>>
                                            <?php echo _l('TCKT'); ?>
                                        </option>
                                        <option value="procurement_supply" data-class="hide" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                                                    if ($rel_type == 'procurement_supply') {
                                                                                                        echo 'selected';
                                                                                                    }
                                                                                                } ?>>
                                            <?php echo _l('Cung ứng thu mua'); ?>
                                        </option>
                                        <option value="COO" data-class="hide" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                                    if ($rel_type == 'COO') {
                                                                                        echo 'selected';
                                                                                    }
                                                                                } ?>>
                                            <?php echo _l('COO'); ?>
                                        </option>
                                        <option value="internal_proposal" data-class="hide" <?php if (isset($task) || $this->input->get('rel_type')) {
                                                                                                if ($rel_type == 'internal_proposal') {
                                                                                                    echo 'selected';
                                                                                                }
                                                                                            } ?>>
                                            <?php echo _l('Phiếu đề xuất nội bộ'); ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 hide">
                                <div class="form-group<?php if (
                                                            $rel_type == 'procurement_supply'
                                                            || $rel_type == 'COO'
                                                            || $rel_type == 'TCKT'
                                                            || $rel_type == 'HCNS'
                                                            || $rel_type == 'QA'
                                                            || $rel_type == 'warehouse'
                                                            || $rel_type == 'KHTH'
                                                            || $rel_type == 'khun'
                                                            || $rel_type == 'template'
                                                            || $rel_type == ''
                                                        ) {
                                                            echo ' hide';
                                                        } ?>" id="rel_id_wrapper">
                                    <label for="rel_id" class="control-label"><span class="rel_id_label"></span></label>
                                    <div id="rel_id_select">
                                        <select name="rel_id" id="rel_id" class="ajax-sesarch" data-width="100%"
                                            data-live-search="true"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <?php if ($rel_id != '' && $rel_type != '') {
                                                $rel_data = get_relation_data($rel_type, $rel_id);
                                                $rel_val = get_relation_values($rel_data, $rel_type);
                                                echo '<option value="' . $rel_val['id'] . '" selected>' . $rel_val['name'] . '</option>';
                                            } ?>
                                            <?php
                                            if (!empty($pod)) {
                                                echo '<option data-quantity-shift="' . $pod['quantity_rest'] . '" value="' . $pod['id'] . '" selected>' . $pod['reference_no'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 hide custom-pod" <?= (!empty($pod) || $rel_type == "order_production_details") ? 'style="display: block;"' : 'style="display: none;"' ?>>
                                <div class="form-group">
                                    <label for="shift_work"><?php echo _l('tnh_shift_work'); ?></label>
                                    <select name="shift_work" id="shift_work"
                                        class="form-control shift_work selectpicker">
                                        <option value=""></option>
                                        <?php if (!empty($shiftWork)) : ?>
                                            <?php foreach ($shiftWork as $key => $value) : ?>
                                                <option <?= (isset($task) && $task->shift_work == $value['id'] ? 'selected' : '') ?>
                                                    value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 hide custom-pod" <?= (!empty($pod) || $rel_type == "order_production_details") ? 'style="display: block;"' : 'style="display: none;"' ?>>
                                <div class="form-group">
                                    <label for="quantity_shift_work"><?php echo _l('quantity'); ?></label>
                                    <input type="text" name="quantity_shift_work" id="quantity_shift_work"
                                        class="form-control number-format"
                                        value="<?= isset($task) ? $task->quantity_shift_work : (!empty($pod) ? formatNumber($pod['quantity_rest']) : 0) ?>">
                                </div>
                            </div>
                        </div>
                        <?php
                        if (
                            isset($task)
                            && $task->status == Tasks_model::STATUS_COMPLETE
                            && (has_permission('create') || has_permission('edit'))
                        ) {
                            echo render_datetime_input('datefinished', 'task_finished', _dt($task->datefinished));
                        }
                        ?>
                        <div class="form-group checklist-templates-wrapper<?php if (count($checklistTemplates) == 0 || isset($task)) {
                                                                                echo ' hide';
                                                                            } ?>">
                            <label for="checklist_items"><?php echo _l('insert_checklist_templates'); ?></label>
                            <select id="checklist_items" name="checklist_items[]"
                                class="selectpicker checklist-items-template-select" multiple="1"
                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex') ?>"
                                data-width="100%" data-live-search="true" data-actions-box="true">
                                <option value="" class="hide"></option>
                                <?php foreach ($checklistTemplates as $chkTemplate) { ?>
                                    <option value="<?php echo $chkTemplate['id']; ?>">
                                        <?php echo $chkTemplate['description']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <div id="inputTagsWrapper">
                                <label for="tags" class="control-label"><i class="fa fa-tag"
                                        aria-hidden="true"></i> <?php echo _l('tags'); ?>
                                </label>
                                <input type="text" class="tagsinput" id="tags" name="tags"
                                    value="<?php echo (isset($task) ? prep_tags_input(get_tags_in($task->id, 'task')) : ''); ?>"
                                    data-role="tagsinput">
                            </div>
                        </div>
                        <?php $rel_id_custom_field = (isset($task) ? $task->id : false); ?>
                        <?php echo render_custom_fields('tasks', $rel_id_custom_field); ?>
                        <hr />
                        <p class="bold"><?php echo _l('task_add_edit_description'); ?></p>
                        <?php
                        // onclick and onfocus used for convert ticket to task too
                        echo render_textarea('description', '', (isset($task) ? $task->description : ''), array('rows' => 6, 'placeholder' => _l('task_add_description'), 'data-task-ae-editor' => true, !is_mobile() ? 'onclick' : 'onfocus' => (!isset($task) || isset($task) && $task->description == '' ? 'init_editor(\'.tinymce-task\', {height:200, auto_focus: true});' : '')), array(), 'no-mbot', 'tinymce-task'); ?>
                    </div>
                </div>
            </div>

            <?php
            if (!empty($rel_append_id)) {
                echo form_hidden('rel_append_id', $rel_append_id);
            }
            ?>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
    <script>
        ajaxSelectParams('#po_id', 'admin/tasks/searchPo', $('#po_id').val(), true, true);
        ajaxSelectParams('#order_id', 'admin/tasks/searchOrders', $('#order_id').val(), true, true);
        ajaxSelectParams('#purchase_order_id', 'admin/tasks/searchPurchaseOrder', $('#purchase_order_id').val(), true, true);
        ajaxSelectParams('#import_id', 'admin/tasks/searchImport', $('#import_id').val(), true, true);

        function changePo(_this) {
            po_id = $(_this).val();
            $.ajax({
                    url: "<?= base_url('admin/tasks/getStageByPo') ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        csrf_token_name: hash,
                        po_id: po_id,
                    },
                })
                .done(function(data) {
                    option = '<option></option>';
                    console.log(data.dtStage.length)
                    if (data.dtStage.length > 0) {
                        $.each(data.dtStage, function(k, v) {
                            option += `<option value="${v.id}">${v.name}</option>`;
                        })
                    }
                    $("select#stage_id").html(option);
                    $("select#stage_id").selectpicker('refresh');
                })
                .fail(function() {
                    console.log("error");
                });
        }
        $('select#category_recommended_id').change(function() {
            var category_recommended_id = $(this).val();
            var data = {};
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            data['category_recommended_id'] = category_recommended_id;

            $.post(admin_url + 'internal_proposal/getSuggestByRecommendedSingle', data, function(data) {
                data = JSON.parse(data);
                $('select#suggest_id').html(`<option></option>`);
                $.each(data, function(index, value) {
                    $('select#suggest_id').append(`<option data-subtext="${value.staff_suggest_name}" value="${value.id}">${value.reference_no}</option>`);
                })
                $('select#suggest_id').selectpicker('refresh');
            });
        });
        var _rel_id = $('#rel_id'),
            _rel_type = $('#rel_type'),
            _rel_id_wrapper = $('#rel_id_wrapper'),
            data = {};
        var _milestone_selected_data;
        _milestone_selected_data = undefined;

        function convertDate(date) {
            day = date.slice(0, 2);
            month = date.slice(3, 5);
            year = date.slice(6, 10);
            return new Date(month + '/' + day + '/' + year);
        }
        $('#startdate').change(function(event) {
            var pickDate = $(this).val();
            $('#duedate').val('');
            $('#duedate').datetimepicker({
                format: app.options.date_format + ' H:i',
                timepicker: 1,
                minDate: convertDate(pickDate),
            })
        });
        $(function() {
            var startdate = $('#startdate').val();
            $('#duedate').datetimepicker({
                format: app.options.date_format + ' H:i',
                timepicker: 1,
                minDate: convertDate(startdate),
            })
            $("body").off("change", "#rel_id");
            var inner_popover_template = '<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';
            $('#_task_modal .task-menu-options .trigger').popover({
                html: true,
                placement: "bottom",
                trigger: 'click',
                title: "<?php echo _l('actions'); ?>",
                content: function() {
                    return $('body').find('#_task_modal .task-menu-options .content-menu').html();
                },
                template: inner_popover_template
            });
            custom_fields_hyperlink();
            appValidateForm($('#task-form'), {
                name: 'required',
                startdate: 'required',
                id_branch: 'required',
                <?= empty($task->repeat_every) && empty($task->custom_recurring) ? "duedate: 'required'," : '' ?>
                <?= !empty($task) && $task->custom_recurring == 2 ? "custom_day: 'required'," : '' ?>
                <?= !empty($task) && $task->custom_recurring == 2 ? "'custom_day[]': 'required'," : '' ?>
                repeat_every_custom: {
                    min: 1
                },
            }, task_form_handler);

            $('#repeat_every').change(function() {
                if ($(this).val() != "") {
                    var objectValidate = {
                        name: 'required',
                        startdate: 'required',
                        id_branch: 'required',
                        repeat_every_custom: {
                            min: 1
                        },
                    };
                    if ($(this).val() == 'custom_day') {
                        objectValidate['custom_day[]'] = 'required';
                        objectValidate['custom_day'] = 'required';
                    }

                    appValidateForm($('#task-form'), objectValidate, task_form_handler);
                    $('label[for="duedate"]').find('.req').remove();
                } else {
                    appValidateForm($('#task-form'), {
                        name: 'required',
                        startdate: 'required',
                        duedate: 'required',
                        id_branch: 'required',
                        repeat_every_custom: {
                            min: 1
                        },
                    }, task_form_handler);
                    $('label[for="custom_day"]').find('.req').remove();
                }
            })

            $('.rel_id_label').html(_rel_type.find('option:selected').text());
            _rel_type.on('change', function() {
                var clonedSelect = _rel_id.html('').clone();
                _rel_id.selectpicker('destroy').remove();
                _rel_id = clonedSelect;
                $('#rel_id_select').append(clonedSelect);
                $('.rel_id_label').html(_rel_type.find('option:selected').text());
                task_rel_select();
                if ($(this).val() != '') {
                    _rel_id_wrapper.removeClass('hide');
                } else {
                    _rel_id_wrapper.addClass('hide');
                }
                if ($('#rel_type').find('option:selected').data('class') == 'hide') {
                    _rel_id_wrapper.addClass('hide');
                }
                rel_type_custom = _rel_type.val();
                if (rel_type_custom == 'order_production_details') {
                    $('.custom-pod').css('display', 'block');
                } else {
                    $('.custom-pod').css('display', 'none');
                }
                init_project_details(_rel_type.val());
            });
            init_datepicker();
            init_color_pickers();
            init_selectpicker();
            task_rel_select();
            $('body').on('change', '#rel_id', function() {
                if ($(this).val() != '') {
                    if (_rel_type.val() == 'project') {
                        $.get(admin_url + 'projects/get_rel_project_data/' + $(this).val() + '/' + taskid, function(project) {
                            $("select[name='milestone']").html(project.milestones);
                            if (typeof(_milestone_selected_data) != 'undefined') {
                                $("select[name='milestone']").val(_milestone_selected_data.id);
                                $('input[name="duedate"]').val(_milestone_selected_data.due_date)
                            }
                            $("select[name='milestone']").selectpicker('refresh');
                            if (project.billing_type == 3) {
                                $('.task-hours').addClass('project-task-hours');
                            } else {
                                $('.task-hours').removeClass('project-task-hours');
                            }
                            if (project.deadline) {
                                var $duedate = $('#_task_modal #duedate');
                                var currentSelectedTaskDate = $duedate.val();
                                $duedate.attr('data-date-end-date', project.deadline);
                                $duedate.datetimepicker('destroy');
                                init_datepicker($duedate);
                                if (currentSelectedTaskDate) {
                                    var dateTask = new Date(unformat_date(currentSelectedTaskDate));
                                    var projectDeadline = new Date(project.deadline);
                                    if (dateTask > projectDeadline) {
                                        $duedate.val(project.deadline_formatted);
                                    }
                                }
                            } else {
                                reset_task_duedate_input();
                            }
                            init_project_details(_rel_type.val(), project.allow_to_view_tasks);
                        }, 'json');
                    } else {
                        reset_task_duedate_input();
                        if (_rel_type.val() == 'order_production_details') {
                            $('#shift_work').val(1).selectpicker('refresh');
                            $.ajax({
                                type: 'GET',
                                url: site.base_url + 'admin/manufactures/getQuantityRest',
                                data: {
                                    'pod_id': $('#rel_id').val()
                                },
                                dataType: "json",
                                success: function(response) {
                                    if (response) {
                                        $('#quantity_shift_work').val(tnhFormatNumber(response.quantity_rest));
                                    }
                                }
                            });
                        }
                    }
                }
            });

            <?php if (!isset($task) && $rel_id != '') { ?>
                _rel_id.change();
            <?php } ?>

        });

        <?php if (isset($_milestone_selected_data)) { ?>
            _milestone_selected_data = '<?php echo json_encode($_milestone_selected_data); ?>';
            _milestone_selected_data = JSON.parse(_milestone_selected_data);
        <?php } ?>

        function task_rel_select() {
            var serverData = {};
            serverData.rel_id = _rel_id.val();
            data.type = _rel_type.val();
            console.log(serverData.rel_id);
            if (serverData.rel_id == null) {
                ajaxReloadFrist(data.type, _rel_id);
            }
            init_ajax_search(_rel_type.val(), _rel_id, serverData);
        }

        function ajaxReloadFrist(type, _rel_id) {
            var data = {};
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            data['type'] = type;
            data['limit_search'] = 5;
            $.ajax({
                url: admin_url + "misc/get_relation_data",
                type: "POST",
                dataType: "JSON",
                data: data,
                success: function(result) {
                    $.each(result, function(index, value) {
                        subtext = '';
                        if (value.subtext) {
                            subtext = ' data-subtext="' + value.subtext + '" ';
                        }
                        content = '';
                        if (value.content) {
                            content = ' data-content="' + value.content + '" ';
                        }
                        $(_rel_id).append(`<option ${content} ${subtext} value="${value.id}">${value.name}</option>`);
                    })
                    $(_rel_id).selectpicker('refresh');
                }
            });
        }

        function init_project_details(type, tasks_visible_to_customer) {
            var wrap = $('.non-project-details');
            var wrap_task_hours = $('.task-hours');
            if (type == 'project') {
                if (wrap_task_hours.hasClass('project-task-hours') == true) {
                    wrap_task_hours.removeClass('hide');
                } else {
                    wrap_task_hours.addClass('hide');
                }
                wrap.addClass('hide');
                $('.project-details').removeClass('hide');
            } else {
                wrap_task_hours.removeClass('hide');
                wrap.removeClass('hide');
                $('.project-details').addClass('hide');
                $('.task-visible-to-customer').addClass('hide').prop('checked', false);
            }
            if (typeof(tasks_visible_to_customer) != 'undefined') {
                if (tasks_visible_to_customer == 1) {
                    $('.task-visible-to-customer').removeClass('hide');
                    $('.task-visible-to-customer input').prop('checked', true);
                } else {
                    $('.task-visible-to-customer').addClass('hide')
                    $('.task-visible-to-customer input').prop('checked', false);
                }
            }
        }

        function reset_task_duedate_input() {
            var $duedate = $('#_task_modal #duedate');
            $duedate.removeAttr('data-date-end-date');
            $duedate.datetimepicker('destroy');
            init_datepicker($duedate);
        }

        <?php if (!empty($typePOD) && $typePOD == "pod") : ?>
            $(document).ready(function() {
                // $('#rel_type').val('order_production_details').trigger('change');
                _rel_id_wrapper.removeClass('hide');
                $('#rel_type option:not(:selected)').attr('disabled', true);
                // $('#rel_type').attr('disabled',true);
                // $('#rel_id').attr('disabled',true);
                // $('#rel_type option:selected').attr('disabled',true);
                // $('#rel_id option:selected').removeAttr('disabled');
            });
        <?php endif ?>


        // $('#category_tasks').change(function () {
        //     $('#name').val($('#category_tasks').find('option:selected').attr('data-subtext'));
        //     var departments = $('#category_tasks').find('option:selected').attr('data-departments') + '';
        //     $('select[name="department_id[]"]').val(departments.split(',')).selectpicker('refresh');
        // })

        $('select[name="department_id[]"]').change(function() {
            $('.table-department').find('tbody').html('');
            var department = $('select[name="department_id[]"]').find('option:selected');
            var ids = '';
            $.each(department, function(index, value) {
                $('.table-department').find('tbody').append(`<tr><td>${$(value).text()}</td></tr>`);
                ids += $(value).val() + ',';
            })
            $('#category_tasks').find('option:gt(0)').remove();
            var checkedid = $('#category_tasks').attr('data-id');
            $.post(admin_url + "tasks/listcodetasks", {
                department: ids,
                [csrfData['token_name']]: csrfData['hash']
            }, function(data) {
                $('#category_tasks').html(data);
                $('#category_tasks').val(checkedid);
                $('#category_tasks').selectpicker('refresh');
            })
        })
        // $('select[name="department_id[]"]').trigger('change');

        // Quy trình mobile: Xếp dọc (1 cột)
        function initMobileLayout() {
            if (window.innerWidth <= 768) {
                var modalBody = document.querySelector('#_task_modal .modal-body');
                if (modalBody) {
                    modalBody.style.flex = 'unset';
                    modalBody.style.display = 'block';
                }

                var rows = document.querySelectorAll('#_task_modal .modal-body .row');
                rows.forEach(function(row) {
                    row.style.display = 'block';
                    row.style.width = '100%';
                    row.style.margin = '0';
                    row.style.padding = '0';
                    row.style.clear = 'both';

                    var cols = row.querySelectorAll('[class*="col-md-"]');
                    cols.forEach(function(col) {
                        col.style.display = 'block';
                        col.style.width = '100%';
                        col.style.maxWidth = '100%';
                        col.style.flex = 'unset';
                        col.style.float = 'none';
                        col.style.paddingLeft = '0';
                        col.style.paddingRight = '0';
                        col.style.marginLeft = '0';
                        col.style.marginRight = '0';
                    });
                });
            }
        }

        // Chạy khi modal mở
        $('#_task_modal').on('shown.bs.modal', function () {
            initMobileLayout();
        });

        // Chạy ngay nếu modal đang hiển thị
        if ($('#_task_modal').hasClass('in')) {
            initMobileLayout();
        }

        // Chạy khi resize window
        window.addEventListener('resize', function() {
            initMobileLayout();
        });
    </script>